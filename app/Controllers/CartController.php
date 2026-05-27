<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Auth;
use App\Core\BaseController;
use App\Core\Session;
use App\Repositories\ProductRepository;
use App\Services\PricingService;

final class CartController extends BaseController
{
    public function add(int $id): void
    {
        $user = Auth::requireUser('/produits/' . $id);
        $this->validateCsrf('cart-add-' . $id, '/produits/' . $id);

        $product = (new ProductRepository())->findById($id);
        if ($product === null || !$product->isAvailable()) {
            $this->error('Ce produit n’est plus disponible.');
            $this->redirect('/');
        }

        if ($product->sellerUserId === $user->id) {
            $this->error('Vous ne pouvez pas acheter votre propre produit.');
            $this->redirect('/produits/' . $id);
        }

        Session::put('cart_product_id', $product->id);
        $this->success('Le produit a été ajouté au panier.');
        $this->redirect('/panier');
    }

    public function show(): void
    {
        Auth::requireUser('/panier');
        $productId = Session::get('cart_product_id');
        $product = null;

        if (is_int($productId) || ctype_digit((string) $productId)) {
            $product = (new ProductRepository())->findById((int) $productId);
        }

        $this->view('cart/show', [
            'product' => $product,
            'pricing' => new PricingService(),
        ]);
    }

    public function clear(): void
    {
        Auth::requireUser('/panier');
        $this->validateCsrf('cart-clear', '/panier');

        Session::forget('cart_product_id');
        $this->success('Le panier a été vidé.');
        $this->redirect('/panier');
    }
}
