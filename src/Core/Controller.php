<?php

namespace Skop\Core;

use Skop\Core\Request;
use Skop\Models\Domain\User;
use Skop\Models\UserModel;
use Twig\Environment;

abstract class Controller
{
    public Request $req;
    public ?User $loggedInUser = null;
    public Environment $twigInstance;
    protected array $persistentFormData = [];

    public function __construct(Environment $twigInstance, Request $req)
    {
        $this->twigInstance = $twigInstance;
        $this->req = $req;
    }
    /**
     * Premesti stanje iz prethodnog kontrolera ka novom
     */
    public static function morph(Controller $existing) : static
    {
        $newController = new static($existing->twigInstance, $existing->req);
        $newController->loggedInUser = $existing->loggedInUser;
        return $newController;
    }

    public function setLoggedInUser(User $user)
    {
        $this->loggedInUser = $user;
        $_SESSION[SKOP_SESSION_USERID_KEY] = $this->loggedInUser->id;
    }
    public function unsetLoggedInUser()
    {
        $this->loggedInUser = null;
        unset($_SESSION[SKOP_SESSION_USERID_KEY]);
    }
    public function getLoggedInUser()
    {
        if (!isset($_SESSION[SKOP_SESSION_USERID_KEY]))
        {
            $this->loggedInUser = null;
            return;
        }
        $this->loggedInUser = UserModel::withId($_SESSION[SKOP_SESSION_USERID_KEY]);
        if ($this->loggedInUser == null)
        {
            $this->unsetLoggedInUser();
            return;
        }
    }

    public function render(string $template, array $data = [])
    {
        echo $this->twigInstance->render($template, [
            ...$data,
            'publicRoot' => SKOP_PUBLIC_PATH,
            'persistentFormData' => $this->persistentFormData,
            'loggedInUser' => $this->loggedInUser,
            'req' => $this->req
        ]);
        exit;
    }
    public function redirect(string $to, int $httpStatus = 303)
    {
        header("Location: $to");
        http_response_code($httpStatus);
        exit;
    }
}
