<?php declare(strict_types=1);

namespace Boctulus\Simplerest\Core\Libs;

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

use Boctulus\Simplerest\Core\Libs\Files;
use Boctulus\Simplerest\Core\Libs\Arrays;
use Boctulus\Simplerest\Core\Libs\Numbers;

class FileUploader
{
    protected array $filenames = [];
    protected string $location = UPLOADS_PATH;
    protected array $erroneous = [];
    protected $renamerFn = null;
    protected const WILDCARD = '*';

    function __construct()
    {
        if (!file_exists($this->location)) {
            Files::mkDirOrFail($this->location);
        }
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

        // Helper to move/store a single file
        $saveOne = function (string $field, ?int $index, string $tmp_path, string $client_name) use ($renamerFn, $subfijo) {
            $new_filename = $renamerFn($subfijo) . '.' . pathinfo($client_name, PATHINFO_EXTENSION);
            $this->filenames[] = [
                'field'     => $field,
                'index'     => $index,
                'ori_name'  => basename($client_name),
                'as_stored' => $new_filename
            ];
            move_uploaded_file($tmp_path, $this->location . DIRECTORY_SEPARATOR . $new_filename);
        };

        // Helper to record an error
        $pushError = function (string $field, ?int $index, string $client_name) {
            $this->erroneous[] = [
                'field'    => $field,
                'index'    => $index,
                'ori_name' => basename($client_name)
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
