<?php

namespace Skop\Controllers;

use DateTime;
use Skop\Core\Controller;

class HomeController extends Controller
{
    public function showLanding()
    {
        $this->render('index.twig', [
            'heroMovie' => [
                'id' => 1,
                'title' => 'Brzina metka',
                'rating' => 'R',
                'release_date' => new DateTime('2024-07-12'),
                'genres' => ['Akcija', 'Avantura']
            ],
            'movies' => [
                [
                    'id' => 2,
                    'title' => 'Suzume',
                    'rating' => 'A',
                    'release_date' => new DateTime('2024-07-10'),
                    'genres' => ['Anime']
                ]
            ]
        ]);
    }
}
