<?php

namespace Model;

use JsonSerializable;

class Livro implements JsonSerializable 
{
  private ?int $id;
  private string $titulo;
  private string $autor;
  private string $descricao;
  private int $ano;
  private int $numeroPaginas;
  private int $isLocated;
  private ?int $numeroLocacoes;
  private int $idGenero;

  public function __construct(
    ?int $id = null,
    string $titulo,
    string $autor,
    string $descricao,
    int $ano,
    int $numeroPaginas,
    int $isLocated,
    ?int $numeroLocacoes = null,
    int $idGenero
  )
  {
    $this->id = $id;
    $this->titulo = $titulo;
    $this->autor = $autor;
    $this->descricao = $descricao;
    $this->ano = $ano;
    $this->numeroPaginas = $numeroPaginas;
    $this->isLocated = $isLocated;
    $this->numeroLocacoes = $numeroLocacoes;
    $this->idGenero = $idGenero;
  }

  public function jsonSerialize(): mixed
  {
    throw new \Exception('Not implemented');
  }
}