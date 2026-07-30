<?php
namespace GTA\Controllers;

use GTA\Models\UserModel;
use GTA\Middleware\AuthMiddleware;
use GTA\Helpers\ResponseHelper;

class UserController
{
    // Egyptian mobile numbers: 01 + operator prefix (0,1,2,5) + 8 digits, optional +20 country code.
    private const EGYPT_PHONE_REGEX = '/^(?:\+20|0)1[0125][0-9]{8}$/';

    private UserModel $users;

    public function __construct() { $this->users = new UserModel(); }

    public function index(): void
    {
        $auth  = AuthMiddleware::require('employee');
        $page  = max(1, (int)($_GET['page']  ?? 1));
        $limit = min(50, max(1, (int)($_GET['limit'] ?? 15)));
        // Employees see client contact info only; admins see everyone (employees + clients).
        $onlyRole = $auth['role'] === 'admin' ? null : 'client';
        ResponseHelper::json($this->users->listPaginated($page, $limit, $onlyRole));
    }

    public function store(): void
    {
        AuthMiddleware::require('admin');
        $body     = json_decode(file_get_contents('php://input'), true) ?? [];
        $name     = trim($body['name']     ?? '');
        $email    = trim($body['email']    ?? '');
        $password = trim($body['password'] ?? '');
        $role     = in_array($body['role'] ?? '', ['employee', 'admin']) ? $body['role'] : 'employee';

        if (!$name || !$email || !$password) {
            ResponseHelper::error('name, email, and password are required', 422);
        }
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            ResponseHelper::error('Invalid email address', 422);
        }
        if ($this->users->findByEmail($email)) {
            ResponseHelper::error('Email is already in use', 409);
        }

        $id   = $this->users->create($name, $email, password_hash($password, PASSWORD_BCRYPT), $role);
        $user = $this->users->findById($id);
        ResponseHelper::json($user, 201);
    }

    public function show(int $id): void
    {
        $auth = AuthMiddleware::require('client');
        if ($auth['role'] !== 'admin' && (int)$auth['sub'] !== $id) {
            ResponseHelper::error('Forbidden', 403);
        }
        $user = $this->users->findById($id);
        $user ? ResponseHelper::json($user) : ResponseHelper::error('User not found', 404);
    }

    public function update(int $id): void
    {
        $auth = AuthMiddleware::require('client');
        if ($auth['role'] !== 'admin' && (int)$auth['sub'] !== $id) {
            ResponseHelper::error('Forbidden', 403);
        }

        $body = json_decode(file_get_contents('php://input'), true) ?? [];

        if (isset($body['role']) && $auth['role'] !== 'admin') {
            ResponseHelper::error('Only admins can change roles', 403);
        }
        if (!empty($body['phone']) && !preg_match(self::EGYPT_PHONE_REGEX, $body['phone'])) {
            ResponseHelper::error('Phone must be a valid Egyptian mobile number (e.g. 01012345678)', 422);
        }

        $this->users->update($id, $body);
        ResponseHelper::json($this->users->findById($id));
    }

    public function destroy(int $id): void
    {
        AuthMiddleware::require('admin');
        $user = $this->users->findById($id);
        if (!$user) ResponseHelper::error('User not found', 404);
        $this->users->delete($id);
        ResponseHelper::json(['message' => 'User deleted']);
    }
}
