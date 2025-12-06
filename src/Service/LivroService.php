<?php 

namespace Service;

use Error\APIException;
use Exception;
use Model\Livro;
use Repository\CategoriaRepository;
use Repository\LivroRepository;

class LivroService
{
  private LivroRepository $repository;
  private CategoriaRepository $categoriaRepository;

  public function __construct()
  {
    $this->repository = new LivroRepository();
    $this->categoriaRepository = new CategoriaRepository();
  }

  public function insert(array $data): Livro {
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

    $this->repository->insert($livro);
    return $livro;
  }

  public function getLivros(?string $titulo) : array|Livro {
    if(!$titulo) return $this->repository->findAll();

    $livro = $this->repository->findByTitulo($titulo);

    if(!$livro) throw new APIException("Não existe um livro com o título informado!", 404);
    return $livro;
  }

  public function getLivroById(int $id): array|Livro {
    if(!$id) return $this->repository->findAll();

    $livro = $this->repository->findLivroById($id);

    if (!$livro) {
      throw new APIException("Livro inexistente!", 400);
    }

    return $livro;
  }

  public function update(Livro $livro): Livro
    {
        if ($livro->getId() === null) {
            throw new APIException("Livro inválido.", 400);
        }
        return $this->repository->update($livro);
    }
  
  private function validateLivro(Livro $livro) {
    if(strlen($livro->getTitulo()) < 5) {
      throw new APIException("O título do livro é muito curto!", 400);
    }

    if($livro->getAno() > 2025) {
      throw new APIException("O ano de publicação do livro é inválido!", 400);
    }

    if($livro->getNumeroPaginas() <= 0) {
      throw new APIException("Quantidade de páginas inválidas!", 400);
    }

    $categoria = $this->categoriaRepository->findById($livro->getIdGenero());
    if (!$categoria) {
      throw new APIException("Categoria inexistente!", 404);
    }
  }
}