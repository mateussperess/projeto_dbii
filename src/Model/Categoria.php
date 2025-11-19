<?php

namespace Model;

use JsonSerializable;

class Categoria implements JsonSerializable
{
  private ?int $id;
  private string $descricao;
  public function __construct(?int $id = null, string $descricao)
  {
    $this->id = $id;
    $this->descricao = $descricao;
  }

  public function jsonSerialize(): mixed
  {
    return [
      'id' => $this->id,
      'descricao' => $this->descricao
    ];
  }

  // Getters
  public function getId()
  {
    return $this->id;
  }

  public function getDescricao()
  {
    return $this->descricao;
  }

  // Setters 
  public function setId($id)
  {
    $this->id = $id;
  }

  public function setDescricao($descricao)
  {
    $this->descricao = $descricao;
  }
}
