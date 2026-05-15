<?php

namespace Boctulus\Simplerest\Modules\Security\Controllers;

use Boctulus\Simplerest\Core\Api\ResourceController;
use Boctulus\Simplerest\Core\Libs\DB;
use Boctulus\Simplerest\Core\Security\Compiler\EffectivePermissionCompiler;
use Boctulus\Simplerest\Core\Security\Compiler\TbPermissionBits;
use Boctulus\Simplerest\Core\Security\Domain\AclContext;
use Boctulus\Simplerest\Core\Security\Snapshot\AclSnapshot;
use Boctulus\Simplerest\Modules\Security\AclResolutionExplainer;

/**
 * Policy Resolution Inspector — read-only ACL introspection endpoints.
 *
 * The controller loads per-user data and delegates ALL authorization logic to
 * AclContext::withCompiled() + AclResolutionExplainer. It never expands
 * read_all, merges roles or interprets bitmasks itself.
 *
 * Inspector scope: role + user policy layers ONLY. Folder, ownership and
 * row-level access are NOT modeled here (see docs/ACL.md).
 */
class AclInspectorController extends ResourceController
{
    private const STD_ACTIONS = ['list_all', 'show_all', 'list', 'show', 'create', 'update', 'delete'];

    function __construct()
    {
        global $api_version;
        $api_version = 'v1';

        parent::__construct();
    }

    // ---------------------------------------------------------------- routes

    public function assignments()
    {
        $uid = $this->requireUserId();

        $a = $this->loadUserAcl($uid);

        $tb = [];
        foreach ($a['tb_rows'] as $row) {
            $tb[$row['tb']] = [
                'can_list_all' => (int) ($row['can_list_all'] ?? 0),
                'can_show_all' => (int) ($row['can_show_all'] ?? 0),
                'can_list'     => (int) ($row['can_list']     ?? 0),
                'can_show'     => (int) ($row['can_show']     ?? 0),
                'can_create'   => (int) ($row['can_create']   ?? 0),
                'can_update'   => (int) ($row['can_update']   ?? 0),
                'can_delete'   => (int) ($row['can_delete']   ?? 0),
                'id'           => $row['id'] ?? null,
            ];
        }

        $denyPerms = [];
        foreach ($a['deny'] as $res => $actions) {
            $denyPerms[$res] = array_keys($actions);
        }

        [$snapVer, $hash] = $this->versions($uid, $a);

        $this->json([
            'user_id'           => $uid,
            'roles'             => $a['role_objs'],
            'user_sp_perms'     => $a['sp'],
            'user_deny_sp_perms'=> array_keys($a['deny_sp']),
            'user_tb_perms'     => $tb,
            'user_deny_perms'   => $denyPerms,
            'acl_context_hash'  => $hash,
            'snapshot_version'  => $snapVer,
        ]);
    }

    public function effective()
    {
        $uid = $this->requireUserId();

        $snapshot = acl()->getSnapshot();
        $a        = $this->loadUserAcl($uid);
        $context  = $this->buildContext($uid, $a, $snapshot);
        $explainer = new AclResolutionExplainer();

        $registry = $this->resourceRegistry($snapshot, $a);

        $resources = [];
        foreach ($registry as $res) {
            $bucket = [];
            foreach (self::STD_ACTIONS as $action) {
                $r = $explainer->explain($context, $snapshot, $res, $action);
                if ($r['result'] === 'deny' && !$r['resolution_path']) {
                    continue;
                }
                if (count($r['resolution_path']) === 1
                    && $r['resolution_path'][0]['origin'] === AclResolutionExplainer::ORIGIN_DEFAULT) {
                    continue;
                }
                $d = $r['decisive'];
                $bucket[$action] = [
                    'result'    => $r['result'],
                    'origin'    => $d['origin'],
                    'source'    => $d['source'],
                    'inherited' => $d['origin'] === AclResolutionExplainer::ORIGIN_ROLE
                                   || $d['origin'] === AclResolutionExplainer::ORIGIN_WILDCARD,
                    'conflict'  => $r['has_conflict'],
                ];
            }
            if ($bucket) {
                $resources[$res] = $bucket;
            }
        }

        $capabilities = [];
        foreach ($this->validSpPerms($snapshot) as $sp) {
            $c = $explainer->explainCapability($context, $sp);
            if ($c['origin'] === AclResolutionExplainer::ORIGIN_DEFAULT) {
                continue;
            }
            $capabilities[$sp] = $c;
        }

        [$snapVer, $hash, $genAt] = $this->versions($uid, $a, true);

        $this->json([
            'user_id'               => $uid,
            'resources'             => $resources,
            'capabilities'          => $capabilities,
            'scope'                 => $this->scopeNote(),
            'acl_context_hash'      => $hash,
            'snapshot_version'      => $snapVer,
            'snapshot_generated_at' => $genAt,
        ]);
    }

    public function explain()
    {
        $uid      = $this->requireUserId();
        $resource = request()->getQuery('resource');
        $action   = request()->getQuery('action');

        if (empty($resource) || empty($action)) {
            $this->json(['error' => 'resource and action are required'], 400);
        }

        $snapshot  = acl()->getSnapshot();
        $a         = $this->loadUserAcl($uid);
        $context   = $this->buildContext($uid, $a, $snapshot);
        $explainer = new AclResolutionExplainer();

        $r = $explainer->explain($context, $snapshot, $resource, $action);

        [$snapVer, $hash, $genAt] = $this->versions($uid, $a, true);

        $this->json($r + [
            'scope'                 => $this->scopeNote(),
            'acl_context_hash'      => $hash,
            'snapshot_version'      => $snapVer,
            'snapshot_generated_at' => $genAt,
        ]);
    }

    public function capabilities()
    {
        $snapshot = acl()->getSnapshot();
        $this->json(['capabilities' => $this->validSpPerms($snapshot)]);
    }

    /**
     * Deduped, ordered list of valid special permissions. The seeded
     * sp_permissions table currently contains repeated rows, so the snapshot's
     * validSpPerms is not unique by itself.
     *
     * @return string[]
     */
    private function validSpPerms(AclSnapshot $snapshot): array
    {
        return array_values(array_unique($snapshot->validSpPerms));
    }

    public function resources()
    {
        $snapshot = acl()->getSnapshot();
        $a        = $this->loadUserAcl((int) (request()->getQuery('user_id') ?? 0), false);
        $this->json([
            'resources'  => array_values($this->resourceRegistry($snapshot, $a)),
            'is_partial' => true,
        ]);
    }

    public function userLookup()
    {
        $q = trim(request()->getQuery('q') ?? '');
        if (strlen($q) < 2) {
            $this->json(['error' => 'q must be at least 2 chars'], 400);
        }

        $usersTable = get_users_table();
        $model      = get_user_model_name();
        $emailField = $model::$email;
        $userField  = $model::$username;
        $idField    = get_id_name($usersTable);

        $rows = DB::table($usersTable)
            ->select([$idField, $emailField, $userField])
            ->orWhereLike($emailField, '%' . $q . '%')
            ->orWhereLike($userField,  '%' . $q . '%')
            ->limit(10)
            ->get();

        $results = array_map(function($r) use ($idField, $emailField, $userField) {
            return [
                'id'       => $r[$idField],
                'email'    => $r[$emailField] ?? '',
                'username' => $r[$userField]  ?? '',
            ];
        }, $rows ?: []);

        $this->json(['results' => $results]);
    }

    public function roleGraph()
    {
        $snapshot = acl()->getSnapshot();

        $nodes = [];
        foreach ($snapshot->rolePerms as $name => $rp) {
            $nodes[] = [
                'name'     => $name,
                'role_id'  => $rp['role_id']      ?? 0,
                'sp'       => $rp['sp_permissions'] ?? [],
                'tb'       => array_keys($rp['tb_permissions'] ?? []),
                'is_guest' => ($name === $snapshot->guestName),
            ];
        }

        // Sort by role_id (level) ascending
        usort($nodes, fn($a, $b) => $a['role_id'] <=> $b['role_id']);

        $edges = [];
        foreach ($snapshot->parentRoleNames as $child => $parent) {
            $edges[] = ['from' => $child, 'to' => $parent];
        }

        $this->json([
            'nodes'       => $nodes,
            'edges'       => $edges,
            'guest'       => $snapshot->guestName,
            'registered'  => $snapshot->registeredName,
        ]);
    }

    // ---------------------------------------------------------------- guards

    private function guardAuthenticated(): void
    {
        if (auth()->isGuest()) {
            $this->json(['error' => 'Not authenticated'], 401);
        }
    }

    private function guardGrant(): void
    {
        $this->guardAuthenticated();

        $hasGrant = false;
        try {
            $hasGrant = acl()->hasSpecialPermission('grant')
                || acl()->hasAnyRoleOrHigher(['admin']);
        } catch (\Throwable $e) {
            $hasGrant = false;
        }

        if (!$hasGrant) {
            $this->json(['error' => "Requires 'grant' capability"], 403);
        }
    }

    private function requireUserId(): int
    {
        $uid = request()->getQuery('user_id');
        if (empty($uid) || !is_numeric($uid)) {
            $this->json(['error' => 'user_id is required'], 400);
        }
        return (int) $uid;
    }

    // ----------------------------------------------------------- data loading

    /**
     * Load the per-user declared ACL state from DB. Pure data — no resolution.
     *
     * @return array{
     *   role_objs:array, role_names:array, sp:array, tb_rows:array,
     *   tb_packed:array, deny:array, deny_sp:array
     * }
     */
    private function loadUserAcl(int $uid, bool $strict = true): array
    {
        $out = [
            'role_objs'  => [],
            'role_names' => [],
            'sp'         => [],
            'tb_rows'    => [],
            'tb_packed'  => [],
            'deny'       => [],
            'deny_sp'    => [],
        ];

        if ($uid <= 0) {
            return $out;
        }

        // roles
        $roleIdName = [];
        foreach (DB::table('roles')->get() as $r) {
            $roleIdName[(int) $r['id']] = $r['name'];
        }
        foreach (DB::table('user_roles')->where(['user_id' => $uid])->get() as $ur) {
            $rid  = (int) $ur['role_id'];
            $name = $roleIdName[$rid] ?? null;
            if ($name === null) {
                continue;
            }
            $out['role_names'][]   = $name;
            $out['role_objs'][]    = ['role_id' => $rid, 'name' => $name];
        }

        // special permissions
        $spIdName = [];
        foreach (DB::table('sp_permissions')->get() as $sp) {
            $spIdName[(int) $sp['id']] = $sp['name'];
        }
        foreach (DB::table('user_sp_permissions')->where(['user_id' => $uid])->get() as $usp) {
            $name = $spIdName[(int) $usp['sp_permission_id']] ?? null;
            if ($name !== null) {
                $out['sp'][] = $name;
            }
        }

        // resource permissions (packed bitmask, replacement semantics)
        $tbRows = DB::table('user_tb_permissions')->where(['user_id' => $uid])->get();
        foreach ($tbRows as $row) {
            $out['tb_rows'][] = $row;
            $packed = 0;
            foreach (TbPermissionBits::ACTIONS as $action => $bit) {
                if (!empty($row['can_' . $action])) {
                    $packed |= $bit;
                }
            }
            $out['tb_packed'][$row['tb']] = $packed;
        }

        // explicit resource denies (v4 table — may be absent on older installs)
        try {
            foreach (DB::table('user_deny_permissions')->where(['user_id' => $uid])->get() as $d) {
                if (!empty($d['resource']) && !empty($d['action'])) {
                    $out['deny'][$d['resource']][$d['action']] = true;
                }
            }
        } catch (\Throwable $e) {
            // table not migrated yet — treat as no denies
        }

        // explicit capability denies (v5 table — deferred; absent for now)
        try {
            foreach (DB::table('user_deny_sp_permissions')->where(['user_id' => $uid])->get() as $d) {
                if (!empty($d['sp_name'])) {
                    $out['deny_sp'][$d['sp_name']] = true;
                }
            }
        } catch (\Throwable $e) {
            // table not migrated yet — capability deny symmetry pending
        }

        return $out;
    }

    private function buildContext(int $uid, array $a, AclSnapshot $snapshot): AclContext
    {
        return AclContext::withCompiled(
            $snapshot,
            new EffectivePermissionCompiler(),
            $uid,
            $a['role_names'],
            true,
            $a['sp'],
            $a['tb_packed'],
            $a['deny'],
            $a['deny_sp']
        );
    }

    private function resourceRegistry(AclSnapshot $snapshot, array $a): array
    {
        $set = [];

        foreach ($snapshot->rolePerms as $rp) {
            foreach (array_keys($rp['tb_permissions'] ?? []) as $res) {
                $set[$res] = true;
            }
        }
        foreach (array_keys($a['tb_packed']) as $res) {
            $set[$res] = true;
        }
        foreach (array_keys($a['deny']) as $res) {
            $set[$res] = true;
        }

        $list = array_keys($set);
        sort($list);
        return $list;
    }

    // -------------------------------------------------------- versions / meta

    /**
     * @return array{0:string,1:string,2?:?string}
     */
    private function versions(int $uid, array $a, bool $withGenAt = false): array
    {
        $snapVer = 'snap:unknown';
        $genAt   = null;

        try {
            $file = config('acl_file');
            if ($file && is_file($file)) {
                $snapVer = 'snap:' . substr(sha1_file($file), 0, 12);
                $genAt   = date('c', filemtime($file));
            }
        } catch (\Throwable $e) {
            // fall back to content-derived version below
        }

        if ($snapVer === 'snap:unknown') {
            $snapshot = acl()->getSnapshot();
            $snapVer  = 'snap:' . substr(sha1(json_encode([
                $snapshot->rolePerms,
                $snapshot->denyRolePerms,
                $snapshot->validSpPerms,
            ])), 0, 12);
        }

        $roles = $a['role_names'];   sort($roles);
        $sp    = $a['sp'];           sort($sp);
        $tb    = $a['tb_packed'];    ksort($tb);
        $deny  = $a['deny'];         ksort($deny);
        $dsp   = array_keys($a['deny_sp']); sort($dsp);

        $hash = 'aclv4:' . substr(sha1(json_encode([
            'snapshot_version' => $snapVer,
            'user_id'          => $uid,
            'roles'            => $roles,
            'sp'               => $sp,
            'tb'               => $tb,
            'deny'             => $deny,
            'deny_sp'          => $dsp,
        ])), 0, 16);

        return $withGenAt ? [$snapVer, $hash, $genAt] : [$snapVer, $hash];
    }

    private function scopeNote(): array
    {
        return [
            'covers'       => ['roles', 'user_sp_permissions', 'user_tb_permissions',
                               'user_deny_permissions', 'user_deny_sp_permissions',
                               'read_all/write_all wildcard'],
            'not_included' => ['folder_permissions', 'ownership-derived', 'row-level', 'attribute-level'],
            'note'         => 'Effective decisions are pre-folder and pre-ownership. '
                            . 'A resource shown as deny here may still be reachable via a shared '
                            . 'folder or ownership; an allow may still be filtered at row level.',
        ];
    }

    private function json($payload, int $code = 200): void
    {
        if (!headers_sent()) {
            http_response_code($code);
            header('Content-Type: application/json; charset=utf-8');
        }
        echo json_encode($payload, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT);
        exit;
    }
}
