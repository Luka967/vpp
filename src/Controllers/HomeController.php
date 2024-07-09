<?php

namespace Skop\Controllers;

use Skop\Core\Controller;
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
}
