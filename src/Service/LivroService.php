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
      0,
      0,
      $id_genero
    );

    $this->validateLivro($livro);

    $this->repository->insert($livro);
    return $livro;
  }

  public function getLivros(?string $titulo) : array|Livro {
    if(!$titulo) return $this->repository->findAll();

    $livro = $this->repository->findByTitulo($titulo);

    if(!$livro) throw new APIException("Não existe um livro com o título informado!", 404);
    return $livro;
  }
  
  private function validateLivro(Livro $livro) {
    if(strlen($livro->getTitulo()) < 5) {
      throw new APIException("O título do livro é muito curto!", 400);
    }

    if($livro->getAno() > 2025) {
      throw new APIException("O ano de publicação do livro é inválido!", 400);
    }

    if($livro->getNumeroPaginas() < 0) {
      throw new APIException("Quantidade de páginas inválidas!", 400);
    }

    // TODO: verificar se o gênero informado existe...
  }
}