<?php

namespace Service;

use Error\APIException;
use Model\Categoria;
use Repository\CategoriaRepository;

class CategoriaService
{
  private CategoriaRepository $repository;
  private LivroService $livroService;

  public function __construct()
  {
    $this->repository = new CategoriaRepository();
    $this->livroService = new LivroService();
  }

  public function getCategorias(?string $descricao): array|Categoria
  {
    if (!$descricao) return $this->repository->findAll();

    $categoria = $this->repository->findByDescricao($descricao);

    if (!$categoria) throw new APIException("Não existe uma categoria cadastrada com esse nome!", 404);

    return $categoria; 
  }

  public function insert(string $descricao): Categoria {
    $categoria = new Categoria(
      null,
      $descricao
    );

    $this->validateCategoria($categoria);

    $this->repository->insert($categoria);
    return $categoria;
  }

  public function update(int $id, string $descricao): Categoria
  {
    $categoria = $this->repository->findById($id);
    if (!$categoria) {
      throw new APIException("Categoria inexistente!");
    }

    $categoria->setDescricao($descricao);
    $this->validateCategoria($categoria);
    return $this->repository->update($categoria);
  }

  public function delete(int $id)
  {
    $categoria = $this->repository->findById($id);
    if (!$categoria) {
      throw new APIException("Categoria não encontrada!", 404);
    }

    $livrosCategoria = $this->livroService->findByCategoriaId($id);
    if (count($livrosCategoria) > 0) {
      throw new APIException("Não foi possível deletar. Existem livros com esta categoria!", 400);
    }

    $this->repository->delete($id);
  }

  private function validateCategoria(Categoria $categoria) {
    if(strlen($categoria->getDescricao()) < 5) {
      throw new APIException("Nome de categoria muito curta!", 400);
    }

    $categoriaAlreadyExists = $this->repository->findByDescricao($categoria->getDescricao());
    if($categoriaAlreadyExists) {
      if($categoriaAlreadyExists !== $categoria->getId()) {
        throw new APIException("Esta categoria já está em uso!", 409);
      }
    }
  }
}
