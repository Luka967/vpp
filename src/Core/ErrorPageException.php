<?php

namespace Skop\Core;

use Exception;
use Throwable;

define('SKOP_ERROR_UNCAUGHT'            , 000);
define('SKOP_ERROR_NO_ROUTE'            , 100);
define('SKOP_ERROR_NO_CONTROLLER'       , 101);
define('SKOP_ERROR_NO_CALLABLE'         , 102);
define('SKOP_ERROR_AUTH_LOGIN'          , 200);
define('SKOP_ERROR_AUTH_LOGOUT'         , 201);
define('SKOP_ERROR_INPUT_MISSING'       , 300);
define('SKOP_ERROR_INPUT_INVALID'       , 301);
define('SKOP_ERROR_INPUT_UNKNOWN_USER'  , 400);

define('SKOP_ERROR_PAGES', [
    SKOP_ERROR_UNCAUGHT => [500, 'Internal server error'],
    SKOP_ERROR_NO_ROUTE => [404, 'No route'],
    SKOP_ERROR_NO_CONTROLLER => [404, 'No controller'],
    SKOP_ERROR_NO_CALLABLE => [404, 'No callable in controller'],
    SKOP_ERROR_AUTH_LOGIN => [401, 'Must be logged in'],
    SKOP_ERROR_AUTH_LOGOUT => [403, 'Must be logged out'],
    SKOP_ERROR_INPUT_MISSING => [400, 'Request has one or more missing inputs'],
    SKOP_ERROR_INPUT_INVALID => [400, 'Request has one or more invalid inputs'],
    SKOP_ERROR_INPUT_UNKNOWN_USER => [404, 'This user does not exist'],
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
    SKOP_ERROR_AUTH_LOGOUT => [
        'title' => 'Morate se odjaviti',
        'description' => 'Ova stranica nije dostupna za korisnike koji su se već prijavili.',
    ],
    SKOP_ERROR_INPUT_MISSING => [
        'title' => 'Pokušajte ponovo',
        'description' => 'Akcija koju ste upravo izvršili nije bila ispravna. Vratite se nazad i pokušajte ponovo.',
    ],
    SKOP_ERROR_INPUT_INVALID => [
        'title' => 'Pokušajte ponovo',
        'description' => 'Akcija koju ste upravo izvršili nije bila ispravna. Vratite se nazad i pokušajte ponovo.',
    ],
    SKOP_ERROR_INPUT_UNKNOWN_USER => [
        'title' => 'Korisnik ne postoji',
        'description' => 'Korisnik pod ovaj ID ili e-mail ne postoji.',
    ]
]);

class ErrorPageException extends Exception {
    public readonly int $errorPageCode;
    public readonly int $httpCode;
    public readonly ?string $extraDetails;

    public function __construct(int $code = 0, ?string $extraDetails = null, ?Throwable $previous = null) {
        parent::__construct(SKOP_ERROR_PAGES[$code][1], E_USER_ERROR, $previous);
        $this->extraDetails = $extraDetails;
        $this->errorPageCode = $code;
        $this->httpCode = SKOP_ERROR_PAGES[$code][0];
    }
}
