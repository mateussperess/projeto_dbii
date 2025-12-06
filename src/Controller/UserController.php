<?php

namespace Controller;

use Error\APIException;
use Service\UserService;
use Http\Request;
use Http\Response;

class UserController
{
  private UserService $service;

  public function __construct()
  {
    $this->service = new UserService();
  }

  public function proccessRequest(Request $request)
  {
    $id = $request->getId();
    $method = $request->getMethod();

    if ($id !== null) { // rotas que possuem um id
      switch ($method) {
        case 'GET':
          $authenticatedUser = $request->getAuthenticatedUser();
          if ((!$authenticatedUser || $authenticatedUser->getIsAdmin() != 1) && $authenticatedUser->getId() != $id) {
            throw new APIException("Acesso negado!", 403);
          }
          $response = $this->service->findUserById($id);

          Response::send($response);
          break;

        case 'PUT':
          $authenticatedUser = $request->getAuthenticatedUser();
          if ((!$authenticatedUser || $authenticatedUser->getIsAdmin() != 1) && $authenticatedUser->getId() != $id) {
            throw new APIException("Acesso negado!", 403);
          }
          $data = $this->validateBody($request->getBody(), "PUT");
          $response = $this->service->updateUserAuthorized($id, $data['nome'], $data['telefone']);

          Response::send($response);
          break;

        case 'PATCH':
          $authenticatedUser = $request->getAuthenticatedUser();
          if ((!$authenticatedUser || $authenticatedUser->getIsAdmin() != 1) && $authenticatedUser->getId() != $id) {
            throw new APIException("Acesso negado!", 403);
          }
          $data = $this->validateBody($request->getBody(), "PATCH");
          $response = $this->service->updateAdminStatus($id, $data['isAdmin']);

          Response::send($response);
          break;
        default:
          throw new APIException("Method not allowed", 405);
          break;
      }
    } else { // rotas que nao possuem um id
      switch ($method) {
        case 'GET':
          $nome = $request->getQuery()["nome"] ?? null;

          $user = $request->getAuthenticatedUser();

          if ($user->getId() != $id && $user->getIsAdmin() != 1) {
            throw new APIException("Acesso negado!", 403);
          }

          $response = $this->service->getUsers($nome);
          Response::send($response);
          break;

        case 'POST':
          $user = $this->validateBody($request->getBody(), $method);
          $response = $this->service->insert(...$user);
          Response::send($response, 201);
          break;

        default:
          throw new APIException("Method not allowed", 405);
          break;
      }
    }
  }

  private function validateBody(array $body, string $method): array
  {
    $user = [];

    if ($method === "POST") {
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
    } else if ($method === "PUT") {
      if (!isset($body["nome"])) {
        throw new APIException("Campo nome é obrigatório!", 400);
      }
      $user["nome"] = $body["nome"];

      if (!isset($body["telefone"])) {
        throw new APIException("Campo telefone é obrigatório!", 400);
      }
      $user["telefone"] = $body["telefone"];
    } else if ($method === "PATCH") {
      if (!isset($body["isAdmin"])) {
        throw new APIException("Campo isAdmin é obrigatório", 400);
      }

      if ($body['isAdmin'] !== 0 && $body['isAdmin'] !== 1) {
        throw new APIException("O campo 'isAdmin' deve ser 0 ou 1", 400);
      }

      $user['isAdmin'] = $body['isAdmin'];
    }

    return $user;
  }
}
