<?php
namespace GTA\Models;

class VerificationCodeModel extends BaseModel
{
    private const TTL_MINUTES      = 10;
    private const COOLDOWN_SECONDS = 60;

    public function issue(string $email, string $purpose = 'guest_order'): array
    {
        $stmt = $this->db->prepare(
            'SELECT created_at FROM verification_codes
             WHERE email = :email AND purpose = :purpose AND used_at IS NULL AND expires_at > NOW()
             ORDER BY created_at DESC LIMIT 1'
        );
        $stmt->execute([':email' => $email, ':purpose' => $purpose]);
        $existing = $stmt->fetch();

        if ($existing) {
            $secondsSince = time() - strtotime($existing['created_at']);
            if ($secondsSince < self::COOLDOWN_SECONDS) {
                return ['sent' => false, 'retry_after' => self::COOLDOWN_SECONDS - $secondsSince];
            }
        }

        $code = str_pad((string) random_int(0, 999999), 6, '0', STR_PAD_LEFT);
        $hash = password_hash($code, PASSWORD_BCRYPT);
        $expiresAt = date('Y-m-d H:i:s', time() + self::TTL_MINUTES * 60);

        $stmt = $this->db->prepare(
            'INSERT INTO verification_codes (email, code_hash, purpose, expires_at)
             VALUES (:email, :code_hash, :purpose, :expires_at)'
        );
        $stmt->execute([
            ':email'      => $email,
            ':code_hash'  => $hash,
            ':purpose'    => $purpose,
            ':expires_at' => $expiresAt,
        ]);

        return ['sent' => true, 'code' => $code];
    }

    public function verify(string $email, string $code, string $purpose = 'guest_order'): bool
    {
        $stmt = $this->db->prepare(
            'SELECT id, code_hash FROM verification_codes
             WHERE email = :email AND purpose = :purpose AND used_at IS NULL AND expires_at > NOW()
             ORDER BY created_at DESC LIMIT 1'
        );
        $stmt->execute([':email' => $email, ':purpose' => $purpose]);
        $row = $stmt->fetch();

        if (!$row || !password_verify($code, $row['code_hash'])) {
            return false;
        }

        $update = $this->db->prepare('UPDATE verification_codes SET used_at = NOW() WHERE id = :id');
        $update->execute([':id' => $row['id']]);

        return true;
    }
}
