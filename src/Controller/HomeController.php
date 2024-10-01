<?php
namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;  
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController {
    
    #[Route(path: '/', name: 'page_home')]
    public function accueil() {
        return $this->render(view: 'index.html.twig');
    }
}