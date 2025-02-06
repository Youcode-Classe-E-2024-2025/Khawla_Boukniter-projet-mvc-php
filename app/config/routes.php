<?php

use App\Core\Router;
use App\Controllers\Front\HomeController;
use App\Controllers\Front\ArticleController;
use App\Controllers\Back\UserController;
use App\Controllers\Back\DashboardController;
use App\Controllers\Front\AuthController;

$router = new Router();
$router->addRoute('GET', '/', [HomeController::class, 'index']);



// Admin routes
$router->addRoute('GET', '/admin/users', [UserController::class, 'index']);
$router->addRoute('GET', '/admin', [DashboardController::class, 'index']);
$router->addRoute('GET', '/admin/articles', [ArticleController::class, 'index']);
$router->addRoute('POST', '/admin/articles/delete/{id}', [ArticleController::class, 'adminDelete']);


$router->addRoute('GET', '/articles', [ArticleController::class, 'index']);
$router->addRoute('GET', '/article/{id}', [ArticleController::class, 'show']);
$router->addRoute('GET', '/articles/create', [ArticleController::class, 'create']);
$router->addRoute('POST', '/articles/create', [ArticleController::class, 'store']);
$router->addRoute('POST', '/articles/delete/{id}', [ArticleController::class, 'delete']);


// Auth routes
$router->addRoute('GET', '/login', [AuthController::class, 'showLogin']);
$router->addRoute('POST', '/login', [AuthController::class, 'login']);
$router->addRoute('GET', '/signup', [AuthController::class, 'showSignup']);
$router->addRoute('POST', '/signup', [AuthController::class, 'signup']);
$router->addRoute('GET', '/admin', [DashboardController::class, 'index']);

$router->addRoute('GET', '/logout', [AuthController::class, 'logout']);
