<?php

namespace Skop\Controllers;
use Skop\Core\Controller;

class UserController extends Controller
{
    public function showLogin()
    {
        $this->render('login.twig');
    }
    public function showRegister()
    {
        $this->render('register.twig');
    }
}
