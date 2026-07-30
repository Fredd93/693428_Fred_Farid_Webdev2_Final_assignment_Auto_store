<?php
namespace GTA\Models;

use PDO;

class StatsModel extends BaseModel
{
    public function pendingOrdersCount(): int
    {
        return (int) $this->db->query("SELECT COUNT(*) FROM orders WHERE status = 'pending'")->fetchColumn();
    }

    public function pendingAppointmentsCount(): int
    {
        return (int) $this->db->query("SELECT COUNT(*) FROM appointments WHERE status = 'pending'")->fetchColumn();
    }

    public function totalCars(): int
    {
        return (int) $this->db->query('SELECT COUNT(*) FROM cars')->fetchColumn();
    }

    public function carsSoldCount(): int
    {
        return (int) $this->db->query("SELECT COUNT(*) FROM cars WHERE status = 'sold'")->fetchColumn();
    }

    public function totalDealValue(): float
    {
        $stmt = $this->db->query(
            "SELECT COALESCE(SUM(COALESCE(o.final_price, c.price)), 0) as total
             FROM orders o JOIN cars c ON o.car_id = c.id
             WHERE o.status = 'completed'"
        );
        return (float) $stmt->fetchColumn();
    }

    public function usersByRoleCount(string $role): int
    {
        $stmt = $this->db->prepare('SELECT COUNT(*) FROM users WHERE role = :role');
        $stmt->execute([':role' => $role]);
        return (int) $stmt->fetchColumn();
    }

    public function staffCount(): int
    {
        return (int) $this->db->query("SELECT COUNT(*) FROM users WHERE role IN ('employee','admin')")->fetchColumn();
    }

    public function myCompletedSalesCount(int $userId): int
    {
        $stmt = $this->db->prepare(
            "SELECT COUNT(*) FROM orders WHERE completed_by = :user_id AND status = 'completed'"
        );
        $stmt->execute([':user_id' => $userId]);
        return (int) $stmt->fetchColumn();
    }

    public function topSalesmenThisMonth(): array
    {
        $stmt = $this->db->query(
            "SELECT u.id as employee_id, u.name, COUNT(o.id) as sales_count
             FROM users u
             LEFT JOIN orders o ON o.completed_by = u.id
                 AND o.status = 'completed'
                 AND MONTH(o.completed_at) = MONTH(CURDATE())
                 AND YEAR(o.completed_at) = YEAR(CURDATE())
             WHERE u.role = 'employee'
             GROUP BY u.id, u.name
             ORDER BY sales_count DESC, u.name ASC"
        );
        return $stmt->fetchAll();
    }

    public function topCarsThisMonth(int $limit = 3): array
    {
        $stmt = $this->db->prepare(
            "SELECT c.id as car_id, c.brand, c.model, c.image_path, COUNT(*) as units_sold
             FROM orders o
             JOIN cars c ON o.car_id = c.id
             WHERE o.status = 'completed'
               AND MONTH(o.completed_at) = MONTH(CURDATE())
               AND YEAR(o.completed_at) = YEAR(CURDATE())
             GROUP BY c.id, c.brand, c.model, c.image_path
             ORDER BY units_sold DESC
             LIMIT :limit"
        );
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }
}
