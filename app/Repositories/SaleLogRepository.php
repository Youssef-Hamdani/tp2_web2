<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Core\Database;
use PDO;

final class SaleLogRepository
{
    private PDO $pdo;

    public function __construct(?PDO $pdo = null)
    {
        $this->pdo = $pdo ?? Database::connection();
    }

    public function create(int $orderId, array $payload): void
    {
        $statement = $this->pdo->prepare(
            'INSERT INTO sales_logs (order_id, payload_json, created_at) VALUES (:order_id, :payload_json, NOW())'
        );
        $statement->execute([
            'order_id' => $orderId,
            'payload_json' => json_encode($payload, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES),
        ]);
    }
}
