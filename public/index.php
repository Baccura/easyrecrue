<?php
include_once '../src/autoload.php';
include_once '../src/Controller/AuthenticatorController.php';
include_once '../src/Controller/ConfidentialFileController.php';

header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: GET,POST,DELETE");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$uri = explode('/', $uri);

$requestMethod = $_SERVER["REQUEST_METHOD"];

if ($uri[1] !== 'api' || $uri[2] !== 'v1' || $uri[3] !== 'file' || $uri[4] !== 'confidential' || ($uri[5] === 'index' && in_array($requestMethod, ['POST', 'DELETE']))) {
    header("HTTP/1.1 404 Not Found");
    exit('Résultat introuvable');
}

if ($requestMethod == 'GET' && $uri[5] !== 'index') {
    header("HTTP/1.1 404 Not Found");
    exit('Résultat introuvable');
}

$file = null;
if ($requestMethod == 'GET' && $uri[5] === 'index' && isset($uri[6])) {
    $file = (string) $uri[6];
}

if ($requestMethod == 'DELETE' && isset($uri[5]) && $uri[5] !== 'index') {
    $file = (string) $uri[5];
}

$authenticate = src\Controller\AuthenticatorController::authenticate();
if (true !== $authenticate) {
    header("HTTP/1.1 401 Unauthorized");
    exit('Unauthorized');
}

$confidentialFiles = new src\Controller\ConfidentialFileController($requestMethod, $file);
$confidentialFiles->processRequest();