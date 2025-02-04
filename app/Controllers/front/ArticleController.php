<?php

namespace App\Controllers\Front;

use App\Core\Controller;
use App\Core\Auth;
use App\Models\Article;
use App\Core\Validator;

class ArticleController extends Controller
{
    private $auth;
    private $articleModel;
    private $validator;

    public function __construct()
    {
        parent::__construct();
        $this->auth = new Auth();
        $this->articleModel = new Article();
        $this->validator = new Validator();

        if (!$this->auth->isLoggedIn()) {
            header('Location: /login');
            exit;
        }
    }

    public function index()
    {
        $userArticles = isset($_GET['my-articles']) ? true : false;
        $articles = $userArticles
            ? $this->articleModel->articlesByUserId($_SESSION['user_id'])
            : $this->articleModel->articlesWithUsers();

        if ($this->auth->isAdmin()) {
            return $this->render('back/article.twig', [
                'title' => $userArticles ? 'My Articles' : 'All Articles',
                'articles' => $articles,
                'showingUserArticles' => $userArticles,
                'csrf_token' => $this->security->generateCsrfToken()
            ]);
        } else {
            return $this->render('front/article.twig', [
                'title' => $userArticles ? 'My Articles' : 'All Articles',
                'articles' => $articles,
                'showingUserArticles' => $userArticles,
                'csrf_token' => $this->security->generateCsrfToken()
            ]);
        }
    }


    public function create()
    {
        if (!$this->auth->isLoggedIn()) {
            header('Location: /login');
            exit;
        }

        return $this->render('front/create.twig', [
            'csrf_token' => $this->security->generateCsrfToken(),
            'user' => $this->auth->getCurrentUser()
        ]);
    }

    public function store()
    {
        if (!$this->session->get('user_id')) {
            header('Location: /login');
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (!$this->security->validateCsrfToken($_POST['csrf_token'])) {
                $this->logger->error('Invalid CSRF token in article creation');
                return $this->render('front/create.twig', [
                    'csrf_token' => $this->security->generateCsrfToken()
                ]);
            }

            $data = $this->security->sanitizeInput($_POST);

            if (!$this->validator->validate($data, [
                'title' => ['required', 'min:3'],
                'content' => ['required', 'min:10']
            ])) {
                return $this->render('front/create.twig', [
                    'errors' => $this->validator->getErrors(),
                    'csrf_token' => $this->security->generateCsrfToken(),
                    // 'old' => $data
                ]);
            }
            $articleData = [
                'title' => $data['title'],
                'content' => $data['content'],
                'user_id' => $_SESSION['user_id'],
                'created_at' => date('Y-m-d H:i:s')
            ];

            if ($this->articleModel->create($articleData)) {
                $this->logger->info('New article created');
                header('Location: /articles');
                exit;
            }
        }


        return $this->render('front/create.twig', [
            'errors' => $this->validator->getErrors(),
            'csrf_token' => $this->security->generateCsrfToken()
        ]);
    }

    public function show($params)
    {
        if (!isset($params[0])) {
            $this->logger->error('Article ID not provided');
            header('Location: /articles');
            exit;
        }

        $article = $this->articleModel->findById($params[0]);

        if (!$article) {
            $this->logger->error('Article not found: ' . $params[0]);
            header('Location: /articles');
            exit;
        }

        return $this->render('front/show.twig', [
            'article' => $article,
            'title' => $article['title'],
            'csrf_token' => $this->security->generateCsrfToken()
        ]);
    }



    public function delete($params)
    {
        if (!$this->session->get('user_id')) {
            return $this->render('front/article.twig', [
                'errors' => ['Authentication required to delete articles'],
                'articles' => $this->articleModel->articlesWithUsers()
            ]);
        }

        $article = $this->articleModel->findById($params[0]);
        if (!$article || (!$this->auth->isAdmin() && $article['user_id'] !== $this->session->get('user_id'))) {
            return $this->render('front/article.twig', [
                'title' => 'All Articles',
                'errors' => ['You are not authorized to delete this article'],
                'articles' => $this->articleModel->articlesWithUsers()
            ]);
        }

        if ($this->articleModel->delete($params[0])) {
            $this->logger->info('Article deleted: ' . $params[0]);
            header('Location: /articles');
            exit;
        }

        return $this->render('front/article.twig', [
            'title' => 'All Articles',
            'errors' => ['Failed to delete article'],
            'articles' => $this->articleModel->articlesWithUsers()
        ]);
    }

    public function adminDelete($params)
    {
        if (!$this->auth->isAdmin()) {
            return $this->render('back/articles.twig', [
                'errors' => ['Admin access required'],
                'articles' => $this->articleModel->articlesWithUsers()
            ]);
        }

        if ($this->articleModel->delete($params[0])) {
            header('Location: /admin/articles');
            exit;
        }

        return $this->render('back/articles.twig', [
            'errors' => ['Failed to delete article'],
            'articles' => $this->articleModel->articlesWithUsers()
        ]);
    }
}
