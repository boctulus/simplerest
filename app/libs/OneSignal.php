<?php

namespace simplerest\libs;

use simplerest\core\Model;

class OneSignal
{
    // estaría en el config.php o en un .env por ejemplo
    static $appId = '9381a718-414c-4f09-b810-2288913de0a0';
    static $restApiKey = 'OWQ3NTRkOWYtZGQ1ZS00ZTkwLThiMjUtNmQ0ODQzNjA2YzMw';

    static function sendPush($oneSignalConfig) {
        if (!sizeof($oneSignalConfig)) {  
            return null;
        }    

        $notifTitle = html_entity_decode($oneSignalConfig['title'],ENT_QUOTES, 'UTF-8');
        $notifContent = html_entity_decode($oneSignalConfig['body'],ENT_QUOTES, 'UTF-8');
    
        $includedSegments = array('All');      
    
        $fields = array(
            'app_id' => $oneSignalConfig['app_id'],
            'headings' => array("en" => $notifTitle),
            'included_segments' => $includedSegments,
            'isAnyWeb' => true,
            'url' => $oneSignalConfig['url'],
            'contents' => array("en" => $notifContent)
        );
    
        $image_url = $oneSignalConfig['image_url'] ?? null;
    
        if (!empty($image_url)) {
            $fields['chrome_web_image'] = $image_url;
        }

        $buttons = $oneSignalConfig['buttons'] ?? null;

        if (!empty($buttons)){
            $fields['web_buttons'] = $buttons;
        }

        // Addtional data
        $extra_data = $oneSignalConfig['extra'] ?? null;

        if (!empty($extra_data)){
            $fields['data'] = $extra_data;
        }
    
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, "https://onesignal.com/api/v1/notifications");
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json',
                                'Authorization: Basic ' . $oneSignalConfig['app_rest_api_key']));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($ch, CURLOPT_HEADER, FALSE);
        curl_setopt($ch, CURLOPT_POST, TRUE);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($fields));
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
    
        $response = curl_exec($ch);
        curl_close($ch);
    
        return $response;
    } 

    // Ej;
    static function sendMessageToAll(Array $content,) {
        $content      = array(
            "en" => 'English Message'
        );
        $hashes_array = array();

        array_push($hashes_array, array(
            "id" => "like-button",
            "text" => "Like",
            "icon" => "http://i.imgur.com/N8SN8ZS.png",
            "url" => "https://yoursite.com"
        ));
        
        array_push($hashes_array, array(
            "id" => "like-button-2",
            "text" => "Like2",
            "icon" => "http://i.imgur.com/N8SN8ZS.png",
            "url" => "https://yoursite.com"
        ));
        
        $fields = array(
            'app_id' => static::$appId,
            'included_segments' => array(
                'Subscribed Users'
            ),
            'data' => array(
                "foo" => "bar"
            ),
            'contents' => $content,
            'web_buttons' => $hashes_array
        );
        
        $fields = json_encode($fields);
        
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, "https://onesignal.com/api/v1/notifications");
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'Content-Type: application/json; charset=utf-8',
            'Authorization: Basic '. static::$restApiKey
        ));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($ch, CURLOPT_HEADER, FALSE);
        curl_setopt($ch, CURLOPT_POST, TRUE);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $fields);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
        
        $response = curl_exec($ch);
        curl_close($ch);
        
        return $response;
    }

    
    
}

