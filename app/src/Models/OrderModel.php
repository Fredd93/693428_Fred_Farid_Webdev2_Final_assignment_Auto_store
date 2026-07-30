<?php
namespace GTA\Models;

class OrderModel extends BaseModel
{
    public function listPaginated(int $page, int $limit, ?int $userId = null): array
    {
        if ($userId !== null) {
            $sql = 'SELECT o.*, c.brand, c.model FROM orders o JOIN cars c ON o.car_id = c.id WHERE o.user_id = :user_id ORDER BY o.created_at DESC';
            return $this->paginate($sql, [':user_id' => $userId], $page, $limit);
        }
        $sql = "SELECT o.*, c.brand, c.model,
                       COALESCE(u.name,  o.guest_name)  as client_name,
                       COALESCE(u.email, o.guest_email) as client_email,
                       COALESCE(u.phone, o.guest_phone) as client_phone,
                       su.name as sold_by_name
                FROM orders o
                JOIN cars c ON o.car_id = c.id
                LEFT JOIN users u ON o.user_id = u.id
                LEFT JOIN users su ON o.completed_by = su.id
                ORDER BY o.created_at DESC";
        return $this->paginate($sql, [], $page, $limit);
    }

    public function myCars(int $userId): array
    {
        $stmt = $this->db->prepare(
            "SELECT o.*, c.brand, c.model, c.year, c.image_path, c.price
             FROM orders o
             JOIN cars c ON o.car_id = c.id
             WHERE o.user_id = :user_id AND o.status = 'completed'
             ORDER BY o.completed_at DESC"
        );
        $stmt->execute([':user_id' => $userId]);
        return $stmt->fetchAll();
    }

    public function findById(int $id): ?array
    {
        $stmt = $this->db->prepare(
            'SELECT o.*, c.brand, c.model, c.price as car_price,
                    COALESCE(u.name,  o.guest_name)  as client_name,
                    COALESCE(u.email, o.guest_email) as client_email,
                    COALESCE(u.phone, o.guest_phone) as client_phone,
                    su.name as sold_by_name
             FROM orders o
             JOIN cars c ON o.car_id = c.id
             LEFT JOIN users u ON o.user_id = u.id
             LEFT JOIN users su ON o.completed_by = su.id
             WHERE o.id = :id'
        );
        $stmt->execute([':id' => $id]);
        return $stmt->fetch() ?: null;
    }

    public function create(array $data): int
    {
        $stmt = $this->db->prepare(
            'INSERT INTO orders (user_id, car_id, order_type, status, notes, down_payment, lease_years, guest_name, guest_email, guest_phone)
             VALUES (:user_id, :car_id, :order_type, :status, :notes, :down_payment, :lease_years, :guest_name, :guest_email, :guest_phone)'
        );
        $stmt->execute(array_merge([
            ':user_id'     => null,
            ':guest_name'  => null,
            ':guest_email' => null,
            ':guest_phone' => null,
        ], $data));
        return (int) $this->db->lastInsertId();
    }

    public function updateStatus(int $id, string $status, ?string $reason, ?float $finalPrice, ?float $downPayment, ?int $leaseYears, ?int $completedBy = null): bool
    {
        $stmt = $this->db->prepare(
            'UPDATE orders SET status = :status, reason = :reason, final_price = :final_price,
             down_payment = :down_payment, lease_years = :lease_years,
             completed_at = CASE WHEN :status2 = "completed" AND completed_at IS NULL THEN NOW() ELSE completed_at END,
             completed_by = CASE WHEN :status3 = "completed" AND completed_by IS NULL THEN :completed_by ELSE completed_by END
             WHERE id = :id'
        );
        return $stmt->execute([
            ':status'       => $status,
            ':status2'      => $status,
            ':status3'      => $status,
            ':reason'       => $reason,
            ':final_price'  => $finalPrice,
            ':down_payment' => $downPayment,
            ':lease_years'  => $leaseYears,
            ':completed_by' => $completedBy,
            ':id'           => $id,
        ]);
    }

    public function exportAll(): array
    {
        $stmt = $this->db->query(
            "SELECT o.id,
                    COALESCE(u.name,  o.guest_name)  as client_name,
                    COALESCE(u.email, o.guest_email) as client_email,
                    COALESCE(u.phone, o.guest_phone) as client_phone,
                    su.name as sold_by_name,
                    c.brand, c.model, c.year, c.price,
                    o.order_type, o.status, o.created_at
             FROM orders o
             JOIN cars  c ON o.car_id  = c.id
             LEFT JOIN users u ON o.user_id = u.id
             LEFT JOIN users su ON o.completed_by = su.id
             ORDER BY o.created_at DESC"
        );
        return $stmt->fetchAll();
    }
}
