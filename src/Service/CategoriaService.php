<?php

namespace Service;

use Error\APIException;
use Model\Categoria;
use Repository\CategoriaRepository;

class CategoriaService
{
  private CategoriaRepository $repository;

  public function __construct()
  {
    $this->repository = new CategoriaRepository();
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
