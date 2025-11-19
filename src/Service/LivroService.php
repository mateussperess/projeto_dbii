<?php 

namespace Service;

use Error\APIException;
use Model\Livro;
use Repository\LivroRepository;

class LivroService
{
  private LivroRepository $repository;

  public function __construct()
  {
    $this->repository = new LivroRepository();
  }

  public function insert(string $titulo, string $autor, string $descricao, int $ano, int $n_paginas, int $id_genero): Livro {
    $livro = new Livro(
      null,
      $titulo,
      $autor,
      $descricao,
      $ano,
      $n_paginas,
      null,
      null,
      $id_genero
    );

    $this->validateLivro($livro);

    $this->repository->insert($livro);
    return $livro;
  }
  
  public function validateLivro(Livro $livro) {
    
  }
}