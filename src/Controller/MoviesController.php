<?php

namespace App\Controller;

use App\Service\MoviesService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;

class MoviesController extends AbstractController
{
    public function __construct(private MoviesService $moviesService) {}
    #[Route('/', name: 'homepage', methods: ['GET', 'POST'])]
    public function index(Request $request): Response
    {
        // Retrieving genres if already selected
        $all = $request->request->all();
        $selectedGenres = array_key_exists('genres', $all) ? $all['genres'] : [null];

        // Transforming the search query: can be separated by comma (AND) or pipe (OR)
        $selectedGenresAnd = implode(',', $selectedGenres);

        // fetch list of movies and genres
        $movies = $this->moviesService->getMoviesByGenre($selectedGenresAnd);
        $genres = $this->moviesService->getGenres();
        $firstMovieId = $movies['results'][0]['id'] ?? null;

        $favoriteMovieVideoKey = null;

        // Setting up the default movie banner (video key)
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
    public function getMovieVideos(string $id): Response
    {
        $videos = $this->moviesService->getMovieVideos($id);

        return $this->json($videos);
    }

    #[Route('/movies/autocomplete', name: 'movies_autocomplete', methods: ['GET'])]
    public function autocomplete(Request $request): Response
    {
        $query = $request->query->get('query');

        if (!$query) {
            return $this->json(['results' => []]);
        }

        $movies = $this->moviesService->searchMovies($query);

        return $this->json($movies);
    }

    #[Route('/movies/{movieId}', name: 'get_movie_details', methods: ['GET'])]
    public function getMovieDetails(string $movieId): JsonResponse
    {
        $movieDetails = $this->moviesService->getMovieDetails($movieId);

        return $this->json($movieDetails);
    }


    #[Route('/movies/{id}/rate', name: 'rate_movie', methods: ['POST'])]
    public function rateMovie(int $id, Request $request): Response
    {
        $data = json_decode($request->getContent(), true);
        $rating = $data['rating'] ?? null;

        if ($rating === null || $rating < 0 || $rating > 10) {
            return $this->json(['success' => false, 'message' => 'Invalid rating.'], Response::HTTP_BAD_REQUEST);
        }

        $result = $this->moviesService->rateMovie($id, $rating);

        if ($result['status_code'] === 1) {
            return $this->json(['success' => true]);
        } else {
            return $this->json(['success' => false, 'message' => 'Failed to rate movie.'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
