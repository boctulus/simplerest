<?php

namespace Boctulus\Simplerest\Core\Libs;

use Boctulus\Simplerest\Core\Interfaces\IWebhookTransport;

class WebhookHttpTransport implements IWebhookTransport
{
    private const CONNECT_TIMEOUT_SECONDS = 5;
    private const TOTAL_TIMEOUT_SECONDS = 10;
    private const MAX_REQUEST_BODY_BYTES = 1048576;
    private const MAX_RESPONSE_BODY_BYTES = 65536;
    private const MAX_RESPONSE_HEADER_BYTES = 16384;

    private WebhookEndpointPolicy $endpointPolicy;

    public function __construct(?WebhookEndpointPolicy $endpointPolicy = null)
    {
        $this->endpointPolicy = $endpointPolicy ?? new WebhookEndpointPolicy();
    }

    public function send(string $callback, string $rawBody, array $headers): mixed
    {
        if (!function_exists('curl_init')) {
            return $this->failure('transport_unavailable');
        }

        if ((int) (curl_version()['version_number'] ?? 0) < 0x071503) {
            return $this->failure('transport_unsupported');
        }

        if (strlen($rawBody) > self::MAX_REQUEST_BODY_BYTES) {
            return $this->failure('request_body_too_large');
        }

        try {
            $endpoint = $this->endpointPolicy->resolve($callback);
            $requestHeaders = $this->formatRequestHeaders($headers);
        } catch (\Throwable $e) {
            return $this->failure('endpoint_policy_rejected');
        }

        return $this->performRequest($endpoint, $rawBody, $requestHeaders);
    }

    protected function performRequest(array $endpoint, string $rawBody, array $requestHeaders): array
    {
        $responseBytes = 0;
        $responseHeaderBytes = 0;
        $responseTooLarge = false;
        $responseHeadersTooLarge = false;

        $writeBody = static function ($handle, string $chunk) use (&$responseBytes, &$responseTooLarge): int {
            $responseBytes += strlen($chunk);
            if ($responseBytes > self::MAX_RESPONSE_BODY_BYTES) {
                $responseTooLarge = true;
                return 0;
            }

            return strlen($chunk);
        };

        $writeHeaders = static function ($handle, string $header) use (&$responseHeaderBytes, &$responseHeadersTooLarge): int {
            $responseHeaderBytes += strlen($header);
            if ($responseHeaderBytes > self::MAX_RESPONSE_HEADER_BYTES) {
                $responseHeadersTooLarge = true;
                return 0;
            }

            return strlen($header);
        };

        $handle = curl_init();
        if ($handle === false) {
            return $this->failure('transport_unavailable');
        }

        try {
            $options = $this->buildCurlOptions(
                $endpoint,
                $rawBody,
                $requestHeaders,
                $writeBody,
                $writeHeaders
            );

            if (!curl_setopt_array($handle, $options)) {
                return $this->failure('transport_configuration_failed');
            }

            $result = @curl_exec($handle);
            $status = (int) curl_getinfo($handle, CURLINFO_RESPONSE_CODE);

            if ($responseTooLarge || $responseHeadersTooLarge) {
                return $this->failure(
                    $responseTooLarge ? 'response_body_too_large' : 'response_headers_too_large',
                    $status ?: null
                );
            }

            if ($result === false) {
                return $this->failure('transport_failure', $status ?: null);
            }

            if ($status >= 300 && $status < 400) {
                return $this->failure('redirect_rejected', $status);
            }

            if ($status < 200 || $status >= 300) {
                return $this->failure('http_error', $status ?: null);
            }

            return [
                'ok' => true,
                'status' => $status,
                'error' => null,
            ];
        } catch (\Throwable $e) {
            return $this->failure('transport_failure');
        } finally {
            curl_close($handle);
        }
    }

    protected function buildCurlOptions(
        array $endpoint,
        string $rawBody,
        array $requestHeaders,
        callable $writeBody,
        callable $writeHeaders
    ): array {
        $protocol = $endpoint['scheme'] === 'https' ? CURLPROTO_HTTPS : CURLPROTO_HTTP;
        $options = [
            CURLOPT_URL => $endpoint['url'],
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => $rawBody,
            CURLOPT_HTTPHEADER => $requestHeaders,
            CURLOPT_RETURNTRANSFER => false,
            CURLOPT_FOLLOWLOCATION => false,
            CURLOPT_MAXREDIRS => 0,
            CURLOPT_CONNECTTIMEOUT => self::CONNECT_TIMEOUT_SECONDS,
            CURLOPT_TIMEOUT => self::TOTAL_TIMEOUT_SECONDS,
            CURLOPT_NOSIGNAL => true,
            CURLOPT_SSL_VERIFYPEER => true,
            CURLOPT_SSL_VERIFYHOST => 2,
            CURLOPT_PROXY => '',
            CURLOPT_NOPROXY => '*',
            CURLOPT_PROTOCOLS => $protocol,
            CURLOPT_REDIR_PROTOCOLS => $protocol,
            CURLOPT_WRITEFUNCTION => $writeBody,
            CURLOPT_HEADERFUNCTION => $writeHeaders,
            CURLOPT_ENCODING => '',
        ];

        if (!$endpoint['is_literal']) {
            $address = $this->selectPinnedAddress($endpoint['addresses']);
            if ($address === null) {
                throw new \RuntimeException('No supported resolved address.');
            }

            $pinnedAddress = filter_var($address, FILTER_VALIDATE_IP, FILTER_FLAG_IPV6)
                ? '[' . $address . ']'
                : $address;
            $options[CURLOPT_RESOLVE] = [
                $endpoint['host'] . ':' . $endpoint['port'] . ':' . $pinnedAddress
            ];
        }

        return $options;
    }

    protected function selectPinnedAddress(array $addresses): ?string
    {
        $curlVersion = (int) (curl_version()['version_number'] ?? 0);
        if ($curlVersion < 0x073900) {
            $addresses = array_values(array_filter(
                $addresses,
                fn (string $address): bool => filter_var($address, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4) !== false
            ));
        }

        if (empty($addresses)) {
            return null;
        }

        return $addresses[random_int(0, count($addresses) - 1)];
    }

    private function formatRequestHeaders(array $headers): array
    {
        $formatted = [];
        foreach ($headers as $name => $value) {
            if (
                !is_string($name)
                || !is_string($value)
                || !preg_match('/^[A-Za-z0-9-]+$/', $name)
                || preg_match('/[\x00-\x1f\x7f]/', $value)
            ) {
                throw new \InvalidArgumentException('Invalid webhook request header.');
            }

            $formatted[] = $name . ': ' . $value;
        }

        return $formatted;
    }

    private function failure(string $error, ?int $status = null): array
    {
        return [
            'ok' => false,
            'status' => $status,
            'error' => $error,
        ];
    }
}
