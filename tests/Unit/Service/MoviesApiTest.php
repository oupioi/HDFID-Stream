<?php

namespace App\Tests\Unit\Service;

use App\Service\MoviesApi;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Contracts\HttpClient\ResponseInterface;

class MoviesApiTest extends TestCase
{
    private MockObject&HttpClientInterface $httpClient;
    private MoviesApi $moviesApi;

    protected function setUp(): void
    {
        $this->httpClient = $this->createMock(HttpClientInterface::class);
        $this->moviesApi = new MoviesApi($this->httpClient);
    }

    public function testSearchTitlesSuccess(): void
    {
        $expectedResults = [
            ['id' => 'tt1234567', 'titleText' => ['text' => 'Spider-Man']],
            ['id' => 'tt7654321', 'titleText' => ['text' => 'Spider-Man 2']],
        ];

        $response = $this->createMock(ResponseInterface::class);
        $response->method('getStatusCode')->willReturn(200);
        $response->method('toArray')->willReturn(['results' => $expectedResults]);

        $this->httpClient
            ->expects($this->once())
            ->method('request')
            ->with('GET', 'titles/search/title/Spider-Man', [
                'query' => [
                    'exact' => 'true',
                    'titleType' => 'movie',
                ],
            ])
            ->willReturn($response);

        $results = $this->moviesApi->searchTitles('Spider-Man');

        $this->assertCount(2, $results);
        $this->assertEquals('tt1234567', $results[0]['id']);
    }

    public function testSearchTitlesWithExactFalse(): void
    {
        $response = $this->createMock(ResponseInterface::class);
        $response->method('getStatusCode')->willReturn(200);
        $response->method('toArray')->willReturn(['results' => []]);

        $this->httpClient
            ->expects($this->once())
            ->method('request')
            ->with('GET', 'titles/search/title/Batman', [
                'query' => [
                    'exact' => 'false',
                    'titleType' => 'tvSeries',
                ],
            ])
            ->willReturn($response);

        $results = $this->moviesApi->searchTitles('Batman', exact: false, titleType: 'tvSeries');

        $this->assertIsArray($results);
    }

    public function testSearchTitlesThrowsExceptionOnError(): void
    {
        $response = $this->createMock(ResponseInterface::class);
        $response->method('getStatusCode')->willReturn(500);
        $response->method('getContent')->willReturn('Internal Server Error');

        $this->httpClient
            ->method('request')
            ->willReturn($response);

        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('Failed to search titles');

        $this->moviesApi->searchTitles('Test');
    }

    public function testGetPopularMoviesSuccess(): void
    {
        $expectedResults = [
            ['id' => 'tt1234567', 'titleText' => ['text' => 'Popular Movie 1']],
            ['id' => 'tt7654321', 'titleText' => ['text' => 'Popular Movie 2']],
        ];

        $response = $this->createMock(ResponseInterface::class);
        $response->method('getStatusCode')->willReturn(200);
        $response->method('toArray')->willReturn(['results' => $expectedResults]);

        $this->httpClient
            ->expects($this->once())
            ->method('request')
            ->with('GET', 'titles', [
                'query' => [
                    'list' => 'most_pop_movies',
                    'limit' => 10,
                    'page' => 1,
                    'info' => 'base_info',
                ],
            ])
            ->willReturn($response);

        $results = $this->moviesApi->getPopularMovies();

        $this->assertCount(2, $results);
    }

    public function testGetPopularMoviesWithCustomParams(): void
    {
        $response = $this->createMock(ResponseInterface::class);
        $response->method('getStatusCode')->willReturn(200);
        $response->method('toArray')->willReturn(['results' => []]);

        $this->httpClient
            ->expects($this->once())
            ->method('request')
            ->with('GET', 'titles', [
                'query' => [
                    'list' => 'most_pop_movies',
                    'limit' => 20,
                    'page' => 2,
                    'info' => 'extended_info',
                ],
            ])
            ->willReturn($response);

        $this->moviesApi->getPopularMovies(limit: 20, page: 2, info: 'extended_info');
    }

    public function testGetPopularMoviesLimitCappedAt50(): void
    {
        $response = $this->createMock(ResponseInterface::class);
        $response->method('getStatusCode')->willReturn(200);
        $response->method('toArray')->willReturn(['results' => []]);

        $this->httpClient
            ->expects($this->once())
            ->method('request')
            ->with('GET', 'titles', [
                'query' => [
                    'list' => 'most_pop_movies',
                    'limit' => 50,
                    'page' => 1,
                    'info' => 'base_info',
                ],
            ])
            ->willReturn($response);

        $this->moviesApi->getPopularMovies(limit: 100);
    }

    public function testGetTopBoxOfficeSuccess(): void
    {
        $expectedResults = [
            ['id' => 'tt1234567', 'titleText' => ['text' => 'Box Office Hit']],
        ];

        $response = $this->createMock(ResponseInterface::class);
        $response->method('getStatusCode')->willReturn(200);
        $response->method('toArray')->willReturn(['results' => $expectedResults]);

        $this->httpClient
            ->expects($this->once())
            ->method('request')
            ->with('GET', 'titles', [
                'query' => [
                    'list' => 'top_boxoffice_200',
                    'limit' => 15,
                    'page' => 1,
                    'info' => 'base_info',
                ],
            ])
            ->willReturn($response);

        $results = $this->moviesApi->getTopBoxOffice(limit: 15);

        $this->assertCount(1, $results);
    }

    public function testGetTopBoxOfficeThrowsExceptionOnError(): void
    {
        $response = $this->createMock(ResponseInterface::class);
        $response->method('getStatusCode')->willReturn(404);
        $response->method('getContent')->willReturn('Not Found');

        $this->httpClient->method('request')->willReturn($response);

        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('Failed to fetch top box office movies');

        $this->moviesApi->getTopBoxOffice();
    }

    public function testGetPopularSeriesSuccess(): void
    {
        $expectedResults = [
            ['id' => 'tt9999999', 'titleText' => ['text' => 'Popular Series']],
        ];

        $response = $this->createMock(ResponseInterface::class);
        $response->method('getStatusCode')->willReturn(200);
        $response->method('toArray')->willReturn(['results' => $expectedResults]);

        $this->httpClient
            ->expects($this->once())
            ->method('request')
            ->with('GET', 'titles', [
                'query' => [
                    'list' => 'most_pop_series',
                    'limit' => 10,
                    'page' => 1,
                    'info' => 'base_info',
                ],
            ])
            ->willReturn($response);

        $results = $this->moviesApi->getPopularSeries();

        $this->assertCount(1, $results);
    }

    public function testGetPopularSeriesThrowsExceptionOnError(): void
    {
        $response = $this->createMock(ResponseInterface::class);
        $response->method('getStatusCode')->willReturn(503);
        $response->method('getContent')->willReturn('Service Unavailable');

        $this->httpClient->method('request')->willReturn($response);

        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('Failed to fetch popular series');

        $this->moviesApi->getPopularSeries();
    }

    public function testGetTopRatedSuccess(): void
    {
        $expectedResults = [
            ['id' => 'tt0111161', 'titleText' => ['text' => 'The Shawshank Redemption']],
        ];

        $response = $this->createMock(ResponseInterface::class);
        $response->method('getStatusCode')->willReturn(200);
        $response->method('toArray')->willReturn(['results' => $expectedResults]);

        $this->httpClient
            ->expects($this->once())
            ->method('request')
            ->with('GET', 'titles', [
                'query' => [
                    'list' => 'top_rated_250',
                    'limit' => 10,
                    'page' => 1,
                    'info' => 'base_info',
                ],
            ])
            ->willReturn($response);

        $results = $this->moviesApi->getTopRated();

        $this->assertCount(1, $results);
    }

    public function testGetTopRatedWithCustomList(): void
    {
        $response = $this->createMock(ResponseInterface::class);
        $response->method('getStatusCode')->willReturn(200);
        $response->method('toArray')->willReturn(['results' => []]);

        $this->httpClient
            ->expects($this->once())
            ->method('request')
            ->with('GET', 'titles', [
                'query' => [
                    'list' => 'top_rated_series_250',
                    'limit' => 25,
                    'page' => 1,
                    'info' => 'base_info',
                ],
            ])
            ->willReturn($response);

        $this->moviesApi->getTopRated(limit: 25, list: 'top_rated_series_250');
    }

    public function testGetTopRatedThrowsExceptionOnError(): void
    {
        $response = $this->createMock(ResponseInterface::class);
        $response->method('getStatusCode')->willReturn(401);
        $response->method('getContent')->willReturn('Unauthorized');

        $this->httpClient->method('request')->willReturn($response);

        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('Failed to fetch top rated titles');

        $this->moviesApi->getTopRated();
    }

    public function testGetUpcomingSuccess(): void
    {
        $expectedResults = [
            ['id' => 'tt8888888', 'titleText' => ['text' => 'Upcoming Movie']],
        ];

        $response = $this->createMock(ResponseInterface::class);
        $response->method('getStatusCode')->willReturn(200);
        $response->method('toArray')->willReturn(['results' => $expectedResults]);

        $this->httpClient
            ->expects($this->once())
            ->method('request')
            ->with('GET', 'titles/x/upcoming', [
                'query' => [
                    'limit' => 10,
                    'page' => 1,
                    'titleType' => 'movie',
                    'info' => 'base_info',
                ],
            ])
            ->willReturn($response);

        $results = $this->moviesApi->getUpcoming();

        $this->assertCount(1, $results);
    }

    public function testGetUpcomingWithTvSeries(): void
    {
        $response = $this->createMock(ResponseInterface::class);
        $response->method('getStatusCode')->willReturn(200);
        $response->method('toArray')->willReturn(['results' => []]);

        $this->httpClient
            ->expects($this->once())
            ->method('request')
            ->with('GET', 'titles/x/upcoming', [
                'query' => [
                    'limit' => 20,
                    'page' => 1,
                    'titleType' => 'tvSeries',
                    'info' => 'base_info',
                ],
            ])
            ->willReturn($response);

        $this->moviesApi->getUpcoming(limit: 20, titleType: 'tvSeries');
    }

    public function testGetUpcomingThrowsExceptionOnError(): void
    {
        $response = $this->createMock(ResponseInterface::class);
        $response->method('getStatusCode')->willReturn(500);
        $response->method('getContent')->willReturn('Error');

        $this->httpClient->method('request')->willReturn($response);

        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('Failed to fetch upcoming titles');

        $this->moviesApi->getUpcoming();
    }

    public function testEmptyResultsReturnEmptyArray(): void
    {
        $response = $this->createMock(ResponseInterface::class);
        $response->method('getStatusCode')->willReturn(200);
        $response->method('toArray')->willReturn([]);

        $this->httpClient->method('request')->willReturn($response);

        $results = $this->moviesApi->getPopularMovies();

        $this->assertIsArray($results);
        $this->assertEmpty($results);
    }
}
