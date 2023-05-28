<?php

namespace simplerest\core\libs;


class GoogleDrive
{
	protected $client;

    function __construct() { $this->__getClient(); }

    // Google API client
	protected function __getClient(){
		$cfg = config();

        $google_console_api_key = $cfg['google_console_api_key'];

        $class  = 'Google\Client';
        $client = new $class();

        // Disable SSL check in local
        if (env('APP_ENV') == 'local'){
            $guzzleClient = new \GuzzleHttp\Client(["curl" => [
                CURLOPT_SSL_VERIFYPEER => false
            ]]);        
        }
        
        $client->setHttpClient($guzzleClient);

        $client->setApplicationName($cfg['app_name']);
        $client->setDeveloperKey($google_console_api_key);

		$this->client = $client;
	}

    protected function __getDriveService(){
        $class = 'Google_Service_Drive';
        return new $class($this->client);
    }

    /*
        Obtiene info (con los permisos correctos) sobre un "drive" o "folder" 
        o sea... primero hace un "list" de archivos

        Ej:
    
        $googleDrive->getInfo('1oUqLiey81m0keXAo1ZtOsGYfd5c1VTeT')

        o

        $googleDrive->getInfo('1oUqLiey81m0keXAo1ZtOsGYfd5c1VTeT', null, 'createdTime', 'modifiedTime')

        o

        googleDrive->getInfo('1oUqLiey81m0keXAo1ZtOsGYfd5c1VTeT', null,  'createdTime, modifiedTime')

        o incluyendo paginacion

        $googleDrive->getInfo('1oUqLiey81m0keXAo1ZtOsGYfd5c1VTeT', [
            'pageSize' => 10
        ], 'id, name, createdTime, modifiedTime');

    */
    function getInfo(?string $folder_id = null, ?array $options = null, ...$file_fields)
    {
        $service = $this->__getDriveService();

        $query = "trashed = false";
        if ($folder_id) {
            // Add the folder ID to the query
            $query .= " and '{$folder_id}' in parents";
        }

        $file_fields_str = '';
        if (!empty($file_fields)){
            $file_fields_str = implode(',', $file_fields); 
        }  

        $_options = [
            'q' => $query,
            'fields' => "files($file_fields_str)",
        ];

        /*
            Podria incluir offset ("page"?),... y hacer un merge entre $_options y $options
        */
        if (isset($options['pageSize'])){
            $_options['pageSize'] =  $options['pageSize'];
            $_options['fields']   = 'nextPageToken, '. $_options['fields'];
        }

        $files = $service->files->listFiles($_options);

        if (is_array($file_fields) && count($file_fields) == 1 && Strings::contains(',', $file_fields[0])){
            $file_fields = explode(',',$file_fields[0]);
            $file_fields = array_map('trim', $file_fields);
        }

        $ret = [];
        foreach ($files->getFiles() as $file) {
            $row = [];

            foreach ($file_fields as $field){
                $getter = "get". ucfirst($field);
                $row[$field] = $file->{$getter}();
            }

            $ret[] = $row;
        }

        return $ret;
    }

    /*
        Obtiene info sobre un ARCHIVO en particular
    */
    function getUpdateDate(string $link_or_id): string
    {
        if (Strings::startsWith('https://docs.google.com/', $link_or_id)){
            $id = Url::getQueryParam($link_or_id, 'id');
        } else {
            $id = $link_or_id;
        }

        $service = $this->__getDriveService();

        // Retrieve the file metadata based on the link or ID
        $file = $service->files->get($id, [
            'fields' => 'modifiedTime'
        ]);

        // Extract the modified time from the file metadata
        $modifiedTime = $file->getModifiedTime();

        // Convert the modified time to the desired format (e.g., 'Y-m-d H:i:s')
        $updateDate = date('Y-m-d H:i:s', strtotime($modifiedTime));

        return $updateDate;
    }

}
