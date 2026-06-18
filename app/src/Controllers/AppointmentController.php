<?php
namespace GTA\Controllers;

use GTA\Models\AppointmentModel;
use GTA\Models\CarModel;
use GTA\Middleware\AuthMiddleware;
use GTA\Helpers\ResponseHelper;
use GTA\Helpers\MailHelper;

class AppointmentController
{
    private AppointmentModel $appointments;

    public function __construct() { $this->appointments = new AppointmentModel(); }

    public function index(): void
    {
        $auth  = AuthMiddleware::require('client');
        $page  = max(1, (int)($_GET['page']  ?? 1));
        $limit = min(50, max(1, (int)($_GET['limit'] ?? 15)));

        $email = in_array($auth['role'], ['employee', 'admin']) ? null : ($auth['email'] ?? '');
        ResponseHelper::json($this->appointments->listPaginated($page, $limit, $email));
    }

    public function store(): void
    {
        $auth = AuthMiddleware::require('client');
        $body = json_decode(file_get_contents('php://input'), true) ?? [];

        $carId   = (int)($body['car_id']           ?? 0);
        $phone   = trim($body['client_phone']       ?? '');
        $date    = trim($body['appointment_date']   ?? '');

        if (!$carId || !$date) {
            ResponseHelper::error('car_id and appointment_date are required', 400);
        }

        $car = (new CarModel())->findById($carId);
        if (!$car) ResponseHelper::error('Car not found', 404);

        $data = [
            ':car_id'           => $carId,
            ':client_name'      => $auth['name']  ?? '',
            ':client_email'     => $auth['email'] ?? '',
            ':client_phone'     => $phone,
            ':appointment_date' => $date,
        ];

        $id          = $this->appointments->create($data);
        $appointment = $this->appointments->findById($id);

        MailHelper::sendAppointmentConfirmation(
            $auth['email'] ?? '',
            $auth['name']  ?? '',
            $appointment
        );

        ResponseHelper::json($appointment, 201);
    }

    public function update(int $id): void
    {
        $auth        = AuthMiddleware::require('employee');
        $appointment = $this->appointments->findById($id);
        if (!$appointment) ResponseHelper::error('Appointment not found', 404);

        $body    = json_decode(file_get_contents('php://input'), true) ?? [];
        $status  = $body['status'] ?? '';
        $allowed = ['pending', 'confirmed', 'cancelled'];
        if (!in_array($status, $allowed)) ResponseHelper::error('Invalid status', 400);

        $this->appointments->updateStatus($id, $status, (int)$auth['sub']);
        $updated = $this->appointments->findById($id);

        MailHelper::sendAppointmentStatusUpdate(
            $appointment['client_email'] ?? '',
            $appointment['client_name']  ?? '',
            $updated
        );

        ResponseHelper::json($updated);
    }

    public function destroy(int $id): void
    {
        $auth        = AuthMiddleware::require('client');
        $appointment = $this->appointments->findById($id);
        if (!$appointment) ResponseHelper::error('Appointment not found', 404);

        if ($auth['role'] === 'client' && $appointment['client_email'] !== ($auth['email'] ?? '')) {
            ResponseHelper::error('Forbidden', 403);
        }

        $this->appointments->delete($id);
        ResponseHelper::json(['message' => 'Appointment cancelled']);
    }
}
