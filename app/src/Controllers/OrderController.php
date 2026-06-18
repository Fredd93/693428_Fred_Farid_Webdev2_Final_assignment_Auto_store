<?php
namespace GTA\Controllers;

use GTA\Models\OrderModel;
use GTA\Middleware\AuthMiddleware;
use GTA\Helpers\ResponseHelper;
use GTA\Helpers\MailHelper;

class OrderController
{
    private OrderModel $orders;

    public function __construct() { $this->orders = new OrderModel(); }

    public function index(): void
    {
        $auth   = AuthMiddleware::require('client');
        $page   = max(1, (int)($_GET['page']  ?? 1));
        $limit  = min(50, max(1, (int)($_GET['limit'] ?? 15)));
        $userId = in_array($auth['role'], ['employee','admin']) ? null : (int)$auth['sub'];
        ResponseHelper::json($this->orders->listPaginated($page, $limit, $userId));
    }

    public function show(int $id): void
    {
        $auth  = AuthMiddleware::require('client');
        $order = $this->orders->findById($id);
        if (!$order) ResponseHelper::error('Order not found', 404);

        if ($auth['role'] === 'client' && (int)$order['user_id'] !== (int)$auth['sub']) {
            ResponseHelper::error('Forbidden', 403);
        }
        ResponseHelper::json($order);
    }

    public function store(): void
    {
        $auth = AuthMiddleware::require('client');
        $body = json_decode(file_get_contents('php://input'), true) ?? [];

        if (empty($body['car_id']) || empty($body['order_type'])) {
            ResponseHelper::error('car_id and order_type are required', 400);
        }

        $data = [
            ':user_id'     => (int)$auth['sub'],
            ':car_id'      => (int)$body['car_id'],
            ':order_type'  => $body['order_type'],
            ':status'      => 'pending',
            ':notes'       => $body['notes']        ?? '',
            ':down_payment'=> isset($body['down_payment']) ? (float)$body['down_payment'] : null,
            ':lease_years' => isset($body['lease_years'])  ? (int)$body['lease_years']    : null,
        ];

        $id    = $this->orders->create($data);
        $order = $this->orders->findById($id);

        MailHelper::sendOrderConfirmation(
            $order['client_email'] ?? '',
            $order['client_name']  ?? '',
            $order
        );

        ResponseHelper::json($order, 201);
    }

    public function export(): void
    {
        AuthMiddleware::require('admin');

        $rows = $this->orders->exportAll();

        header('Content-Type: text/csv; charset=UTF-8');
        header('Content-Disposition: attachment; filename="orders_' . date('Y-m-d') . '.csv"');
        header('Cache-Control: no-cache');

        $out = fopen('php://output', 'w');
        fputcsv($out, ['Order #', 'Client', 'Email', 'Car', 'Year', 'Price (€)', 'Type', 'Status', 'Date'], ',', '"', '\\');
        foreach ($rows as $row) {
            fputcsv($out, [
                $row['id'],
                $row['client_name'],
                $row['client_email'],
                $row['brand'] . ' ' . $row['model'],
                $row['year'],
                number_format($row['price'], 2, '.', ''),
                $row['order_type'],
                $row['status'],
                date('Y-m-d', strtotime($row['created_at'])),
            ], ',', '"', '\\');
        }
        fclose($out);
        exit;
    }

    public function update(int $id): void
    {
        AuthMiddleware::require('employee');
        $order = $this->orders->findById($id);
        if (!$order) ResponseHelper::error('Order not found', 404);

        $body   = json_decode(file_get_contents('php://input'), true) ?? [];
        $status = $body['status'] ?? '';
        $allowed = ['pending','approved','denied','completed'];
        if (!in_array($status, $allowed)) ResponseHelper::error('Invalid status', 400);

        $reason      = isset($body['reason'])       ? trim($body['reason'])          : null;
        $finalPrice  = isset($body['final_price'])  ? (float)$body['final_price']    : null;
        $downPayment = isset($body['down_payment']) ? (float)$body['down_payment']   : null;
        $leaseYears  = isset($body['lease_years'])  ? (int)$body['lease_years']      : null;

        $this->orders->updateStatus($id, $status, $reason, $finalPrice, $downPayment, $leaseYears);
        $updated = $this->orders->findById($id);

        MailHelper::sendOrderStatusUpdate(
            $updated['client_email'] ?? '',
            $updated['client_name']  ?? '',
            $updated
        );

        ResponseHelper::json($updated);
    }
}
