<?php

namespace JSONWebToken\Tests;

use JSONWebToken\JWT as FBJWT;
use Firebase\JWT\ExpiredException;
use PHPUnit\Framework\TestCase;

class JWTTest extends TestCase
{
    public function testEncodeDecode()
    {
        $instance = $this->getInstance();

        $iss = 'issuer';
        $aud = 'audience';
        $sub = 'subject';
        $exp = '+ 1 hour';

        $data = [
            'key1' => 'v1',
            'key2' => 'v2',
        ];

        $jwt = $instance->encode(
            $iss,
            $aud,
            $sub,
            $exp,
            $data
        );

        $expected = [
            'key1' => 'v1',
            'key2' => 'v2',
            'iss' => 'issuer',
            'aud' => 'audience',
            'sub' => 'subject',
            'iat' => ( new \DateTimeImmutable() )->getTimestamp(),
            'exp' => ( new \DateTimeImmutable() )->modify( '+ 1 hour' )->getTimestamp(),
        ];

        $this->assertSame( $expected, $instance->decode( $jwt ) );
    }

    public function testException()
    {
        $this->expectException( ExpiredException::class );
        $this->expectExceptionMessage( 'Expired token' );

        $instance = $this->getInstance();

        $iss = 'issuer';
        $aud = 'audience';
        $sub = 'subject';
        $exp = '- 1 hour';

        $data = [
            'key1' => 'v1',
            'key2' => 'v2',
        ];

        $jwt = $instance->encode(
            $iss,
            $aud,
            $sub,
            $exp,
            $data
        );

        $expectedPayload = [
            'key1' => 'v1',
            'key2' => 'v2',
            'iss' => 'issuer',
            'aud' => 'audience',
            'sub' => 'subject',
            'iat' => ( new \DateTimeImmutable() )->getTimestamp(),
            'exp' => ( new \DateTimeImmutable() )->modify( '- 1 hour' )->getTimestamp(),
        ];

        $this->assertSame( $expectedPayload, $instance->decode( $jwt ) );
    }

    public function testMalform()
    {
        $this->expectException( \Exception::class );

        $this->expectExceptionMessage( 'Malformed jwt received.' );

        $jwt = 'eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJzdWIiOiIxMjM0NTY3ODkwIiwibmFtZSI6IkpvaG4gRG9lIiwiaWF0IjoxNTE2MjM5MDIyfQ';

        $this->getInstance()->payload( $jwt );
    }

    protected function getInstance()
    {
        return new FBJWT(
            file_get_contents(__DIR__ . '/private_key.pem'),
            file_get_contents(__DIR__ . '/public_key.pem')
        );
    }
}
