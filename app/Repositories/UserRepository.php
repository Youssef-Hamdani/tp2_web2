<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Core\Database;
use App\Models\User;
use PDO;

class UserRepository
{
    private PDO $pdo;

    public function __construct(?PDO $pdo = null)
    {
        $this->pdo = $pdo ?? Database::connection();
    }

    public function findById(int $id): ?User
    {
        $statement = $this->pdo->prepare('SELECT * FROM users WHERE id = :id LIMIT 1');
        $statement->execute(['id' => $id]);
        $row = $statement->fetch();

        return is_array($row) ? $this->map($row) : null;
    }

    public function findByEmail(string $email): ?User
    {
        $statement = $this->pdo->prepare('SELECT * FROM users WHERE email = :email LIMIT 1');
        $statement->execute(['email' => mb_strtolower(trim($email))]);
        $row = $statement->fetch();

        return is_array($row) ? $this->map($row) : null;
    }

    public function create(string $email, string $passwordHash, string $activationTokenHash, string $activationExpiresAt): int
    {
        $statement = $this->pdo->prepare(
            'INSERT INTO users (email, password_hash, role, is_active, activation_token_hash, activation_expires_at, created_at, updated_at)
             VALUES (:email, :password_hash, :role, 0, :activation_token_hash, :activation_expires_at, NOW(), NOW())'
        );
        $statement->execute([
            'email' => mb_strtolower(trim($email)),
            'password_hash' => $passwordHash,
            'role' => 'member',
            'activation_token_hash' => $activationTokenHash,
            'activation_expires_at' => $activationExpiresAt,
        ]);

        return (int) $this->pdo->lastInsertId();
    }

    public function updateActivationToken(int $userId, string $activationTokenHash, string $expiresAt): void
    {
        $statement = $this->pdo->prepare(
            'UPDATE users SET activation_token_hash = :token, activation_expires_at = :expires_at, updated_at = NOW() WHERE id = :id'
        );
        $statement->execute([
            'id' => $userId,
            'token' => $activationTokenHash,
            'expires_at' => $expiresAt,
        ]);
    }

    public function activateWithToken(string $email, string $tokenHash): bool
    {
        $statement = $this->pdo->prepare(
            'UPDATE users
             SET is_active = 1, activation_token_hash = NULL, activation_expires_at = NULL, updated_at = NOW()
             WHERE email = :email
               AND activation_token_hash = :token
               AND activation_expires_at IS NOT NULL
               AND activation_expires_at >= NOW()'
        );
        $statement->execute([
            'email' => mb_strtolower(trim($email)),
            'token' => $tokenHash,
        ]);

        return $statement->rowCount() === 1;
    }

    public function updateResetToken(int $userId, string $tokenHash, string $expiresAt): void
    {
        $statement = $this->pdo->prepare(
            'UPDATE users SET reset_token_hash = :token, reset_expires_at = :expires_at, updated_at = NOW() WHERE id = :id'
        );
        $statement->execute([
            'id' => $userId,
            'token' => $tokenHash,
            'expires_at' => $expiresAt,
        ]);
    }

    public function findByResetToken(string $email, string $tokenHash): ?User
    {
        $statement = $this->pdo->prepare(
            'SELECT * FROM users
             WHERE email = :email
               AND reset_token_hash = :token
               AND reset_expires_at IS NOT NULL
               AND reset_expires_at >= NOW()
             LIMIT 1'
        );
        $statement->execute([
            'email' => mb_strtolower(trim($email)),
            'token' => $tokenHash,
        ]);
        $row = $statement->fetch();

        return is_array($row) ? $this->map($row) : null;
    }

    public function updatePassword(int $userId, string $passwordHash): void
    {
        $statement = $this->pdo->prepare(
            'UPDATE users
             SET password_hash = :password_hash, reset_token_hash = NULL, reset_expires_at = NULL, updated_at = NOW()
             WHERE id = :id'
        );
        $statement->execute([
            'id' => $userId,
            'password_hash' => $passwordHash,
        ]);
    }

    public function storeRememberToken(int $userId, string $tokenHash, string $expiresAt): void
    {
        $statement = $this->pdo->prepare(
            'UPDATE users SET remember_token_hash = :token, remember_expires_at = :expires_at, updated_at = NOW() WHERE id = :id'
        );
        $statement->execute([
            'id' => $userId,
            'token' => $tokenHash,
            'expires_at' => $expiresAt,
        ]);
    }

    public function clearRememberToken(int $userId): void
    {
        $statement = $this->pdo->prepare(
            'UPDATE users SET remember_token_hash = NULL, remember_expires_at = NULL, updated_at = NOW() WHERE id = :id'
        );
        $statement->execute(['id' => $userId]);
    }

    public function findForRememberToken(int $userId, string $tokenHash): ?User
    {
        $statement = $this->pdo->prepare(
            'SELECT * FROM users
             WHERE id = :id
               AND remember_token_hash = :token
               AND remember_expires_at IS NOT NULL
               AND remember_expires_at >= NOW()
             LIMIT 1'
        );
        $statement->execute([
            'id' => $userId,
            'token' => $tokenHash,
        ]);
        $row = $statement->fetch();

        return is_array($row) ? $this->map($row) : null;
    }

    private function map(array $row): User
    {
        return new User(
            (int) $row['id'],
            (string) $row['email'],
            (string) $row['password_hash'],
            (string) $row['role'],
            (bool) $row['is_active'],
            $row['activation_token_hash'] !== null ? (string) $row['activation_token_hash'] : null,
            $row['activation_expires_at'] !== null ? (string) $row['activation_expires_at'] : null,
            $row['reset_token_hash'] !== null ? (string) $row['reset_token_hash'] : null,
            $row['reset_expires_at'] !== null ? (string) $row['reset_expires_at'] : null,
            $row['remember_token_hash'] !== null ? (string) $row['remember_token_hash'] : null,
            $row['remember_expires_at'] !== null ? (string) $row['remember_expires_at'] : null,
            (string) $row['created_at'],
            (string) $row['updated_at'],
        );
    }
}
