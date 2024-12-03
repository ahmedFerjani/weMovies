<?php

namespace App\Controller;

use App\Service\MoviesService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class MoviesController extends AbstractController
{
    public function __construct(private MoviesService $moviesService) {}
    #[Route('/movies', name: 'homepage')]
    public function index(): Response
    {
        $genres = $this->moviesService->getGenres();
        return $this->render('index.html.twig', ['genres' => $genres['genres']]);
    }
}
