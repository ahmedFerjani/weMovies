<?php

namespace App\Service;

use Symfony\Contracts\HttpClient\HttpClientInterface;

class MoviesService
{
    private string $apiToken;

    public function __construct(
        private HttpClientInterface $httpClient,
        private string $tmdbApiToken
    ) {
        $this->apiToken = $tmdbApiToken;
    }

    private function request(string $url, array $params = []): array
    {
        $response = $this->httpClient->request('GET', "https://api.themoviedb.org/3/$url", [
            'headers' => [
                'Authorization' => 'Bearer ' . $this->apiToken,
            ],
            'query' => $params,
        ]);

        return $response->toArray();
    }

    public function getGenres(): array
    {
        return $this->request('genre/movie/list');
    }

    public function getMoviesByGenre(int $genreId): array
    {
        return $this->request('discover/movie', ['with_genres' => $genreId]);
    }

    public function getMovieDetails(int $movieId): array
    {
        return $this->request("movie/$movieId");
    }
}
