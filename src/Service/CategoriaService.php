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
}
