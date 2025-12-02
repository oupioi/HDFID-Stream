<?php

namespace App\Service;

use App\Exception\MoviesApiException;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class MoviesApi
{
    public function __construct(
        private HttpClientInterface $moviesApiClient,
    ) {}

    public function searchTitles(string $query, bool $exact = true, string $titleType = 'movie'): array
    {
        $path = "titles/search/title/{$query}";
        $response = $this->moviesApiClient->request('GET', $path, [
            'query' => [
                'exact' => $exact ? 'true' : 'false',
                'titleType' => $titleType,
            ],
        ]);

        if (200 !== $response->getStatusCode()) {
            throw new MoviesApiException(
                sprintf(
                    'Failed to search titles. API responded with status code %d: %s',
                    $response->getStatusCode(),
                    $response->getContent(false)
                )
            );
        }

        $data = $response->toArray();
        return $data['results'] ?? [];
    }

    public function getPopularMovies(int $limit = 10, int $page = 1, string $info = 'base_info'): array
    {
        $response = $this->moviesApiClient->request('GET', 'titles', [
            'query' => [
                'list' => 'most_pop_movies',
                'limit' => min($limit, 50),
                'page' => $page,
                'info' => $info,
            ],
        ]);

        if (200 !== $response->getStatusCode()) {
            throw new MoviesApiException(
                sprintf(
                    'Failed to fetch popular movies. API responded with status code %d: %s',
                    $response->getStatusCode(),
                    $response->getContent(false)
                )
            );
        }

        $data = $response->toArray();
        return $data['results'] ?? [];
    }

    public function getTopBoxOffice(int $limit = 10, int $page = 1, string $info = 'base_info'): array
    {
        $response = $this->moviesApiClient->request('GET', 'titles', [
            'query' => [
                'list' => 'top_boxoffice_200',
                'limit' => min($limit, 50),
                'page' => $page,
                'info' => $info,
            ],
        ]);

        if (200 !== $response->getStatusCode()) {
            throw new MoviesApiException(
                sprintf(
                    'Failed to fetch top box office movies. API responded with status code %d: %s',
                    $response->getStatusCode(),
                    $response->getContent(false)
                )
            );
        }

        $data = $response->toArray();
        return $data['results'] ?? [];
    }

    public function getPopularSeries(int $limit = 10, int $page = 1, string $info = 'base_info'): array
    {
        $response = $this->moviesApiClient->request('GET', 'titles', [
            'query' => [
                'list' => 'most_pop_series',
                'limit' => min($limit, 50),
                'page' => $page,
                'info' => $info,
            ],
        ]);

        if (200 !== $response->getStatusCode()) {
            throw new MoviesApiException(
                sprintf(
                    'Failed to fetch popular series. API responded with status code %d: %s',
                    $response->getStatusCode(),
                    $response->getContent(false)
                )
            );
        }

        $data = $response->toArray();
        return $data['results'] ?? [];
    }

    public function getTopRated(int $limit = 10, int $page = 1, string $list = 'top_rated_250', string $info = 'base_info'): array
    {
        $response = $this->moviesApiClient->request('GET', 'titles', [
            'query' => [
                'list' => $list,
                'limit' => min($limit, 50),
                'page' => $page,
                'info' => $info,
            ],
        ]);

        if (200 !== $response->getStatusCode()) {
            throw new MoviesApiException(
                sprintf(
                    'Failed to fetch top rated titles. API responded with status code %d: %s',
                    $response->getStatusCode(),
                    $response->getContent(false)
                )
            );
        }

        $data = $response->toArray();
        return $data['results'] ?? [];
    }

    public function getUpcoming(int $limit = 10, int $page = 1, string $titleType = 'movie', string $info = 'base_info'): array
    {
        $response = $this->moviesApiClient->request('GET', 'titles/x/upcoming', [
            'query' => [
                'limit' => min($limit, 50),
                'page' => $page,
                'titleType' => $titleType,
                'info' => $info,
            ],
        ]);

        if (200 !== $response->getStatusCode()) {
            throw new MoviesApiException(
                sprintf(
                    'Failed to fetch upcoming titles. API responded with status code %d: %s',
                    $response->getStatusCode(),
                    $response->getContent(false)
                )
            );
        }

        $data = $response->toArray();
        return $data['results'] ?? [];
    }
}
