<?php

use Controller\CategoriaController;
use Controller\LivroController;
use Controller\UserController;
use Controller\TokenController;
use Error\APIException;
use Http\Request;
use Http\Response;

require_once "src/config.php";

ini_set('display_errors', 1);
ini_set('display_startup_erros', 1);
error_reporting(E_ALL);

$uri = $_SERVER["REQUEST_URI"];
$method = $_SERVER["REQUEST_METHOD"];
$body = file_get_contents("php://input");
$request = new Request($uri, $method, $body);

switch ($request->getResource()) {
  case 'user':
    $userController = new UserController();
    $userController->proccessRequest($request);
    break;

  case "categoria":
    $categoriasController = new CategoriaController();
    $categoriasController->proccessRequest($request);
    break;

  case "livro":
    $livroController = new LivroController();
    $livroController->proccessRequest($request);
    break;

  case "token":
    $tokenController = new TokenController();
    $tokenController->proccessRequest($request);
    break;

  case null:
    $endpoints = [
      "GET /api/users"
    ];

    Response::send(["endpoints" => $endpoints]);
    break;

  default:
    throw new APIException("Resource not found!", 406);
}
