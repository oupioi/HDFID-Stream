<?php

namespace App\Controller\Api;

use App\Service\MoviesApi;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/api')]
class SearchController extends AbstractController
{
    public function __construct(
        private MoviesApi $moviesApi,
    ) {}

    #[Route('/search', name: 'api_search', methods: ['GET'])]
    public function search(Request $request): JsonResponse
    {
        $query = $request->query->get('q', '');

        if (strlen($query) < 2) {
            return $this->json(['results' => []]);
        }

        $results = $this->moviesApi->searchTitles($query, exact: false);

        return $this->json([
            'results' => array_slice($results, 0, 10)
        ]);
    }
}
