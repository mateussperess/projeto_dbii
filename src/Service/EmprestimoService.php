<?php

namespace Service;

use Error\APIException;
use Model\Emprestimo;
use Repository\EmprestimoRepository;

class EmprestimoService 
{
    private EmprestimoRepository $emprestimoRepository;
    private LivroService $livroService;
    private UserService $userService;

    public function __construct(LivroService $livroService)
    {
        $this->emprestimoRepository = new EmprestimoRepository();
        $this->livroService = $livroService;
        $this->userService = new UserService();
    }

    public function criarEmprestimo(int $idUser, int $idLivro): Emprestimo
    {
        $livro = $this->livroService->getLivroById($idLivro);

        $openLoan = $this->emprestimoRepository->findOpenLoansByUser($idUser);
        if ($openLoan) {
            throw new APIException("Você possui empréstimo/s em atraso!", 400);
        }

        $emprestimoAbertoLivro = $this->emprestimoRepository->findOpenLoanByBook($idLivro);
        if ($emprestimoAbertoLivro) {
            throw new APIException("Este livro já está emprestado!", 400);
        }

        $emprestimo = new Emprestimo(
            null,
            $idUser,
            $idLivro,
            date("Y-m-d H:i:s"),
            null
        );

        $emprestimo = $this->emprestimoRepository->insert($emprestimo);

        $livro->setIsAlocated(1);
        $livro->setNumeroLocacoes($livro->getNumeroLocacoes() + 1);

        $this->livroService->updateIsAlocatedAndNumeroAlocacoes($livro);
        return $emprestimo;
    }

    public function devolverEmprestimo(int $idUser, int $idLivro): Emprestimo
    {
        $livro = $this->livroService->getLivroById($idLivro);

        $openLoan = $this->emprestimoRepository->findOpenLoansByUser($idUser);
        if ($openLoan) {
            $emprestimoAbertoLivro = $this->emprestimoRepository->findOpenLoanByBook($idLivro);
            if ($emprestimoAbertoLivro) {
                $this->emprestimoRepository->updateEntrega($emprestimoAbertoLivro->getId());
                $livro->setIsAlocated(0);

                $this->livroService->updateIsAlocatedAndNumeroAlocacoes($livro);

                throw new APIException("Livro Devolvido", 201);
            }else{
                throw new APIException("Este livro não está emprestado!", 400);
            }
        }else{
            throw new APIException("Você não possui empréstimo/s em atraso!", 400);
        }


    }


    public function getAllEmprestimos(){
        $openLoan = $this->emprestimoRepository->findAllOpenLoans();
        if (!$openLoan) {
            throw new APIException("Não existem emprestimos", 400);
        }

        return $openLoan;

    }

    public function getEmprestimos(?int $idUser=null){
        if($idUser){
            $openLoan = $this->emprestimoRepository->findAllLoansByUser($idUser);
            if (!$openLoan) {
                throw new APIException("Usuario não possui emprestimos", 400);
            }
        }else{
           $openLoan = $this->emprestimoRepository->findAllOpenLoans();
            if (!$openLoan) {
                throw new APIException("Não existem emprestimos", 400);
            } 
        }
        
        return $openLoan;
    }
    public function findEmprestimosAtivosPorLivro(int $livroId): array
    {
        return $this->emprestimoRepository->findEmprestimosAtivosPorLivroId($livroId);
    }
}