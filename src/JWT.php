<?php

namespace JSONWebToken;

use Exception;
use DateTimeImmutable;
use Firebase\JWT\JWT as FBJWT;
use Firebase\JWT\Key;

class JWT implements JWTInterface
{
    private const ALG = 'RS256';

    private $privateKey;

    private $publicKey;

    public function __construct( string $privateKey = null, string $publicKey = null )
    {
        $this->privateKey = $privateKey;
        $this->publicKey = $publicKey;
    }

    /**
     * Generate JWT Token using private key
     *
     * @param string $iss Issuer
     * @param string $aud Audition
     * @param string $sub Subject
     * @param string|null $expires Expiration constraint (example: + 10 days)
     * @param array $claims Public claims (example: ['user_id' => 10])
     * @return string Returns JWT token as string
     * @throws Exception
     */
    public function encode(string $iss, string $aud, string $sub, string $expires = null, array $claims = []): string
    {
        $now = new DateTimeImmutable();

        $claims = array_merge($claims, [
            'iss' => $iss,
            'aud' => $aud,
            'sub' => $sub,
            'iat' => $now->getTimestamp(),
            'exp' => $this->expires($now, $expires)
        ]);

        return FBJWT::encode($claims, $this->privateKey, self::ALG);
    }

    /**
     * Decode JWT Token using public key
     *
     * @param string $token JWT Token as string
     * @param bool $ignoreExceptions If true will ignore any kind of exception and return null
     * @return array|null Returns payload as array or null if exceptions are ignored
     * @throws Exception
     */
    public function decode( string $token, bool $ignoreExceptions = false ): ?array
    {
        try {
            $payload = FBJWT::decode($token, new Key($this->publicKey, self::ALG));

            return json_decode(json_encode($payload), true);

        } catch (Exception $exception)
        {
            $this->exceptionHandler($exception, $ignoreExceptions);

            return null;
        }
    }

    /**
     * Extract payload of jwt token without validations
     *
     * @param string $token JWT Token as string
     * @return array Returns payload as array
     * @throws Exception
     */
    public function payload( string $token ): array
    {
        $segments = $this->segments($token);

        return json_decode(
            FBJWT::urlsafeB64Decode($segments[1]), true
        );
    }

    /**
     * Verify the number of segments to assert a valid token
     *
     * @param string $token
     * @return array
     * @throws Exception
     */
    protected function segments( string $token ) : array
    {
        $segments = explode('.', $token);

        if ( 3 !== count( $segments ) )
        {
            throw new Exception("Malformed jwt received.");
        }

        return $segments;
    }

    /**
     * Expiration verification.
     *
     * @param DateTimeImmutable $now
     * @param string|null $expires
     * @return int|null
     */
    protected function expires( DateTimeImmutable $now, string $expires = null )
    {
        return !$expires ? null : $now->modify($expires)->getTimestamp();
    }

    /**
     * Exception handler
     *
     * @param Exception $exception
     * @param bool $ignoreException
     * @throws Exception
     */
    protected function exceptionHandler( Exception $exception, $ignoreException = false )
    {
        if ( !$ignoreException )
        {
            throw $exception;
        }
    }
}
