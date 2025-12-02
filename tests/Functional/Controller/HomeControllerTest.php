<?php

namespace App\Tests\Functional\Controller;

use App\Exception\MoviesApiException;
use App\Service\MoviesApi;
use App\Tests\Functional\AuthenticatedWebTestCaseTrait;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class HomeControllerTest extends WebTestCase
{
    use AuthenticatedWebTestCaseTrait;

    public function testHomePageRequiresAuthentication(): void
    {
        $client = static::createClient();

        $client->request('GET', '/home');

        $this->assertResponseRedirects();
    }

    public function testHomePageReturnsSuccessfulResponseForAuthenticatedUser(): void
    {
        $client = static::createAuthenticatedClient();

        $moviesApiMock = $this->createMock(MoviesApi::class);
        $moviesApiMock->method('getTopBoxOffice')->willReturn($this->getMockMovies());
        $moviesApiMock->method('getPopularMovies')->willReturn($this->getMockMovies());
        $moviesApiMock->method('getPopularSeries')->willReturn($this->getMockSeries());
        $moviesApiMock->method('getTopRated')->willReturn($this->getMockMovies());
        $moviesApiMock->method('getUpcoming')->willReturn($this->getMockMovies());

        static::getContainer()->set(MoviesApi::class, $moviesApiMock);

        $client->request('GET', '/home');

        $this->assertResponseIsSuccessful();
    }

    public function testHomePageHandlesEmptyResults(): void
    {
        $client = static::createAuthenticatedClient();

        $moviesApiMock = $this->createMock(MoviesApi::class);
        $moviesApiMock->method('getTopBoxOffice')->willReturn([]);
        $moviesApiMock->method('getPopularMovies')->willReturn([]);
        $moviesApiMock->method('getPopularSeries')->willReturn([]);
        $moviesApiMock->method('getTopRated')->willReturn([]);
        $moviesApiMock->method('getUpcoming')->willReturn([]);

        static::getContainer()->set(MoviesApi::class, $moviesApiMock);

        $client->request('GET', '/home');

        $this->assertResponseIsSuccessful();
    }

    public function testHomePageHandlesApiException(): void
    {
        $client = static::createAuthenticatedClient();

        $moviesApiMock = $this->createMock(MoviesApi::class);
        $moviesApiMock->method('getTopBoxOffice')
            ->willThrowException(new MoviesApiException('API Error'));

        static::getContainer()->set(MoviesApi::class, $moviesApiMock);

        $client->request('GET', '/home');

        $this->assertResponseStatusCodeSame(500);
    }

    public function testHomePageFallbackWhenPopularMoviesEmpty(): void
    {
        $client = static::createAuthenticatedClient();

        $moviesApiMock = $this->createMock(MoviesApi::class);

        $moviesApiMock->method('getTopBoxOffice')->willReturn([]);
        $moviesApiMock->method('getPopularMovies')->willReturn($this->getMockMovies());
        $moviesApiMock->method('getPopularSeries')->willReturn($this->getMockSeries());
        $moviesApiMock->method('getTopRated')->willReturn($this->getMockMovies());
        $moviesApiMock->method('getUpcoming')->willReturn($this->getMockMovies());

        static::getContainer()->set(MoviesApi::class, $moviesApiMock);

        $client->request('GET', '/home');

        $this->assertResponseIsSuccessful();
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

    private function getMockSeries(): array
    {
        return [
            [
                'id' => 'tt9999999',
                'titleText' => ['text' => 'Test Series 1'],
                'primaryImage' => ['url' => 'https://example.com/series1.jpg'],
                'ratingsSummary' => ['aggregateRating' => 9.0],
                'releaseYear' => ['year' => 2024],
            ],
        ];
    }
}
