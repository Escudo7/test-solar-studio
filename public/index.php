<?php

require __DIR__ . '/../vendor/autoload.php';

use App\Account;
use function Routes\GetAccounts\routeGetAccounts;
use function Routes\PostAccounts\routePostAccounts;

$method = $_SERVER['REQUEST_METHOD'];
$uri = $_SERVER['REQUEST_URI'];

include(__DIR__ . "/../configDb.php");
$dbHost = DBHOST;
$user = DBUSER;
$pass = DBPWD;
$dbName = DBNAME;
$pdo = mysqli_connect($dbHost, $user, $pass, $dbName) or exit('MySQL connection error');
Account::setConnection($pdo);

switch ($method) {
    case 'GET':
        routeGetAccounts();
    case 'POST':
        routePostAccounts();
}
mysqli_close($pdo);