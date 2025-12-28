<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;

class HomeController extends Controller
{
    public function index(): void
    {
        echo $this->render('home/index', [
            'title' => 'Головна сторінка',
            'siteName' => getenv('APP_NAME') ?: 'CMS4Blog',
        ]);
    }

    public function about(): void
    {
        echo $this->render('home/about', [
            'title' => 'Про систему',
            'siteName' => getenv('APP_NAME') ?: 'CMS4Blog',
        ]);
    }

    public function contact(): void
    {
        echo $this->render('home/contact', [
            'title' => 'Контакти',
            'siteName' => getenv('APP_NAME') ?: 'CMS4Blog',
        ]);
    }
}
