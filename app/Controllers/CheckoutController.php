<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Auth;
use App\Core\BaseController;
use App\Core\Response;
use App\Core\Session;
use App\Core\ValidationException;
use App\Repositories\OrderRepository;
use App\Repositories\ProductRepository;
use App\Services\OrderService;
use App\Services\PricingService;
use App\Services\StripeService;

final class CheckoutController extends BaseController
{
    public function show(): void
    {
        $user = Auth::requireUser('/commande');
        $product = $this->currentCartProduct();

        if ($product === null) {
            $this->error('Votre panier est vide.');
            $this->redirect('/panier');
        }

        if ($product->sellerUserId === $user->id) {
            throw new ValidationException('Vous ne pouvez pas acheter votre propre produit.', '/panier');
        }

        $this->view('checkout/show', [
            'product' => $product,
            'pricing' => new PricingService(),
            'stripeKey' => (new StripeService())->publishableKey(),
            'stripeConfigured' => (new StripeService())->isConfigured(),
        ]);
    }

    public function createStripeSession(): void
    {
        $user = Auth::requireUser('/commande');
        $this->validateCsrf('checkout', '/commande');

        $product = $this->currentCartProduct();
        if ($product === null) {
            Response::json(['message' => 'Votre panier est vide.'], 422);
        }

        if ($product->sellerUserId === $user->id) {
            Response::json(['message' => 'Vous ne pouvez pas acheter votre propre produit.'], 422);
        }

        $data = $this->validateCheckoutInput();
        $service = new OrderService();
        $order = $service->createPendingOrder($product, $data);
        try {
            $session = $service->startStripeCheckout($order, $product);
        } catch (ValidationException $exception) {
            Response::json(['message' => $exception->getMessage()], 422);
        }

        Response::json([
            'id' => $session['id'],
            'publicKey' => (new StripeService())->publishableKey(),
        ]);
    }

    public function checkoutSuccess(): void
    {
        $user = Auth::requireUser('/commande/succes');
        $orderId = (int) $this->request->query('order_id');
        $sessionId = (string) $this->request->query('session_id');

        $order = (new OrderRepository())->findById($orderId);
        if ($order === null || $order->buyerUserId !== $user->id || $order->stripeSessionId !== $sessionId) {
            throw new ValidationException('Commande introuvable.', '/compte/achats');
        }

        $stripeSession = (new StripeService())->retrieveCheckoutSession($sessionId);
        if (($stripeSession['payment_status'] ?? '') !== 'paid') {
            $this->error('Le paiement n’a pas été confirmé.');
            $this->redirect('/commande');
        }

        (new OrderService())->finalizeOrder($order, $stripeSession);
        Session::forget('cart_product_id');
        $this->success('Paiement confirmé. Votre achat apparaît maintenant dans votre historique.');
        $this->redirect('/compte/achats');
    }

    public function cancel(): void
    {
        Auth::requireUser('/commande/annulee');
        $this->error('Le paiement a été annulé.');
        $this->redirect('/commande');
    }

    private function currentCartProduct(): ?\App\Models\Product
    {
        $productId = Session::get('cart_product_id');

        if (!is_int($productId) && !ctype_digit((string) $productId)) {
            return null;
        }

        return (new ProductRepository())->findById((int) $productId);
    }

    private function validateCheckoutInput(): array
    {
        $fields = [
            'buyer_first_name',
            'buyer_last_name',
            'billing_address_line1',
            'billing_address_line2',
            'billing_city',
            'billing_province',
            'billing_postal_code',
            'shipping_address_line1',
            'shipping_address_line2',
            'shipping_city',
            'shipping_province',
            'shipping_postal_code',
        ];

        $values = [];
        foreach ($fields as $field) {
            $values[$field] = trim((string) $this->request->post($field));
        }

        foreach ([
            'buyer_first_name' => 'Le prénom est requis.',
            'buyer_last_name' => 'Le nom est requis.',
            'billing_address_line1' => 'L’adresse de facturation est requise.',
            'billing_city' => 'La ville de facturation est requise.',
            'billing_province' => 'La province de facturation est requise.',
            'billing_postal_code' => 'Le code postal de facturation est requis.',
            'shipping_address_line1' => 'L’adresse de livraison est requise.',
            'shipping_city' => 'La ville de livraison est requise.',
            'shipping_province' => 'La province de livraison est requise.',
            'shipping_postal_code' => 'Le code postal de livraison est requis.',
        ] as $field => $message) {
            if ($values[$field] === '') {
                Response::json(['message' => $message], 422);
            }
        }

        if (!$this->isValidPostalCode($values['billing_postal_code']) || !$this->isValidPostalCode($values['shipping_postal_code'])) {
            Response::json(['message' => 'Le code postal doit respecter le format canadien.'], 422);
        }

        return array_map(static fn (string $value): string => strip_tags($value), $values);
    }

    private function isValidPostalCode(string $postalCode): bool
    {
        return preg_match('/^[A-Za-z]\d[A-Za-z][ -]?\d[A-Za-z]\d$/', $postalCode) === 1;
    }
}
