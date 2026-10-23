<?php declare(strict_types=1);

namespace Boctulus\Simplerest\Core\Libs;

use Boctulus\Simplerest\Core\Libs\Files;
use Boctulus\Simplerest\Core\Libs\Arrays;
use Boctulus\Simplerest\Core\Libs\Numbers;
use Boctulus\Simplerest\Core\Libs\Config;

/*
	Funciona con form-data

	- Si se excede el POST Content-Length (post_max_size) ...
	
		<b>Warning</b>:  POST Content-Length of .... bytes exceeds the limit of 33554432 bytes in <b>Unknown</b> on line <b>0</b><br />
	
	- Si el numero de archivos excede max_file_uploads ... ni llegan ... al script, solo el maximo (e.g. 20)
	
	- Si un archivo supera upload_max_filesize ... ese archivo se procesa con error=1, los demas se procesan... 
*/

/*
	Uso:

		$uploader = (new FileUploader('uploads'));
        //debug($uploader->doUpload('file_*')->getFileNames(),'file_*');
        debug($uploader->doUpload()->getFileNames(),'Cargados:');
        //debug($uploader->doUpload('other_file')->getFileNames(),'other_file:');
        //debug($uploader->doUpload()->getFileNames(),'Cargados:');
        
        //debug($uploader->doUpload('otro')->getFileNames(),'otro:');
        //debug($uploader->doUpload('some_file')->getFileNames(),'some_file:');
        
        if($uploader->getErrors()){
            debug($uploader->getErrors(),'Errors:');
        }
*/

class FileUploader
{
    protected array $filenames = [];
    protected string $location = UPLOADS_PATH;
    protected array $erroneous = [];
    protected ?array $renamerFn = null;
    protected const WILDCARD = '*';
    protected array $allowedExtensions = [
        'jpg', 'jpeg', 'png', 'gif', 'webp', 'pdf',
        'doc', 'docx', 'xls', 'xlsx', 'csv', 'txt', 'md',
    ];
    protected array $allowedMimeTypes = [
        'image/jpeg', 'image/png', 'image/gif', 'image/webp', 'application/pdf',
        'application/msword',
        'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
        'application/vnd.ms-excel',
        'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        'text/csv', 'text/plain', 'text/markdown',
    ];

    function __construct()
    {
        $this->loadConfiguredTypes();

        if (!file_exists($this->location)) {
            Files::mkDirOrFail($this->location);
        }
    }

    /**
     * Load the host application's upload allowlist when configured.
     * The built-in lists remain active when a setting is absent or empty.
     */
    protected function loadConfiguredTypes(): void
    {
        if (!defined('CONFIG_PATH')
            || !is_file(CONFIG_PATH . 'config.php')
            || !is_file(CONFIG_PATH . 'databases.php')) {
            return;
        }

        $extensions = Config::get('files.media_accepted.extensions');
        if (is_array($extensions) && $extensions !== []) {
            $this->allowedExtensions = $this->normalizeTypeList($extensions, true);
        }

        $mimeTypes = Config::get('files.media_accepted.mime_types');
        if (is_array($mimeTypes) && $mimeTypes !== []) {
            $this->allowedMimeTypes = $this->normalizeTypeList($mimeTypes, false);
        }
    }

    /**
     * Restrict accepted filename extensions and sniffed MIME types.
     *
     * @param string[] $extensions
     * @param string[] $mimeTypes
     */
    function setAllowedTypes(array $extensions, array $mimeTypes): self
    {
        $this->allowedExtensions = $this->normalizeTypeList($extensions, true);
        $this->allowedMimeTypes = $this->normalizeTypeList($mimeTypes, false);
        return $this;
    }

    protected function normalizeTypeList(array $types, bool $isExtension): array
    {
        $normalized = [];

        foreach ($types as $type) {
            if (!is_string($type)) {
                continue;
            }

            $type = strtolower(trim($type));
            if ($isExtension) {
                $type = ltrim($type, '.');
            }

            if ($type !== '') {
                $normalized[] = $type;
            }
        }

        return array_values(array_unique($normalized));
    }

    protected function isUploadedFile(string $tmpPath): bool
    {
        return is_uploaded_file($tmpPath);
    }

    protected function detectMimeType(string $tmpPath): ?string
    {
        if (!is_file($tmpPath) || !is_readable($tmpPath) || !function_exists('finfo_open')) {
            return null;
        }

        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        if ($finfo === false) {
            return null;
        }

        try {
            $mimeType = finfo_file($finfo, $tmpPath);
        } finally {
            finfo_close($finfo);
        }

        if (!is_string($mimeType) || trim($mimeType) === '') {
            return null;
        }

        return strtolower(trim($mimeType));
    }

    protected function isTypeAllowed(string $tmpPath, string $clientName): bool
    {
        $extension = strtolower(pathinfo($clientName, PATHINFO_EXTENSION));
        if (!in_array($extension, $this->allowedExtensions, true) || !$this->isUploadedFile($tmpPath)) {
            return false;
        }

        $mimeType = $this->detectMimeType($tmpPath);
        return $mimeType !== null && in_array($mimeType, $this->allowedMimeTypes, true);
    }

    // path without trailing slash
    function setLocation(string $path): self
    {
        $this->location = $path;

        if (!file_exists($this->location)) {
            Files::mkDirOrFail($this->location);
        }

        return $this;
    }

    function getLocation(): string
    {
        return $this->location;
    }

    // Renamer
    function setFileHandler(callable $fn, ...$params): self
    {
        $this->renamerFn = [$fn, $params];
        return $this;
    }

    // Returns original/stored names plus field and index
    function getFileNames(): array
    {
        return $this->filenames;
    }

    // Files with errors
    function getErrors(): array
    {
        return $this->erroneous;
    }

    /**
     * Process upload.
     * If $input_name is null, processes all inputs in $_FILES.
     * Supports arrays of files and wildcard names (e.g. "file_*").
     */
    function doUpload(?string $input_name = null): self
    {
        if (empty($_FILES)) {
            return $this;
        }

        // Renamer fallback
        $renamerFn = function (string $suffix = ''): string {
            return uniqid($suffix, true);
        };

        $subfijo = '';
        if ($this->renamerFn !== null) {
            $renamerFn = $this->renamerFn[0];
            $subfijo   = $this->renamerFn[1][0] ?? '';
        }

        // reset
        $this->filenames = [];
        $this->erroneous = [];

        Files::mkDirOrFail($this->location);
        Files::writableOrFail($this->location);

        $key_0 = Arrays::arrayKeyFirst($_FILES);
        $file0 = $_FILES[$key_0] ?? null;
        $name  = $input_name !== null ? $input_name : $key_0;

        // Helper to record an error
        $pushError = function (string $field, ?int $index, string $client_name) {
            $this->erroneous[] = [
                'field'    => $field,
                'index'    => $index,
                'ori_name' => basename($client_name)
            ];
        };

        // Helper to move/store a single file
        $saveOne = function (string $field, ?int $index, string $tmp_path, string $client_name) use ($renamerFn, $subfijo, $pushError) {
            if (!$this->isTypeAllowed($tmp_path, $client_name)) {
                $pushError($field, $index, $client_name);
                return;
            }

            $extension = strtolower(pathinfo($client_name, PATHINFO_EXTENSION));
            $new_filename = $renamerFn($subfijo) . '.' . $extension;
            $destination = $this->location . DIRECTORY_SEPARATOR . $new_filename;

            if (!move_uploaded_file($tmp_path, $destination)) {
                $pushError($field, $index, $client_name);
                return;
            }

            $this->filenames[] = [
                'field'     => $field,
                'index'     => $index,
                'ori_name'  => basename($client_name),
                'as_stored' => $new_filename,
                'full_path' => $destination,
                'size'      => filesize($destination)
            ];
        };

        // Case 1: explicit array for a given name
        if ($file0 !== null && is_array($file0['error']) && isset($_FILES[$name]['error']) && is_array($_FILES[$name]['error'])) {
            foreach ($_FILES[$name]['error'] as $idx => $error) {
                if ($error === UPLOAD_ERR_OK) {
                    $saveOne($name, $idx, $_FILES[$name]['tmp_name'][$idx], $_FILES[$name]['name'][$idx]);
                } else {
                    $pushError($name, $idx, $_FILES[$name]['name'][$idx]);
                }
            }
            return $this;
        }

        // Case 2: explicit single input name
        if ($input_name !== null && isset($_FILES[$input_name]['error'])) {
            if ($_FILES[$input_name]['error'] === UPLOAD_ERR_OK) {
                $saveOne($input_name, null, $_FILES[$input_name]['tmp_name'], $_FILES[$input_name]['name']);
            } else {
                $pushError($input_name, null, $_FILES[$input_name]['name']);
            }
            return $this;
        }

        // Case 3: wildcard (e.g., "file_*")
        if ($input_name !== null && substr($input_name, -1) === self::WILDCARD) {
            $starts_with = substr($input_name, 0, strlen($input_name) - 1);
            foreach ($_FILES as $_name => $file) {
                if (strpos($_name, $starts_with) !== 0) {
                    continue;
                }
                if (is_array($file['error'])) {
                    foreach ($file['error'] as $idx => $error) {
                        if ($error === UPLOAD_ERR_OK) {
                            $saveOne($_name, $idx, $file['tmp_name'][$idx], $file['name'][$idx]);
                        } else {
                            $pushError($_name, $idx, $file['name'][$idx]);
                        }
                    }
                } else {
                    if ($file['error'] === UPLOAD_ERR_OK) {
                        $saveOne($_name, null, $file['tmp_name'], $file['name']);
                    } else {
                        $pushError($_name, null, $file['name']);
                    }
                }
            }
            return $this;
        }

        // Case 4: no input_name -> iterate every $_FILES entry
        foreach ($_FILES as $_name => $file) {
            if (is_array($file['error'])) {
                foreach ($file['error'] as $idx => $error) {
                    if ($error === UPLOAD_ERR_OK) {
                        $saveOne($_name, $idx, $file['tmp_name'][$idx], $file['name'][$idx]);
                    } else {
                        $pushError($_name, $idx, $file['name'][$idx]);
                    }
                }
            } else {
                if ($file['error'] === UPLOAD_ERR_OK) {
                    $saveOne($_name, null, $file['tmp_name'], $file['name']);
                } else {
                    $pushError($_name, null, $file['name']);
                }
            }
        }

        return $this;
    }
}
