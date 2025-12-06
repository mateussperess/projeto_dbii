<?php

namespace Controller;

use Error\APIException;
use Http\Request;
use Http\Response;
use Service\EmprestimoService;
use Service\LivroService;

class EmprestimoController
{
    private EmprestimoService $emprestimoService;

    public function __construct()
    {
        $this->emprestimoService = new EmprestimoService(new LivroService());
    }

    public function processRequest(Request $request)
    {
        $id = $request->getId();
        $method = $request->getMethod();

        if ($id !== null) {
        } else {
            switch ($method) {
                case "POST":
                    $user = $request->getAuthenticatedUser();
                    $data = $this->validateBody($request->getBody());
                    $emprestimo = $this->emprestimoService->criarEmprestimo($user->getId(), $data['id_livro']);
                    Response::send($emprestimo);
                    break;
                    
                default:
                    throw new APIException("Method not allowed", 405);
                    break;
            }
        }
    }

    private function validateBody(array $body): array
    {
        if (!isset($body['id_livro'])) {
            throw new APIException("O id do livro é obrigatório!", 400);
        }

        return [
            "id_livro" => $body['id_livro']
        ];
    }
}
