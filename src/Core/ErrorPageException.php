<?php

namespace Skop\Core;

use Exception;
use Throwable;

define('SKOP_ERROR_UNCAUGHT'                    , 000);
define('SKOP_ERROR_NO_ROUTE'                    , 100);
define('SKOP_ERROR_NO_CONTROLLER'               , 101);
define('SKOP_ERROR_NO_CALLABLE'                 , 102);
define('SKOP_ERROR_AUTH_LOGIN'                  , 200);
define('SKOP_ERROR_AUTH_LOGOUT'                 , 201);
define('SKOP_ERROR_AUTH_NOPERMS'                , 202);
define('SKOP_ERROR_INPUT_MISSING'               , 300);
define('SKOP_ERROR_INPUT_INVALID'               , 301);
define('SKOP_ERROR_UNKNOWN_USER'                , 400);
define('SKOP_ERROR_UNKNOWN_MOVIE'               , 401);
define('SKOP_ERROR_UNKNOWN_GENRE'               , 402);
define('SKOP_ERROR_UNKNOWN_SCREENINGFEATURE'    , 403);
define('SKOP_ERROR_UNKNOWN_SEATTYPE'            , 404);
define('SKOP_ERROR_UNKNOWN_THEATER'             , 405);
define('SKOP_ERROR_UNKNOWN_REPERTOIRE'          , 406);
define('SKOP_ERROR_UNKNOWN_TICKET'              , 407);
define('SKOP_ERROR_EXISTING_TICKET'             , 408);
define('SKOP_ERROR_CONFLICTING_USER'            , 500);
define('SKOP_ERROR_CONFLICTING_MOVIE'           , 501);
define('SKOP_ERROR_CONFLICTING_GENRE'           , 502);
define('SKOP_ERROR_CONFLICTING_SCREENINGFEATURE', 503);
define('SKOP_ERROR_CONFLICTING_SEATTYPE'        , 504);
define('SKOP_ERROR_CONFLICTING_THEATER'         , 505);
define('SKOP_ERROR_CONFLICTING_REPERTOIRE'      , 506);
define('SKOP_ERROR_UNREACHABLE_REPERTOIRE'      , 507);
define('SKOP_ERROR_CONFLICTING_TICKET'          , 508);
define('SKOP_ERROR_TOOLARGE_TICKET'             , 509);

define('SKOP_ERROR_PAGES', [
    SKOP_ERROR_UNCAUGHT => [500, 'Internal server error'],
    SKOP_ERROR_NO_ROUTE => [404, 'No route'],
    SKOP_ERROR_NO_CONTROLLER => [404, 'No controller'],
    SKOP_ERROR_NO_CALLABLE => [404, 'No callable in controller'],
    SKOP_ERROR_AUTH_LOGIN => [401, 'Must be logged in'],
    SKOP_ERROR_AUTH_LOGOUT => [403, 'Must be logged out'],
    SKOP_ERROR_AUTH_NOPERMS => [403, 'You have no permissions'],
    SKOP_ERROR_INPUT_MISSING => [400, 'Request has one or more missing inputs'],
    SKOP_ERROR_INPUT_INVALID => [400, 'Request has one or more invalid inputs'],
    SKOP_ERROR_UNKNOWN_USER => [404, 'This user does not exist'],
    SKOP_ERROR_UNKNOWN_MOVIE => [404, 'This movie does not exist'],
    SKOP_ERROR_UNKNOWN_GENRE => [404, 'This genre does not exist'],
    SKOP_ERROR_UNKNOWN_SCREENINGFEATURE => [404, 'This screening feature does not exist'],
    SKOP_ERROR_UNKNOWN_SEATTYPE => [404, 'This theater seat type does not exist'],
    SKOP_ERROR_UNKNOWN_THEATER => [404, 'This theater does not exist'],
    SKOP_ERROR_UNKNOWN_REPERTOIRE => [404, 'This repertoire entry does not exist'],
    SKOP_ERROR_UNKNOWN_TICKET => [404, 'This ticket does not exist'],
    SKOP_ERROR_EXISTING_TICKET => [404, 'Ticket already exists'],
    SKOP_ERROR_CONFLICTING_USER => [400, 'User already exists with this email'],
    SKOP_ERROR_CONFLICTING_MOVIE => [400, 'Movie already exists with this title'],
    SKOP_ERROR_CONFLICTING_GENRE => [400, 'Genre already exists with this name'],
    SKOP_ERROR_CONFLICTING_SCREENINGFEATURE => [400, 'Screening feature already exists with this description'],
    SKOP_ERROR_CONFLICTING_SEATTYPE => [400, 'Theater seat type already exists with this name'],
    SKOP_ERROR_CONFLICTING_THEATER => [400, 'Theater already exists with this name'],
    SKOP_ERROR_CONFLICTING_REPERTOIRE => [400, 'Time span of new repertoire entry is colliding with existing'],
    SKOP_ERROR_UNREACHABLE_REPERTOIRE => [400, 'Repertoire entry is not taking any more online reservations'],
    SKOP_ERROR_CONFLICTING_TICKET => [400, 'Reservation has seats that have already been reserved'],
    SKOP_ERROR_TOOLARGE_TICKET => [400, 'Reservation has too many seats picked']
]);

define('SKOP_ERROR_PAGES_LANG', [
    SKOP_ERROR_UNCAUGHT => [
        'title' => 'Serverska greška',
        'description' => 'Greška se desila prilikom obrade zahteva. Molimo pokušajte ponovo kasnije. ' .
            'Ukoliko se problem nastavi, kontaktirajte nas.'
    ],
    SKOP_ERROR_NO_ROUTE => [
        'title' => 'Stranica ne postoji',
        'description' => 'Ova stranica trenutno ne postoji ili je nedavno bila obrisana. ' .
            'Ukoliko mislite da je greška kontaktirajte nas.'
    ],
    SKOP_ERROR_NO_CONTROLLER => [
        'title' => 'Stranica ne postoji',
        'description' => 'Ova stranica trenutno ne postoji ili je nedavno bila obrisana. ' .
            'Ukoliko mislite da je greška kontaktirajte nas.'
    ],
    SKOP_ERROR_NO_CALLABLE => [
        'title' => 'Stranica ne postoji',
        'description' => 'Ova stranica trenutno ne postoji ili je nedavno bila obrisana. ' .
            'Ukoliko mislite da je greška kontaktirajte nas.'
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
    SKOP_ERROR_AUTH_NOPERMS => [
        'title' => 'Pristup zabranjen',
        'description' => 'Ova stranica Vama nije dostupna.',
    ],
    SKOP_ERROR_INPUT_MISSING => [
        'title' => 'Pokušajte ponovo',
        'description' => 'Akcija koju ste upravo izvršili nije bila ispravna. Vratite se nazad i pokušajte ponovo.',
    ],
    SKOP_ERROR_INPUT_INVALID => [
        'title' => 'Pokušajte ponovo',
        'description' => 'Akcija koju ste upravo izvršili nije bila ispravna. Vratite se nazad i pokušajte ponovo.',
    ],
    SKOP_ERROR_UNKNOWN_USER => [
        'title' => 'Korisnik ne postoji',
        'description' => 'Ovaj korisnik više ne postoji.',
    ],
    SKOP_ERROR_UNKNOWN_MOVIE => [
        'title' => 'Film ne postoji',
        'description' => 'Ovaj film više ne postoji.',
    ],
    SKOP_ERROR_UNKNOWN_GENRE => [
        'title' => 'Žanr ne postoji',
        'description' => 'Ovaj žanr više ne postoji.',
    ],
    SKOP_ERROR_UNKNOWN_SCREENINGFEATURE => [
        'title' => 'Tip projekcije ne postoji',
        'description' => 'Ovaj tip projekcije više ne postoji.',
    ],
    SKOP_ERROR_UNKNOWN_SEATTYPE => [
        'title' => 'Tip sedišta ne postoji',
        'description' => 'Ovaj tip sedišta više ne postoji.',
    ],
    SKOP_ERROR_UNKNOWN_THEATER => [
        'title' => 'Bioskopska sala ne postoji',
        'description' => 'Ova bioskopska sala više ne postoji.',
    ],
    SKOP_ERROR_UNKNOWN_REPERTOIRE => [
        'title' => 'Stavka repertoara ne postoji',
        'description' => 'Ova stavka repertoara više ne postoji.',
    ],
    SKOP_ERROR_UNKNOWN_TICKET => [
        'title' => 'Rezervacija ne postoji',
        'description' => 'Ova rezervacija više ne postoji.'
    ],
    SKOP_ERROR_EXISTING_TICKET => [
        'title' => 'Već imate rezervaciju',
        'description' => 'Već ste postavili rezervaciju na ovu stavku repertoara.' .
            ' Ukoliko želite da je izmenite, morate je prvo otkazati preko Vašeg profila.'
    ],
    SKOP_ERROR_CONFLICTING_USER => [
        'title' => 'Korisnik već postoji',
        'description' => 'Već postoji korisnik sa ovim e-mailom.',
    ],
    SKOP_ERROR_CONFLICTING_MOVIE => [
        'title' => 'Film već postoji',
        'description' => 'Već postoji film sa ovim naslovom.',
    ],
    SKOP_ERROR_CONFLICTING_GENRE => [
        'title' => 'Žanr već postoji',
        'description' => 'Već postoji žanr sa ovim imenom.',
    ],
    SKOP_ERROR_CONFLICTING_SCREENINGFEATURE => [
        'title' => 'Tip projekcije već postoji',
        'description' => 'Već postoji tip projekcije sa ovim opisom.',
    ],
    SKOP_ERROR_CONFLICTING_SEATTYPE => [
        'title' => 'Tip sedišta već postoji',
        'description' => 'Već postoji tip sedišta sa ovim imenom.',
    ],
    SKOP_ERROR_CONFLICTING_THEATER => [
        'title' => 'Bioskopska sala već postoji',
        'description' => 'Već postoji bioskopska sala sa ovim imenom.',
    ],
    SKOP_ERROR_CONFLICTING_REPERTOIRE => [
        'title' => 'Nova stavka repertoara se podudara',
        'description' => 'Ubacivanje nove stavke repertoara je nemoguće jer ' .
            'početak i kraj prikazivanja prelazi u vremenski period nekog već postojećeg prikazivanja.',
    ],
    SKOP_ERROR_UNREACHABLE_REPERTOIRE => [
        'title' => 'Stavka repertoara je nedostupna',
        'description' => 'Rezervacija na ovo vreme više nije moguće. Molimo Vas da pretražite drugo vreme.'
    ],
    SKOP_ERROR_CONFLICTING_TICKET => [
        'title' => 'Neka sedišta su već rezervisana',
        'description' => 'Rezervacija ovih sedišta je nemoguće. Molimo Vas da odaberete nova sedišta.'
    ],
    SKOP_ERROR_TOOLARGE_TICKET => [
        'title' => 'Previše sedišta odabrana',
        'description' => 'Najveći broj sedišta koji možete odabrati je ' . SKOP_CONFIG['reservationMaxSeats'] .
            '. Molimo pokušajte ponovo sa rezervacijom.'
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
