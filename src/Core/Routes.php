<?php

use Skop\Models\Domain\Genre;
use Skop\Models\Domain\Movie;
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
            'password_repeat' => [ 'type' => 'string', 'min' => 8, 'max' => 63]
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
            'id' => [ 'type' => 'ignore' ],
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
            'id' => [ 'type' => 'ignore' ],
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
];
