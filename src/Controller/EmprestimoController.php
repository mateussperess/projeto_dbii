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

                    if($data["devolver"] == 1){
                        $emprestimo = $this->emprestimoService->devolverEmprestimo($user->getId(), $data['id_livro']);
                    }else{
                        $emprestimo = $this->emprestimoService->criarEmprestimo($user->getId(), $data['id_livro']);
                    }
                    
                    Response::send($emprestimo);
                    break;
                    
                case "GET":
                    $userId = $request->getQuery()["user_id"] ?? null;
                    $mode = $request->getQuery()["mode"] ?? null;

                    $user = $request->getAuthenticatedUser();

                    if($mode == "all" && $user->getIsAdmin() == 1){
                        $emprestimos = $this->emprestimoService->getEmprestimos();
                    }else{
                        if ($user->getId() != $userId && $user->getIsAdmin() != 1) {
                            throw new APIException("Acesso negado!", 403);
                        }else{
                            $emprestimos = $this->emprestimoService->getEmprestimos($userId);
                        }
                    }
                    

                    
                    Response::send($emprestimos);
                    break;
                default:
                    throw new APIException("Method not allowed", 405);
                    break;
            }
        }
    }

    private function validateBody(array $body): array
    {
        $devolver = 0;
        
        if (!isset($body['id_livro'])) {
            throw new APIException("O id do livro é obrigatório!", 400);
        }
        if (isset($body['devolver']) && $body['devolver'] == 1) {
            $devolver = 1;
        }

        return [
            "id_livro" => $body['id_livro'],
            "devolver" => $devolver
        ];
    }
}
