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
          $user = $this->service->insert($request->getBody());
          // echo json_encode($user);
          // $user = $this->validateBody($request->getBody(), $method);

          // $response = $this->service->createNewUser(...$user);
          // Response::send($response, 201);
          break;

        default:
          throw new APIException("Method not allowed!", 405);
      }
    }
  }

  // private function validateBody(array $body, string $method): array {}
}
