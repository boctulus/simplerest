# Jwt-Wrapper for Firebase Jwt

[![Opensource ByJG](https://img.shields.io/badge/opensource-byjg.com-brightgreen.svg)](http://opensource.byjg.com)
[![Build Status](https://travis-ci.org/byjg/jwt-wrapper.svg?branch=master)](https://travis-ci.org/byjg/jwt-wrapper)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/byjg/jwt-wrapper/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/byjg/jwt-wrapper/?branch=master)

A very simple wrapper for create, encode, decode JWT Tokens and abstract the PHP JWT Component


# How it works

This library is intented to be located at server side. 

The flow is

Without Token:

```
         Request         Return 
         Token           Token
CLIENT ---------->LOGIN----------->CLIENT
  |                 |                 |
Without          Generate           Store
Token            Token              Locally
           (JwtWrapper::createJwtData)
           (JwtWrapper::generateToken)
```

With token

```
                        Return the 
       Pass Token       API Result
CLIENT ----------> API ----------->CLIENT
  |                 |                 
Wants to       Validate and         
Access         Extract Token        
Private      (JwtWrapper::extractData)
Resource
```

# Create your Jwt Secret Key

You can use two type of secret keys. A Hash (HS512) that is faster, or a RSA (RS512) that is more secure. 

## Hash Key

```
openssl rand -base64 64     # set here the size of your key
```

## RSA

```
ssh-keygen -t rsa -C "Jwt RSA Key" -b 2048 -f private.pem
openssl rsa -in private.pem -outform PEM -pubout -out public.pem
```

**Note**: Save without password 

# Create JWT Token (Hash Encoding):

```php
<?php
$server = "example.com";
$secret = new \ByJG\Util\JwtKeySecret(base64_encode("secrect_key_for_test"));

$jwtWrapper = new \ByJG\Util\JwtWrapper($server, $secret);

$token = $jwtWrapper->createJwtData([
    "key" => "value",
    "key2" => "value2"
]);
```

# Create JWT Token (RSA Encoding):

```php
<?php
$server = "example.com";
$secret = <<<TEXT
***PRIVATE KEY REMOVED***
TEXT;
          
$public = <<<TEXT
-----BEGIN PUBLIC KEY-----
MIIBIjANBgkqhkiG9w0BAQEFAAOCAQ8AMIIBCgKCAQEA5PMdWRa+rUJmg6QMNAPI
Xa+BJVN7W0vxPN3WTK/OIv5gxgmj2inHGGc6f90TW/to948LnqGtcD3CD9KsI55M
ubafwBYjcds1o9opZ0vYwwdIV80cOVZX1IUZFTbnyyKcXeFmKt49A52haCiy4iNx
cRK38tOCApjZySx/NzMDeaXuWe+1nd3pbgYa/I8MkECa5EyabhZJPJo9fGoSZIkl
Nnyq4TfAUSwl+KN/zjj3CXad1oDT7XDDgMJDUu/Vxs7h3CQI9zILSYcL9zwttbLn
JW1WcLlAAIaAfABtSZboznsStMnYto01wVknXKyERFs7FLHYqKQANIvRhFTptseh
owIDAQAB
-----END PUBLIC KEY-----
TEXT;

# Note that if you want to use RSA just pass the 3rd argument (public key)
# See above how to create the RSA Key pair.
$jwtKey = new \ByJG\Util\JwtRsaKey($secret, $public);
$jwtWrapper = new \ByJG\Util\JwtWrapper($server, $jwtKey);

$token = $jwtWrapper->createJwtData([
    "key" => "value",
    "key2" => "value2"
]);
```

# Extracting

```php
<?php
# If exists $_SERVER['HTTP_AUTHENTICATION'] = "Bearer $TOKEN"
$data = $jwtWrapper->extractData();

# If you want decode directly:
$data = $jwtWrapper->extractData($token);
```

## Adding a Leeway

You can add a leeway to account for when there is a clock skew times between
the signing and verifying servers. It is recommended that this leeway should
not be bigger than a few minutes.

```php
$jwtWrapper->setLeeway(60)
```

Important: Since the Firebase JWT class set the leeway value as a "static" property
once you call the method above it will set up the same value to all JwtWrapper instances

# Install

```bash
composer require "byjg/jwt-wrapper=2.0.*"
```

# Running a sample test

Start a local server:

```
php -S localhost:8080
```

Access from you web browser the client.html

```
http://localhost:8080/client.html
```

----
[Open source ByJG](http://opensource.byjg.com)

