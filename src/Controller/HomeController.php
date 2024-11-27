<?php
namespace App\Controller;

use App\Repository\MediaRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController {
    
    #[Route(path: '/', name: 'page_home')]
    public function home(
        MediaRepository $mediaRepository
    ) : Response {
        $medias = $mediaRepository->findPopularMedia(9);
        return $this->render('index.html.twig', [
            'medias' => $medias
        ]);
    }
}