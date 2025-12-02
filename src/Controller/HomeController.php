<?php

namespace App\Controller;

use App\Service\MoviesApi;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class HomeController extends AbstractController
{
    public function __construct(
        private MoviesApi $moviesApi,
    ) {}

    #[Route('/home', name: 'app_home')]
    public function index(): Response
    {
        $popularMovies = $this->moviesApi->getTopBoxOffice(limit: 20);
        if (empty($popularMovies)) {
            $popularMovies = $this->moviesApi->getPopularMovies(limit: 20);
        }
        if (empty($popularMovies)) {
            $popularMovies = $this->moviesApi->getTopRated(limit: 20, list: 'top_rated_250');
        }

        $popularSeries = $this->moviesApi->getPopularSeries(limit: 20);
        if (empty($popularSeries)) {
            $popularSeries = $this->moviesApi->getTopRated(limit: 20, list: 'top_rated_series_250');
        }

        $topRated = $this->moviesApi->getTopRated(limit: 20);
        if (empty($topRated)) {
            $topRated = $this->moviesApi->getTopRated(limit: 20, list: 'top_rated_english_250');
        }

        $upcoming = $this->moviesApi->getUpcoming(limit: 20);
        if (empty($upcoming)) {
            $upcoming = $this->moviesApi->getUpcoming(limit: 20, titleType: 'tvSeries');
        }

        return $this->render('home/index.html.twig', [
            'popularMovies' => $popularMovies,
            'popularSeries' => $popularSeries,
            'topRated' => $topRated,
            'upcoming' => $upcoming,
        ]);
    }
}
