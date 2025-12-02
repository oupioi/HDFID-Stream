<?php

namespace App\Tests\Functional\Controller;

use App\Exception\MoviesApiException;
use App\Service\MoviesApi;
use App\Tests\Functional\AuthenticatedWebTestCaseTrait;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class MoviesControllerTest extends WebTestCase
{
    use AuthenticatedWebTestCaseTrait;

    public function testMoviesPageRequiresAuthentication(): void
    {
        $client = static::createClient();

        $client->request('GET', '/movies');

        $this->assertResponseRedirects();
    }

    public function testMoviesPageReturnsSuccessfulResponse(): void
    {
        $client = static::createAuthenticatedClient();

        $moviesApiMock = $this->createMock(MoviesApi::class);
        $moviesApiMock->method('getPopularMovies')->willReturn($this->getMockMovies());
        $moviesApiMock->method('getTopBoxOffice')->willReturn($this->getMockMovies());
        $moviesApiMock->method('getTopRated')->willReturn($this->getMockMovies());
        $moviesApiMock->method('getUpcoming')->willReturn($this->getMockMovies());

        static::getContainer()->set(MoviesApi::class, $moviesApiMock);

        $client->request('GET', '/movies');

        $this->assertResponseIsSuccessful();
    }

    public function testMoviesPageHandlesEmptyPopularMovies(): void
    {
        $client = static::createAuthenticatedClient();

        $moviesApiMock = $this->createMock(MoviesApi::class);
        $moviesApiMock->method('getPopularMovies')->willReturn([]);
        $moviesApiMock->method('getTopBoxOffice')->willReturn($this->getMockMovies());
        $moviesApiMock->method('getTopRated')->willReturn($this->getMockMovies());
        $moviesApiMock->method('getUpcoming')->willReturn($this->getMockMovies());

        static::getContainer()->set(MoviesApi::class, $moviesApiMock);

        $client->request('GET', '/movies');

        $this->assertResponseIsSuccessful();
    }

    public function testMoviesPageHandlesAllEmptyResults(): void
    {
        $client = static::createAuthenticatedClient();

        $moviesApiMock = $this->createMock(MoviesApi::class);
        $moviesApiMock->method('getPopularMovies')->willReturn([]);
        $moviesApiMock->method('getTopBoxOffice')->willReturn([]);
        $moviesApiMock->method('getTopRated')->willReturn([]);
        $moviesApiMock->method('getUpcoming')->willReturn([]);

        static::getContainer()->set(MoviesApi::class, $moviesApiMock);

        $client->request('GET', '/movies');

        $this->assertResponseIsSuccessful();
    }

    public function testMoviesPageHandlesApiException(): void
    {
        $client = static::createAuthenticatedClient();

        $moviesApiMock = $this->createMock(MoviesApi::class);
        $moviesApiMock->method('getPopularMovies')
            ->willThrowException(new MoviesApiException('API Error'));

        static::getContainer()->set(MoviesApi::class, $moviesApiMock);

        $client->request('GET', '/movies');

        $this->assertResponseStatusCodeSame(500);
    }

    private function getMockMovies(): array
    {
        return [
            [
                'id' => 'tt1234567',
                'titleText' => ['text' => 'Test Movie 1'],
                'primaryImage' => ['url' => 'https://example.com/image1.jpg'],
                'releaseYear' => ['year' => 2024],
                'ratingsSummary' => ['aggregateRating' => 8.5],
            ],
            [
                'id' => 'tt7654321',
                'titleText' => ['text' => 'Test Movie 2'],
                'primaryImage' => ['url' => 'https://example.com/image2.jpg'],
                'releaseYear' => ['year' => 2023],
                'ratingsSummary' => ['aggregateRating' => 7.2],
            ],
        ];
    }
}
