<?php
/**
 * Created by PhpStorm.
 * User: jg
 * Date: 03/05/16
 * Time: 15:20
 */

//https://github.com/travist/jsencrypt

$publicKey = "-----BEGIN PUBLIC KEY-----
MIGfMA0GCSqGSIb3DQEBAQUAA4GNADCBiQKBgQC+ALczN1La2w7FxXSjccRQ/nTC
7O3IqjPTiaZPlQTpfg+dOi1BnJy5aw73/qg9RMit+tuJ1M9kvTcxqn87ObinkabF
xiMuEk8m57Subc4ePt3bGQfKAHz/2TUA5u8pIXPsEuHKbKdocc0W71VpCCBdNqiN
N9LXqRFOdTTUudNDIQIDAQAB
-----END PUBLIC KEY-----";

$privateKey = "***PRIVATE KEY REMOVED***";

$crypted = "";
openssl_public_encrypt ( 'Hello World', $crypted , $publicKey);

echo base64_encode($crypted) . "\n\n";

$decrypted = "";
openssl_private_decrypt($crypted, $decrypted, $privateKey);

echo $decrypted . "\n\n";
