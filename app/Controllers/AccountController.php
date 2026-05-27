<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Auth;
use App\Core\BaseController;
use App\Repositories\OrderRepository;
use App\Repositories\ProductRepository;
use App\Services\AuthService;
use App\Services\PricingService;

final class AccountController extends BaseController
{
    public function index(): void
    {
        $user = Auth::requireUser('/compte');
        $this->view('account/index', [
            'user' => $user,
        ]);
    }

    public function showPasswordForm(): void
    {
        Auth::requireUser('/compte/mot-de-passe');
        $this->view('account/password');
    }

    public function updatePassword(): void
    {
        $user = Auth::requireUser('/compte/mot-de-passe');
        $this->validateCsrf('change-password', '/compte/mot-de-passe');

        $ok = (new AuthService())->changePassword(
            $user,
            (string) $this->request->post('current_password'),
            (string) $this->request->post('password'),
            (string) $this->request->post('password_confirmation')
        );

        if (!$ok) {
            $this->error('Le mot de passe actuel est invalide.');
            $this->redirect('/compte/mot-de-passe');
        }

        $this->success('Votre mot de passe a été mis à jour.');
        $this->redirect('/compte');
    }

    public function purchases(): void
    {
        $user = Auth::requireUser('/compte/achats');
        $this->view('account/purchases', [
            'orders' => (new OrderRepository())->purchaseHistory($user->id),
            'pricing' => new PricingService(),
        ]);
    }

    public function sales(): void
    {
        $user = Auth::requireUser('/compte/ventes');
        $this->view('account/sales', [
            'orders' => (new OrderRepository())->salesHistory($user->id),
            'pricing' => new PricingService(),
        ]);
    }
}
