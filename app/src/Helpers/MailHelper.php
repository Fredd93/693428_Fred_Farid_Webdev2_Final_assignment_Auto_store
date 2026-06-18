<?php
namespace GTA\Helpers;

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

class MailHelper
{
    private static function mailer(): PHPMailer
    {
        $mail = new PHPMailer(true);
        $mail->isSMTP();
        $mail->Host     = $_ENV['MAIL_HOST'] ?? getenv('MAIL_HOST') ?: 'mailhog';
        $mail->Port     = (int)($_ENV['MAIL_PORT'] ?? getenv('MAIL_PORT') ?: 1025);
        $mail->CharSet  = 'UTF-8';

        $user = $_ENV['MAIL_USER'] ?? getenv('MAIL_USER') ?: '';
        $pass = $_ENV['MAIL_PASS'] ?? getenv('MAIL_PASS') ?: '';

        if ($user && $pass) {
            $mail->SMTPAuth   = true;
            $mail->Username   = $user;
            $mail->Password   = $pass;
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        } else {
            $mail->SMTPAuth = false;
        }

        $from     = $_ENV['MAIL_FROM']      ?? getenv('MAIL_FROM')      ?: 'noreply@grandtransmissionauto.com';
        $fromName = $_ENV['MAIL_FROM_NAME'] ?? getenv('MAIL_FROM_NAME') ?: 'Grand Transmission Auto';
        $mail->setFrom($from, $fromName);

        return $mail;
    }

    public static function sendWelcome(string $toEmail, string $toName): void
    {
        try {
            $mail = self::mailer();
            $mail->addAddress($toEmail, $toName);
            $mail->isHTML(true);
            $mail->Subject = 'Welcome to Grand Transmission Auto';
            $mail->Body    = "
                <h2>Welcome, {$toName}!</h2>
                <p>Your account has been created successfully at <strong>Grand Transmission Auto</strong>.</p>
                <p>You can now browse our inventory and place purchase or lease requests.</p>
                <p>Visit us at <a href='http://localhost'>grandtransmissionauto.com</a></p>
            ";
            $mail->send();
        } catch (Exception $e) {
            // Mail failure is non-fatal — log and continue
            error_log('MailHelper::sendWelcome failed: ' . $e->getMessage());
        }
    }

    public static function sendOrderConfirmation(string $toEmail, string $toName, array $order): void
    {
        try {
            $mail = self::mailer();
            $mail->addAddress($toEmail, $toName);
            $mail->isHTML(true);
            $type          = ucfirst($order['order_type']);
            $mail->Subject = "Order #{$order['id']} Received — {$order['brand']} {$order['model']}";
            $mail->Body    = "
                <h2>Order Confirmation</h2>
                <p>Hi {$toName}, we've received your {$type} request.</p>
                <table cellpadding='8' style='border-collapse:collapse'>
                    <tr><td><strong>Order #</strong></td><td>{$order['id']}</td></tr>
                    <tr><td><strong>Vehicle</strong></td><td>{$order['brand']} {$order['model']}</td></tr>
                    <tr><td><strong>Type</strong></td><td>{$type}</td></tr>
                    <tr><td><strong>Status</strong></td><td>Pending</td></tr>
                </table>
                <p>Our team will review your request and get back to you shortly.</p>
            ";
            $mail->send();
        } catch (Exception $e) {
            error_log('MailHelper::sendOrderConfirmation failed: ' . $e->getMessage());
        }
    }

    public static function sendAppointmentConfirmation(string $toEmail, string $toName, array $appt): void
    {
        try {
            $mail = self::mailer();
            $mail->addAddress($toEmail, $toName);
            $mail->isHTML(true);
            $mail->Subject = "Test Drive Booked — {$appt['brand']} {$appt['model']}";
            $date = date('D, d M Y H:i', strtotime($appt['appointment_date']));
            $mail->Body    = "
                <h2>Test Drive Confirmed</h2>
                <p>Hi {$toName}, your test drive appointment has been received.</p>
                <table cellpadding='8' style='border-collapse:collapse'>
                    <tr><td><strong>Vehicle</strong></td><td>{$appt['brand']} {$appt['model']}</td></tr>
                    <tr><td><strong>Date &amp; Time</strong></td><td>{$date}</td></tr>
                    <tr><td><strong>Status</strong></td><td>Pending confirmation</td></tr>
                </table>
                <p>We will confirm your appointment shortly.</p>
            ";
            $mail->send();
        } catch (\Exception $e) {
            error_log('MailHelper::sendAppointmentConfirmation failed: ' . $e->getMessage());
        }
    }

    public static function sendAppointmentStatusUpdate(string $toEmail, string $toName, array $appt): void
    {
        try {
            $mail = self::mailer();
            $mail->addAddress($toEmail, $toName);
            $mail->isHTML(true);
            $status = ucfirst($appt['status']);
            $date   = date('D, d M Y H:i', strtotime($appt['appointment_date']));
            $mail->Subject = "Appointment Update — {$status}";
            $mail->Body    = "
                <h2>Appointment Status Update</h2>
                <p>Hi {$toName}, your test drive appointment has been updated.</p>
                <table cellpadding='8' style='border-collapse:collapse'>
                    <tr><td><strong>Vehicle</strong></td><td>{$appt['brand']} {$appt['model']}</td></tr>
                    <tr><td><strong>Date &amp; Time</strong></td><td>{$date}</td></tr>
                    <tr><td><strong>New Status</strong></td><td><strong>{$status}</strong></td></tr>
                </table>
            ";
            $mail->send();
        } catch (\Exception $e) {
            error_log('MailHelper::sendAppointmentStatusUpdate failed: ' . $e->getMessage());
        }
    }

    public static function sendOrderStatusUpdate(string $toEmail, string $toName, array $order): void
    {
        try {
            $mail = self::mailer();
            $mail->addAddress($toEmail, $toName);
            $mail->isHTML(true);
            $status        = ucfirst($order['status']);
            $type          = ucfirst($order['order_type']);
            $mail->Subject = "Order #{$order['id']} Update — {$status}";
            $mail->Body    = "
                <h2>Order Status Update</h2>
                <p>Hi {$toName}, your order status has been updated.</p>
                <table cellpadding='8' style='border-collapse:collapse'>
                    <tr><td><strong>Order #</strong></td><td>{$order['id']}</td></tr>
                    <tr><td><strong>Vehicle</strong></td><td>{$order['brand']} {$order['model']}</td></tr>
                    <tr><td><strong>Type</strong></td><td>{$type}</td></tr>
                    <tr><td><strong>New Status</strong></td><td><strong>{$status}</strong></td></tr>
                </table>
                <p>If you have any questions, please contact us.</p>
            ";
            $mail->send();
        } catch (Exception $e) {
            error_log('MailHelper::sendOrderStatusUpdate failed: ' . $e->getMessage());
        }
    }
}
