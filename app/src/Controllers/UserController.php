<?php
namespace GTA\Controllers;

use GTA\Models\UserModel;
use GTA\Middleware\AuthMiddleware;
use GTA\Helpers\ResponseHelper;

class UserController
{
    private UserModel $users;

    public function __construct() { $this->users = new UserModel(); }

    public function index(): void
    {
        AuthMiddleware::require('admin');
        $page  = max(1, (int)($_GET['page']  ?? 1));
        $limit = min(50, max(1, (int)($_GET['limit'] ?? 15)));
        ResponseHelper::json($this->users->listPaginated($page, $limit));
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
