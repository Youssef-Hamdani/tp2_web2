<?php

declare(strict_types=1);

namespace App\Services;

use App\Core\Auth;
use App\Core\ValidationException;
use App\Models\User;
use App\Repositories\UserRepository;

final class AuthService
{
    public function __construct(
        private readonly UserRepository $users = new UserRepository(),
        private readonly Mailer $mailer = new Mailer(),
    ) {
    }

    public function register(string $email, string $password, string $passwordConfirmation): void
    {
        $email = mb_strtolower(trim($email));
        $this->validateEmail($email);
        $this->validatePassword($password, $passwordConfirmation, '/inscription');

        $existing = $this->users->findByEmail($email);
        $token = bin2hex(random_bytes(32));
        $tokenHash = hash('sha256', $token);
        $expiresAt = date('Y-m-d H:i:s', time() + (int) config('auth.activation_for_seconds', 3600));

        if ($existing instanceof User) {
            if (!$existing->isActive) {
                $this->users->updateActivationToken($existing->id, $tokenHash, $expiresAt);
                $this->sendActivationEmail($email, $token);
            } else {
                $this->mailer->send(
                    $email,
                    'Tentative de création de compte',
                    "Un compte existe déjà pour cette adresse. Si c'est vous, vous pouvez vous connecter ou réinitialiser votre mot de passe."
                );
            }

            return;
        }

        $passwordHash = password_hash($password, PASSWORD_DEFAULT);
        $this->users->create($email, $passwordHash, $tokenHash, $expiresAt);
        $this->sendActivationEmail($email, $token);
    }

    public function authenticate(string $email, string $password, bool $rememberMe): bool
    {
        $user = $this->users->findByEmail($email);

        if (!$user instanceof User || !password_verify($password, $user->passwordHash) || !$user->isActive) {
            return false;
        }

        Auth::login($user, $rememberMe);
        return true;
    }

    public function activate(string $email, string $token): bool
    {
        $email = mb_strtolower(trim($email));
        $this->validateEmail($email);

        return $this->users->activateWithToken($email, hash('sha256', $token));
    }

    public function sendResetLink(string $email): void
    {
        $email = mb_strtolower(trim($email));
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return;
        }

        $user = $this->users->findByEmail($email);
        if (!$user instanceof User || !$user->isActive) {
            return;
        }

        $token = bin2hex(random_bytes(32));
        $this->users->updateResetToken(
            $user->id,
            hash('sha256', $token),
            date('Y-m-d H:i:s', time() + (int) config('auth.reset_for_seconds', 3600))
        );

        $link = url('/reinitialiser-mot-de-passe?email=' . rawurlencode($email) . '&token=' . rawurlencode($token));
        $this->mailer->send(
            $email,
            'Réinitialisation de votre mot de passe',
            "Bonjour,\n\nPour réinitialiser votre mot de passe, cliquez sur ce lien dans l'heure qui suit :\n{$link}\n\nSi vous n'avez rien demandé, ignorez ce message."
        );
    }

    public function resetPassword(string $email, string $token, string $password, string $passwordConfirmation): bool
    {
        $email = mb_strtolower(trim($email));
        $this->validateEmail($email);
        $this->validatePassword($password, $passwordConfirmation, '/reinitialiser-mot-de-passe?email=' . rawurlencode($email));

        $user = $this->users->findByResetToken($email, hash('sha256', $token));
        if (!$user instanceof User) {
            return false;
        }

        $this->users->updatePassword($user->id, password_hash($password, PASSWORD_DEFAULT));
        return true;
    }

    public function changePassword(User $user, string $currentPassword, string $newPassword, string $confirmation): bool
    {
        if (!password_verify($currentPassword, $user->passwordHash)) {
            return false;
        }

        $this->validatePassword($newPassword, $confirmation, '/compte/mot-de-passe');
        $this->users->updatePassword($user->id, password_hash($newPassword, PASSWORD_DEFAULT));
        return true;
    }

    private function validateEmail(string $email): void
    {
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw new ValidationException('Veuillez fournir une adresse courriel valide.', '/inscription');
        }
    }

    private function validatePassword(string $password, string $confirmation, string $redirectTo): void
    {
        if ($password !== $confirmation) {
            throw new ValidationException('La confirmation du mot de passe est invalide.', $redirectTo);
        }

        if (mb_strlen($password) < (int) config('auth.min_password_length', 10)) {
            throw new ValidationException('Le mot de passe doit contenir au moins 10 caractères.', $redirectTo);
        }
    }

    private function sendActivationEmail(string $email, string $token): void
    {
        $link = url('/activation?email=' . rawurlencode($email) . '&token=' . rawurlencode($token));
        $this->mailer->send(
            $email,
            'Activez votre compte',
            "Bonjour,\n\nPour activer votre compte, cliquez sur ce lien dans l'heure qui suit :\n{$link}\n\nSi vous n'êtes pas à l'origine de cette demande, ignorez simplement ce message."
        );
    }
}
