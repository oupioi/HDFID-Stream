<?php

namespace App\Controller;

use App\Service\MoviesApi;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class SerieController extends AbstractController
{
    public function __construct(
        private MoviesApi $moviesApi,
    ) {
    }

    #[Route('/series', name: 'app_series')]
    public function index(): Response
    {
        $popularSeries = $this->moviesApi->getPopularSeries(limit: 50);
        if (empty($popularSeries)) {
            $popularSeries = $this->moviesApi->getTopRated(limit: 50, list: 'top_rated_series_250');
        }

        $topRatedSeries = $this->moviesApi->getTopRated(limit: 50, list: 'top_rated_series_250');
        if (empty($topRatedSeries)) {
            $topRatedSeries = $this->moviesApi->getPopularSeries(limit: 50);
        }

        $upcomingSeries = $this->moviesApi->getUpcoming(limit: 50, titleType: 'tvSeries');
        if (empty($upcomingSeries)) {
            $upcomingSeries = $this->moviesApi->getUpcoming(limit: 50, titleType: 'tvMiniSeries');
        }

        return $this->render('series/index.html.twig', [
            'popularSeries' => $popularSeries,
            'topRatedSeries' => $topRatedSeries,
            'upcomingSeries' => $upcomingSeries,
        ]);
    }
}
