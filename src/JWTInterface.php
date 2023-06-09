<?php

namespace JSONWebToken;

interface JWTInterface
{
    public function encode( string $iss, string $aud, string $sub, string $expires = null, array $claims = []) : string;
    public function decode( string $token, bool $ignoreExceptions = false ) : ?array;
}
