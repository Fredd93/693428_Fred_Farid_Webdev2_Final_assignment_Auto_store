<?php
namespace GTA\Controllers;

use GTA\Models\UserModel;
use GTA\Middleware\AuthMiddleware;
use GTA\Helpers\ResponseHelper;

class AuthController
{
    private UserModel $users;

    public function __construct() { $this->users = new UserModel(); }

    public function login(): void
    {
        $body = json_decode(file_get_contents('php://input'), true) ?? [];
        $email    = trim($body['email']    ?? '');
        $password = trim($body['password'] ?? '');

        if (!$email || !$password) {
            ResponseHelper::error('Email and password are required', 400);
        }

        $user = $this->users->findByEmail($email);
        if (!$user || !password_verify($password, $user['password'])) {
            ResponseHelper::error('Invalid credentials', 401);
        }

        $token = AuthMiddleware::generateToken($user);
        ResponseHelper::json([
            'token' => $token,
            'user'  => ['id' => $user['id'], 'name' => $user['name'], 'email' => $user['email'], 'role' => $user['role']],
        ]);
    }

    public function register(): void
    {
        $body = json_decode(file_get_contents('php://input'), true) ?? [];
        $name     = trim($body['name']     ?? '');
        $email    = trim($body['email']    ?? '');
        $password = trim($body['password'] ?? '');

        if (!$name || !$email || !$password) {
            ResponseHelper::error('Name, email and password are required', 400);
        }
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            ResponseHelper::error('Invalid email', 400);
        }
        if ($this->users->findByEmail($email)) {
            ResponseHelper::error('Email already registered', 409);
        }

        $hash = password_hash($password, PASSWORD_BCRYPT);
        $id   = $this->users->create($name, $email, $hash);
        $user = $this->users->findById($id);
        $token = AuthMiddleware::generateToken($user);

        ResponseHelper::json([
            'token' => $token,
            'user'  => ['id' => $user['id'], 'name' => $user['name'], 'email' => $user['email'], 'role' => $user['role']],
        ], 201);
    }
}
