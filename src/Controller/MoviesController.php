<?php

namespace App\Controller;

use App\Service\MoviesApi;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class MoviesController extends AbstractController
{
    public function __construct(
        private MoviesApi $moviesApi,
    ) {}

    #[Route('/movies', name: 'app_movies')]
    public function index(): Response
    {
        $popularMovies = $this->moviesApi->getPopularMovies(limit: 50);
        if (empty($popularMovies)) {
            $popularMovies = $this->moviesApi->getTopBoxOffice(limit: 50);
        }
        if (empty($popularMovies)) {
            $popularMovies = $this->moviesApi->getTopRated(limit: 50, list: 'top_rated_250');
        }

        $topRatedMovies = $this->moviesApi->getTopRated(limit: 50, list: 'top_rated_250');
        if (empty($topRatedMovies)) {
            $topRatedMovies = $this->moviesApi->getTopRated(limit: 50, list: 'top_rated_english_250');
        }

        $upcomingMovies = $this->moviesApi->getUpcoming(limit: 50, titleType: 'movie');
        if (empty($upcomingMovies)) {
            $upcomingMovies = $this->moviesApi->getTopRated(limit: 50, list: 'top_boxoffice_last_weekend_10');
        }

        return $this->render('movies/index.html.twig', [
            'popularMovies' => $popularMovies,
            'topRatedMovies' => $topRatedMovies,
            'upcomingMovies' => $upcomingMovies,
        ]);
    }
}
