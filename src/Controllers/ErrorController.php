<?php

namespace Skop\Controllers;
use Skop\Core\Controller;
use Skop\Core\ErrorPageException;

class ErrorController extends Controller
{
    public function showPage(ErrorPageException $ex)
    {
        http_response_code($ex->httpCode);
        $this->render('error.twig', [
            'errorCode' => $ex->errorPageCode,
            'errorTitle' => SKOP_ERROR_PAGES_LANG[$ex->errorPageCode]['title'],
            'errorDescription' => SKOP_ERROR_PAGES_LANG[$ex->errorPageCode]['description'],
            'errorExtra' => $ex->extraDetails,
            'linkBack' => SKOP_ERROR_PAGES_LANG[$ex->errorPageCode]['linkBack'] ?? null
        ]);
    }
}
