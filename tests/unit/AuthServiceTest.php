<?php

declare(strict_types=1);

namespace Tests\Unit;

use App\Models\User;
use App\Repositories\UserRepository;
use App\Services\AuthService;
use App\Services\Mailer;
use Tests\Support\TestCase;

final class AuthServiceTest extends TestCase
{
    public function testInscriptionCompteExistantEnvoieUnCourrielGenerique(): void
    {
        $repository = new class extends UserRepository {
            public bool $createCalled = false;

            public function __construct()
            {
            }

            public function findByEmail(string $email): ?User
            {
                return new User(99, $email, 'hash', 'member', true, null, null, null, null, null, null, date('c'), date('c'));
            }

            public function create(string $email, string $passwordHash, string $activationTokenHash, string $activationExpiresAt): int
            {
                $this->createCalled = true;
                return 100;
            }
        };

        $mailer = new class extends Mailer {
            public array $sent = [];

            public function send(string $to, string $subject, string $textBody): void
            {
                $this->sent[] = compact('to', 'subject', 'textBody');
            }
        };

        $service = new AuthService($repository, $mailer);
        $service->register('existing@example.com', 'Motdepasse123', 'Motdepasse123');

        $this->assertFalse($repository->createCalled, 'Aucun nouvel utilisateur ne devrait être créé.');
        $this->assertSame(1, count($mailer->sent), 'Un courriel générique doit être envoyé.');
        $this->assertContains('compte existe déjà', $mailer->sent[0]['textBody']);
    }
}
