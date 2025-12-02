<?php

namespace App\Tests\Functional\Controller;

use App\Service\MoviesApi;
use App\Tests\Functional\AuthenticatedWebTestCaseTrait;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class MovieControllerTest extends WebTestCase
{
    use AuthenticatedWebTestCaseTrait;

    public function testSearchPageRequiresAuthentication(): void
    {
        $client = static::createClient();

        $client->request('GET', '/movies/search');

        $this->assertResponseRedirects();
    }

    public function testSearchPageReturnsSuccessfulResponse(): void
    {
        $client = static::createAuthenticatedClient();

        $moviesApiMock = $this->createMock(MoviesApi::class);
        $moviesApiMock->method('searchTitles')->willReturn($this->getMockSearchResults());

        static::getContainer()->set(MoviesApi::class, $moviesApiMock);

        $client->request('GET', '/movies/search');

        $this->assertResponseIsSuccessful();
    }

    public function testSearchPageHandlesEmptyResults(): void
    {
        $client = static::createAuthenticatedClient();

        $moviesApiMock = $this->createMock(MoviesApi::class);
        $moviesApiMock->method('searchTitles')->willReturn([]);

        static::getContainer()->set(MoviesApi::class, $moviesApiMock);

        $client->request('GET', '/movies/search');

        $this->assertResponseIsSuccessful();
    }

    private function getMockSearchResults(): array
    {
        return [
            [
                'id' => 'tt0145487',
                'titleText' => ['text' => 'Spider-Man'],
                'primaryImage' => ['url' => 'https://example.com/spiderman.jpg'],
                'releaseYear' => ['year' => 2002],
            ],
            [
                'id' => 'tt0316654',
                'titleText' => ['text' => 'Spider-Man 2'],
                'primaryImage' => ['url' => 'https://example.com/spiderman2.jpg'],
                'releaseYear' => ['year' => 2004],
            ],
        ];
    }
}
