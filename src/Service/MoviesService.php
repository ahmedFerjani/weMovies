<?php

namespace App\Service;

use Symfony\Contracts\HttpClient\HttpClientInterface;

class MoviesService
{
    private string $apiToken;

    public function __construct(
        private HttpClientInterface $httpClient,
        private string $tmdbApiToken,
    ) {
        $this->apiToken = $tmdbApiToken;
    }

    private function request(string $url, array $params = [], string $method = 'GET'): array
    {
        $options = [
            'headers' => [
                'Authorization' => 'Bearer ' . $this->apiToken,
            ],
            'query' => ['language' => 'fr', ...$params],
        ];

        // send data in body if it's a post request
        if ($method === 'POST') {
            $options['json'] = $params;
        }

        $response = $this->httpClient->request($method, "https://api.themoviedb.org/3/$url", $options);

        return $response->toArray();
    }

    /**
     * Get Genres
     * @return array The response from the TMDb API
     */
    public function getGenres(): array
    {
        return $this->request('genre/movie/list');
    }

    /**
     * Get Movies by genre
     * @param string $genreId list of genre ids separated by comma or pipe
     * @return array The response from the TMDb API
     */
    public function getMoviesByGenre(string $genreId = null): array
    {
        return $this->request(
            'discover/movie',
            ['with_genres' => $genreId, 'sort_by' => 'popularity.desc']
        );
    }

    /**
     * Get Movie videos
     * @param int $movieId The movie ID
     * @return array The response from the TMDb API
     */
    public function getMovieVideos(int $movieId): array
    {
        return $this->request("movie/$movieId/videos");
    }

    /**
     * Rate a movie
     * @param int $movieId The movie ID
     * @param float $rating The rating value (0 to 10)
     * @return array The response from the TMDb API
     */
    public function rateMovie(int $movieId, float $rating): array
    {
        $url = "movie/$movieId/rating";
        $params = [
            'value' => $rating
        ];

        return $this->request($url, $params, 'POST'); // Send a POST request to rate the movie
    }
}
