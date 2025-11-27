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
          if ((int) $authenticatedUser->getId() != $id && (int) $authenticatedUser->getIsAdmin() != 1) {
            throw new APIException("Acesso negado! Você só pode ver seus próprios dados.", 403);
          }

          $response = $this->service->getUserById($id);
          Response::send($response);
          break;

        case 'PUT':
          $authenticatedUser = $request->getAuthenticatedUser();
          if ((int) $authenticatedUser->getId() != $id && (int) $authenticatedUser->getIsAdmin() != 1) {
            throw new APIException("Acesso negado! Você só pode alterar seus próprios dados.", 403);
          }

          $user = $this->validateBody($request->getBody(), $method);

          if ((int) $authenticatedUser->getIsAdmin() != 1 && isset($user["isAdmin"])) {
            throw new APIException("Acesso negado! Apenas administradores podem alterar permissões.", 403);
          }

          $user["id"] = $id;
          $response = $this->service->updateUser(
            $user["id"],
            $user["nome"],
            $user["email"],
            $user["senha"],
            $user["telefone"],
            $user["isAdmin"] ?? null
          );
          
          Response::send($response);
          break;

        default:
          throw new APIException("Method not allowed!", 405);
      }
    } else { // rotas que nao possuem um id
      switch ($method) {
        case 'GET':
          $authenticatedUser = $request->getAuthenticatedUser();

          if (!$authenticatedUser || (int) $authenticatedUser->getIsAdmin() != 1) {
            throw new APIException("Acesso negado! Apenas administradores podem listar usuários.", 403);
          }

          $nome = $request->getQuery()["nome"] ?? null;
          $response = $this->service->getUsersWithPermission($authenticatedUser, $nome);
          Response::send($response);
          break;

        case 'POST':
          $user = $this->validateBody($request->getBody(), $method);

          $user["isAdmin"] = 0;

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

      if (isset($body["isAdmin"])) {
        $user["isAdmin"] = $body["isAdmin"];
      }
    }

    return $user;
  }
}
