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
}
