<?php

namespace Controller;

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
    } else {

      switch ($method) {
        case 'GET':
          $descricao = $request->getQuery()["descricao"] ?? null;
          $response = $this->service->getCategorias($descricao);
          Response::send($response);
          break;

        default:
          # code...
          break;
      }
    }
  }
}
