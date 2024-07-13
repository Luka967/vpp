<?php

namespace Skop\Controllers;

use Skop\Core\Controller;
use Skop\Core\ErrorPageException;
use Skop\Models\MovieModel;
use Skop\Models\RepertoireModel;
use Skop\Models\TheaterSeatTypeModel;

class HomeController extends Controller
{
    public function showLanding()
    {
        $movies = RepertoireModel::tryGenerate()['movies'];
        $moviesList = [];
        foreach ($movies as $movie)
            $moviesList[] = $movie;

        $heroMovie = $moviesList[random_int(0, count($moviesList) - 1)];
        $this->render('index.twig', [
            'heroMovie' => $heroMovie,
            'movies' => $moviesList
        ]);
    }

    public function showMovie()
    {
        $movie = MovieModel::withId($this->req->query['id']);
        if ($movie == null)
            throw new ErrorPageException(SKOP_ERROR_UNKNOWN_MOVIE);
        $this->render('view/movie.twig', [
            'movie' => $movie,
            'repertoire' => RepertoireModel::tryGenerate()
        ]);
    }

    public function showRepertoire()
    {
        $this->render('view/repertoire.twig', [
            'repertoire' => RepertoireModel::tryGenerate(),
            'minsReceptionOnly' => SKOP_CONFIG['minsReceptionOnly']
        ]);
    }

    public function showPricing()
    {
        $this->render('view/pricing.twig', [
            'seatTypes' => TheaterSeatTypeModel::all()
        ]);
    }
}
