<?php

namespace Model;

use JsonSerializable;

class User implements JsonSerializable
{
  private ?int $id;
  private string $nome;
  private string $email;
  private string $senha;
  private string $telefone;
  private int $isAdmin;

  public function __construct(
    ?int $id = null,
    string $nome = '',
    string $email = '',
    string $senha = '',
    string $telefone = '',
    int $isAdmin = 0
  ) {
    $this->id = $id;
    $this->nome = $nome;
    $this->email = $email;
    $this->senha = $senha;
    $this->telefone = $telefone;
    $this->isAdmin = $isAdmin;
  }

  public function jsonSerialize(): mixed
  {
    return [
      'id' => $this->id,
      'nome' => $this->nome,
      'email' => $this->email,
      'telefone' => $this->telefone,
      'isAdmin' => $this->isAdmin
    ];
  }

  // Getters
  public function getId(): ?int
  {
    return $this->id;
  }

  public function getNome(): string
  {
    return $this->nome;
  }

  public function getEmail(): string
  {
    return $this->email;
  }

  public function getSenha(): string
  {
    return $this->senha;
  }

  public function getTelefone(): string
  {
    return $this->telefone;
  }

  public function getIsAdmin(): int
  {
    return $this->isAdmin;
  }

  // Setters
  public function setId(?int $id): void
  {
    $this->id = $id;
  }

  public function setNome(string $nome): void
  {
    $this->nome = $nome;
  }

  public function setEmail(string $email): void
  {
    $this->email = $email;
  }

  public function setSenha(string $senha): void
  {
    $this->senha = password_hash($senha, PASSWORD_DEFAULT);
  }

  public function setTelefone(string $telefone): void
  {
    $this->telefone = $telefone;
  }

  public function setIsAdmin(int $isAdmin): void
  {
    $this->isAdmin = $isAdmin;
  }
}
