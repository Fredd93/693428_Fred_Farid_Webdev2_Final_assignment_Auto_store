<?php
namespace GTA\Controllers;

use GTA\Models\OrderModel;
use GTA\Models\LeaseInstallmentModel;
use GTA\Middleware\AuthMiddleware;
use GTA\Helpers\ResponseHelper;

class LeaseInstallmentController
{
    public function index(int $orderId): void
    {
        $auth  = AuthMiddleware::require('client');
        $order = (new OrderModel())->findById($orderId);
        if (!$order) ResponseHelper::error('Order not found', 404);

        if ($auth['role'] === 'client' && (int)$order['user_id'] !== (int)$auth['sub']) {
            ResponseHelper::error('Forbidden', 403);
        }

        ResponseHelper::json((new LeaseInstallmentModel())->listForOrder($orderId));
    }

    public function pay(int $id): void
    {
        $auth         = AuthMiddleware::require('client');
        $installments = new LeaseInstallmentModel();
        $installment  = $installments->findById($id);
        if (!$installment) ResponseHelper::error('Installment not found', 404);

        $order = (new OrderModel())->findById((int)$installment['order_id']);
        if ($auth['role'] === 'client' && (int)$order['user_id'] !== (int)$auth['sub']) {
            ResponseHelper::error('Forbidden', 403);
        }
        if ($installment['status'] === 'paid') {
            ResponseHelper::error('Installment already paid', 400);
        }

        $installments->markPaid($id);
        ResponseHelper::json($installments->findById($id));
    }
}
