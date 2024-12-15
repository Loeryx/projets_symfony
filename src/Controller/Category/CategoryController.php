<?php

namespace App\Controller\Category;

//use App\Controller\Category;
use App\Entity\Category;
use App\Repository\CategoryRepository;
use App\Repository\MovieRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class CategoryController extends AbstractController
{
    #[Route('/category/{id}', 'page_category')]
    public function category(
        string $id,
        CategoryRepository $categoryRepository,
    ): Response
    {
        $category = $categoryRepository->find($id);
        return $this->render('movie/category.html.twig', [
            'category' => $category,
        ]);
    }

    #[Route('/discover', name: 'page_discover')]
    public function discover(EntityManagerInterface $entityManager, CategoryRepository $categoryRepository): Response
    {
        return $this->render('movie/discover.html.twig', [
            'categories' => $categoryRepository->findAll()
        ]);
    }
}
