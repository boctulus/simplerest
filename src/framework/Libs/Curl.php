<?php

namespace Boctulus\Simplerest\Core\Libs;

use Boctulus\Simplerest\Core\Libs\Strings;

class Curl
{
    static function convertCurlToPowershell($curlCommand, $as_json = false)
    {
        $curlCommand = str_replace('2>&1', '', $curlCommand);
        $curlCommand = preg_replace('/^curl\s+/', '', trim($curlCommand));

        $url = '';
        $method = 'Get';
        $headers = [];
        $data = '';

        if (preg_match('/("[^"]+"|\'[^\']+\'|\S+)(?=\s*$)/', $curlCommand, $matches)) {
            $url = trim($matches[0], "'\"");
            $curlCommand = trim(substr($curlCommand, 0, -strlen($matches[0])));
        }

        $pattern = '/(?:^|\s)(-[A-Za-z]|\-\-[A-Za-z\-]+)(?:[\s=]("[^"]+"|\'[^\']+\'|\S+))?/';
        preg_match_all($pattern, $curlCommand, $matches, PREG_SET_ORDER);

        foreach ($matches as $match) {
            $option = $match[1];
            $value = isset($match[2]) ? trim($match[2], "'\"") : null;

            switch ($option) {
                case '-X':
                case '--request':
                    $method = $value;
                    break;
                case '-H':
                case '--header':
                    if (strpos($value, ':') !== false) {
                        list($headerName, $headerValue) = array_map('trim', explode(':', $value, 2));
                        $headers[$headerName] = $headerValue;
                    }
                    break;
                case '-d':
                case '--data':
                case '--data-binary':
                    $data = $value;
                    break;
            }
        }

        $headersString = '';
        if (!empty($headers)) {
            $headerParts = [];
            foreach ($headers as $name => $value) {
                $headerParts[] = "\"$name\" = \"$value\"";
            }
            $headersString = "@{" . implode('; ', $headerParts) . "}";
        }

        $psCommand = $as_json ?
            "\$response = Invoke-RestMethod -Method $method -Uri '$url'" :
            "Invoke-RestMethod -Method $method -Uri '$url'";

        if (!empty($headersString)) {
            $psCommand .= " -Headers $headersString";
        }

        if ($data) {
            $psCommand .= " -Body '$data';";
        }

        if ($as_json) {
            $psCommand .= "\necho \$response | ConvertTo-Json";
        }

        return $psCommand;
    }
}
