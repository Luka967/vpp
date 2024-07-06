<?php

namespace Skop\Controllers;
use Skop\Core\Controller;
use Skop\Core\ErrorPageException;

class ErrorController extends Controller
{
    public static function from(Controller $existing) : ErrorController
    {
        $newController = new ErrorController($existing->twigInstance, $existing->req);
        $newController->loggedInUser = $existing->loggedInUser;
        return $newController;
    }

    public function showPage(ErrorPageException $ex)
    {
        http_response_code($ex->httpCode);
        $this->render('error.twig', [
            'errorCode' => $ex->errorPageCode,
            'errorTitle' => SKOP_ERROR_PAGES_LANG[$ex->errorPageCode]['title'],
            'errorDescription' => SKOP_ERROR_PAGES_LANG[$ex->errorPageCode]['description'],
            'linkBack' => SKOP_ERROR_PAGES_LANG[$ex->errorPageCode]['linkBack'] ?? null
        ]);
    }
}
