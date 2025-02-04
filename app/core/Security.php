<?php

namespace App\Core;

class Security
{
    private $session;

    public function __construct()
    {
        $this->session = new Session();
    }

    public function generateCsrfToken(): string
    {
        $token = bin2hex(random_bytes(32));
        $this->session->set('csrf_token', $token);
        return $token;
    }

    public function validateCsrfToken(?string $token): bool
    {
        if (!$token) return false;
        $storedToken = $this->session->get('csrf_token');
        return $storedToken && hash_equals($storedToken, $token);
    }

    public function escape(string $data): string
    {
        return htmlspecialchars($data, ENT_QUOTES, 'UTF-8');
    }

    public function sanitizeInput($data)
    {
        if (is_array($data)) {
            return array_map([$this, 'sanitizeInput'], $data);
        }
        return $this->escape(trim($data));
    }

    public function validateInteger($value): int
    {
        return filter_var($value, FILTER_VALIDATE_INT);
    }

    public function validateEmail(string $email): bool
    {
        return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
    }

    public function hashPassword(string $password): string
    {
        return password_hash($password, PASSWORD_DEFAULT);
    }

    public function verifyPassword(string $password, string $hash): bool
    {
        return password_verify($password, $hash);
    }
}
