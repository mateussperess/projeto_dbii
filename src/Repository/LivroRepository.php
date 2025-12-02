<?php

namespace Repository;

use Database\Database;
use Model\Livro;
use PDO;
use PDOException;

class LivroRepository
{
  private $connection;

  public function __construct()
  {
    $this->connection = Database::getConnection();
  }

  public function insert(Livro $livro): Livro|bool
  {

    try {
      $stmt = $this->connection->prepare("INSERT INTO livros (titulo, autor, descricao, ano, n_paginas, id_genero) VALUES (:titulo, :autor, :descricao, :ano, :n_paginas, :id_genero)");
      $stmt->bindValue(":titulo", $livro->getTitulo(), PDO::PARAM_STR);
      $stmt->bindValue(":autor", $livro->getAutor(), PDO::PARAM_STR);
      $stmt->bindValue(":descricao", $livro->getDescricao(), PDO::PARAM_STR);
      $stmt->bindValue(":ano", $livro->getAno(), PDO::PARAM_INT);
      $stmt->bindValue(":n_paginas", $livro->getNumeroPaginas(), PDO::PARAM_INT);
      $stmt->bindValue(":id_genero", $livro->getIdGenero(), PDO::PARAM_INT);

      $stmt->execute();

      $lastId = $this->connection->lastInsertId();
      $livro->setId($lastId);

      return $livro;
    } catch (PDOException $e) {
      throw new PDOException("Erro ao inserir livro no banco de dados: " . $e->getMessage(), 500);
    }
  }

  public function findAll(): array|bool
  {
    $stmt = $this->connection->prepare("SELECT id, titulo, autor, descricao, ano, n_paginas, isAlocated, n_alocated, id_genero FROM livros");
    $stmt->execute();

    $rows = $stmt->fetchAll();

    $livros = [];

    foreach ($rows as $row) {
      $livro = new Livro(
        $row["id"],
        $row["titulo"],
        $row["autor"],
        $row["descricao"],
        $row["ano"],
        $row["n_paginas"],
        $row["isAlocated"],
        $row["n_alocated"],
        $row["id_genero"],
      );

      $livros[] = $livro;
    }

    return $livros;
  }

  public function findByTitulo(string $titulo): ?Livro
  {
    $stmt = $this->connection->prepare("SELECT * FROM livros WHERE titulo LIKE :titulo");
    $stmt->bindValue(":titulo", $titulo, PDO::PARAM_STR);
    $stmt->execute();

    $row = $stmt->fetch();

    if (!$row) return null;

    $livro = new Livro(
      $row["id"],
      $row["titulo"],
      $row["autor"],
      $row["descricao"],
      $row["ano"],
      $row["n_paginas"],
      $row["isAlocated"],
      $row["n_alocated"],
      $row["id_genero"],
    );

    return $livro;
  }

  public function findLivroById(int $id): ?Livro
  {
    $stmt = $this->connection->prepare("SELECT * FROM livros WHERE id = :id");
    $stmt->bindValue(":id", $id, PDO::PARAM_INT);
    $stmt->execute();

    $row = $stmt->fetch();

    if (!$row) return null;

    $livro = new Livro(
      $row["id"],
      $row["titulo"],
      $row["autor"],
      $row["descricao"],
      $row["ano"],
      $row["n_paginas"],
      $row["isAlocated"],
      $row["n_alocated"],
      $row["id_genero"],
    );

    return $livro;
  }
}
