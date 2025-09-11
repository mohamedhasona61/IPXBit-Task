<?php

namespace App\Services;


use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Exception;


class JWTService
{
    public static function sign(array $claims, ?int $ttl = null): string
    {
        $secret = base64_decode(env('JWT_SECRET')) ?: env('JWT_SECRET');
        $now = time();
        $exp = $now + ($ttl ?? (int) (env('JWT_TTL', 3600)));
        $payload = array_merge([
            'iat' => $now,
            'nbf' => $now,
            'exp' => $exp,
        ], $claims);
        return JWT::encode($payload, $secret, 'HS256');
    }
    public static function decode(string $token): object
    {
        $secret = base64_decode(env('JWT_SECRET')) ?: env('JWT_SECRET');
        try {
            return JWT::decode($token, new Key($secret, 'HS256'));
        } catch (Exception $e) {
            throw $e;
        }
    }
}
