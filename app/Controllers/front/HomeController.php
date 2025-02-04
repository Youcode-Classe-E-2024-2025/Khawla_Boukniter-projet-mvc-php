<?php

namespace App\Controllers\Front;

use App\Core\Controller;

class HomeController extends Controller
{
    public function index()
    {
        return $this->render('front/home.twig', [
            'title' => 'Welcome',
            'content' => 'This is the homepage',
        ]);
    }
}
