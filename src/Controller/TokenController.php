<?php

namespace Controller;

use Error\APIException;
use Service\TokenService;
use Http\Request;
use Http\Response;
use Model\Token;

class TokenController
{
  private TokenService $service;

  public function __construct()
  {
    $this->service = new TokenService();
  }

  
  public function proccessRequest(Request $request)
  {

    $id = $request->getId();
    $method = $request->getMethod();

    switch ($method) {

        case 'POST':
          $login = $this->validateBody($request->getBody());
          $response = $this->service->verifyLogin($login["email"], $login["senha"]);
          Response::send($response, 201);
          break;

        default:
          throw new APIException("Method not allowed!", 405);
    }
 }
  

  private function validateBody(array $body): array
  {
    $login = [];
      
      if (!isset($body["email"])) {
        throw new APIException("Campo email é obrigatório!", 400);
      }
      $login["email"] = $body["email"];

      if (!isset($body["senha"])) {
        throw new APIException("Campo senha é obrigatório!", 400);
      }
      $login["senha"] = $body["senha"];

    return $login;
  }


}
