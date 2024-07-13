<?php

namespace Skop\Core;

use Monolog\Logger;
use Skop\Core\Request;
use Skop\Models\Domain\User;
use Skop\Models\UserModel;
use Twig\Environment;

abstract class Controller
{
    public Request $req;
    protected readonly string $reqId;
    public ?User $loggedInUser = null;
    public Environment $twigInstance;
    public Logger $logger;
    protected array $persistentFormData = [];

    public function __construct(Environment $twigInstance, Request $req, Logger $logger)
    {
        $this->twigInstance = $twigInstance;
        $this->req = $req;
        $this->reqId = $req->id;
        $this->logger = $logger;
    }
    /**
     * Premesti stanje iz prethodnog kontrolera ka novom
     */
    public static function morph(Controller $existing) : static
    {
        $newController = new static($existing->twigInstance, $existing->req, $existing->logger);
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
        $wholeContext = [
            ...$data,
            'publicRoot' => SKOP_PUBLIC_PATH,
            'persistentFormData' => $this->persistentFormData,
            'loggedInUser' => $this->loggedInUser,
            'req' => $this->req
        ];
        $this->logger->debug("$this->reqId rendered", ['template' => $template]);
        echo $this->twigInstance->render($template, $wholeContext);
        exit;
    }
    public function redirect(string $to, int $httpStatus = 303)
    {
        $this->logger->debug("$this->reqId redirected", ['to' => $to, 'httpStatus' => $httpStatus]);

        header("Location: $to");
        http_response_code($httpStatus);
        exit;
    }
}
