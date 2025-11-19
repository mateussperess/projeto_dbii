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

  // Getters
  public function getId()
  {
    return $this->id;
  }

  public function getTitulo()
  {
    return $this->titulo;
  }

  public function getAutor()
  {
    return $this->autor;
  }

  public function getDescricao()
  {
    return $this->descricao;
  }

  public function getAno()
  {
    return $this->ano;
  }

  public function getNumeroPaginas()
  {
    return $this->numeroPaginas;
  }

  public function getIsLocated()
  {
    return $this->isLocated;
  }

  public function getNumeroLocacoes()
  {
    return $this->numeroLocacoes;
  }

  public function getIdGenero()
  {
    return $this->idGenero;
  }

  // Setters
  public function setId($id)
  {
    $this->id = $id;
  }

  public function setTitulo($titulo)
  {
    $this->titulo = $titulo;
  }

  public function setAutor($autor)
  {
    $this->autor = $autor;
  }

  public function setDescricao($descricao)
  {
    $this->descricao = $descricao;
  }

  public function setAno($ano)
  {
    $this->ano = $ano;
  }

  public function setNumeroPaginas($numeroPaginas)
  {
    $this->numeroPaginas = $numeroPaginas;
  }

  public function setIsLocated($isLocated)
  {
    $this->isLocated = $isLocated;
  }

  public function setNumeroLocacoes($numeroLocacoes)
  {
    $this->numeroLocacoes = $numeroLocacoes;
  }

  public function setIdGenero($idGenero)
  {
    $this->idGenero = $idGenero;
  }
}