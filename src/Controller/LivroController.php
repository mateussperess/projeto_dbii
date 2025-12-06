<?php

namespace Controller;

use Error\APIException;
use Http\Request;
use Http\Response;
use Service\LivroService;

class LivroController
{
  private LivroService $livroService;

  public function __construct()
  {
    $this->livroService = new LivroService();
  }

  public function proccessRequest(Request $request)
  {
    $id = $request->getId();
    $method = $request->getMethod();

    if ($id !== null) {
      switch ($method) {
        case 'GET':
          $response = $this->livroService->getLivroById($id);
          Response::send($response);
          break;

        case 'PUT':
          $authenticatedUser = $request->getAuthenticatedUser();
          if (!$authenticatedUser || $authenticatedUser->getIsAdmin() !== 1) {
            throw new APIException("Acesso negado!", 403);
          }

          $data = $this->validateBody($request->getBody(), $method);
          $response = $this->livroService->update($id, $data);

          Response::send($response);
          break;

        case 'DELETE':
          $authenticatedUser = $request->getAuthenticatedUser();
          if (!$authenticatedUser || $authenticatedUser->getIsAdmin() !== 1) {
            throw new APIException("Acesso negado!", 403);
          }

          $this->livroService->delete($id);
          Response::send(["status" => "success"], 200);
          break;

        default:
          throw new APIException("Method not allowed", 405);
          break;
      }
    } else {
      switch ($method) {
        case 'GET':
          $tituloLivro = $request->getQuery()["titulo"] ?? null;
          $response = $this->livroService->getLivros($tituloLivro);
          Response::send($response);
          break;

        case 'POST':
          $authenticatedUser = $request->getAuthenticatedUser();
          if (!$authenticatedUser || $authenticatedUser->getIsAdmin() !== 1) {
            throw new APIException("Acesso negado!", 403);
          }

          $data = $this->validateBody($request->getBody(), $method);
          $response = $this->livroService->insert($data);
          Response::send($response);
          break;

        default:
          throw new APIException("Method not allowed", 405);
          break;
      }
    }
  }

  private function validateBody(array $body, string $method): array
  {
    $livro = [];

    if ($method === "POST" || $method === "PUT") {
      if (!isset($body["titulo"])) {
        throw new APIException("Campo titulo é obrigatório!", 400);
      }
      $livro["titulo"] = $body["titulo"];

      if (!isset($body["autor"])) {
        throw new APIException("Campo autor é obrigatório!", 400);
      }
      $livro["autor"] = $body["autor"];

      if (!isset($body["descricao"])) {
        throw new APIException("Campo descricao é obrigatório!", 400);
      }
      $livro["descricao"] = $body["descricao"];

      if (!isset($body["ano"])) {
        throw new APIException("Campo ano é obrigatório!", 400);
      }
      $livro["ano"] = $body["ano"];

      if (!isset($body["n_paginas"])) {
        throw new APIException("Campo n_paginas é obrigatório!", 400);
      }
      $livro["n_paginas"] = $body["n_paginas"];

      if (!isset($body["id_genero"])) {
        throw new APIException("Campo id_genero é obrigatório!", 400);
      }
      $livro["id_genero"] = $body["id_genero"];
    }

    return $livro;
  }
}
