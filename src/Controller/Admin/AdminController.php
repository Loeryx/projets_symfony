<?php

namespace App\Controller\Admin;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

#[Route(path: '/admin')]
class AdminController extends AbstractController {
    
    #[Route(path: '/', name:'page_admin')]
    public function homepage() {
        return $this->render('admin/admin.html.twig');
    }

    #[Route(path: '/add_movies', name:'admin_add_movies')]
    public function admin_add_movies() {
        return $this->render('admin/admin_add_films.html.twig');
    }

    #[Route(path: '/movies', name:'admin_movies')]
    public function admin_movies() {
        return $this->render('admin/admin_films.html.twig');
    }

    #[Route(path: '/admin/users', name:'page_users')]
    public function admin_users() {
        return $this->render('admin/admin_users.html.twig');
    }
}

