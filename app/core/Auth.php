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

    /**
     * Creates a new user account
     * 
     * @param string $username User's chosen username
     * @param string $email User's email address
     * @param string $password User's password
     * @return bool Success status of signup operation
     */
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


    /**
     * Authenticates user and creates session
     * 
     * @param string $email User's email address
     * @param string $password User's password
     * @return bool Success status of login operation
     */
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

    /**
     * Checks if user is currently logged in
     * 
     * @return bool User's login status
     */
    public function isLoggedIn(): bool
    {
        return $this->session->get('user_id') !== null;
    }

    /**
     * Checks if current user has admin role
     * 
     * @return bool User's admin status
     */
    public function isAdmin(): bool
    {
        return $this->session->get('user_role') === 'admin';
    }

    /**
     * Retrieves current logged-in user's data
     * 
     * @return array|null User data or null if not logged in
     */
    public function getCurrentUser()
    {
        if ($this->isLoggedIn()) {
            return $this->db->find("SELECT * FROM users WHERE id = ?", [$this->session->get('user_id')]);
        }
        return null;
    }

    /**
     * Terminates user session and logs out
     * 
     * @return void
     */
    public function logout(): void
    {
        $this->session->destroy();
    }
}
