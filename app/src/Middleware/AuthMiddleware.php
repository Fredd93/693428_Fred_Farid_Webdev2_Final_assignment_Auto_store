<?php
namespace GTA\Middleware;

use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use GTA\Helpers\ResponseHelper;

class AuthMiddleware
{
    private static array $roleHierarchy = ['client' => 1, 'employee' => 2, 'admin' => 3];

    public static function require(string $minRole = 'client'): array
    {
        $header = $_SERVER['HTTP_AUTHORIZATION'] ?? '';

        if (!str_starts_with($header, 'Bearer ')) {
            ResponseHelper::error('Unauthorized', 401);
        }

        $token = substr($header, 7);
        $secret = $_ENV['APP_SECRET'] ?? getenv('APP_SECRET') ?: 'changeme_secret';

        try {
            $decoded = JWT::decode($token, new Key($secret, 'HS256'));
            $payload = (array) $decoded;
        } catch (\Exception $e) {
            ResponseHelper::error('Invalid or expired token', 401);
        }

        $userRole  = $payload['role'] ?? 'client';
        $userLevel = self::$roleHierarchy[$userRole]  ?? 0;
        $minLevel  = self::$roleHierarchy[$minRole]   ?? 99;

        if ($userLevel < $minLevel) {
            ResponseHelper::error('Forbidden', 403);
        }

        return $payload;
    }

    public static function generateToken(array $user): string
    {
        $secret = $_ENV['APP_SECRET'] ?? getenv('APP_SECRET') ?: 'changeme_secret';

        $payload = [
            'sub'   => $user['id'],
            'role'  => $user['role'],
            'name'  => $user['name'],
            'email' => $user['email'],
            'exp'   => time() + 86400,
        ];

        return JWT::encode($payload, $secret, 'HS256');
    }
}
