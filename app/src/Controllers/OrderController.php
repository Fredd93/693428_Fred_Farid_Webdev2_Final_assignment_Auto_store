<?php
namespace GTA\Controllers;

use GTA\Models\OrderModel;
use GTA\Models\CarModel;
use GTA\Models\LeaseInstallmentModel;
use GTA\Middleware\AuthMiddleware;
use GTA\Helpers\ResponseHelper;
use GTA\Helpers\MailHelper;
use GTA\Helpers\ValidationHelper;

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

    public function myCars(): void
    {
        $auth = AuthMiddleware::require('client');
        ResponseHelper::json($this->orders->myCars((int)$auth['sub']));
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
        if (isset($body['down_payment']) && $body['down_payment'] !== '' && !ValidationHelper::isNonNegativeNumber($body['down_payment'])) {
            ResponseHelper::error('down_payment must be a non-negative number', 422);
        }
        if (isset($body['lease_years']) && $body['lease_years'] !== '' && !ValidationHelper::isIntInRange($body['lease_years'], 1, 120)) {
            ResponseHelper::error('lease_years must be a whole number between 1 and 120', 422);
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
        fputcsv($out, ['Order #', 'Client', 'Email', 'Phone', 'Car', 'Year', 'Price (€)', 'Type', 'Status', 'Sold By', 'Date'], ',', '"', '\\');
        foreach ($rows as $row) {
            fputcsv($out, [
                $row['id'],
                $row['client_name'],
                $row['client_email'],
                $row['client_phone'],
                $row['brand'] . ' ' . $row['model'],
                $row['year'],
                number_format($row['price'], 2, '.', ''),
                $row['order_type'],
                $row['status'],
                $row['sold_by_name'] ?? '',
                date('Y-m-d', strtotime($row['created_at'])),
            ], ',', '"', '\\');
        }
        fclose($out);
        exit;
    }

    public function update(int $id): void
    {
        $auth  = AuthMiddleware::require('employee');
        $order = $this->orders->findById($id);
        if (!$order) ResponseHelper::error('Order not found', 404);

        $body   = json_decode(file_get_contents('php://input'), true) ?? [];
        $status = $body['status'] ?? '';
        $allowed = ['pending','approved','denied','completed','counter_offer'];
        if (!in_array($status, $allowed)) ResponseHelper::error('Invalid status', 400);

        if ($status === 'completed' && $order['order_type'] === 'lease' && $order['status'] !== 'approved') {
            ResponseHelper::error('A lease must be approved before it can be marked completed', 422);
        }

        if (isset($body['final_price']) && $body['final_price'] !== '' && !ValidationHelper::isNonNegativeNumber($body['final_price'])) {
            ResponseHelper::error('final_price must be a non-negative number', 422);
        }
        if (isset($body['down_payment']) && $body['down_payment'] !== '' && !ValidationHelper::isNonNegativeNumber($body['down_payment'])) {
            ResponseHelper::error('down_payment must be a non-negative number', 422);
        }
        if (isset($body['lease_years']) && $body['lease_years'] !== '' && !ValidationHelper::isIntInRange($body['lease_years'], 1, 120)) {
            ResponseHelper::error('lease_years must be a whole number between 1 and 120', 422);
        }

        // Preserve the existing value when a field isn't included in this update — a plain
        // status change (e.g. approving an order) must not wipe out the client's original
        // down_payment/lease_years/final_price.
        $reason      = isset($body['reason']) ? trim($body['reason']) : null;
        $finalPrice  = (isset($body['final_price'])  && $body['final_price']  !== '') ? (float)$body['final_price']  : (isset($order['final_price'])  ? (float)$order['final_price']  : null);
        $downPayment = (isset($body['down_payment']) && $body['down_payment'] !== '') ? (float)$body['down_payment'] : (isset($order['down_payment']) ? (float)$order['down_payment'] : null);
        $leaseYears  = (isset($body['lease_years'])  && $body['lease_years']  !== '') ? (int)$body['lease_years']    : (isset($order['lease_years'])  ? (int)$order['lease_years']    : null);

        $wasAlreadyCompleted = $order['completed_at'] !== null;

        $this->orders->updateStatus($id, $status, $reason, $finalPrice, $downPayment, $leaseYears, (int)$auth['sub']);
        $updated = $this->orders->findById($id);

        if (!$wasAlreadyCompleted && $updated['status'] === 'completed') {
            (new CarModel())->update((int)$updated['car_id'], ['status' => 'sold']);

            if ($updated['order_type'] === 'lease') {
                $basePrice = $updated['final_price'] !== null ? (float)$updated['final_price'] : (float)$updated['car_price'];
                $downPaid  = (float)($updated['down_payment'] ?? 0);
                $termMonths = max(1, (int)($updated['lease_years'] ?? 36));
                $financedAmount = max(0, $basePrice - $downPaid);

                (new LeaseInstallmentModel())->generateForOrder(
                    $id,
                    $financedAmount,
                    $termMonths,
                    $updated['completed_at']
                );
            }
        }

        MailHelper::sendOrderStatusUpdate(
            $updated['client_email'] ?? '',
            $updated['client_name']  ?? '',
            $updated
        );

        ResponseHelper::json($updated);
    }
}
