<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Auth;
use App\Core\BaseController;
use App\Core\ValidationException;
use App\Repositories\ProductRepository;
use App\Services\ImageService;
use App\Services\PricingService;

final class ProductController extends BaseController
{
    public function index(): void
    {
        $this->redirect('/');
    }

    public function show(int $id): void
    {
        $product = (new ProductRepository())->findById($id);
        if ($product === null) {
            http_response_code(404);
            $this->view('errors/404');
            return;
        }

        $this->view('products/show', [
            'product' => $product,
            'pricing' => new PricingService(),
        ]);
    }

    public function create(): void
    {
        Auth::requireUser('/produits/ajouter');
        $this->view('products/form', [
            'mode' => 'create',
            'product' => null,
            'pricing' => new PricingService(),
        ]);
    }

    public function store(): void
    {
        $user = Auth::requireUser('/produits/ajouter');
        $this->validateCsrf('product-create', '/produits/ajouter');
        $input = $this->request->only(['name', 'description', 'price']);
        $this->request->storeOldInput($input);

        $data = $this->validateProductInput('/produits/ajouter');
        $imagePath = (new ImageService())->storeUploadedProductImage($this->request->file('image'), '/produits/ajouter');
        $serviceFee = (new PricingService())->serviceFeeCents($data['price_cents']);

        (new ProductRepository())->create(
            $user->id,
            $data['name'],
            $data['description'],
            $imagePath,
            $data['price_cents'],
            $serviceFee
        );

        $this->request->clearOldInput();
        $this->success('Le produit a été ajouté.');
        $this->redirect('/compte');
    }

    public function edit(int $id): void
    {
        $user = Auth::requireUser('/produits/' . $id . '/modifier');
        $product = (new ProductRepository())->findById($id);

        if ($product === null || $product->sellerUserId !== $user->id || !$product->isAvailable()) {
            throw new ValidationException('Vous ne pouvez pas modifier ce produit.', '/compte');
        }

        $this->view('products/form', [
            'mode' => 'edit',
            'product' => $product,
            'pricing' => new PricingService(),
        ]);
    }

    public function update(int $id): void
    {
        $user = Auth::requireUser('/produits/' . $id . '/modifier');
        $this->validateCsrf('product-edit-' . $id, '/produits/' . $id . '/modifier');
        $product = (new ProductRepository())->findById($id);

        if ($product === null || $product->sellerUserId !== $user->id || !$product->isAvailable()) {
            throw new ValidationException('Vous ne pouvez pas modifier ce produit.', '/compte');
        }

        $input = $this->request->only(['name', 'description', 'price']);
        $this->request->storeOldInput($input);

        $data = $this->validateProductInput('/produits/' . $id . '/modifier');
        $imagePath = $product->imagePath;
        $file = $this->request->file('image');
        if ($file !== null && (int) ($file['error'] ?? UPLOAD_ERR_NO_FILE) === UPLOAD_ERR_OK) {
            $imagePath = (new ImageService())->storeUploadedProductImage($file, '/produits/' . $id . '/modifier');
        }

        $serviceFee = (new PricingService())->serviceFeeCents($data['price_cents']);
        (new ProductRepository())->updateOwnedAvailable(
            $id,
            $user->id,
            $data['name'],
            $data['description'],
            $imagePath,
            $data['price_cents'],
            $serviceFee
        );

        $this->request->clearOldInput();
        $this->success('Le produit a été mis à jour.');
        $this->redirect('/compte');
    }

    public function destroy(int $id): void
    {
        $user = Auth::requireUser('/compte');
        $this->validateCsrf('product-delete-' . $id, '/compte');

        $deleted = (new ProductRepository())->deleteOwnedAvailable($id, $user->id);
        if (!$deleted) {
            $this->error('Impossible de supprimer ce produit.');
            $this->redirect('/compte');
        }

        $this->success('Le produit a été supprimé.');
        $this->redirect('/compte');
    }

    private function validateProductInput(string $redirectTo): array
    {
        $name = trim((string) $this->request->post('name'));
        $description = trim((string) $this->request->post('description'));
        $price = str_replace(',', '.', trim((string) $this->request->post('price')));

        if ($name === '' || mb_strlen($name) > 120) {
            throw new ValidationException('Le nom du produit est requis et doit contenir au plus 120 caractères.', $redirectTo);
        }

        if ($description === '' || mb_strlen($description) > 5000) {
            throw new ValidationException('La description est requise et doit contenir au plus 5000 caractères.', $redirectTo);
        }

        if (!preg_match('/^\d{1,8}(\.\d{1,2})?$/', $price)) {
            throw new ValidationException('Le prix doit être un montant valide en dollars canadiens.', $redirectTo);
        }

        $priceCents = (int) round(((float) $price) * 100);
        if ($priceCents < 1) {
            throw new ValidationException('Le prix minimal est de 0,01 $.', $redirectTo);
        }

        return [
            'name' => strip_tags($name),
            'description' => strip_tags($description),
            'price_cents' => $priceCents,
        ];
    }
}
