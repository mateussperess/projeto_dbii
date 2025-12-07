<?php

namespace Controller;

use Error\APIException;
use Http\Request;
use Http\Response;
use Service\CategoriaService;

class CategoriaController
{
  private CategoriaService $service;

  public function __construct()
  {
    $this->service = new CategoriaService();
  }

  public function proccessRequest(Request $request)
  {

    $id = $request->getId();
    $method = $request->getMethod();

    if ($id !== null) {
      switch ($method) {
        case 'PUT':
          $authenticatedUser = $request->getAuthenticatedUser();
          if (!$authenticatedUser || $authenticatedUser->getIsAdmin() != 1) {
            throw new APIException("Acesso negado!", 403);
          }

          $categoria = $this->validateBody($request->getBody(), $method);
          $response = $this->service->update($id, ...$categoria);
          Response::send($response, 200);
          break;
        case 'DELETE':
          $authenticatedUser = $request->getAuthenticatedUser();
          if (!$authenticatedUser || $authenticatedUser->getIsAdmin() != 1) {
            throw new APIException("Acesso negado!", 403);
          }

          $this->service->delete($id);
          Response::send(["status" => "success"], 200);
          break;
      }
    } else {

      switch ($method) {
        case 'GET':
          $descricao = $request->getQuery()["descricao"] ?? null;
          $response = $this->service->getCategorias($descricao);
          Response::send($response);
          break;

        case 'POST':
          $authenticatedUser = $request->getAuthenticatedUser();
          if (!$authenticatedUser || $authenticatedUser->getIsAdmin() != 1) {
            throw new APIException("Acesso negado!", 403);
          }

          $categoria = $this->validateBody($request->getBody(), $method);
          $response = $this->service->insert(...$categoria);
          Response::send($response, 201);
          break;

        default:
          # code...
          break;
      }
    }
  }

  private function validateBody(array $body, string $method): array
  {
    $categoria = [];

    if ($method === "POST" || $method === "PUT") {
      if (!isset($body["descricao"])) {
        throw new APIException("Campo descricao é obrigatório!", 400);
      }

      $categoria["descricao"] = $body["descricao"];
    }

    return $categoria;
  }
}
