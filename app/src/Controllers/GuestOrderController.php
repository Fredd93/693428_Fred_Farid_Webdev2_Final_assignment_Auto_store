<?php
namespace GTA\Controllers;

use GTA\Models\CarModel;
use GTA\Models\OrderModel;
use GTA\Models\VerificationCodeModel;
use GTA\Helpers\ResponseHelper;
use GTA\Helpers\MailHelper;
use GTA\Helpers\ValidationHelper;

class GuestOrderController
{
    private const PURPOSE = 'guest_order';

    // Egyptian mobile numbers: 01 + operator prefix (0,1,2,5) + 8 digits, optional +20 country code.
    private const EGYPT_PHONE_REGEX = '/^(?:\+20|0)1[0125][0-9]{8}$/';

    public function requestCode(): void
    {
        $body  = json_decode(file_get_contents('php://input'), true) ?? [];
        $email = trim($body['email'] ?? '');

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            ResponseHelper::error('Valid email is required', 422);
        }

        $result = (new VerificationCodeModel())->issue($email, self::PURPOSE);

        if (!$result['sent']) {
            ResponseHelper::error(
                "A code was already sent recently. Try again in {$result['retry_after']}s.",
                429
            );
        }

        MailHelper::sendGuestVerificationCode($email, $result['code']);
        ResponseHelper::json(['message' => 'Verification code sent']);
    }

    public function store(): void
    {
        $body = json_decode(file_get_contents('php://input'), true) ?? [];

        $carId = (int)($body['car_id']     ?? 0);
        $type  = $body['order_type']       ?? '';
        $name  = trim($body['name']        ?? '');
        $email = trim($body['email']       ?? '');
        $phone = trim($body['phone']       ?? '');
        $code  = trim($body['code']        ?? '');

        if (!$carId || !in_array($type, ['purchase', 'lease']) || !$name || !$email || !$phone || !$code) {
            ResponseHelper::error('car_id, order_type, name, email, phone, and code are required', 400);
        }
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            ResponseHelper::error('Invalid email address', 422);
        }
        if (!preg_match(self::EGYPT_PHONE_REGEX, $phone)) {
            ResponseHelper::error('Phone must be a valid Egyptian mobile number (e.g. 01012345678)', 422);
        }
        if (isset($body['down_payment']) && $body['down_payment'] !== '' && !ValidationHelper::isNonNegativeNumber($body['down_payment'])) {
            ResponseHelper::error('down_payment must be a non-negative number', 422);
        }
        if (isset($body['lease_years']) && $body['lease_years'] !== '' && !ValidationHelper::isIntInRange($body['lease_years'], 1, 120)) {
            ResponseHelper::error('lease_years must be a whole number between 1 and 120', 422);
        }

        $car = (new CarModel())->findById($carId);
        if (!$car) ResponseHelper::error('Car not found', 404);

        if (!(new VerificationCodeModel())->verify($email, $code, self::PURPOSE)) {
            ResponseHelper::error('Invalid or expired verification code', 400);
        }

        $orders = new OrderModel();
        $id = $orders->create([
            ':car_id'       => $carId,
            ':order_type'   => $type,
            ':status'       => 'pending',
            ':notes'        => $body['notes'] ?? '',
            ':down_payment' => isset($body['down_payment']) && $body['down_payment'] !== '' ? (float)$body['down_payment'] : null,
            ':lease_years'  => isset($body['lease_years'])  && $body['lease_years']  !== '' ? (int)$body['lease_years']    : null,
            ':guest_name'   => $name,
            ':guest_email'  => $email,
            ':guest_phone'  => $phone,
        ]);

        $order = $orders->findById($id);
        MailHelper::sendOrderConfirmation($email, $name, $order);

        ResponseHelper::json($order, 201);
    }
}
