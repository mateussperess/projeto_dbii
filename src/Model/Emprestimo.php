<?php

namespace Model;

use JsonSerializable;

class Emprestimo implements JsonSerializable
{
    private ?int $id;
    private int $idUser;
    private int $idLivro;
    private string $dataInicio;
    private ?string $dataEntrega;

    public function __construct(
        ?int $id,
        int $idUser,
        int $idLivro,
        string $dataInicio,
        ?string $dataEntrega
    ) {
        $this->id = $id;
        $this->idUser = $idUser;
        $this->idLivro = $idLivro;
        $this->dataInicio = $dataInicio;
        $this->dataEntrega = $dataEntrega;
    }

    public function jsonSerialize(): mixed
    {
        return [
            "id" => $this->id,
            "idUser" => $this->idUser,
            "idLivro" => $this->idLivro,
            "dataInicio" => $this->dataInicio,
            "dataEntrega" => $this->dataEntrega
        ];
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(int $id): void
    {
        $this->id = $id;
    }

    public function getIdUser(): int
    {
        return $this->idUser;
    }

    public function setIdUser(int $idUser): void
    {
        $this->idUser = $idUser;
    }

    public function getIdLivro(): int
    {
        return $this->idLivro;
    }

    public function setIdLivro(int $idLivro): void
    {
        $this->idLivro = $idLivro;
    }

    public function getDataInicio(): string
    {
        return $this->dataInicio;
    }

    public function setDataInicio(string $dataInicio): void
    {
        $this->dataInicio = $dataInicio;
    }

    public function getDataEntrega(): ?string
    {
        return $this->dataEntrega;
    }

    public function setDataEntrega(string $dataEntrega): void
    {
        $this->dataEntrega = $dataEntrega;
    }
}
