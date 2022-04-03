<?php

namespace simplerest\core\interfaces;

interface IMail {
    
    static function send(Array $to, $subject = '', $body = '', $alt_body = null, $attachments = null, Array $from = [], Array $cc = [], Array $bcc = [], Array $reply_to = []);

}