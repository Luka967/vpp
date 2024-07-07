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
            'first_name' => [ 'type' => 'string|alphabetical', 'min' => 2, 'max' => 31],
            'last_name' => [ 'type' => 'string|alphabetical', 'min' => 2, 'max' => 31],
            'email' => [ 'type' => 'string|email', 'min' => 8, 'max' => 63],
            'password' => [ 'type' => 'string', 'min' => 8, 'max' => 63],
            'password_repeat' => [ 'type' => 'string', 'min' => 8, 'max' => 63]
        ]
    ]
];
