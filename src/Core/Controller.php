<?php

namespace Skop\Core;

use Skop\Core\Request;
use Skop\Models\Domain\User;
use Skop\Models\UserModel;
use Twig\Environment;

abstract class Controller
{
    public Request $req;
    public ?User $loggedInUser;
    public Environment $twigInstance;

    public function __construct(Environment $twigInstance, Request $req)
    {
        $this->twigInstance = $twigInstance;
        $this->req = $req;
    }

    public function fetchLoggedInUser()
    {
        if (!isset($_SESSION[SKOP_SESSION_USERID_KEY]))
        {
            $this->loggedInUser = null;
            return;
        }
        $fetchedUser = UserModel::getOneById($_SESSION[SKOP_SESSION_USERID_KEY]);
        $this->loggedInUser = $fetchedUser !== false ? $fetchedUser : null;
    }

    public function render(string $template, array $data = [])
    {
        echo $this->twigInstance->render($template, array_merge($data, [
            'publicRoot' => SKOP_PUBLIC_PATH
        ]));
    }
}
