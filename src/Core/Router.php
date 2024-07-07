<?php

namespace Skop\Core;

use Exception;
use Skop\Controllers\ErrorController;
use Twig\Environment;
use Twig\Loader\FilesystemLoader;

final class Router
{
    private readonly array $routes;
    private readonly FilesystemLoader $twigLoader;
    private readonly Environment $twigInstance;

    public function __construct()
    {
        session_start();

        $filename = SKOP_APPLICATION_PATH.'Core/Routes.php';
        if (!is_file($filename))
            throw new Exception('No routes defined');
        $this->routes = include $filename;

        $this->twigLoader = new FilesystemLoader(SKOP_APPLICATION_PATH.'Views');
        $this->twigInstance = new Environment($this->twigLoader, [
            'debug' => true
        ]);
    }

    public function dispatch()
    {
        $req = new Request();
        $controller = null;

        // var_dump($_SERVER);
        // echo '<br><br>';
        // var_dump($req);
        // echo '<br><br>';

        try
        {
            $routeName = "$req->method $req->path";

            if (!isset($this->routes[$routeName]))
                throw new ErrorPageException(SKOP_ERROR_NO_ROUTE);
            $route = $this->routes[$routeName];

            $controller = $route['controller'];
            $controllerClass = "\\Skop\\Controllers\\$controller";
            $action = $route['action'];

            // var_dump($routeName, $route, $controllerClass);
            // echo '<br><br>';

            if (!class_exists($controllerClass, true))
                throw new ErrorPageException(SKOP_ERROR_NO_CONTROLLER);

            $controller = new $controllerClass($this->twigInstance, $req);

            if (!method_exists($controller, $action))
                throw new ErrorPageException(SKOP_ERROR_NO_CALLABLE);

            $req->validatePostInput($route);

            $controller->getLoggedInUser();

            if (isset($route['forceLoggedOut']) && $controller->loggedInUser != null)
                throw new ErrorPageException(SKOP_ERROR_AUTH_LOGOUT);
            if (isset($route['forceLoggedIn']) && $controller->loggedInUser == null)
                throw new ErrorPageException(SKOP_ERROR_AUTH_LOGIN);

            $controller->$action();
        }
        catch (ErrorPageException $ex)
        {
            if ($controller != null)
                $controller = ErrorController::morph($controller);
            else
                $controller = new ErrorController($this->twigInstance, $req);
            $controller->showPage($ex);
        }
    }
}
