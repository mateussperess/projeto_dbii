<?php

namespace Model;

use JsonSerializable;

class Token implements JsonSerializable
{
  private ?int $id;
  private string $token;
  private int $expiredAt;
  private ?int $idUser;

  public function __construct(
    ?int $id = null,
    string $token = "",
    ?int $expiredAt = 0,
    ?int $idUser = 0
    
  ) {
    $this->id = $id;
    $this->token = $token;
    $this->expiredAt = $expiredAt ?? time() + 3600;
    $this->idUser = $idUser;
  }

  public function jsonSerialize(): mixed
  {
    return [
      "id" => $this->id,
      "token" => $this->token,
      "expiredAt" => $this->expiredAt,
      "idUser" => $this->idUser
    ];
  }

  // Getters

  public function getId(): ?int
  {
    return $this->id;
  }

  public function getToken(): string
  {
    return $this->token;
  }

  public function getExpiredAt(): ?int
  {
    return $this->expiredAt;
  }

  public function getIdUser(): ?int
  {
    return $this->idUser;
  }

  // Setters
  public function setId(?int $id): void
  {
    $this->id = $id;
  }

  public function setToken(string $token): void
  {
    $this->token = $token;
  }

  public function setExpiredAt(int $expiredAt): void
  {
    $this->expiredAt = $expiredAt;
  }

  public function setIdUser(?int $idUser): void
  {
    $this->idUser = $idUser;
  }
}
