<?php

namespace App\Core;

class Auth
{
    private $db;
    private $session;
    private $security;
    private $validator;

    public function __construct()
    {
        $this->db = Database::getInstance();
        $this->session = new Session();
        $this->security = new Security();
        $this->validator = new Validator();
    }

    public function signup(string $username, string $email, string $password): bool
    {

        if ($this->validator->validate(['email' => $email, 'password' => $password], [
            'email' => ['required', 'email'],
            'password' => ['required']
        ])) {
            $hashedPassword = $this->security->hashPassword($password);

            $this->db->query(
                "INSERT INTO users (username, email, password, role) VALUES (?, ?, ?, 'user')",
                [$username, $email, $hashedPassword]
            );

            return true;
        }
        return false;
    }

    public function login(string $email, string $password): bool
    {
        $user = $this->db->find("SELECT * FROM users WHERE email = ?", [$email]);

        if ($user && $this->security->verifyPassword($password, $user['password'])) {
            $this->session->set('user_id', $user['id']);
            $this->session->set('user_role', $user['role']);

            if ($user['role'] === 'admin') {
                header('Location: /admin');
            } else {
                header('Location: /articles');
            }
            exit;
        }
        return false;
    }

    public function isLoggedIn(): bool
    {
        return $this->session->get('user_id') !== null;
    }

    public function isAdmin(): bool
    {
        return $this->session->get('user_role') === 'admin';
    }

    public function getCurrentUser()
    {
        if ($this->isLoggedIn()) {
            return $this->db->find("SELECT * FROM users WHERE id = ?", [$this->session->get('user_id')]);
        }
        return null;
    }

    public function logout(): void
    {
        $this->session->destroy();
    }
}
