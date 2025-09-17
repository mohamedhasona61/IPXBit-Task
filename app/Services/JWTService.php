<?php

namespace App\Services;

use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use App\Models\RefreshToken;


class JWTService
{
    private string $key;

    public function __construct()
    {
        $this->key = config('app.jwt_secret', env('APP_KEY'));
    }
    public function encode(array $payload, int $ttl = 3600): string
    {
        $payload['iat'] = time();
        $payload['exp'] = time() + $ttl;
        return JWT::encode($payload, $this->key, 'HS256');
    }
    public function decode(string $token): object
    {
        return JWT::decode($token, new Key($this->key, 'HS256'));
    }
    public function generateTokens(array $payload): array
    {
        $accessToken = $this->encode($payload, 3600);
        $refreshToken = $this->encode([
            'sub'       => $payload['sub'],
            'tenant_id' => $payload['tenant_id'],
            'type'      => 'refresh'
        ], 604800);
        RefreshToken::create([
            'tenant_id'  => $payload['tenant_id'],
            'user_id'    => $payload['sub'],
            'token'      => $refreshToken,
            'expires_at' => now()->addDays(7),
        ]);
        return [
            'access_token'  => $accessToken,
            'refresh_token' => $refreshToken,
            'token_type'    => 'Bearer',
            'expires_in'    => 3600
        ];
    }
}
