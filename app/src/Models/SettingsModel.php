<?php
namespace GTA\Models;

class SettingsModel extends BaseModel
{
    public function get(string $key, string $default = ''): string
    {
        $stmt = $this->db->prepare('SELECT value FROM settings WHERE `key` = :key LIMIT 1');
        $stmt->execute([':key' => $key]);
        $row = $stmt->fetch();
        return $row ? $row['value'] : $default;
    }

    public function set(string $key, string $value): void
    {
        $stmt = $this->db->prepare(
            'INSERT INTO settings (`key`, value) VALUES (:key, :value)
             ON DUPLICATE KEY UPDATE value = :value2'
        );
        $stmt->execute([':key' => $key, ':value' => $value, ':value2' => $value]);
    }
}
