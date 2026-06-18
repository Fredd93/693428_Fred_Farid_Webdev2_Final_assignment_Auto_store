<?php
namespace GTA\Models;

use PDO;

class CarModel extends BaseModel
{
    public function listPaginated(array $filters, int $page, int $limit): array
    {
        $where = ['1=1'];
        $params = [];

        if (!empty($filters['brand'])) {
            $where[] = 'brand = :brand';
            $params[':brand'] = $filters['brand'];
        }
        if (!empty($filters['year'])) {
            $where[] = 'year = :year';
            $params[':year'] = $filters['year'];
        }
        if (!empty($filters['transmission'])) {
            $where[] = 'transmission = :transmission';
            $params[':transmission'] = $filters['transmission'];
        }
        if (isset($filters['min_price'])) {
            $where[] = 'price >= :min_price';
            $params[':min_price'] = $filters['min_price'];
        }
        if (isset($filters['max_price'])) {
            $where[] = 'price <= :max_price';
            $params[':max_price'] = $filters['max_price'];
        }
        if (isset($filters['on_sale'])) {
            $where[] = 'on_sale = :on_sale';
            $params[':on_sale'] = $filters['on_sale'];
        }

        $sql = 'SELECT * FROM cars WHERE ' . implode(' AND ', $where) . ' ORDER BY id DESC';
        return $this->paginate($sql, $params, $page, $limit);
    }

    public function findById(int $id): ?array
    {
        $stmt = $this->db->prepare('SELECT * FROM cars WHERE id = :id');
        $stmt->execute([':id' => $id]);
        return $stmt->fetch() ?: null;
    }

    public function create(array $data): int
    {
        $stmt = $this->db->prepare('
            INSERT INTO cars (brand, model, year, transmission, engine_spec, car_condition,
                description, color, price, on_sale, discount, lease_available, lease_terms,
                status, image_path)
            VALUES (:brand, :model, :year, :transmission, :engine_spec, :car_condition,
                :description, :color, :price, :on_sale, :discount, :lease_available, :lease_terms,
                :status, :image_path)
        ');
        $stmt->execute($data);
        return (int) $this->db->lastInsertId();
    }

    public function update(int $id, array $data): bool
    {
        $allowed = ['brand','model','year','transmission','engine_spec','car_condition',
                    'description','color','price','on_sale','discount','lease_available',
                    'lease_terms','status','image_path'];
        $sets = [];
        $params = [':id' => $id];
        foreach ($allowed as $field) {
            if (array_key_exists($field, $data)) {
                $sets[] = "$field = :$field";
                $params[":$field"] = $data[$field];
            }
        }
        if (empty($sets)) return false;
        $stmt = $this->db->prepare('UPDATE cars SET ' . implode(', ', $sets) . ' WHERE id = :id');
        return $stmt->execute($params);
    }

    public function delete(int $id): bool
    {
        $stmt = $this->db->prepare('DELETE FROM cars WHERE id = :id');
        return $stmt->execute([':id' => $id]);
    }

    public function getFilterOptions(): array
    {
        return [
            'brands'        => $this->db->query('SELECT DISTINCT brand FROM cars ORDER BY brand')->fetchAll(PDO::FETCH_COLUMN),
            'years'         => $this->db->query('SELECT DISTINCT year FROM cars ORDER BY year DESC')->fetchAll(PDO::FETCH_COLUMN),
            'transmissions' => $this->db->query('SELECT DISTINCT transmission FROM cars')->fetchAll(PDO::FETCH_COLUMN),
            'price_bounds'  => $this->db->query('SELECT MIN(price) as min, MAX(price) as max FROM cars')->fetch(),
        ];
    }
}
