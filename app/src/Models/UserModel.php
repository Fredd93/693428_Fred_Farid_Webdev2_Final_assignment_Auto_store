<?php
namespace GTA\Models;

class UserModel extends BaseModel
{
    public function findByEmail(string $email): ?array
    {
        $stmt = $this->db->prepare('SELECT * FROM users WHERE email = :email LIMIT 1');
        $stmt->execute([':email' => $email]);
        return $stmt->fetch() ?: null;
    }

    public function findById(int $id): ?array
    {
        $stmt = $this->db->prepare('SELECT id, name, email, role, created_at FROM users WHERE id = :id');
        $stmt->execute([':id' => $id]);
        return $stmt->fetch() ?: null;
    }

    public function create(string $name, string $email, string $passwordHash, string $role = 'client'): int
    {
        $stmt = $this->db->prepare(
            'INSERT INTO users (name, email, password, role) VALUES (:name, :email, :password, :role)'
        );
        $stmt->execute([':name' => $name, ':email' => $email, ':password' => $passwordHash, ':role' => $role]);
        return (int) $this->db->lastInsertId();
    }

    public function listPaginated(int $page, int $limit): array
    {
        return $this->paginate(
            'SELECT id, name, email, role, created_at FROM users ORDER BY created_at DESC',
            [],
            $page,
            $limit
        );
    }

    public function update(int $id, array $data): bool
    {
        $allowed = ['name', 'email', 'role'];
        $sets = [];
        $params = [':id' => $id];
        foreach ($allowed as $field) {
            if (isset($data[$field])) {
                $sets[] = "$field = :$field";
                $params[":$field"] = $data[$field];
            }
        }
        if (empty($sets)) return false;
        $stmt = $this->db->prepare('UPDATE users SET ' . implode(', ', $sets) . ' WHERE id = :id');
        return $stmt->execute($params);
    }

    public function delete(int $id): bool
    {
        $stmt = $this->db->prepare('DELETE FROM users WHERE id = :id');
        return $stmt->execute([':id' => $id]);
    }
}
