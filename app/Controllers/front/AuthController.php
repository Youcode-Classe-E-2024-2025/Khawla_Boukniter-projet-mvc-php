<?php

namespace App\Controllers\Front;

use App\Core\Controller;
use App\Core\Auth;
use App\Core\Validator;

class AuthController extends Controller
{
    private $auth;
    private $validator;

    public function __construct()
    {
        parent::__construct();
        $this->auth = new Auth();
        $this->validator = new Validator();
    }

    public function showLogin()
    {
        $token = $this->security->generateCsrfToken();
        return $this->render('front/login.twig', [
            'csrf_token' => $token
        ]);
    }

    public function login()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (!$this->security->validateCsrfToken($_POST['csrf_token'])) {
                $this->logger->error('Invalid CSRF token in login attempt');
                return $this->render('front/login.twig', [
                    'error' => 'Invalid request',
                    'csrf_token' => $this->security->generateCsrfToken()
                ]);
            }

            $data = $this->security->sanitizeInput($_POST);

            if (!$this->security->validateEmail($data['email'])) {
                return $this->render('front/login.twig', [
                    'error' => 'Invalid email format',
                    'csrf_token' => $this->security->generateCsrfToken()
                ]);
            }

            if ($this->validator->validate($data, [
                'email' => ['required', 'email'],
                'password' => ['required']
            ])) {

                if ($this->auth->login($data['email'], $data['password'])) {
                    $this->logger->info('User logged in successfully: ' . $data['email']);
                    if ($this->auth->isAdmin()) {
                        header('Location: /admin');
                    } else {
                        header('Location: /articles');
                    }
                    exit;
                }

                $this->logger->error('Failed login attempt for email: ' . $data['email']);
            }
        }

        return $this->render('front/login.twig', [
            'csrf_token' => $this->security->generateCsrfToken(),
        ]);
    }

    public function showSignup()
    {
        return $this->render('front/signup.twig', [
            'csrf_token' => $this->security->generateCsrfToken()
        ]);
    }

    public function signup()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (!$this->security->validateCsrfToken($_POST['csrf_token'])) {
                $this->logger->error('Invalid CSRF token in signup attempt');
                return $this->render('front/signup.twig', [
                    'error' => 'Invalid request'
                ]);
            }

            $data = $this->security->sanitizeInput($_POST);

            if ($this->validator->validate($data, [
                'username' => ['required', 'min:3'],
                'email' => ['required', 'email'],
                'password' => ['required', 'min:6']
            ])) {
                if ($this->auth->signup($data['username'], $data['email'], $data['password'])) {
                    $this->logger->info('New user registered: ' . $data['email']);
                    header('Location: /login');
                    exit;
                }
            }
        }

        return $this->render('front/signup.twig', [
            'csrf_token' => $this->security->generateCsrfToken(),
            'errors' => $this->validator->getErrors()
        ]);
    }

    public function logout()
    {
        $this->auth->logout();
        $this->session->destroy();
        $this->logger->info('User logged out');
        header('Location: /');
        exit;
    }
}
