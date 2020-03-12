<?php
use Src\Controller\Authenticator;
use Src\Controller\ConfidentialFile;

header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: GET,POST,DELETE");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$uri = explode('/', $uri);

if ($uri[1] !== 'api' || $uri[2] !== 'v1' || $uri[3] !== 'file' || $uri[4] !== 'confidential') {
    header("HTTP/1.1 404 Not Found");
    exit();
}

$file = null;
if (isset($uri[3])) {
    $file = (string) $uri[2];
}

if (! Authenticator::authenticate()) {
    header("HTTP/1.1 401 Unauthorized");
    exit('Unauthorized');
}

$requestMethod = $_SERVER["REQUEST_METHOD"];

$confidentialFiles = new ConfidentialFile($requestMethod, $file);
$confidentialFiles->processRequest();