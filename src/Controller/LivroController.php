<?php

namespace Controller;

use Error\APIException;
use Http\Request;
use Http\Response;
use Service\LivroService;

class LivroController
{
  private LivroService $service;

  public function __construct()
  {
    $this->service = new LivroService();
  }

  public function proccessRequest(Request $request)
  {
    $id = $request->getId();
    $method = $request->getMethod();

    if ($id !== null) {
      switch ($method) {
        case 'GET':
          $response = $this->service->getLivroById($id);
          Response::send($response);
          break;
        
        default:
          # code...
          break;
      }
    } else {
      switch ($method) {
        case 'GET':
          $tituloLivro = $request->getQuery()["titulo"] ?? null;
          $response = $this->service->getLivros($tituloLivro);
          Response::send($response);
          break;

        case 'POST':
          $authenticatedUser = $request->getAuthenticatedUser();
          if(!$authenticatedUser || (int) $authenticatedUser->getIsAdmin() !== 1) {
            throw new APIException("Acesso negado! Apenas administradores podem cadastrar livros!", 403);
          }

          $livro = $this->validateBody($request->getBody(), $method);
          break;

        default:
          throw new APIException("Em desenvolvimento...", 404);
      }
    }
  }

  private function validateBody(array $body, string $method): array
  {
    $livro = [];

    if ($method !== "PATCH") {
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
