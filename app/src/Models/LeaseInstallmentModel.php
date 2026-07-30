<?php
namespace GTA\Models;

class LeaseInstallmentModel extends BaseModel
{
    public function generateForOrder(int $orderId, float $totalAmount, int $termMonths, string $startDate): void
    {
        $check = $this->db->prepare('SELECT COUNT(*) FROM lease_installments WHERE order_id = :order_id');
        $check->execute([':order_id' => $orderId]);
        if ((int) $check->fetchColumn() > 0) return;

        $termMonths     = max(1, $termMonths);
        $totalAmount    = max(0, $totalAmount);
        $perInstallment = round($totalAmount / $termMonths, 2);

        $insert = $this->db->prepare(
            'INSERT INTO lease_installments (order_id, installment_no, due_date, amount)
             VALUES (:order_id, :installment_no, :due_date, :amount)'
        );

        $runningTotal = 0;
        for ($i = 1; $i <= $termMonths; $i++) {
            $dueDate = date('Y-m-d', strtotime("$startDate +{$i} month"));
            $amount  = $i === $termMonths ? round($totalAmount - $runningTotal, 2) : $perInstallment;
            $runningTotal += $amount;

            $insert->execute([
                ':order_id'       => $orderId,
                ':installment_no' => $i,
                ':due_date'       => $dueDate,
                ':amount'         => $amount,
            ]);
        }
    }

    public function listForOrder(int $orderId): array
    {
        $stmt = $this->db->prepare(
            'SELECT * FROM lease_installments WHERE order_id = :order_id ORDER BY installment_no ASC'
        );
        $stmt->execute([':order_id' => $orderId]);
        return $stmt->fetchAll();
    }

    public function findById(int $id): ?array
    {
        $stmt = $this->db->prepare('SELECT * FROM lease_installments WHERE id = :id');
        $stmt->execute([':id' => $id]);
        return $stmt->fetch() ?: null;
    }

    public function markPaid(int $id): bool
    {
        $stmt = $this->db->prepare(
            "UPDATE lease_installments SET status = 'paid', paid_at = NOW() WHERE id = :id AND status = 'due'"
        );
        return $stmt->execute([':id' => $id]);
    }
}
