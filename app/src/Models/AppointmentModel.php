<?php
namespace GTA\Models;

class AppointmentModel extends BaseModel
{
    public function listPaginated(int $page, int $limit, ?string $clientEmail = null): array
    {
        if ($clientEmail !== null) {
            $sql = 'SELECT a.*, c.brand, c.model
                    FROM appointments a
                    JOIN cars c ON a.car_id = c.id
                    WHERE a.client_email = :email
                    ORDER BY a.appointment_date DESC';
            return $this->paginate($sql, [':email' => $clientEmail], $page, $limit);
        }

        $sql = 'SELECT a.*, c.brand, c.model
                FROM appointments a
                JOIN cars c ON a.car_id = c.id
                ORDER BY a.appointment_date DESC';
        return $this->paginate($sql, [], $page, $limit);
    }

    public function findById(int $id): ?array
    {
        $stmt = $this->db->prepare(
            'SELECT a.*, c.brand, c.model
             FROM appointments a
             JOIN cars c ON a.car_id = c.id
             WHERE a.appointment_id = :id'
        );
        $stmt->execute([':id' => $id]);
        return $stmt->fetch() ?: null;
    }

    public function create(array $data): int
    {
        $stmt = $this->db->prepare(
            'INSERT INTO appointments (car_id, client_name, client_email, client_phone, appointment_date)
             VALUES (:car_id, :client_name, :client_email, :client_phone, :appointment_date)'
        );
        $stmt->execute($data);
        return (int) $this->db->lastInsertId();
    }

    public function updateStatus(int $id, string $status, ?int $employeeId = null): bool
    {
        $stmt = $this->db->prepare(
            'UPDATE appointments SET status = :status, employee_id = :employee_id WHERE appointment_id = :id'
        );
        return $stmt->execute([':status' => $status, ':employee_id' => $employeeId, ':id' => $id]);
    }

    public function delete(int $id): bool
    {
        $stmt = $this->db->prepare('DELETE FROM appointments WHERE appointment_id = :id');
        return $stmt->execute([':id' => $id]);
    }
}
