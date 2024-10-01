<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class AuthController extends AbstractController
{
    #[Route('/auth', name: 'page_login')]
    public function index(): Response
    {
        return $this->render('auth/login.html.twig');
    }

    #[Route(path: '/auth/register', name:'page_register')]
    public function login() {
        return $this->render('auth/register.html.twig');
    }

    #[Route(path: '/auth/forgot-password', name:'page_forgot_password')]
    public function forgotPassword() {
        return $this->render('auth/forgot.html.twig');
    }

    #[Route(path: '/auth/reset-password', name:'page_reset_password')]
    public function resetPassword() {
        return $this->render('auth/reset.html.twig');
    }

    #[Route(path: '/auth/abonnements', name:'page_abonnements')]
    public function abonnements() {
        return $this->render('auth/abonnements.html.twig');
    }

}
