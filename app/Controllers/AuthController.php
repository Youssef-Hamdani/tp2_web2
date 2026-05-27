<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Auth;
use App\Core\BaseController;
use App\Services\AuthService;

final class AuthController extends BaseController
{
    private AuthService $authService;

    public function __construct()
    {
        parent::__construct();
        $this->authService = new AuthService();
    }

    public function showRegister(): void
    {
        $this->view('auth/register');
    }

    public function register(): void
    {
        $this->validateCsrf('register', '/inscription');
        $input = $this->request->only(['email']);
        $this->request->storeOldInput($input);

        $this->authService->register(
            (string) $this->request->post('email'),
            (string) $this->request->post('password'),
            (string) $this->request->post('password_confirmation')
        );

        $this->request->clearOldInput();
        $this->success('Si le compte peut être créé, un courriel d’activation a été envoyé.');
        $this->redirect('/connexion');
    }

    public function showLogin(): void
    {
        $this->view('auth/login');
    }

    public function login(): void
    {
        $this->validateCsrf('login', '/connexion');
        $input = $this->request->only(['email']);
        $this->request->storeOldInput($input);

        $ok = $this->authService->authenticate(
            (string) $this->request->post('email'),
            (string) $this->request->post('password'),
            $this->request->post('remember_me') === '1'
        );

        if (!$ok) {
            $this->error('Nom d’utilisateur ou mot de passe invalide.');
            $this->redirect('/connexion');
        }

        $this->request->clearOldInput();
        $this->success('Connexion réussie.');
        $this->redirect(redirect_back_default('/'));
    }

    public function logout(): void
    {
        $this->validateCsrf('logout', '/');
        Auth::logout();
        $this->success('Vous êtes maintenant déconnecté.');
        $this->redirect('/');
    }

    public function activate(): void
    {
        $ok = $this->authService->activate(
            (string) $this->request->query('email'),
            (string) $this->request->query('token')
        );

        if ($ok) {
            $this->success('Votre compte est maintenant activé. Vous pouvez vous connecter.');
            $this->redirect('/connexion');
        }

        $this->error('Le lien d’activation est invalide ou expiré.');
        $this->redirect('/inscription');
    }

    public function showForgotPassword(): void
    {
        $this->view('auth/forgot-password');
    }

    public function sendResetLink(): void
    {
        $this->validateCsrf('forgot-password', '/mot-de-passe-oublie');
        $input = $this->request->only(['email']);
        $this->request->storeOldInput($input);

        $this->authService->sendResetLink((string) $this->request->post('email'));
        $this->request->clearOldInput();

        $this->success('Si le compte existe, un courriel vous a été envoyé.');
        $this->redirect('/mot-de-passe-oublie');
    }

    public function showResetPassword(): void
    {
        $this->view('auth/reset-password', [
            'email' => (string) $this->request->query('email'),
            'token' => (string) $this->request->query('token'),
        ]);
    }

    public function resetPassword(): void
    {
        $this->validateCsrf('reset-password', '/reinitialiser-mot-de-passe');

        $ok = $this->authService->resetPassword(
            (string) $this->request->post('email'),
            (string) $this->request->post('token'),
            (string) $this->request->post('password'),
            (string) $this->request->post('password_confirmation')
        );

        if (!$ok) {
            $this->error('Le lien de réinitialisation est invalide ou expiré.');
            $this->redirect('/mot-de-passe-oublie');
        }

        $this->success('Votre mot de passe a été modifié. Vous pouvez vous connecter.');
        $this->redirect('/connexion');
    }
}
