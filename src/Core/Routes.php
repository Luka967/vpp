<?php

use Skop\Models\Domain\Genre;
use Skop\Models\Domain\Movie;
use Skop\Models\Domain\Repertoire;
use Skop\Models\Domain\ScreeningFeature;
use Skop\Models\Domain\Theater;
use Skop\Models\Domain\TheaterSeatType;
use Skop\Models\Domain\User;

function filterDomainObjectColumns(array $columns, bool $partial, bool $editable): array
{
    $ret = [];
    foreach ($columns as $key => $column)
        if ($column['partial'] == $partial && $column['editable'] == $editable)
            $ret[$key] = $column;
    return $ret;
}

return [
    // default
    'GET ' => [
        'controller' => 'HomeController',
        'action' => 'showLanding'
    ],

    'GET /movie' => [
        'controller' => 'HomeController',
        'action' => 'showMovie',
        'dataQuery' => [
            'id' => Movie::$columnTraits['id']
        ]
    ],
    'GET /repertoar' => [
        'controller' => 'HomeController',
        'action' => 'showRepertoire'
    ],
    'GET /cenovnik' => [
        'controller' => 'HomeController',
        'action' => 'showPricing'
    ],

    'GET /rezervacija' => [
        'controller' => 'ReservationController',
        'action' => 'showPage',
        'forceLoggedIn' => true,
        'dataQuery' => [
            'id' => Repertoire::$columnTraits['id']
        ]
    ],
    'POST /rezervacija' => [
        'controller' => 'ReservationController',
        'action' => 'doInsert',
        'forceLoggedIn' => true,
        'dataQuery' => [
            'id' => Repertoire::$columnTraits['id']
        ],
        'dataPost' => [
            'seats_picked' => [ 'type' => 'string', 'min' => 4, 'max' => 63 ]
        ]
    ],

    'GET /login' => [
        'controller' => 'UserController',
        'action' => 'showLogin',
        'forceLoggedOut' => true
    ],
    'POST /login' => [
        'controller' => 'UserController',
        'action' => 'doLogin',
        'forceLoggedOut' => true,
        'dataPost' => [
            'email' => [ 'type' => 'string|email', 'min' => 8, 'max' => 63],
            'password' => [ 'type' => 'string', 'min' => 8, 'max' => 63]
        ]
    ],
    'GET /register' => [
        'controller' => 'UserController',
        'action' => 'showRegister',
        'forceLoggedOut' => true
    ],
    'POST /register' => [
        'controller' => 'UserController',
        'action' => 'doRegister',
        'forceLoggedOut' => true,
        'dataPost' => [
            ...filterDomainObjectColumns(User::$columnTraits, false, true),
            'password_repeat' => User::$columnTraits['password']
        ]
    ],
    'GET /me' => [
        'controller' => 'UserController',
        'action' => 'showMe',
        'forceLoggedIn' => true
    ],
    'GET /logout' => [
        'controller' => 'UserController',
        'action' => 'doLogout',
        'forceLoggedIn' => true
    ],

    'GET /manage' => [
        'controller' => 'ManagerController',
        'action' => 'showIndex',
        'forceLoggedIn' => true,
        'forceUserPermissions' => User::PERMISSIONS_MANAGER
    ],

    'GET /manage/movies' => [
        'controller' => 'ManagerMoviesController',
        'action' => 'showMovies',
        'forceLoggedIn' => true,
        'forceUserPermissions' => User::PERMISSIONS_MANAGER
    ],
    'GET /manage/movies/edit' => [
        'controller' => 'ManagerMoviesController',
        'action' => 'showMovies',
        'forceLoggedIn' => true,
        'forceUserPermissions' => User::PERMISSIONS_MANAGER,
        'dataQuery' => [
            'id' => Movie::$columnTraits['id']
        ]
    ],
    'POST /manage/movies/add' => [
        'controller' => 'ManagerMoviesController',
        'action' => 'doInsertMovie',
        'forceLoggedIn' => true,
        'forceUserPermissions' => User::PERMISSIONS_MANAGER,
        'dataPost' => [
            'id' => [ 'type' => 'ignore', 'setToNull' => true ],
            ...filterDomainObjectColumns(Movie::$columnTraits, false, true),
            'genres' => [
                ...Genre::$columnTraits['name'],
                'type' => 'array|' . Genre::$columnTraits['name']['type'], 'minArray' => 1, 'maxArray' => 3
            ]
        ],
        'filesPost' => [
            'trailer_file' => [
                'mimeTypes' => ['video/mp4'],
                'fileExtensions' => ['mp4']
            ],
            'poster_file' => [
                'mimeTypes' => ['image/jpeg'],
                'fileExtensions' => ['jpg', 'jpeg']
            ]
        ]
    ],
    'POST /manage/movies/edit' => [
        'controller' => 'ManagerMoviesController',
        'action' => 'doUpdateMovie',
        'forceLoggedIn' => true,
        'forceUserPermissions' => User::PERMISSIONS_MANAGER,
        'dataPost' => [
            ...filterDomainObjectColumns(Movie::$columnTraits, true, false),
            ...filterDomainObjectColumns(Movie::$columnTraits, false, true),
            'genres' => [
                ...Genre::$columnTraits['name'],
                'type' => 'array|' . Genre::$columnTraits['name']['type'], 'minArray' => 1, 'maxArray' => 3
            ]
        ],
        'filesPost' => [
            'trailer_file' => [
                'optional' => true,
                'mimeTypes' => ['video/mp4'],
                'fileExtensions' => ['mp4']
            ],
            'poster_file' => [
                'optional' => true,
                'mimeTypes' => ['image/jpeg'],
                'fileExtensions' => ['jpg', 'jpeg']
            ]
        ]
    ],
    'GET /manage/movies/delete' => [
        'controller' => 'ManagerMoviesController',
        'action' => 'doDeleteMovie',
        'forceLoggedIn' => true,
        'forceUserPermissions' => User::PERMISSIONS_MANAGER,
        'dataQuery' => [
            'id' => Movie::$columnTraits['id']
        ]
    ],

    'GET /manage/genres' => [
        'controller' => 'ManagerController',
        'action' => 'showGenres',
        'forceLoggedIn' => true,
        'forceUserPermissions' => User::PERMISSIONS_MANAGER,
    ],
    'POST /manage/genres/add' => [
        'controller' => 'ManagerController',
        'action' => 'doInsertGenre',
        'forceLoggedIn' => true,
        'forceUserPermissions' => User::PERMISSIONS_MANAGER,
        'dataPost' => [
            'id' => [ 'type' => 'ignore', 'setToNull' => true ],
            ...filterDomainObjectColumns(Genre::$columnTraits, false, true)
        ],
    ],
    'GET /manage/genres/delete' => [
        'controller' => 'ManagerController',
        'action' => 'doDeleteGenre',
        'forceLoggedIn' => true,
        'forceUserPermissions' => User::PERMISSIONS_MANAGER,
        'dataQuery' => [
            'id' => Movie::$columnTraits['id']
        ]
    ],

    'GET /manage/repertoire/features' => [
        'controller' => 'ManagerController',
        'action' => 'showScreeningFeatures',
        'forceLoggedIn' => true,
        'forceUserPermissions' => User::PERMISSIONS_MANAGER,
    ],
    'POST /manage/repertoire/features/add' => [
        'controller' => 'ManagerController',
        'action' => 'doInsertScreeningFeature',
        'forceLoggedIn' => true,
        'forceUserPermissions' => User::PERMISSIONS_MANAGER,
        'dataPost' => [
            'id' => [ 'type' => 'ignore', 'setToNull' => true ],
            ...filterDomainObjectColumns(ScreeningFeature::$columnTraits, false, true)
        ],
    ],
    'GET /manage/repertoire/features/delete' => [
        'controller' => 'ManagerController',
        'action' => 'doDeleteScreeningFeature',
        'forceLoggedIn' => true,
        'forceUserPermissions' => User::PERMISSIONS_MANAGER,
        'dataQuery' => [
            'id' => ScreeningFeature::$columnTraits['id']
        ]
    ],

    'GET /manage/theaters/seats' => [
        'controller' => 'ManagerController',
        'action' => 'showTheaterSeatTypes',
        'forceLoggedIn' => true,
        'forceUserPermissions' => User::PERMISSIONS_MANAGER,
    ],
    'POST /manage/theaters/seats/add' => [
        'controller' => 'ManagerController',
        'action' => 'doInsertTheaterSeatType',
        'forceLoggedIn' => true,
        'forceUserPermissions' => User::PERMISSIONS_MANAGER,
        'dataPost' => [
            'id' => [ 'type' => 'ignore', 'setToNull' => true ],
            ...filterDomainObjectColumns(TheaterSeatType::$columnTraits, false, true)
        ],
    ],
    'GET /manage/theaters/seats/edit' => [
        'controller' => 'ManagerController',
        'action' => 'showTheaterSeatTypes',
        'forceLoggedIn' => true,
        'forceUserPermissions' => User::PERMISSIONS_MANAGER,
        'dataQuery' => [
            'id' => TheaterSeatType::$columnTraits['id']
        ]
    ],
    'POST /manage/theaters/seats/edit' => [
        'controller' => 'ManagerController',
        'action' => 'doUpdateTheaterSeatType',
        'forceLoggedIn' => true,
        'forceUserPermissions' => User::PERMISSIONS_MANAGER,
        'dataPost' => [
            ...filterDomainObjectColumns(TheaterSeatType::$columnTraits, true, false),
            ...filterDomainObjectColumns(TheaterSeatType::$columnTraits, false, true)
        ]
    ],
    'GET /manage/theaters/seats/delete' => [
        'controller' => 'ManagerController',
        'action' => 'doDeleteTheaterSeatType',
        'forceLoggedIn' => true,
        'forceUserPermissions' => User::PERMISSIONS_MANAGER,
        'dataQuery' => [
            'id' => TheaterSeatType::$columnTraits['id']
        ]
    ],

    'GET /manage/theaters' => [
        'controller' => 'ManagerTheatersController',
        'action' => 'showTheaters',
        'forceLoggedIn' => true,
        'forceUserPermissions' => User::PERMISSIONS_MANAGER,
    ],
    'POST /manage/theaters/add' => [
        'controller' => 'ManagerTheatersController',
        'action' => 'doInsertTheater',
        'forceLoggedIn' => true,
        'forceUserPermissions' => User::PERMISSIONS_MANAGER,
        'dataPost' => [
            'id' => [ 'type' => 'ignore', 'setToNull' => true ],
            ...filterDomainObjectColumns(Theater::$columnTraits, false, true),
            'seating' => [ 'type' => 'string', 'min' => 4, 'max' => 5000 ]
        ]
    ],
    'GET /manage/theaters/edit' => [
        'controller' => 'ManagerTheatersController',
        'action' => 'showEditTheater',
        'forceLoggedIn' => true,
        'forceUserPermissions' => User::PERMISSIONS_MANAGER,
        'dataQuery' => [
            'id' => Theater::$columnTraits['id']
        ]
    ],
    'POST /manage/theaters/edit' => [
        'controller' => 'ManagerTheatersController',
        'action' => 'doUpdateTheater',
        'forceLoggedIn' => true,
        'forceUserPermissions' => User::PERMISSIONS_MANAGER,
        'dataPost' => [
            ...filterDomainObjectColumns(Theater::$columnTraits, true, false),
            ...filterDomainObjectColumns(Theater::$columnTraits, false, true),
            'seating' => [ 'type' => 'string', 'min' => 4, 'max' => 5000 ]
        ]
    ],

    'GET /manage/repertoire' => [
        'controller' => 'ManagerRepertoireController',
        'action' => 'showRepertoire',
        'forceLoggedIn' => true,
        'forceUserPermissions' => User::PERMISSIONS_MANAGER
    ],
    'POST /manage/repertoire/add' => [
        'controller' => 'ManagerRepertoireController',
        'action' => 'doInsertEntry',
        'forceLoggedIn' => true,
        'forceUserPermissions' => User::PERMISSIONS_MANAGER,
        'dataPost' => [
            'id' => [ 'type' => 'ignore', 'setToNull' => true ],
            ...filterDomainObjectColumns(Repertoire::$columnTraits, false, false),
            'features' => [
                ...ScreeningFeature::$columnTraits['description'],
                'type' => 'array|' . ScreeningFeature::$columnTraits['description']['type'], 'minArray' => 1, 'maxArray' => 3
            ]
        ]
    ],
    'GET /manage/repertoire/edit' => [
        'controller' => 'ManagerRepertoireController',
        'action' => 'showEditEntry',
        'forceLoggedIn' => true,
        'forceUserPermissions' => User::PERMISSIONS_MANAGER,
        'dataQuery' => [
            'id' => Repertoire::$columnTraits['id']
        ]
    ],
    'POST /manage/repertoire/edit' => [
        'controller' => 'ManagerRepertoireController',
        'action' => 'doUpdateEntry',
        'forceLoggedIn' => true,
        'forceUserPermissions' => User::PERMISSIONS_MANAGER,
        'dataPost' => [
            'id' => Repertoire::$columnTraits['id'],
            'movie_id' => [ 'type' => 'ignore' ],
            'theater_id' => [ 'type' => 'ignore' ],
            'screening_start' => [ 'type' => 'ignore' ],
            'features' => [
                ...ScreeningFeature::$columnTraits['description'],
                'type' => 'array|' . ScreeningFeature::$columnTraits['description']['type'], 'minArray' => 1, 'maxArray' => 3
            ]
        ]
    ],
    'GET /manage/repertoire/delete' => [
        'controller' => 'ManagerRepertoireController',
        'action' => 'doDeleteEntry',
        'forceLoggedIn' => true,
        'forceUserPermissions' => User::PERMISSIONS_MANAGER,
        'dataQuery' => [
            'id' => Repertoire::$columnTraits['id']
        ]
    ],
];
