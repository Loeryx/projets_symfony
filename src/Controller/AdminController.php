<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AdminController extends AbstractController {
    #[Route(path: '/admin', name:'page_admin')]

    public function accueil() {
        return $this->render('admin/admin.html.twig');
    }

    #[Route(path: '/admin/add_films', name:'page_admin_add_films')]
    public function admin_add_films() {
        return $this->render('admin/admin_add_films.html.twig');
    }

    #[Route(path: '/admin/films', name:'page_admin_films')]
    public function admin_films() {
        return $this->render('admin/admin_films.html.twig');
    }

    #[Route(path: '/admin/users', name:'page_users')]
    public function admin_users() {
        return $this->render('admin/admin_users.html.twig');
    }
}

