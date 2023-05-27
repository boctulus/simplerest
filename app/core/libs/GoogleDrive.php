<?php

namespace simplerest\core\libs;


class GoogleDrive
{
	protected $client;

    function __construct() { $this->__getClient(); }

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

	function getUpdateDate(string $link_or_id): string
    {
        $class   = 'Google_Service_Drive';
        $service = new $class($this->client);

        // Retrieve the file metadata based on the link or ID
        $file = $service->files->get($link_or_id, ['fields' => 'modifiedTime']);

        // Extract the modified time from the file metadata
        $modifiedTime = $file->getModifiedTime();

        // Convert the modified time to the desired format (e.g., 'Y-m-d H:i:s')
        $updateDate = date('Y-m-d H:i:s', strtotime($modifiedTime));

		//here();

        return $updateDate;
    }

}
