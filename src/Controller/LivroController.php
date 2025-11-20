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
    } else {
      switch ($method) {
        case 'GET':
          $tituloLivro = $request->getQuery()["titulo"] ?? null;
          $response = $this->service->getLivros($tituloLivro);
          Response::send($response);
          break;

        default:
          throw new APIException("Em desenvolvimento...", 404);
      }
    }
  }
}
