<?php

declare(strict_types=1);

namespace App\Services;

use App\Core\Auth;
use App\Core\Logger;
use App\Core\ValidationException;
use App\Models\Order;
use App\Models\Product;
use App\Repositories\OrderRepository;
use App\Repositories\ProductRepository;
use App\Repositories\SaleLogRepository;

final class OrderService
{
    public function __construct(
        private readonly OrderRepository $orders = new OrderRepository(),
        private readonly ProductRepository $products = new ProductRepository(),
        private readonly SaleLogRepository $saleLogs = new SaleLogRepository(),
        private readonly StripeService $stripe = new StripeService(),
        private readonly PricingService $pricing = new PricingService(),
    ) {
    }

    public function createPendingOrder(Product $product, array $data): Order
    {
        $buyerId = Auth::id();
        if ($buyerId === null) {
            throw new ValidationException('Veuillez vous connecter pour continuer.', '/connexion');
        }

        if (!$product->isAvailable()) {
            throw new ValidationException('Ce produit n’est plus disponible.', '/panier');
        }

        if ($product->sellerUserId === $buyerId) {
            throw new ValidationException('Vous ne pouvez pas acheter votre propre produit.', '/produits/' . $product->id);
        }

        $summary = $this->pricing->summary($product->priceCents);
        $payload = [
            'buyer_user_id' => $buyerId,
            'seller_user_id' => $product->sellerUserId,
            'product_id' => $product->id,
            'product_name' => $product->name,
            'subtotal_cents' => $summary['subtotal_cents'],
            'service_fee_cents' => $summary['service_fee_cents'],
            'gst_cents' => $summary['gst_cents'],
            'qst_cents' => $summary['qst_cents'],
            'total_cents' => $summary['total_cents'],
            'buyer_first_name' => $data['buyer_first_name'],
            'buyer_last_name' => $data['buyer_last_name'],
            'billing_address_line1' => $data['billing_address_line1'],
            'billing_address_line2' => $data['billing_address_line2'] ?: null,
            'billing_city' => $data['billing_city'],
            'billing_province' => $data['billing_province'],
            'billing_postal_code' => $data['billing_postal_code'],
            'shipping_address_line1' => $data['shipping_address_line1'],
            'shipping_address_line2' => $data['shipping_address_line2'] ?: null,
            'shipping_city' => $data['shipping_city'],
            'shipping_province' => $data['shipping_province'],
            'shipping_postal_code' => $data['shipping_postal_code'],
            'stripe_status' => 'pending',
            'status' => 'pending',
        ];

        $orderId = $this->orders->createPending($payload);
        $order = $this->orders->findById($orderId);

        if (!$order instanceof Order) {
            throw new ValidationException('Impossible de préparer la commande.', '/commande');
        }

        return $order;
    }

    public function startStripeCheckout(Order $order, Product $product): array
    {
        $session = $this->stripe->createCheckoutSession($order, $product);
        $this->orders->updateStripeSession($order->id, (string) $session['id']);

        return $session;
    }

    public function finalizeOrder(Order $order, array $checkoutSession): bool
    {
        $paymentIntent = $checkoutSession['payment_intent'] ?? [];
        $payload = [
            'user_id' => Auth::id(),
            'order_id' => $order->id,
            'product_id' => $order->productId,
            'checkout_session' => $checkoutSession['id'] ?? null,
            'payment_status' => $checkoutSession['payment_status'] ?? null,
            'payment_intent_id' => $paymentIntent['id'] ?? null,
            'payment_intent_status' => $paymentIntent['status'] ?? null,
            'total_cents' => $order->totalCents,
        ];

        $success = $this->orders->completePaidOrder(
            $order,
            (string) ($paymentIntent['id'] ?? ''),
            (string) ($paymentIntent['status'] ?? 'paid'),
            $checkoutSession
        );

        if ($success) {
            Logger::info('Sale completed', $payload);
            $this->saleLogs->create($order->id, $payload);
        }

        return $success;
    }
}
