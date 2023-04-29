<?php
declare( strict_types = 1 );

$autoloader = require __DIR__ . '/vendor/autoload.php';
use JSONWebToken\JWT as JWT;

define('PRIVATE_KEY', file_get_contents(__DIR__ . '/tests/private_key.pem'));
define('PUBLIC_KEY', file_get_contents(__DIR__ . '/tests/public_key.pem'));

// JWT instance
$jwt = new JWT( PRIVATE_KEY, PUBLIC_KEY ); // RS256

$iss = 'issuer';
$aud = 'audience';
$sub = 'subject';
$exp = '+ 1 hour';

// Encode
$encoded = $jwt->encode($iss, $aud, $sub, $exp, [ 'Key1' => 'value1', 'Key2' => 'value2' ] );

echo json_encode(
[
    'token' => $encoded
]) . PHP_EOL;


// Decode
$decoded = $jwt->decode( $encoded ) ;

print_r( $decoded );