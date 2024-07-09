<?php

require_once __DIR__.'/../vendor/autoload.php';

error_reporting(E_ALL);

$publicPath = str_replace(str_replace('\\', '/', $_SERVER['DOCUMENT_ROOT']), '', str_replace('\\', '/', __DIR__));
if ($publicPath[strlen($publicPath) - 1] != '/')
    $publicPath .= '/';
define('SKOP_PUBLIC_PATH', $publicPath);
define('SKOP_PUBLIC_PATH_FILESYSTEM', $_SERVER['DOCUMENT_ROOT'] . $publicPath);

define('SKOP_APPLICATION_PATH', substr(realpath(__DIR__), 0, -6).'src/');

require_once SKOP_APPLICATION_PATH.'Core/Constant.php';

use Skop\Core\Router;

$router = new Router();
$router->dispatch();


// use Skop\Models\Domain\DiscountClub;
// use \Skop\Models\DiscountClubModel;

// $creating = new DiscountClub();
// $creating->id = 1;
// $creating->name = 'Skop Silver';
// $creating->discount = 10;
// // DiscountClubModel::insert_one($creating);

// var_dump($creating->asArray());
