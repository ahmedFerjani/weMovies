<?php

namespace App\Controller;

use App\Service\MoviesService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;

class MoviesController extends AbstractController
{
    public function __construct(private MoviesService $moviesService) {}
    #[Route('/movies', name: 'homepage')]
    public function index(Request $request): Response
    {
        // retrieve genres if already selected
        $all = $request->request->all();
        $selectedGenres = array_key_exists('genres', $all) ? $all['genres'] : [null];

        // fetch list of movies and genres
        $movies = $this->moviesService->getMoviesByGenre(end($selectedGenres));
        $genres = $this->moviesService->getGenres();

        return $this->render('pages/movies_page/index.html.twig', [
            'movies' => $movies['results'],
            'genres' => $genres['genres']
        ]);
    }

    #[Route('/movies/{id}/videos', name: 'movie_videos', methods: ['GET'])]
    public function getMovieVideos(int $id): Response
    {
        $videos = $this->moviesService->getMovieVideos($id);

        return $this->json($videos);
    }
}
