<?php

return [
    // default
    'GET ' => [
        'controller' => 'HomeController',
        'action' => 'showLanding'
    ],
    'GET /login' => [
        'controller' => 'UserController',
        'action' => 'showLogin',
        'forceLoggedOut' => true
    ],
    'GET /register' => [
        'controller' => 'UserController',
        'action' => 'showRegister',
        'forceLoggedOut' => true
    ],
    'POST /register' => [
        'controller' => 'UserController',
        'action' => 'showRegister',
        'forceLoggedOut' => true,
        'paramsPost' => [
            'email' => [ 'type' => 'string', 16, 63],
            'password' => ['string', 8, 63]
        ]
    ]
];
