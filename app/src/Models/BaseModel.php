<?php
namespace GTA\Models;

use PDO;

class BaseModel
{
    protected PDO $db;

    public function __construct()
    {
        $host     = $_ENV['DB_HOST']     ?? getenv('DB_HOST');
        $name     = $_ENV['DB_NAME']     ?? getenv('DB_NAME');
        $user     = $_ENV['DB_USER']     ?? getenv('DB_USER');
        $pass     = $_ENV['DB_PASSWORD'] ?? getenv('DB_PASSWORD');
        $charset  = $_ENV['DB_CHARSET']  ?? getenv('DB_CHARSET') ?: 'utf8mb4';

        $dsn = "mysql:host=$host;dbname=$name;charset=$charset";

        $this->db = new PDO($dsn, $user, $pass, [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        ]);
    }

    protected function paginate(string $sql, array $params, int $page, int $limit): array
    {
        $countSql = "SELECT COUNT(*) as total FROM ($sql) as sub";
        $stmt = $this->db->prepare($countSql);
        $stmt->execute($params);
        $total = (int) $stmt->fetchColumn();

        $offset = ($page - 1) * $limit;
        $stmt = $this->db->prepare("$sql LIMIT :limit OFFSET :offset");
        foreach ($params as $k => $v) {
            $stmt->bindValue($k, $v);
        }
        $stmt->bindValue(':limit',  $limit,  PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();

        return [
            'data' => $stmt->fetchAll(),
            'meta' => [
                'total' => $total,
                'page'  => $page,
                'limit' => $limit,
                'pages' => (int) ceil($total / $limit),
            ],
        ];
    }
}
