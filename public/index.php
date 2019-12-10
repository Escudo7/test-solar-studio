<?php

require __DIR__ . '/../vendor/autoload.php';

use App\Account;

include(__DIR__ . "/../configDb.php");
$dbHost = DBHOST;
$user = DBUSER;
$pass = DBPWD;
$dbName = DBNAME;
$pdo = mysqli_connect($dbHost, $user, $pass, $dbName) or exit('MySQL connection error');
Account::setConnection($pdo);

$method = $_SERVER['REQUEST_METHOD'];
$uri = $_SERVER['REQUEST_URI'];
$hendlers = [
    ['/', 'GET', 'Routes\GetAccounts\routeGetAccounts'],
    ['/', 'POST', 'Routes\PostAccounts\routePostAccounts']
];

foreach ($hendlers as $item) {
    [$handlerRoute, $handlerMethod, $handler] = $item;
    $preparedHandlerRoute = str_replace('/', '\/', $handlerRoute);
    if ($method == $handlerMethod && preg_match("/^$preparedHandlerRoute(\?[\w=&]*)?$/i", $uri)) {
        $handler();
        break;
    }
}

mysqli_close($pdo);
