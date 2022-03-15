<?php

namespace simplerest\core\libs;

class OneSignal
{
    static function send($config) {
        if (!sizeof($config)) {  
            return null;
        }  

        /*  
            Segments

            https://documentation.onesignal.com/docs/segmentation
        */

        $segments = $config['segments'] ?? ['All'];

        $config['title'] = $config['title'] ?? '';
        $config['body']  = $config['body'] ?? '';

        $notifTitle   = html_entity_decode($config['title'],ENT_QUOTES, 'UTF-8');
        $notifContent = html_entity_decode($config['body'],ENT_QUOTES, 'UTF-8');  
    
        $fields = array(
            'app_id' => $config['app_id'],
            'headings' => array("en" => $notifTitle),
            'included_segments' => $segments,
            'isAnyWeb' => true,
            'url' => $config['url'],
            'contents' => array("en" => $notifContent)
        );
    
        $image = $config['image'] ?? null;
    
        if (!empty($image)) {
            // Chrome
            $fields['chrome_web_image']   = $image;
            
            // Android
            $fields['big_picture']        = $image;

            // Huawei
            $fields['huawei_big_picture'] = $image;
            
            // iOS
            $fields['ios_attachments'] = ['id1' => $image];
        }

        $icon = $config['icon'] ?? null;
    
        if (!empty($icon)) {
            $fields['chrome_web_icon']  = $icon;
            $fields['firefox_icon'] = $icon;
        }

        $badge = $config['badge'] ?? null;
    
        if (!empty($badge)) {
            $fields['chrome_web_badge']  = $badge;
        }

        $buttons = $config['buttons'] ?? null;

        if (!empty($buttons)){
            $fields['web_buttons'] = $buttons;
        }

        // Addtional data
        $extra_data = $config['extra'] ?? null;

        if (!empty($extra_data)){
            $fields['data'] = $extra_data;
        }

        $response = Url::consume_api("https://onesignal.com/api/v1/notifications", 'POST', $fields, 
            [             
                'Content-Type: application/json',
                'Authorization: Basic ' . $config['app_rest_api_key']
            ],  
            
            [
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_HEADER => false
            ]
        );

        // En general solo cambios variables
        Files::dump([
            'DATA' => $fields,
            'RESPONSE' => $response
        ], 'curl_log.txt', true);
    
        return $response;
    } 
    
    
}

