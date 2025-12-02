<?php

namespace App\Controller;

use App\Service\MoviesApi;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class MovieController extends AbstractController
{
    #[Route('/movies/search', name: 'movies_search')]
    public function search(MoviesApi $moviesApi): Response
    {
        $query = 'Spider-Man';

        $results = $moviesApi->searchTitles($query);

        return $this->render('movies/search.html.twig', [
            'query'   => $query,
            'results' => $results,
        ]);
    }
}
