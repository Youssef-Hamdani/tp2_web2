<?php

declare(strict_types=1);

namespace App\Services;

use App\Core\ValidationException;
use App\Models\Order;
use App\Models\Product;

final class StripeService
{
    public function isConfigured(): bool
    {
        return (string) config('stripe.secret_key') !== '' && (string) config('stripe.publishable_key') !== '';
    }

    public function publishableKey(): string
    {
        return (string) config('stripe.publishable_key');
    }

    public function createCheckoutSession(Order $order, Product $product): array
    {
        if (!$this->isConfigured()) {
            throw new ValidationException('Stripe n’est pas configuré. Ajoutez vos clés Stripe pour activer les paiements.', '/commande');
        }

        $baseUrl = rtrim((string) config('app.base_url'), '/');
        $payload = [
            'mode' => 'payment',
            'success_url' => $baseUrl . '/commande/succes?session_id={CHECKOUT_SESSION_ID}&order_id=' . $order->id,
            'cancel_url' => $baseUrl . '/commande/annulee?order_id=' . $order->id,
            'client_reference_id' => (string) $order->id,
            'payment_method_types[0]' => 'card',
            'metadata[order_id]' => (string) $order->id,
            'line_items[0][price_data][currency]' => (string) config('stripe.currency', 'cad'),
            'line_items[0][price_data][product_data][name]' => $product->name,
            'line_items[0][price_data][product_data][description]' => mb_strimwidth($product->description, 0, 120, '...'),
            'line_items[0][price_data][unit_amount]' => (string) $order->subtotalCents,
            'line_items[0][quantity]' => '1',
            'line_items[1][price_data][currency]' => (string) config('stripe.currency', 'cad'),
            'line_items[1][price_data][product_data][name]' => 'Frais de service',
            'line_items[1][price_data][unit_amount]' => (string) $order->serviceFeeCents,
            'line_items[1][quantity]' => '1',
            'line_items[2][price_data][currency]' => (string) config('stripe.currency', 'cad'),
            'line_items[2][price_data][product_data][name]' => 'TPS',
            'line_items[2][price_data][unit_amount]' => (string) $order->gstCents,
            'line_items[2][quantity]' => '1',
            'line_items[3][price_data][currency]' => (string) config('stripe.currency', 'cad'),
            'line_items[3][price_data][product_data][name]' => 'TVQ',
            'line_items[3][price_data][unit_amount]' => (string) $order->qstCents,
            'line_items[3][quantity]' => '1',
        ];

        return $this->request('POST', 'https://api.stripe.com/v1/checkout/sessions', $payload);
    }

    public function retrieveCheckoutSession(string $sessionId): array
    {
        if (!$this->isConfigured()) {
            throw new ValidationException('Stripe n’est pas configuré.', '/commande');
        }

        return $this->request(
            'GET',
            'https://api.stripe.com/v1/checkout/sessions/' . rawurlencode($sessionId) . '?expand[]=payment_intent'
        );
    }

    private function request(string $method, string $url, array $payload = []): array
    {
        $ch = curl_init();
        curl_setopt_array($ch, [
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_CUSTOMREQUEST => $method,
            CURLOPT_USERPWD => (string) config('stripe.secret_key') . ':',
            CURLOPT_HTTPAUTH => CURLAUTH_BASIC,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTPHEADER => [
                'Content-Type: application/x-www-form-urlencoded',
            ],
        ]);

        if ($method === 'POST') {
            curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($payload));
        }

        $raw = curl_exec($ch);
        $status = (int) curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $error = curl_error($ch);
        curl_close($ch);

        if ($raw === false || $error !== '') {
            throw new ValidationException('Impossible de joindre Stripe pour le moment.', '/commande');
        }

        $decoded = json_decode($raw, true);
        if (!is_array($decoded) || $status >= 400) {
            $message = is_array($decoded) ? ($decoded['error']['message'] ?? 'Erreur Stripe.') : 'Erreur Stripe.';
            throw new ValidationException((string) $message, '/commande');
        }

        return $decoded;
    }
}
