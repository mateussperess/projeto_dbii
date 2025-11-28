<?php

namespace Controller;

use Error\APIException;
use Http\Request;
use Http\Response;
use Service\AuthService;

class AuthController
{
    private AuthService $authService;

    public function __construct()
    {
        $this->authService = new AuthService();
    }

    public function proccessRequest(Request $request)
    {
        $method = $request->getMethod();

        switch ($method) {
            case 'POST':
                $data = $this->validateBody($request->getBody());
                $token = $this->authService->login($data["email"], $data["senha"]);
                
                Response::send($token, 201);
                break;
            case "DELETE": 
                $user = $request->getAuthenticatedUser();
                $this->authService->logout($user);

                Response::send([
                    "status" => "success"
                ], 200);
                break;
            default:
                throw new APIException("Method not allowed!", 405);
        }
    }


    private function validateBody(array $body): array
    {
        if (!isset($body["email"])) {
            throw new APIException("Campo email é obrigatório!", 400);
        }

        if (!isset($body["senha"])) {
            throw new APIException("Campo senha é obrigatório!", 400);
        }

        return [
            "email" => $body["email"],
            "senha" => $body["senha"]
        ];
    }
}
