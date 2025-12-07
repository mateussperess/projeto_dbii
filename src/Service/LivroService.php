<?php

namespace Service;

use Error\APIException;
use Model\Livro;
use Repository\CategoriaRepository;
use Repository\LivroRepository;

class LivroService
{
  private LivroRepository $livroRepository;
  private CategoriaRepository $categoriaRepository;
  private EmprestimoService $emprestimoService;

  public function __construct()
  {
    $this->livroRepository = new LivroRepository();
    $this->categoriaRepository = new CategoriaRepository();
    $this->emprestimoService = new EmprestimoService($this);
  }

  public function insert(array $data): Livro
  {
    $livro = new Livro(
      null,
      $data['titulo'],
      $data['autor'],
      $data['descricao'],
      $data['ano'],
      $data['n_paginas'],
      0,
      0,
      $data['id_genero']
    );

    $this->validateLivro($livro);

    $this->livroRepository->insert($livro);
    return $livro;
  }

  public function getLivros(?string $titulo): array|Livro
  {
    if (!$titulo) return $this->livroRepository->findAll();

    $livro = $this->livroRepository->findByTitulo($titulo);

    if (!$livro) throw new APIException("Não existe um livro com o título informado!", 404);
    return $livro;
  }

  public function getLivroById(int $id): array|Livro
  {
    if (!$id) return $this->livroRepository->findAll();

    $livro = $this->livroRepository->findLivroById($id);

    if (!$livro) {
      throw new APIException("Livro inexistente!", 400);
    }

    return $livro;
  }

  public function update(int $id, array $data): Livro
  {
    $livro = $this->livroRepository->findLivroById($id);
    if (!$livro) {
      throw new APIException("Livro inexistente!");
    }

    $livro->setTitulo($data['titulo']);
    $livro->setAutor($data['autor']);
    $livro->setDescricao($data['descricao']);
    $livro->setAno($data['ano']);
    $livro->setNumeroPaginas($data['n_paginas']);
    $livro->setIdGenero($data['id_genero']);

    $this->validateLivro($livro);

    return $this->livroRepository->update($livro);
  }

  public function updateIsAlocatedAndNumeroAlocacoes(Livro $livro): Livro
  {
    if ($livro->getId() === null) {
      throw new APIException("Livro inválido.", 400);
    }
    return $this->livroRepository->updateIsAlocatedAndNumeroAlocacoes($livro);
  }

  public function findByCategoriaId(int $categoriaId): array
  {
    return $this->livroRepository->findByCategoriaId($categoriaId);
  }

  public function delete(int $id)
  {
    $livro = $this->livroRepository->findLivroById($id);
    if (!$livro) {
      throw new APIException("Livro não encontrado!", 404);
    }

    $emprestimos = $this->emprestimoService->findEmprestimosAtivosPorLivro($id);
    if (count($emprestimos) > 0) {
      throw new APIException("Livro não pode ser deletado. Possui empréstimos ativos!", 400);
    }

    $this->livroRepository->delete($id);
  }

  private function validateLivro(Livro $livro)
  {
    if (strlen($livro->getTitulo()) < 5) {
      throw new APIException("O título do livro é muito curto!", 400);
    }

    if ($livro->getAno() > 2025) {
      throw new APIException("O ano de publicação do livro é inválido!", 400);
    }

    if ($livro->getNumeroPaginas() <= 0) {
      throw new APIException("Quantidade de páginas inválidas!", 400);
    }

    $categoria = $this->categoriaRepository->findById($livro->getIdGenero());
    if (!$categoria) {
      throw new APIException("Categoria inexistente!", 404);
    }
  }
}
