<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Core\Database;
use App\Models\Product;
use PDO;

final class ProductRepository
{
    private PDO $pdo;

    public function __construct(?PDO $pdo = null)
    {
        $this->pdo = $pdo ?? Database::connection();
    }

    public function allAvailable(): array
    {
        $statement = $this->pdo->query(
            "SELECT p.*, u.email AS seller_email
             FROM products p
             INNER JOIN users u ON u.id = p.seller_user_id
             WHERE p.status = 'available'
             ORDER BY p.created_at DESC"
        );

        return array_map(fn (array $row): Product => $this->map($row), $statement->fetchAll());
    }

    public function findById(int $id): ?Product
    {
        $statement = $this->pdo->prepare(
            'SELECT p.*, u.email AS seller_email
             FROM products p
             INNER JOIN users u ON u.id = p.seller_user_id
             WHERE p.id = :id
             LIMIT 1'
        );
        $statement->execute(['id' => $id]);
        $row = $statement->fetch();

        return is_array($row) ? $this->map($row) : null;
    }

    public function create(int $sellerUserId, string $name, string $description, string $imagePath, int $priceCents, int $serviceFeeCents): int
    {
        $statement = $this->pdo->prepare(
            "INSERT INTO products (seller_user_id, name, description, image_path, price_cents, service_fee_cents, status, created_at, updated_at)
             VALUES (:seller_user_id, :name, :description, :image_path, :price_cents, :service_fee_cents, 'available', NOW(), NOW())"
        );
        $statement->execute([
            'seller_user_id' => $sellerUserId,
            'name' => $name,
            'description' => $description,
            'image_path' => $imagePath,
            'price_cents' => $priceCents,
            'service_fee_cents' => $serviceFeeCents,
        ]);

        return (int) $this->pdo->lastInsertId();
    }

    public function updateOwnedAvailable(int $productId, int $sellerUserId, string $name, string $description, string $imagePath, int $priceCents, int $serviceFeeCents): bool
    {
        $statement = $this->pdo->prepare(
            "UPDATE products
             SET name = :name, description = :description, image_path = :image_path, price_cents = :price_cents, service_fee_cents = :service_fee_cents, updated_at = NOW()
             WHERE id = :id AND seller_user_id = :seller_user_id AND status = 'available'"
        );
        $statement->execute([
            'id' => $productId,
            'seller_user_id' => $sellerUserId,
            'name' => $name,
            'description' => $description,
            'image_path' => $imagePath,
            'price_cents' => $priceCents,
            'service_fee_cents' => $serviceFeeCents,
        ]);

        return $statement->rowCount() === 1;
    }

    public function deleteOwnedAvailable(int $productId, int $sellerUserId): bool
    {
        $statement = $this->pdo->prepare(
            "DELETE FROM products WHERE id = :id AND seller_user_id = :seller_user_id AND status = 'available'"
        );
        $statement->execute([
            'id' => $productId,
            'seller_user_id' => $sellerUserId,
        ]);

        return $statement->rowCount() === 1;
    }

    public function bySeller(int $sellerUserId): array
    {
        $statement = $this->pdo->prepare(
            'SELECT p.*, u.email AS seller_email
             FROM products p
             INNER JOIN users u ON u.id = p.seller_user_id
             WHERE p.seller_user_id = :seller_user_id
             ORDER BY p.created_at DESC'
        );
        $statement->execute(['seller_user_id' => $sellerUserId]);

        return array_map(fn (array $row): Product => $this->map($row), $statement->fetchAll());
    }

    public function markSold(int $productId): bool
    {
        $statement = $this->pdo->prepare(
            "UPDATE products SET status = 'sold', sold_at = NOW(), updated_at = NOW() WHERE id = :id AND status = 'available'"
        );
        $statement->execute(['id' => $productId]);

        return $statement->rowCount() === 1;
    }

    private function map(array $row): Product
    {
        return new Product(
            (int) $row['id'],
            (int) $row['seller_user_id'],
            (string) $row['name'],
            (string) $row['description'],
            (string) $row['image_path'],
            (int) $row['price_cents'],
            (int) $row['service_fee_cents'],
            (string) $row['status'],
            $row['sold_at'] !== null ? (string) $row['sold_at'] : null,
            (string) $row['created_at'],
            (string) $row['updated_at'],
            $row['seller_email'] !== null ? (string) $row['seller_email'] : null,
        );
    }
}
