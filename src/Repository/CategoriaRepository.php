<?php

namespace Repository;

use Database\Database;
use Model\Categoria;
use PDO;
use PDOException;

class CategoriaRepository
{
  private $connection;

  public function __construct()
  {
    $this->connection = Database::getConnection();
  }

  public function findAll()
  {
    $stmt = $this->connection->prepare("SELECT id, descricao FROM categorias");
    $stmt->execute();
    $rows = $stmt->fetchAll();

    $categorias = [];

    foreach ($rows as $row) {
      $categoria = new Categoria(
        $row["id"],
        $row["descricao"]
      );

      $categorias[] = $categoria;
    }

    return $categorias;
  }
  public function findByDescricao(string $descricao): ?Categoria
  {
    $stmt = $this->connection->prepare("SELECT id, descricao FROM categorias WHERE descricao LIKE :descricao");
    $stmt->bindValue(":descricao", $descricao, PDO::PARAM_STR);
    $stmt->execute();

    $row = $stmt->fetch();

    if (!$row) return null;

    $categoria = new Categoria(
      $row["id"],
      $row["descricao"],
    );

    return $categoria;
  }

  public function insert(Categoria $categoria): Categoria|bool
  {
    try {
      $stmt = $this->connection->prepare("INSERT INTO categorias (descricao) VALUES (:descricao)");
      $stmt->bindValue(":descricao", $categoria->getDescricao(), PDO::PARAM_STR);

      $stmt->execute();

      $lastId = $this->connection->lastInsertId();
      $categoria->setId($lastId);
      return $categoria;
    } catch (PDOException $e) {
      throw new PDOException("Erro ao inserir categoria no banco de dados", 500);
    }
  }

  public function findById(int $id): ?Categoria
  {
    $stmt = $this->connection->prepare("SELECT * FROM categorias WHERE id = :id");
    $stmt->bindValue(":id", $id, PDO::PARAM_INT);
    $stmt->execute();
    $row = $stmt->fetch();

    return $row ?
      new Categoria(
        $row["id"],
        $row["descricao"]
      ) : null;
  }
}
