<?php 

function convertCurlToPowershell($curlCommand) {
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
    
    // Construir los headers con comillas dobles escapadas
    $headerParts = [];
    foreach ($headers as $name => $value) {
        $headerParts[] = "`\"$name`\" = `\"$value`\"";
    }
    $headersString = "@{" . implode('; ', $headerParts) . "}";
    
    // Construir el comando completo con comillas simples para la URL y el body
    $psCommand = "Invoke-RestMethod -Method $method -Uri '$url' -Headers $headersString";
    
    if ($data) {
        $psCommand .= " -Body '$data'";
    }
    
    return $psCommand;
}

function execPowerShell($psCommand) {
    $command = sprintf('powershell.exe -NoProfile -ExecutionPolicy Bypass -Command "%s"', $psCommand);
    $output = [];
    $returnCode = 0;
    
    exec($command, $output, $returnCode);
    
    return [
        'output' => $output,
        'code' => $returnCode
    ];
}

// Ejemplo de uso:
$curlCommand = <<<CMD
curl -sSi -X "POST" -H "apikey: cebc90896c0445599e6d2269b9f89c8f" -H "Content-Type: application/json" -H "Content-Length: 39" --data '{"period":1,"email":"email@correo.com"}' "https://api.haulmer.dev/v2.0/partners/signature/generate" 
CMD;


$ps_command = convertCurlToPowershell($curlCommand);
var_dump($ps_command);

print_r("\r\n");

$res = execPowerShell($ps_command);
var_dump($res);


