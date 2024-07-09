<?php

namespace Skop\Controllers;

use Skop\Core\Controller;
use Skop\Core\ErrorPageException;
use Skop\Models\MovieModel;

class HomeController extends Controller
{
    public function showLanding()
    {
        $movies = MovieModel::all();
        $heroMovie = $movies[random_int(0, count($movies) - 1)];
        $this->render('index.twig', [
            'heroMovie' => $heroMovie,
            'movies' => $movies
        ]);
    }

    public function showMovie()
    {
        $movie = MovieModel::withId($this->req->query['id']);
        if ($movie == null)
            throw new ErrorPageException(SKOP_ERROR_UNKNOWN_MOVIE);
        $this->render('view/movie.twig', [
            'movie' => $movie
        ]);
    }
}
