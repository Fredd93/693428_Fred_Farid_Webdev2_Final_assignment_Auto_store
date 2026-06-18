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
        $sql = 'SELECT o.*, c.brand, c.model, u.name as client_name FROM orders o JOIN cars c ON o.car_id = c.id JOIN users u ON o.user_id = u.id ORDER BY o.created_at DESC';
        return $this->paginate($sql, [], $page, $limit);
    }

    public function findById(int $id): ?array
    {
        $stmt = $this->db->prepare(
            'SELECT o.*, c.brand, c.model, u.name as client_name, u.email as client_email
             FROM orders o
             JOIN cars c ON o.car_id = c.id
             JOIN users u ON o.user_id = u.id
             WHERE o.id = :id'
        );
        $stmt->execute([':id' => $id]);
        return $stmt->fetch() ?: null;
    }

    public function create(array $data): int
    {
        $stmt = $this->db->prepare(
            'INSERT INTO orders (user_id, car_id, order_type, status, notes)
             VALUES (:user_id, :car_id, :order_type, :status, :notes)'
        );
        $stmt->execute($data);
        return (int) $this->db->lastInsertId();
    }

    public function updateStatus(int $id, string $status): bool
    {
        $stmt = $this->db->prepare('UPDATE orders SET status = :status WHERE id = :id');
        return $stmt->execute([':status' => $status, ':id' => $id]);
    }

    public function exportAll(): array
    {
        $stmt = $this->db->query(
            'SELECT o.id, u.name as client_name, u.email as client_email,
                    c.brand, c.model, c.year, c.price,
                    o.order_type, o.status, o.created_at
             FROM orders o
             JOIN cars  c ON o.car_id  = c.id
             JOIN users u ON o.user_id = u.id
             ORDER BY o.created_at DESC'
        );
        return $stmt->fetchAll();
    }
}
