<?php

namespace Boctulus\Simplerest\Core\Libs;

class WebhookEndpointPolicy
{
    /**
     * Conservative deny lists based on the IANA special-purpose registries.
     * Broader containing blocks are rejected even where they have narrower
     * globally reachable exceptions; see the webhook security task for sources.
     */
    private const BLOCKED_IPV4 = [
        '0.0.0.0/8',
        '10.0.0.0/8',
        '100.64.0.0/10',
        '127.0.0.0/8',
        '169.254.0.0/16',
        '172.16.0.0/12',
        '192.0.0.0/24',
        '192.0.2.0/24',
        '192.88.99.0/24',
        '192.168.0.0/16',
        '198.18.0.0/15',
        '198.51.100.0/24',
        '203.0.113.0/24',
        '224.0.0.0/4',
        '240.0.0.0/4',
    ];

    private const BLOCKED_IPV6 = [
        '::/96',
        '::ffff:0:0/96',
        '64:ff9b::/96',
        '64:ff9b:1::/48',
        '100::/64',
        '100:0:0:1::/64',
        '2001::/23',
        '2001:2::/48',
        '2001:10::/28',
        '2001:db8::/32',
        '2002::/16',
        '3fff::/20',
        '5f00::/16',
        'fc00::/7',
        'fe80::/10',
        'ff00::/8',
    ];

    private ?string $environment;

    public function __construct(?string $environment = null)
    {
        $this->environment = $environment;
    }

    /**
     * Validate a callback and resolve its hostname once for a pinned delivery.
     *
     * @return array{url:string,scheme:string,host:string,port:int,addresses:array,is_literal:bool}
     */
    public function resolve(string $callback): array
    {
        if ($callback === '' || preg_match('/[\x00-\x20\x7f\\\\]/', $callback)) {
            $this->reject();
        }

        $parts = parse_url($callback);
        if (!is_array($parts)) {
            $this->reject();
        }

        $scheme = strtolower((string) ($parts['scheme'] ?? ''));
        if (!in_array($scheme, ['http', 'https'], true)) {
            $this->reject();
        }

        if ($this->isProduction() && $scheme !== 'https') {
            $this->reject();
        }

        if (
            !isset($parts['host'])
            || array_key_exists('user', $parts)
            || array_key_exists('pass', $parts)
            || array_key_exists('fragment', $parts)
        ) {
            $this->reject();
        }

        $rawHost = (string) $parts['host'];
        $host = trim($rawHost, '[]');
        $isIp = filter_var($host, FILTER_VALIDATE_IP) !== false;

        if ($isIp) {
            $host = $this->canonicalIp($host);
            $addresses = [$host];
        } else {
            $host = $this->canonicalHostname($host);
            $addresses = $this->resolveHostAddresses($host);
        }

        if (empty($addresses)) {
            $this->reject();
        }

        foreach ($addresses as $address) {
            if (!is_string($address) || !$this->isGloballyRoutable($address)) {
                $this->reject();
            }
        }

        $port = (int) ($parts['port'] ?? ($scheme === 'https' ? 443 : 80));
        if ($port < 1 || $port > 65535) {
            $this->reject();
        }

        $isIpv6 = filter_var($host, FILTER_VALIDATE_IP, FILTER_FLAG_IPV6) !== false;
        $authorityHost = $isIpv6 ? '[' . $host . ']' : $host;
        $portSuffix = array_key_exists('port', $parts) ? ':' . $port : '';
        $url = $scheme . '://' . $authorityHost . $portSuffix . ($parts['path'] ?? '');
        if (array_key_exists('query', $parts)) {
            $url .= '?' . $parts['query'];
        }

        return [
            'url' => $url,
            'scheme' => $scheme,
            'host' => $host,
            'port' => $port,
            'addresses' => array_values(array_unique($addresses)),
            'is_literal' => $isIp,
        ];
    }

    protected function lookupDnsRecords(string $hostname): array
    {
        $records = @dns_get_record($hostname, DNS_A | DNS_AAAA | DNS_CNAME);

        return is_array($records) ? $records : [];
    }

    protected function resolveHostAddresses(string $hostname): array
    {
        $path = [];
        $addresses = $this->collectDnsAddresses($hostname, $path, 0);

        return array_values(array_unique($addresses));
    }

    private function collectDnsAddresses(string $hostname, array $path, int $depth): array
    {
        $hostname = strtolower(rtrim($hostname, '.'));
        if ($depth > 8 || isset($path[$hostname])) {
            $this->reject();
        }
        $path[$hostname] = true;

        $records = $this->lookupDnsRecords($hostname);
        $addresses = [];
        foreach ($records as $record) {
            $type = strtoupper((string) ($record['type'] ?? ''));
            if ($type === 'A') {
                if (!isset($record['ip'])) {
                    $this->reject();
                }
                $addresses[] = $this->canonicalIp((string) $record['ip']);
            } elseif ($type === 'AAAA') {
                if (!isset($record['ipv6'])) {
                    $this->reject();
                }
                $addresses[] = $this->canonicalIp((string) $record['ipv6']);
            } elseif ($type === 'CNAME') {
                if (empty($record['target'])) {
                    $this->reject();
                }
                $addresses = array_merge(
                    $addresses,
                    $this->collectDnsAddresses((string) $record['target'], $path, $depth + 1)
                );
            } else {
                $this->reject();
            }
        }

        return $addresses;
    }

    private function canonicalHostname(string $hostname): string
    {
        if (preg_match('/[^\x21-\x7e]/', $hostname)) {
            $this->reject();
        }

        $hostname = strtolower(rtrim($hostname, '.'));
        if ($hostname === '' || strlen($hostname) > 253 || str_contains($hostname, ':')) {
            $this->reject();
        }

        if (preg_match('/^(?:0x[0-9a-f]+|[0-9]+)(?:\.(?:0x[0-9a-f]+|[0-9]+))*$/i', $hostname)) {
            $this->reject();
        }

        if (substr_count($hostname, '.') < 1) {
            $this->reject();
        }

        foreach (['.localhost', '.local', '.internal', '.lan', '.home.arpa', '.test', '.example', '.invalid', '.onion'] as $suffix) {
            if ($hostname === ltrim($suffix, '.') || str_ends_with($hostname, $suffix)) {
                $this->reject();
            }
        }

        foreach (explode('.', $hostname) as $label) {
            if (
                strlen($label) > 63
                || !preg_match('/^[a-z0-9](?:[a-z0-9-]*[a-z0-9])?$/', $label)
            ) {
                $this->reject();
            }
        }

        return $hostname;
    }

    private function canonicalIp(string $ip): string
    {
        if (filter_var($ip, FILTER_VALIDATE_IP) === false) {
            $this->reject();
        }

        $packed = inet_pton($ip);
        if ($packed === false) {
            $this->reject();
        }

        $canonical = inet_ntop($packed);
        if ($canonical === false) {
            $this->reject();
        }

        return strtolower($canonical);
    }

    private function isGloballyRoutable(string $ip): bool
    {
        $packed = inet_pton($ip);
        if ($packed === false) {
            return false;
        }

        if (strlen($packed) === 4) {
            foreach (self::BLOCKED_IPV4 as $cidr) {
                if ($this->matchesCidr($packed, $cidr)) {
                    return false;
                }
            }

            return true;
        }

        if (strlen($packed) !== 16 || !$this->matchesCidr($packed, '2000::/3')) {
            return false;
        }

        foreach (self::BLOCKED_IPV6 as $cidr) {
            if ($this->matchesCidr($packed, $cidr)) {
                return false;
            }
        }

        return true;
    }

    private function matchesCidr(string $packedIp, string $cidr): bool
    {
        [$network, $prefix] = explode('/', $cidr, 2);
        $packedNetwork = inet_pton($network);
        if ($packedNetwork === false || strlen($packedNetwork) !== strlen($packedIp)) {
            return false;
        }

        $prefix = (int) $prefix;
        $fullBytes = intdiv($prefix, 8);
        $remainingBits = $prefix % 8;

        if (substr($packedIp, 0, $fullBytes) !== substr($packedNetwork, 0, $fullBytes)) {
            return false;
        }

        if ($remainingBits === 0) {
            return true;
        }

        $mask = (0xff << (8 - $remainingBits)) & 0xff;

        return (ord($packedIp[$fullBytes]) & $mask)
            === (ord($packedNetwork[$fullBytes]) & $mask);
    }

    private function isProduction(): bool
    {
        $environment = $this->environment ?? Config::get('app_env', '');

        return in_array(strtolower(trim((string) $environment)), ['prod', 'production'], true);
    }

    private function reject(): void
    {
        throw new \InvalidArgumentException('Callback endpoint rejected by policy.');
    }
}
