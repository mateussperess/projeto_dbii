<?php

namespace Service;

use Error\APIException;
use Model\Emprestimo;
use Repository\EmprestimoRepository;

class EmprestimoService 
{
    private EmprestimoRepository $emprestimoRepository;
    private LivroService $livroService;

    public function __construct(LivroService $livroService)
    {
        $this->emprestimoRepository = new EmprestimoRepository();
        $this->livroService = $livroService;
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

        $this->livroService->updateIsAlocatedAndNAlocated($livro);
        return $emprestimo;
    }

    public function findEmprestimosAtivosPorLivro(int $livroId): array
    {
        return $this->emprestimoRepository->findEmprestimosAtivosPorLivroId($livroId);
    }
}