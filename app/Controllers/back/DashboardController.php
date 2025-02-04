<?php

namespace App\Controllers\Back;

use App\Core\Controller;
use App\Core\Auth;
use App\Models\User;
use App\Models\Article;

class DashboardController extends Controller
{
    private $auth;
    private $userModel;
    private $articleModel;

    public function __construct()
    {
        parent::__construct();
        $this->auth = new Auth();
        $this->userModel = new User();
        $this->articleModel = new Article();

        if (!$this->auth->isAdmin()) {
            header('Location: /login');
            exit;
        }
    }

    public function index()
    {
        if (!$this->auth->isAdmin()) {
            header('Location: /');
            exit;
        }

        $this->logger->info('Admin accessed dashboard');
        $stats = [
            'users' => count($this->userModel->findAll()),
            'articles' => count($this->articleModel->findAll())
        ];

        return $this->render('back/dashboard.twig', [
            'stats' => $stats,
            'user' => $this->auth->getCurrentUser()
        ]);
    }
}
