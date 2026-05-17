<div class="container-fluid" id="aclInspector">

    <div class="row mb-3">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title"><i class="fas fa-shield-alt mr-2"></i> Policy Resolution Inspector</h3>
                </div>
                <div class="card-body">
                    <div class="row align-items-end g-2">
                        <div class="col-md-5 position-relative">
                            <label class="form-label mb-1">Usuario <small class="text-muted">(ID, email o username)</small></label>
                            <input type="text" id="userSearchInput" class="form-control" placeholder="ID, email o username…" autocomplete="off">
                            <div id="userSuggestions" class="list-group position-absolute w-100" style="z-index:1000;top:100%;display:none;max-height:200px;overflow-y:auto"></div>
                        </div>
                        <div class="col-md-auto">
                            <button id="btnLoadUser" class="btn btn-primary">
                                <i class="fas fa-search"></i> Inspect
                            </button>
                            <button id="btnRoles" class="btn btn-outline-info ms-1" title="Ver jerarquía de roles">
                                <i class="fas fa-sitemap"></i> Roles
                            </button>
                            <button id="btnClearUser" class="btn btn-outline-secondary d-none">&times; Clear</button>
                        </div>
                        <div class="col">
                            <div id="ctxMeta" class="small text-muted text-end d-none">
                                <span class="badge bg-secondary" id="ctxSnapVer">snapshot</span>
                                <span class="badge bg-dark" id="ctxHash" title="acl_context_hash (concurrency guard)">context</span>
                            </div>
                        </div>
                    </div>
                    <div id="globalAlert" class="alert mt-3 mb-0 d-none"></div>
                </div>
            </div>
        </div>
    </div>

    <!-- ============ MODAL: ROLE GRAPH ============ -->
    <div class="modal fade" id="rolesModal" tabindex="-1">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title"><i class="fas fa-sitemap me-2"></i> Role Hierarchy — <code>config/acl.php</code></h5>
                    <!-- botón a sintaxis Bootstrap 4 para compatibilidad con AdminLTE 3 -->
                    <button type="button"
                            class="close"
                            data-dismiss="modal">
                        <span>&times;</span>
                    </button>
                </div>
                <div class="modal-body" id="rolesModalBody">
                    <div class="text-center py-4"><i class="fas fa-spinner fa-spin"></i> Loading…</div>
                </div>
            </div>
        </div>
    </div>

    <div id="inspectorBody" class="d-none">
        <ul class="nav nav-tabs" id="aclTabs" role="tablist">
            <li class="nav-item" role="presentation">
                <a class="nav-link active" id="tab-assign-btn" data-toggle="tab" href="#tab-assign" role="tab">
                    <i class="fas fa-user-pen"></i> Assignments
                </a>
            </li>
            <li class="nav-item" role="presentation">
                <a class="nav-link" id="tab-eff-btn" data-toggle="tab" href="#tab-eff" role="tab">
                    <i class="fas fa-table-list"></i> Effective
                </a>
            </li>
            <li class="nav-item" role="presentation">
                <a class="nav-link" id="tab-exp-btn" data-toggle="tab" href="#tab-exp" role="tab">
                    <i class="fas fa-magnifying-glass-chart"></i> Explain
                </a>
            </li>
        </ul>

        <div class="tab-content border border-top-0 p-3 bg-white">

            <!-- ============ TAB 1: ASSIGNMENTS ============ -->
            <div class="tab-pane fade show active" id="tab-assign" role="tabpanel">

                <div id="dirtyBanner" class="alert alert-warning d-flex justify-content-between align-items-center" style="display:none!important">
                    <span><i class="fas fa-triangle-exclamation"></i> Unsaved Resource Policy changes.</span>
                    <span class="text-muted small">Use each resource's <strong>Save</strong> button to persist.</span>
                    <button type="button" class="close ms-2" id="btnDismissDirty" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                </div>

                <div class="card mb-3">
                    <div class="card-header"><strong><i class="fas fa-tags"></i> Roles</strong>
                        <span class="text-muted small ms-2">(read-only — edit via <code>user_roles</code> API)</span>
                    </div>
                    <div class="card-body" id="rolesBox"></div>
                </div>

                <div class="card mb-3">
                    <div class="card-header"><strong><i class="fas fa-key"></i> Capabilities</strong>
                        <span class="text-muted small ms-2">role-inherited vs. user-granted</span>
                    </div>
                    <div class="card-body"><div id="capsBox" class="row g-2"></div></div>
                </div>

                <div class="card mb-3">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <strong><i class="fas fa-table"></i> Resource Policies</strong>
                        <div class="input-group input-group-sm" style="max-width:320px">
                            <input type="text" id="newTbName" class="form-control" placeholder="add resource (e.g. products)">
                            <button class="btn btn-success" id="btnAddTb"><i class="fas fa-plus"></i></button>
                        </div>
                    </div>
                    <div class="card-body">
                        <p class="text-muted small">A user resource policy <strong>replaces</strong> the role-inherited set for that resource (replacement semantics).</p>
                        <div id="tbBox"></div>
                        <div id="tbEmpty" class="text-muted text-center py-3">No resource policy overrides.</div>
                    </div>
                </div>

                <div class="card">
                    <div class="card-header"><strong><i class="fas fa-ban text-danger"></i> Explicit Denies</strong>
                        <span class="text-muted small ms-2">beat every allow (incl. read_all/write_all)</span>
                    </div>
                    <div class="card-body">
                        <div class="row g-2 mb-3">
                            <div class="col-md-4"><input type="text" id="denyRes" class="form-control form-control-sm" placeholder="resource"></div>
                            <div class="col-md-4">
                                <select id="denyAct" class="form-select form-select-sm">
                                    <option value="">action…</option>
                                    <option>list_all</option><option>show_all</option><option>list</option>
                                    <option>show</option><option>create</option><option>update</option><option>delete</option>
                                </select>
                            </div>
                            <div class="col-md-4"><button id="btnAddDeny" class="btn btn-danger btn-sm"><i class="fas fa-plus"></i> Add deny</button></div>
                        </div>
                        <div id="denyBox"></div>
                        <div id="denyEmpty" class="text-muted text-center py-2">No explicit denies.</div>
                    </div>
                </div>
            </div>

            <!-- ============ TAB 2: EFFECTIVE ============ -->
            <div class="tab-pane fade" id="tab-eff" role="tabpanel">
                <div id="scopeBanner" class="alert alert-info small"></div>

                <div class="row g-2 mb-3 align-items-center">
                    <div class="col-md-3"><input type="text" id="fRes" class="form-control form-control-sm" placeholder="search resource"></div>
                    <div class="col-md-3"><input type="text" id="fAct" class="form-control form-control-sm" placeholder="search action"></div>
                    <div class="col-md-6">
                        <div class="form-check form-check-inline"><input class="form-check-input" type="checkbox" id="fConf"><label class="form-check-label small" for="fConf">conflicts</label></div>
                        <div class="form-check form-check-inline"><input class="form-check-input" type="checkbox" id="fDeny"><label class="form-check-label small" for="fDeny">denies</label></div>
                        <div class="form-check form-check-inline"><input class="form-check-input" type="checkbox" id="fOver"><label class="form-check-label small" for="fOver">overrides (user)</label></div>
                    </div>
                </div>

                <div id="effResources" class="accordion mb-3"></div>

                <div class="card">
                    <div class="card-header"><strong><i class="fas fa-key"></i> Effective Capabilities</strong></div>
                    <div class="card-body"><div id="effCaps" class="row g-2"></div></div>
                </div>

                <div class="text-muted small mt-3" id="effFooter"></div>
            </div>

            <!-- ============ TAB 3: EXPLAIN ============ -->
            <div class="tab-pane fade" id="tab-exp" role="tabpanel">
                <div id="scopeBanner2" class="alert alert-info small"></div>
                <div class="row g-2 mb-3">
                    <div class="col-md-4"><input type="text" id="expRes" class="form-control" placeholder="resource"></div>
                    <div class="col-md-4">
                        <select id="expAct" class="form-select">
                            <option value="">action…</option>
                            <option>list_all</option><option>show_all</option><option>list</option>
                            <option>show</option><option>create</option><option>update</option><option>delete</option>
                        </select>
                    </div>
                    <div class="col-md-4"><button id="btnExplain" class="btn btn-primary"><i class="fas fa-bolt"></i> Explain</button></div>
                </div>
                <div id="explainOut"></div>
            </div>
        </div>
    </div>
</div>

<style>
#aclInspector .chip{display:inline-block;padding:.3rem .7rem;margin:.15rem;border-radius:14px;font-size:.82rem}
#aclInspector .chip-role{background:#e8eaf6;color:#283593}
#aclInspector .chip-allow{background:#fff3e0;color:#e65100}
#aclInspector .chip-deny{background:#fdecea;color:#b71c1c}
#aclInspector .chip-muted{background:#eceff1;color:#607d8b}
#aclInspector .cap-card{border:1px solid #e0e0e0;border-radius:6px;padding:.5rem .75rem;height:100%}
#aclInspector .cap-state{font-size:.72rem;text-transform:uppercase;letter-spacing:.04em}
#aclInspector .tb-card{border-left:4px solid #17a2b8;margin-bottom:1rem}
#aclInspector .res-allow{background:#e8f5e9}
#aclInspector .res-allow-strong{background:#c8e6c9}
#aclInspector .res-wild{background:#e3f2fd}
#aclInspector .res-deny{background:#ffebee}
#aclInspector .res-cell{cursor:pointer}
#aclInspector .res-cell:hover{outline:2px solid #90caf9}
#aclInspector .tl-row{display:flex;align-items:center;gap:.6rem;padding:.4rem .2rem;border-bottom:1px dashed #e0e0e0}
#aclInspector .tl-decisive{background:#fff8e1;font-weight:600}
#aclInspector .badge-ALLOW{background:#2e7d32}
#aclInspector .badge-DENY{background:#c62828}
</style>

<script>
document.addEventListener('DOMContentLoaded', function () {
    'use strict';
    var $ = window.jQuery;

    var API   = base_url + '/api/v1/';
    var state = { uid: null, hash: null, snap: null, caps: [], spCatalog: {}, dirty: {} };
    var dirtyTimer = null;

    function showDirtyBanner(){
        clearTimeout(dirtyTimer);
        $('#dirtyBanner').css('display', 'flex');
        dirtyTimer = setTimeout(function(){ $('#dirtyBanner').css('display', 'none'); }, 10000);
    }

    function hdrs(){ return { 'Content-Type': 'application/json' }; }

    function esc(s){ return String(s == null ? '' : s).replace(/[&<>"]/g, function(c){
        return {'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;'}[c]; }); }

    function alertBox(msg, kind){
        var $a = $('#globalAlert');
        $a.removeClass('d-none alert-danger alert-success alert-warning')
          .addClass('alert-' + (kind || 'danger')).html(msg);
        if (kind === 'success') setTimeout(function(){ $a.addClass('d-none'); }, 2500);
    }

    function api(method, path, body){
        return $.ajax({
            url: API + path, method: method, headers: hdrs(),
            data: body ? JSON.stringify(body) : undefined, dataType: 'json'
        }).then(function(r){ return r; }, function(xhr){
            return $.Deferred().reject(xhr);
        });
    }
    // CRUD endpoints wrap payload in {data:...}; inspector endpoints return raw.
    function unwrap(r){ return (r && r.data !== undefined) ? r.data : r; }

    // ---------------------------------------------------------- user search
    var searchTimer = null;
    $('#userSearchInput').on('input', function(){
        clearTimeout(searchTimer);
        var q = $(this).val().trim();
        if (!q){ $('#userSuggestions').hide(); return; }
        // If pure number: no search needed, user typed an ID directly
        if (/^\d+$/.test(q)){ $('#userSuggestions').hide(); return; }
        searchTimer = setTimeout(function(){
            $.ajax({ url: API + 'acl/user_lookup?q=' + encodeURIComponent(q), dataType:'json' })
            .then(function(r){
                var list = r.results || [];
                if (!list.length){ $('#userSuggestions').hide(); return; }
                var html = '';
                list.forEach(function(u){
                    html += '<button type="button" class="list-group-item list-group-item-action py-1 px-2 suggItem" '
                          + 'data-id="'+u.id+'"><strong>#'+esc(u.id)+'</strong> '+esc(u.email||u.username)+'</button>';
                });
                $('#userSuggestions').html(html).show();
            });
        }, 300);
    });

    $(document).on('click', '.suggItem', function(){
        var id = $(this).data('id');
        $('#userSearchInput').val(id);
        $('#userSuggestions').hide();
        loadUser(id);
    });

    $(document).on('click', function(e){
        if (!$(e.target).closest('#userSearchInput, #userSuggestions').length)
            $('#userSuggestions').hide();
    });

    // ---------------------------------------------------------- role graph modal
    var rolesLoaded = false;
    $('#btnRoles').on('click', function(){
        $('#rolesModal').modal('show');
        if (rolesLoaded) return;
        $.ajax({ url: API + 'acl/role_graph', dataType: 'json' }).then(function(r){
            rolesLoaded = true;
            renderRoleGraph(r);
        }).fail(function(){
            $('#rolesModalBody').html('<div class="alert alert-danger">Failed to load role graph.</div>');
        });
    });

    function renderRoleGraph(r){
        var nodes = r.nodes || [], edges = r.edges || [];
        // Build parent map: child -> parent
        var parentMap = {};
        edges.forEach(function(e){ parentMap[e.from] = e.to; });

        // Render as cascading columns: guest → registered → [supervisor, admin] → superadmin
        var cols = {};   // level_group → [nodes]
        nodes.forEach(function(n){
            var col = n.role_id;
            if (!cols[col]) cols[col] = [];
            cols[col].push(n);
        });

        var levelKeys = Object.keys(cols).sort(function(a,b){ return a-b; });

        var html = '<div class="d-flex flex-wrap gap-3 align-items-start">';
        levelKeys.forEach(function(lvl){
            cols[lvl].forEach(function(n){
                var parent = parentMap[n.name];
                var badge  = n.is_guest ? 'bg-secondary' : n.role_id >= 5000 ? 'bg-danger' : n.role_id >= 1000 ? 'bg-warning text-dark' : n.role_id >= 500 ? 'bg-info text-dark' : 'bg-primary';
                var spList = (n.sp||[]).map(function(s){ return '<span class="badge bg-light text-dark border me-1 mb-1">'+esc(s)+'</span>'; }).join('');
                var tbList = (n.tb||[]).map(function(t){ return '<span class="badge bg-light text-dark border me-1 mb-1"><i class="fas fa-table fa-xs me-1"></i>'+esc(t)+'</span>'; }).join('');
                html += '<div class="card" style="min-width:200px;max-width:260px">'
                      + '<div class="card-header py-2"><span class="badge '+badge+' me-2">'+esc(n.name)+'</span>'
                      + '<small class="text-muted">lvl '+esc(n.role_id)+'</small>'
                      + (parent ? '<br><small class="text-muted"><i class="fas fa-arrow-up fa-xs"></i> inherits <strong>'+esc(parent)+'</strong></small>' : '')
                      + '</div>';
                if (spList || tbList){
                    html += '<div class="card-body py-2 px-2"><div class="mb-1">'+spList+tbList+'</div></div>';
                }
                html += '</div>';
            });
        });
        html += '</div>';
        $('#rolesModalBody').html(html);
    }

    // ---------------------------------------------------------- load user
    function loadUser(uid){
        state.uid = uid;
        $('#inspectorBody').addClass('d-none');
        alertBox('<i class="fas fa-spinner fa-spin"></i> Loading…', 'warning');

        api('GET', 'acl/assignments?user_id=' + uid).then(function(a){
            state.hash = a.acl_context_hash;
            state.snap = a.snapshot_version;
            $('#ctxSnapVer').text(a.snapshot_version);
            $('#ctxHash').text(a.acl_context_hash);
            $('#ctxMeta').removeClass('d-none');
            $('#globalAlert').addClass('d-none');
            $('#inspectorBody').removeClass('d-none');
            $('#btnClearUser').removeClass('d-none');
            renderAssignments(a);
            return $.when(api('GET','sp_permissions'), api('GET','user_sp_permissions?user_id='+uid));
        }).then(function(cat, usp){
            indexCatalog(unwrap(cat[0]));
            renderCapabilities(unwrap(usp[0]));
            loadEffective();
        }).fail(function(xhr){
            alertBox('Failed to load user #' + uid + ' (' + (xhr.status||'?') + ').');
        });
    }

    function indexCatalog(rows){
        state.spCatalog = {};                 // name -> first id  (table has dup rows)
        var byId = {};                        // id -> name
        (rows || []).forEach(function(r){
            if (!(r.name in state.spCatalog)) state.spCatalog[r.name] = r.id;
            byId[r.id] = r.name;
        });
        state.spById = byId;
    }

    // ---------------------------------------------------------- concurrency
    function guardThen(fn){
        api('GET', 'acl/assignments?user_id=' + state.uid).then(function(a){
            if (a.acl_context_hash !== state.hash){
                alertBox('ACL state changed elsewhere — reloading to avoid overwriting.', 'warning');
                loadUser(state.uid);
            } else {
                fn();
            }
        }).fail(function(){ alertBox('Concurrency check failed.'); });
    }

    // ---------------------------------------------------------- Tab 1
    function renderAssignments(a){
        var $r = $('#rolesBox').empty();
        (a.roles || []).forEach(function(ro){
            $r.append('<span class="chip chip-role">' + esc(ro.name) + ' <small>#' + ro.role_id + '</small></span>');
        });
        if (!a.roles || !a.roles.length) $r.append('<span class="text-muted">No roles.</span>');

        // Resource Policies
        var $tb = $('#tbBox').empty();
        var tb = a.user_tb_perms || {};
        var keys = Object.keys(tb);
        $('#tbEmpty').toggle(keys.length === 0);
        var fields = [['can_list_all','list_all'],['can_show_all','show_all'],['can_list','list'],
                      ['can_show','show'],['can_create','create'],['can_update','update'],['can_delete','delete']];
        keys.forEach(function(res){
            var row = tb[res], cells = '';
            fields.forEach(function(f){
                var ck = row[f[0]] ? 'checked' : '';
                cells += '<div class="col-4 col-md-3"><div class="form-check">'
                  + '<input class="form-check-input tbchk" type="checkbox" data-res="'+esc(res)+'" data-f="'+f[0]+'" id="c_'+esc(res)+'_'+f[0]+'" '+ck+'>'
                  + '<label class="form-check-label small" for="c_'+esc(res)+'_'+f[0]+'">'+f[1]+'</label></div></div>';
            });
            $tb.append(
              '<div class="card tb-card" data-res="'+esc(res)+'"><div class="card-body">'
              + '<div class="d-flex justify-content-between mb-2"><code>'+esc(res)+'</code>'
              + '<div><button class="btn btn-sm btn-primary btnSaveTb" data-res="'+esc(res)+'" data-id="'+(row.id||'')+'">Save</button> '
              + '<button class="btn btn-sm btn-outline-danger btnDelTb" data-id="'+(row.id||'')+'" title="Remove resource policy">&times;</button></div></div>'
              + '<div class="row g-1">'+cells+'</div></div></div>');
        });

        // Explicit denies
        var $d = $('#denyBox').empty(), dp = a.user_deny_perms || {}, any = false;
        Object.keys(dp).forEach(function(res){
            (dp[res]||[]).forEach(function(act){
                any = true;
                $d.append('<span class="chip chip-deny" data-res="'+esc(res)+'" data-act="'+esc(act)+'">'
                  + esc(res)+'.'+esc(act)
                  + ' <a href="#" class="text-danger btnDelDeny" data-res="'+esc(res)+'" data-act="'+esc(act)+'">&times;</a></span>');
            });
        });
        $('#denyEmpty').toggle(!any);
        state.dirty = {};
        clearTimeout(dirtyTimer); $('#dirtyBanner').css('display', 'none');
    }

    function renderCapabilities(uspRows){
        var granted = {};   // name -> user_sp_permissions row id
        (uspRows || []).forEach(function(r){
            var nm = state.spById[r.sp_permission_id];
            if (nm) granted[nm] = r.id;
        });
        var $c = $('#capsBox').empty();
        Object.keys(state.spCatalog).forEach(function(name){
            var isUser = (name in granted), st, cls, btn;
            if (isUser){ st='user-granted'; cls='chip-allow';
                btn='<button class="btn btn-sm btn-outline-secondary btnCapReset" data-id="'+granted[name]+'">Reset</button>'; }
            else { st='role-inherited'; cls='chip-muted';
                btn='<button class="btn btn-sm btn-outline-warning btnCapAllow" data-name="'+esc(name)+'">Allow</button>'; }
            $c.append('<div class="col-md-4"><div class="cap-card d-flex justify-content-between align-items-center">'
              + '<div><span class="chip '+cls+' mb-0">'+esc(name)+'</span><div class="cap-state text-muted">'+st+'</div></div>'
              + btn + '</div></div>');
        });
    }

    // ---------------------------------------------------------- Tab 2
    function loadEffective(){
        api('GET', 'acl/effective?user_id=' + state.uid).then(function(e){
            var sc = e.scope || {};
            var bn = '<strong>Inspector scope:</strong> ' + esc(sc.note || '')
                   + ' <em>Not modeled: ' + esc((sc.not_included||[]).join(', ')) + '.</em>';
            $('#scopeBanner').html(bn); $('#scopeBanner2').html(bn);
            $('#effFooter').html('ACL ' + esc(e.snapshot_version) + ' · generated '
                + esc(e.snapshot_generated_at || '?') + ' · context <code>' + esc(e.acl_context_hash) + '</code>');
            state.effRaw = e;
            renderEffective();
            var $ec = $('#effCaps').empty(), caps = e.capabilities || {};
            Object.keys(caps).forEach(function(n){
                var c = caps[n], cls = c.result === 'deny' ? 'chip-deny' : 'chip-allow';
                $ec.append('<div class="col-md-4"><div class="cap-card"><span class="chip '+cls+'">'+esc(n)
                  + '</span><div class="cap-state text-muted">'+esc(c.result)+' · '+esc(c.origin)+'</div></div></div>');
            });
        });
    }

    function renderEffective(){
        var e = state.effRaw; if (!e) return;
        var fRes = $('#fRes').val().toLowerCase(), fAct = $('#fAct').val().toLowerCase();
        var onlyC = $('#fConf').is(':checked'), onlyD = $('#fDeny').is(':checked'), onlyO = $('#fOver').is(':checked');
        var res = e.resources || {}, $acc = $('#effResources').empty(), i = 0;

        Object.keys(res).forEach(function(rname){
            if (fRes && rname.toLowerCase().indexOf(fRes) < 0) return;
            var acts = res[rname], rows = '';
            Object.keys(acts).forEach(function(an){
                var c = acts[an];
                if (fAct && an.toLowerCase().indexOf(fAct) < 0) return;
                if (onlyC && !c.conflict) return;
                if (onlyD && c.result !== 'deny') return;
                if (onlyO && c.origin !== 'USER') return;
                var cls = c.result === 'deny' ? 'res-deny'
                        : c.origin === 'WILDCARD' ? 'res-wild'
                        : c.origin === 'USER' ? 'res-allow-strong' : 'res-allow';
                rows += '<tr class="res-cell '+cls+'" data-res="'+esc(rname)+'" data-act="'+esc(an)+'">'
                  + '<td>'+esc(an)+'</td><td><strong>'+esc(c.result)+'</strong></td><td>'+esc(c.origin)+'</td>'
                  + '<td><small>'+esc(c.source)+'</small></td><td>'+(c.inherited?'yes':'no')+'</td>'
                  + '<td>'+(c.conflict?'<span class="text-danger">&#9888;</span>':'')+'</td></tr>';
            });
            if (!rows) return;
            i++;
            $acc.append(
              '<div class="accordion-item"><h2 class="accordion-header mt-3">'
              + '<button class="accordion-button '+(i>1?'collapsed':'')+'" type="button" data-bs-toggle="collapse" data-bs-target="#ea'+i+'">'
              + '<code>'+esc(rname)+'</code></button></h2>'
              + '<div id="ea'+i+'" class="accordion-collapse collapse '+(i===1?'show':'')+'"><div class="accordion-body p-0">'
              + '<table class="table table-sm mb-0"><thead><tr><th>action</th><th>result</th><th>origin</th><th>source</th><th>inherited</th><th>conflict</th></tr></thead><tbody>'
              + rows + '</tbody></table></div></div></div>');
        });
        if (!$acc.children().length) $acc.html('<div class="text-muted text-center py-3">No rows match filters.</div>');
    }

    // ---------------------------------------------------------- Tab 3
    function explain(resource, action){
        if (!resource || !action){ alertBox('resource and action are required.', 'warning'); return; }
        $('#explainOut').html('<i class="fas fa-spinner fa-spin"></i>');
        api('GET', 'acl/explain?user_id='+state.uid+'&resource='+encodeURIComponent(resource)+'&action='+encodeURIComponent(action))
        .then(function(r){
            var icon = { 'ROLE-ALLOW':'fa-user-check text-success','ROLE-DENY':'fa-user-xmark text-danger',
                'USER-ALLOW':'fa-user-pen text-warning','USER-DENY':'fa-ban text-danger',
                'WILDCARD-ALLOW':'fa-globe text-primary','WILDCARD-DENY':'fa-globe text-danger',
                'DEFAULT-DENY':'fa-lock text-muted' };
            var dec = r.decisive || {};
            var tl = (r.resolution_path || []).map(function(p){
                var k = p.origin + '-' + p.effect;
                var isDec = dec.origin === p.origin && dec.effect === p.effect && dec.source === p.source;
                return '<div class="tl-row '+(isDec?'tl-decisive':'')+'">'
                  + '<i class="fas '+(icon[k]||'fa-circle')+'"></i>'
                  + '<span class="badge badge-'+p.effect+'">'+esc(p.effect)+'</span>'
                  + '<strong>'+esc(p.origin)+'</strong><span class="text-muted">'+esc(p.source)+'</span>'
                  + (isDec?'<span class="badge bg-warning text-dark ms-auto">decisive</span>':'') + '</div>';
            }).join('');
            var conf = r.has_conflict
              ? '<div class="alert alert-warning"><i class="fas fa-triangle-exclamation"></i> Conflicting policies resolved by deny precedence.</div>' : '';
            $('#explainOut').html(
              '<div class="card"><div class="card-body">'
              + '<h4><code>'+esc(r.resource)+'.'+esc(r.action)+'</code> &rarr; '
              + '<span class="badge badge-'+(r.result==='deny'?'DENY':'ALLOW')+'">'+esc(r.result.toUpperCase())+'</span></h4>'
              + conf
              + '<h6 class="mt-3">Resolution timeline</h6>' + (tl || '<p class="text-muted">No matching policy (default deny).</p>')
              + '<div class="text-muted small mt-3">ACL '+esc(r.snapshot_version)+' · '+esc(r.snapshot_generated_at||'')
              + ' · context <code>'+esc(r.acl_context_hash)+'</code></div>'
              + '</div></div>');
        }).fail(function(xhr){
            var m = (xhr.responseJSON && xhr.responseJSON.error) || ('HTTP ' + xhr.status);
            $('#explainOut').html('<div class="alert alert-danger">'+esc(m)+'</div>');
        });
    }

    // ---------------------------------------------------------- events
    $('#btnLoadUser').on('click', function(){
        var raw = $('#userSearchInput').val().trim();
        if (!raw){ alertBox('Ingresá ID, email o username.', 'warning'); return; }
        if (/^\d+$/.test(raw)){
            loadUser(parseInt(raw, 10));
        } else {
            // lookup by email/username first
            $.ajax({ url: API + 'acl/user_lookup?q=' + encodeURIComponent(raw), dataType:'json' })
            .then(function(r){
                var list = r.results || [];
                if (!list.length){ alertBox('Usuario no encontrado: ' + esc(raw), 'warning'); return; }
                loadUser(list[0].id);
            }).fail(function(){ alertBox('Error buscando usuario.'); });
        }
    });
    $('#userSearchInput').on('keydown', function(e){ if (e.key === 'Enter') $('#btnLoadUser').click(); });
    $('#btnClearUser').on('click', function(){
        state.uid = null; $('#inspectorBody').addClass('d-none');
        $('#ctxMeta').addClass('d-none'); $(this).addClass('d-none'); $('#userSearchInput').val('');
    });

    $('#tbBox').on('change', '.tbchk', function(){
        state.dirty[$(this).data('res')] = true; showDirtyBanner();
    });

    $('#btnDismissDirty').on('click', function(){
        clearTimeout(dirtyTimer); $('#dirtyBanner').css('display', 'none');
    });

    // BUG FIX: send ALL checkbox fields so replacement semantics keep the full set
    $('#tbBox').on('click', '.btnSaveTb', function(){
        var res = $(this).data('res');
        var payload = { user_id: state.uid, tb: res };
        $('.tbchk[data-res="'+res+'"]').each(function(){ payload[$(this).data('f')] = $(this).is(':checked') ? 1 : 0; });
        guardThen(function(){
            api('POST', 'user_tb_permissions', payload).then(function(){
                alertBox('Saved resource policy for ' + esc(res), 'success'); loadUser(state.uid);
            }).fail(function(){ alertBox('Save failed for ' + esc(res)); });
        });
    });
    $('#tbBox').on('click', '.btnDelTb', function(){
        var id = $(this).data('id'); if (!id || !confirm('Remove this resource policy?')) return;
        guardThen(function(){ api('DELETE', 'user_tb_permissions/'+id).then(function(){
            alertBox('Removed.', 'success'); loadUser(state.uid); }); });
    });
    $('#btnAddTb').on('click', function(){
        var tb = $('#newTbName').val().trim(); if (!tb) return;
        guardThen(function(){ api('POST','user_tb_permissions',{user_id:state.uid,tb:tb,can_list:1}).then(function(){
            $('#newTbName').val(''); loadUser(state.uid); }); });
    });

    $('#capsBox').on('click', '.btnCapAllow', function(){
        var name = $(this).data('name'), id = state.spCatalog[name];
        if (!id) return;
        guardThen(function(){ api('POST','user_sp_permissions',{user_id:state.uid,sp_permission_id:id}).then(function(){
            alertBox('Capability "'+esc(name)+'" granted.', 'success'); loadUser(state.uid); }); });
    });
    $('#capsBox').on('click', '.btnCapReset', function(){
        var id = $(this).data('id');
        guardThen(function(){ api('DELETE','user_sp_permissions/'+id).then(function(){
            alertBox('Capability reset to role-inherited.', 'success'); loadUser(state.uid); }); });
    });

    $('#btnAddDeny').on('click', function(){
        var res = $('#denyRes').val().trim(), act = $('#denyAct').val();
        if (!res || !act){ alertBox('resource and action required.', 'warning'); return; }
        guardThen(function(){ api('POST','user_deny_permissions',{user_id:state.uid,resource:res,action:act}).then(function(){
            $('#denyRes').val(''); $('#denyAct').val(''); alertBox('Deny added.', 'success'); loadUser(state.uid); }); });
    });
    $('#denyBox').on('click', '.btnDelDeny', function(e){
        e.preventDefault();
        var res = $(this).data('res'), act = $(this).data('act');
        api('GET','user_deny_permissions?user_id='+state.uid).then(function(r){
            var rows = unwrap(r), hit = (Array.isArray(rows)?rows:[]).filter(function(x){
                return x.resource === res && x.action === act; })[0];
            if (!hit){ alertBox('Deny row not found.'); return; }
            guardThen(function(){ api('DELETE','user_deny_permissions/'+hit.id).then(function(){
                alertBox('Deny removed.', 'success'); loadUser(state.uid); }); });
        });
    });

    $('#fRes,#fAct').on('input', renderEffective);
    $('#fConf,#fDeny,#fOver').on('change', renderEffective);

    $('#effResources').on('click', '.res-cell', function(){
        var res = $(this).data('res'), act = $(this).data('act');
        $('#expRes').val(res); $('#expAct').val(act);
        $('#tab-exp-btn').tab('show');
        explain(res, act);
    });
    $('#btnExplain').on('click', function(){ explain($('#expRes').val().trim(), $('#expAct').val()); });
});
</script>
