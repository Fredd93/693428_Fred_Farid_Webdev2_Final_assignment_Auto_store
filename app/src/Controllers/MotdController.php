<?php
namespace GTA\Controllers;

use GTA\Models\SettingsModel;
use GTA\Middleware\AuthMiddleware;
use GTA\Helpers\ResponseHelper;

class MotdController
{
    private SettingsModel $settings;

    public function __construct() { $this->settings = new SettingsModel(); }

    public function show(): void
    {
        ResponseHelper::json(['message' => $this->settings->get('motd', '')]);
    }

    public function update(): void
    {
        AuthMiddleware::require('employee');
        $body = json_decode(file_get_contents('php://input'), true) ?? [];
        $msg  = trim($body['message'] ?? '');
        $this->settings->set('motd', $msg);
        ResponseHelper::json(['message' => $msg]);
    }
}
