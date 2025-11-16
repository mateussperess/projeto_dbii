<?php

use Controller\UsersController;
use Error\APIException;
use Http\Request;
use Http\Response;

require_once "src/config.php";

$uri = $_SERVER["REQUEST_URI"];
$method = $_SERVER["REQUEST_METHOD"];
$body = file_get_contents("php://input");
$request = new Request($uri, $method, $body);

switch ($request->getResource()) {
  case 'users':
    $usersController = new UsersController();
    break;
  case null:
    $endpoints = [
      "GET /api/users"
    ];

    Response::send(["endpoints" => $endpoints]);
    break;

  default:
    throw new APIException("Resource not found!", 404);
}
