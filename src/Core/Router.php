<?php

namespace Skop\Core;

use Exception;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;
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

        $routesFile = SKOP_APPLICATION_PATH . 'Core/Routes.php';
        if (!is_file($routesFile))
            throw new Exception('No routes defined');
        $this->routes = include $routesFile;

        $this->twigLoader = new FilesystemLoader(SKOP_APPLICATION_PATH . 'Views');
        $this->twigInstance = new Environment($this->twigLoader, []);
    }

    public function dispatch()
    {
        $logger = new Logger('router');
        $logger->pushHandler(new StreamHandler(SKOP_CONFIG['logFile'], Logger::INFO));

        $req = new Request($logger);
        $controller = null;

        $logger->debug("$req->id request started", [
            'date' => date(DATE_ATOM),
            'method' => $req->method,
            'path' => $req->path,
            'query' => $req->query,
            'data' => $req->data,
            'files' => $req->files
        ]);

        try
        {
            $routeName = "$req->method $req->path";

            if (!isset($this->routes[$routeName]))
                throw new ErrorPageException(SKOP_ERROR_NO_ROUTE);
            $route = $this->routes[$routeName];

            $controllerName = $route['controller'];
            $controllerClass = "\\Skop\\Controllers\\$controllerName";
            $action = $route['action'];

            // var_dump($routeName, $route, $controllerClass);
            // echo '<br><br>';

            if (!class_exists($controllerClass, true))
                throw new ErrorPageException(SKOP_ERROR_NO_CONTROLLER);

            $controller = new $controllerClass($this->twigInstance, $req, $logger);

            if (!method_exists($controller, $action))
                throw new ErrorPageException(SKOP_ERROR_NO_CALLABLE);

            $req->validateQueryInput($route);
            $req->validatePostInput($route);
            $req->validatePostFiles($route);

            $logger->debug("$req->id request passed validation & sanitization", [
                'query' => $req->query,
                'data' => $req->data,
                'files' => $req->files
            ]);

            $controller->getLoggedInUser();

            if (isset($route['forceLoggedOut']) && $controller->loggedInUser != null)
                throw new ErrorPageException(SKOP_ERROR_AUTH_LOGOUT);
            if (isset($route['forceLoggedIn']) && $controller->loggedInUser == null)
                throw new ErrorPageException(SKOP_ERROR_AUTH_LOGIN);
            if (isset($route['forceUserPermissions']))
            {
                $permissions = $controller->loggedInUser?->permissions ?? -1;
                if ($permissions <= $route['forceUserPermissions'])
                    throw new ErrorPageException(SKOP_ERROR_AUTH_NOPERMS);
            }

            $controller->$action();
        }
        catch (ErrorPageException $ex)
        {
            if ($ex->errorPageCode == 0)
                $logger->critical("$req->id request failed unexpectedly", ['exception' => $ex]);
            else
                $logger->info("$req->id request failed with error page", ['exception' => $ex]);

            if ($controller != null)
                $errorController = ErrorController::morph($controller);
            else
            {
                $errorController = new ErrorController($this->twigInstance, $req, $logger);
                $errorController->getLoggedInUser();
            }
            $errorController->showPage($ex);
        }
        catch (Exception $ex)
        {
            $logger->error('Request failed with unhandled exception', [
                'exception' => $ex,
                'request' => $req,
                'controller' => $controller
            ]);
        }
    }
}
