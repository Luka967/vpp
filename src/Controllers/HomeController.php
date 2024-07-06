<?php

namespace Skop\Controllers;
use Skop\Core\Controller;

class HomeController extends Controller
{
    public function showLanding()
    {
        $this->render('layout.twig');
    }
}
