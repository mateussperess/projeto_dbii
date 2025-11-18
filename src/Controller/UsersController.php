<?php

namespace Controller;

use Error\APIException;
use Service\UsersService;
use Http\Request;
use Http\Response;

class UsersController
{
  private UsersService $service;

  public function __construct()
  {
    $this->service = new UsersService();
  }

  public function proccessRequest(Request $request)
  {

    $id = $request->getId();
    $method = $request->getMethod();

    if ($id !== null) { // rotas que possuem um id
      switch ($method) {
        case 'GET':
          $response = $this->service->getUserById($id);
          Response::send($response);
          break;
        
        case 'PUT':
          $user = $this->validateBody($request->getBody(), $method);
          $user["id"] = $id;
          $response = $this->service->updateUser(...$user);
          Response::send($response);
          break;

        default:
          # code...
          break;
      }
    } else { // rotas que nao possuem um id
      switch ($method) {
        case 'GET':
          $nome = $request->getQuery()["nome"] ?? null;
          $response = $this->service->getUsers($nome);
          Response::send($response);
          break;

        case 'POST':
          $user = $this->validateBody($request->getBody(), $method);
          $response = $this->service->insert(...$user);
          Response::send($response, 201);
          break;

        default:
          throw new APIException("Method not allowed!", 405);
      }
    }
  }

  private function validateBody(array $body, string $method): array
  {
    $user = [];

    if ($method !== "PATCH") {
      if (!isset($body["nome"])) {
        throw new APIException("Campo nome é obrigatório!", 400);
      }
      $user["nome"] = $body["nome"];

      if (!isset($body["email"])) {
        throw new APIException("Campo email é obrigatório!", 400);
      }
      $user["email"] = $body["email"];

      if (!isset($body["senha"])) {
        throw new APIException("Campo senha é obrigatório!", 400);
      }
      $user["senha"] = $body["senha"];

      if (!isset($body["telefone"])) {
        throw new APIException("Campo telefone é obrigatório!", 400);
      }
      $user["telefone"] = $body["telefone"];

      if (!isset($body["isAdmin"])) {
        throw new APIException("Campo isAdmin é obrigatório!", 400);
      }
      $user["isAdmin"] = $body["isAdmin"];
    }

    return $user;
  }
}
