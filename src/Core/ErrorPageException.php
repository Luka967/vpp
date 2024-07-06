<?php

namespace Skop\Core;

use Exception;
use Throwable;

define('SKOP_ERROR_UNCAUGHT'        , 000);
define('SKOP_ERROR_NO_ROUTE'        , 100);
define('SKOP_ERROR_NO_CONTROLLER'   , 101);
define('SKOP_ERROR_NO_CALLABLE'     , 102);
define('SKOP_ERROR_AUTH_LOGIN'      , 200);
define('SKOP_ERROR_AUTH_LOGOUT'     , 201);

define('SKOP_ERROR_PAGES', [
    SKOP_ERROR_UNCAUGHT => [500, 'Internal server error'],
    SKOP_ERROR_NO_ROUTE => [404, 'No route'],
    SKOP_ERROR_NO_CONTROLLER => [404, 'No controller'],
    SKOP_ERROR_NO_CALLABLE => [404, 'No callable'],
    SKOP_ERROR_AUTH_LOGIN => [403, 'Route forces session to be logged in'],
    SKOP_ERROR_AUTH_LOGOUT => [403, 'Route forces session to be logged out'],
]);

define('SKOP_ERROR_PAGES_LANG', [
    SKOP_ERROR_UNCAUGHT => [
        'title' => 'Serverska greška',
        'description' => 'Interna greška se desila prilikom obrade zahteva. Molimo pokušajte ponovo kasnije. Ukoliko se problem nastavi, kontaktirajte nas.'
    ],
    SKOP_ERROR_NO_ROUTE => [
        'title' => 'Stranica ne postoji',
        'description' => 'Ova stranica trenutno ne postoji ili je nedavno bila obrisana. Ukoliko mislite da je greška kontaktirajte nas.'
    ],
    SKOP_ERROR_NO_CONTROLLER => [
        'title' => 'Stranica ne postoji',
        'description' => 'Ova stranica trenutno ne postoji ili je nedavno bila obrisana. Ukoliko mislite da je greška kontaktirajte nas.'
    ],
    SKOP_ERROR_NO_CALLABLE => [
        'title' => 'Stranica ne postoji',
        'description' => 'Ova stranica trenutno ne postoji ili je nedavno bila obrisana. Ukoliko mislite da je greška kontaktirajte nas.'
    ],
    SKOP_ERROR_AUTH_LOGIN => [
        'title' => 'Morate biti prijavljeni',
        'description' => 'Ova stranica nije dostupna za korisnike koji se nisu prijavili.',
        'linkBack' => [
            'to' => '/login',
            'label' => 'Prijavi se'
        ]
    ],
    SKOP_ERROR_NO_CALLABLE => [
        'Stranica ne postoji',
        'Ova stranica trenutno ne postoji ili je nedavno bila obrisana. Ukoliko mislite da je greška kontaktirajte nas.'
    ],
]);

class ErrorPageException extends Exception {
    public readonly int $errorPageCode;
    public readonly int $httpCode;

    public function __construct(int $code = 0, ?Throwable $previous = null) {
        parent::__construct(SKOP_ERROR_PAGES[$code][1], E_USER_ERROR, $previous);
        $this->errorPageCode = $code;
        $this->httpCode = SKOP_ERROR_PAGES[$code][0];
    }
}
