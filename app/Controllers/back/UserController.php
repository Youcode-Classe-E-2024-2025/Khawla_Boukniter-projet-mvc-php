<?php

namespace App\Controllers\Back;

use App\Core\Controller;
use App\Core\Auth;
use App\Models\User;

class UserController extends Controller
{
    private $auth;
    private $userModel;

    public function __construct()
    {
        parent::__construct();
        $this->auth = new Auth();
        $this->userModel = new User();

        if (!$this->auth->isAdmin()) {
            header('Location: /login');
            exit;
        }
    }

    public function index()
    {
        if ($this->session->get('user_role') !== 'admin') {
            $this->logger->info('Admin accessed users list');
            header('Location: /');
            exit;
        }

        $users = $this->userModel->findAll();
        return $this->render('back/user.twig', [
            'users' => $users,
        ]);
    }
}
