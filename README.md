RS256 Public/Private key JWT class

```bash
$ openssl genpkey -algorithm RSA -out /tests/private_key.pem -pkeyopt rsa_keygen_bits:2048;
$ openssl rsa -pubout -in /tests/private_key.pem -out /tests/public_key.pem
$ composer install
```

##### Encode/Decode JWT string
```php
declare( strict_types = 1 );

$autoloader = require __DIR__ . '/vendor/autoload.php';
use JSONWebToken\JWT as JWT;

define( 'PUBLIC_KEY' , file_get_contents( __DIR__ . '/tests/public_key.pem'  ) );
define( 'PRIVATE_KEY', file_get_contents( __DIR__ . '/tests/private_key.pem' ) );

// JWT class instance
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

//{"token":"eyJ0eXAiOiJKV1QiLCJhbGciOiJSUzI1NiJ9.eyJLZXkxIjoidmFsdWUxIiwiS2V5MiI6InZhbHVlMiIsImlzcyI6Imlzc3VlciIsImF1ZCI6ImF1ZGllbmNlIiwic3ViIjoic3ViamVjdCIsImlhdCI6MTY4MjczMDExMiwiZXhwIjoxNjgyNzMzNzEyfQ.p5mVHvtgbvTBhjhBg7s7q980SMjCT1BWIRm-hJCemmCrZ8bWUmmXNDE2yae8ExAjI17NV2PuaT7APspf5AzHw6IXm7HzEjoOEbPdrhwDi8gOcKSFEDj4mcD09fWoTdoGJ04RomleuybtJ0NKdcyAEzuFkR3fJyPmO4PZ0G8GpE9rB3B-8iVSNSbiaBBJ3YysveOf2AJmI0yIE6UaoDEISZnUFlLFLaIE1INKjCwit8KXYaAig4sbzbUOOlpmwNMN62n1PdkFaS8rFJNuifJ-0fAqYF-GpC0oSc_8Wq2e9rtqVWE9g7V_NPZJ7KwpPqMbdZUYcoIi148UwFuEFDzyMg"}

// Decode
$decoded = $jwt->decode( $encoded ) ;

print_r( $decoded );

//Array([Key1] => value1, [Key2] => value2, [iss] => issuer, [aud] => audience, [sub] => subject, [iat] => 1682730112, [exp] => 1682733712)
```

##### Test
```bash
./cli/phpunit tests
```

## License

This software is open source, licensed under the The MIT License (MIT).



