<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Core\Database;
use App\Models\Order;
use PDO;
use Throwable;

final class OrderRepository
{
    private PDO $pdo;

    public function __construct(?PDO $pdo = null)
    {
        $this->pdo = $pdo ?? Database::connection();
    }

    public function createPending(array $data): int
    {
        $statement = $this->pdo->prepare(
            'INSERT INTO orders (
                buyer_user_id, seller_user_id, product_id, product_name,
                subtotal_cents, service_fee_cents, gst_cents, qst_cents, total_cents,
                buyer_first_name, buyer_last_name,
                billing_address_line1, billing_address_line2, billing_city, billing_province, billing_postal_code,
                shipping_address_line1, shipping_address_line2, shipping_city, shipping_province, shipping_postal_code,
                stripe_status, status, created_at, updated_at
             ) VALUES (
                :buyer_user_id, :seller_user_id, :product_id, :product_name,
                :subtotal_cents, :service_fee_cents, :gst_cents, :qst_cents, :total_cents,
                :buyer_first_name, :buyer_last_name,
                :billing_address_line1, :billing_address_line2, :billing_city, :billing_province, :billing_postal_code,
                :shipping_address_line1, :shipping_address_line2, :shipping_city, :shipping_province, :shipping_postal_code,
                :stripe_status, :status, NOW(), NOW()
             )'
        );
        $statement->execute($data);

        return (int) $this->pdo->lastInsertId();
    }

    public function updateStripeSession(int $orderId, string $sessionId): void
    {
        $statement = $this->pdo->prepare(
            'UPDATE orders SET stripe_session_id = :stripe_session_id, updated_at = NOW() WHERE id = :id'
        );
        $statement->execute([
            'id' => $orderId,
            'stripe_session_id' => $sessionId,
        ]);
    }

    public function findById(int $orderId): ?Order
    {
        $statement = $this->pdo->prepare('SELECT * FROM orders WHERE id = :id LIMIT 1');
        $statement->execute(['id' => $orderId]);
        $row = $statement->fetch();

        return is_array($row) ? $this->map($row) : null;
    }

    public function completePaidOrder(Order $order, string $paymentIntentId, string $stripeStatus, array $payload): bool
    {
        $this->pdo->beginTransaction();

        try {
            $current = $this->findByIdForUpdate($order->id);
            if (!$current instanceof Order) {
                $this->pdo->rollBack();
                return false;
            }

            if ($current->status === 'paid') {
                $this->pdo->commit();
                return true;
            }

            $productStatement = $this->pdo->prepare(
                "UPDATE products SET status = 'sold', sold_at = NOW(), updated_at = NOW() WHERE id = :id AND status = 'available'"
            );
            $productStatement->execute(['id' => $current->productId]);

            if ($productStatement->rowCount() !== 1) {
                $this->pdo->rollBack();
                return false;
            }

            $orderStatement = $this->pdo->prepare(
                "UPDATE orders
                 SET stripe_payment_intent_id = :payment_intent_id,
                     stripe_status = :stripe_status,
                     status = 'paid',
                     payment_payload = :payment_payload,
                     purchased_at = NOW(),
                     updated_at = NOW()
                 WHERE id = :id"
            );
            $orderStatement->execute([
                'id' => $current->id,
                'payment_intent_id' => $paymentIntentId,
                'stripe_status' => $stripeStatus,
                'payment_payload' => json_encode($payload, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES),
            ]);

            $this->pdo->commit();
            return true;
        } catch (Throwable $throwable) {
            if ($this->pdo->inTransaction()) {
                $this->pdo->rollBack();
            }

            throw $throwable;
        }
    }

    public function purchaseHistory(int $buyerUserId): array
    {
        $statement = $this->pdo->prepare(
            "SELECT * FROM orders WHERE buyer_user_id = :buyer_user_id AND status = 'paid' ORDER BY purchased_at DESC"
        );
        $statement->execute(['buyer_user_id' => $buyerUserId]);

        return array_map(fn (array $row): Order => $this->map($row), $statement->fetchAll());
    }

    public function salesHistory(int $sellerUserId): array
    {
        $statement = $this->pdo->prepare(
            "SELECT * FROM orders WHERE seller_user_id = :seller_user_id AND status = 'paid' ORDER BY purchased_at DESC"
        );
        $statement->execute(['seller_user_id' => $sellerUserId]);

        return array_map(fn (array $row): Order => $this->map($row), $statement->fetchAll());
    }

    private function findByIdForUpdate(int $orderId): ?Order
    {
        $statement = $this->pdo->prepare('SELECT * FROM orders WHERE id = :id LIMIT 1 FOR UPDATE');
        $statement->execute(['id' => $orderId]);
        $row = $statement->fetch();

        return is_array($row) ? $this->map($row) : null;
    }

    private function map(array $row): Order
    {
        return new Order(
            (int) $row['id'],
            (int) $row['buyer_user_id'],
            (int) $row['seller_user_id'],
            (int) $row['product_id'],
            (string) $row['product_name'],
            (int) $row['subtotal_cents'],
            (int) $row['service_fee_cents'],
            (int) $row['gst_cents'],
            (int) $row['qst_cents'],
            (int) $row['total_cents'],
            (string) $row['buyer_first_name'],
            (string) $row['buyer_last_name'],
            (string) $row['billing_address_line1'],
            $row['billing_address_line2'] !== null ? (string) $row['billing_address_line2'] : null,
            (string) $row['billing_city'],
            (string) $row['billing_province'],
            (string) $row['billing_postal_code'],
            (string) $row['shipping_address_line1'],
            $row['shipping_address_line2'] !== null ? (string) $row['shipping_address_line2'] : null,
            (string) $row['shipping_city'],
            (string) $row['shipping_province'],
            (string) $row['shipping_postal_code'],
            $row['stripe_session_id'] !== null ? (string) $row['stripe_session_id'] : null,
            $row['stripe_payment_intent_id'] !== null ? (string) $row['stripe_payment_intent_id'] : null,
            (string) $row['stripe_status'],
            (string) $row['status'],
            $row['payment_payload'] !== null ? (string) $row['payment_payload'] : null,
            $row['purchased_at'] !== null ? (string) $row['purchased_at'] : null,
            (string) $row['created_at'],
            (string) $row['updated_at'],
        );
    }
}
