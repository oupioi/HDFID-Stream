<?php

namespace App\Tests\Functional\Controller;

use App\Exception\MoviesApiException;
use App\Service\MoviesApi;
use App\Tests\Functional\AuthenticatedWebTestCaseTrait;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class SerieControllerTest extends WebTestCase
{
    use AuthenticatedWebTestCaseTrait;

    public function testSeriesPageRequiresAuthentication(): void
    {
        $client = static::createClient();

        $client->request('GET', '/series');

        $this->assertResponseRedirects();
    }

    public function testSeriesPageReturnsSuccessfulResponse(): void
    {
        $client = static::createAuthenticatedClient();

        $moviesApiMock = $this->createMock(MoviesApi::class);
        $moviesApiMock->method('getPopularSeries')->willReturn($this->getMockSeries());
        $moviesApiMock->method('getTopRated')->willReturn($this->getMockSeries());
        $moviesApiMock->method('getUpcoming')->willReturn($this->getMockSeries());

        static::getContainer()->set(MoviesApi::class, $moviesApiMock);

        $client->request('GET', '/series');

        $this->assertResponseIsSuccessful();
    }

    public function testSeriesPageHandlesEmptyPopularSeries(): void
    {
        $client = static::createAuthenticatedClient();

        $moviesApiMock = $this->createMock(MoviesApi::class);
        $moviesApiMock->method('getPopularSeries')->willReturn([]);
        $moviesApiMock->method('getTopRated')->willReturn($this->getMockSeries());
        $moviesApiMock->method('getUpcoming')->willReturn($this->getMockSeries());

        static::getContainer()->set(MoviesApi::class, $moviesApiMock);

        $client->request('GET', '/series');

        $this->assertResponseIsSuccessful();
    }

    public function testSeriesPageHandlesAllEmptyResults(): void
    {
        $client = static::createAuthenticatedClient();

        $moviesApiMock = $this->createMock(MoviesApi::class);
        $moviesApiMock->method('getPopularSeries')->willReturn([]);
        $moviesApiMock->method('getTopRated')->willReturn([]);
        $moviesApiMock->method('getUpcoming')->willReturn([]);

        static::getContainer()->set(MoviesApi::class, $moviesApiMock);

        $client->request('GET', '/series');

        $this->assertResponseIsSuccessful();
    }

    public function testSeriesPageHandlesApiException(): void
    {
        $client = static::createAuthenticatedClient();

        $moviesApiMock = $this->createMock(MoviesApi::class);
        $moviesApiMock->method('getPopularSeries')
            ->willThrowException(new MoviesApiException('API Error'));

        static::getContainer()->set(MoviesApi::class, $moviesApiMock);

        $client->request('GET', '/series');

        // Controller should handle the exception gracefully
        $this->assertResponseStatusCodeSame(500);
    }

    private function getMockSeries(): array
    {
        return [
            [
                'id' => 'tt9999999',
                'titleText' => ['text' => 'Test Series 1'],
                'primaryImage' => ['url' => 'https://example.com/series1.jpg'],
                'releaseYear' => ['year' => 2024],
                'ratingsSummary' => ['aggregateRating' => 9.0],
            ],
            [
                'id' => 'tt8888888',
                'titleText' => ['text' => 'Test Series 2'],
                'primaryImage' => ['url' => 'https://example.com/series2.jpg'],
                'releaseYear' => ['year' => 2023],
                'ratingsSummary' => ['aggregateRating' => 8.3],
            ],
        ];
    }
}
