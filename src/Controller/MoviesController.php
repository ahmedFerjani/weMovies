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

        // can be a comma (AND) or pipe (OR) separated query
        $selectedGenresAnd = implode(',', $selectedGenres);

        // fetch list of movies and genres
        $movies = $this->moviesService->getMoviesByGenre($selectedGenresAnd);
        $genres = $this->moviesService->getGenres();
        $firstMovieId = $movies['results'][0]['id'] ?? null;

        $favoriteMovieVideoKey = null;

        if ($firstMovieId) {
            $videos = $this->moviesService->getMovieVideos($firstMovieId);

            if (isset($videos['results'][0]['key'])) {
                $favoriteMovieVideoKey = $videos['results'][0]['key'];
            }
        }

        return $this->render('pages/movies_page/index.html.twig', [
            'movies' => $movies['results'],
            'genres' => $genres['genres'],
            'favoriteMovieVideoKey' => $favoriteMovieVideoKey
        ]);
    }

    #[Route('/movies/{id}/videos', name: 'movie_videos', methods: ['GET'])]
    public function getMovieVideos(int $id): Response
    {
        $videos = $this->moviesService->getMovieVideos($id);

        return $this->json($videos);
    }
}
