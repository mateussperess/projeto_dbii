<?php

namespace Repository;

use Database\Database;
use Model\Users;
use PDO;

class UsersRepository
{
  private $connection;

  public function __construct()
  {
    $this->connection = Database::getConnection();
  }

  public function insert(Users $user): Users|bool
  {
    $stmt = $this->connection->prepare("INSERT INTO users (nome, email, senha, telefone, isAdmin) VALUES (:nome, :email, :senha, :telefone, :isAdmin)");
    $stmt->bindValue(":nome", $user->getNome(), PDO::PARAM_STR);
    $stmt->bindValue(":email", $user->getEmail(), PDO::PARAM_STR);
    $stmt->bindValue(":senha", password_hash($user->getSenha(), PASSWORD_DEFAULT), PDO::PARAM_STR);
    $stmt->bindValue(":telefone", $user->getTelefone(), PDO::PARAM_STR);
    $stmt->bindValue(":isAdmin", $user->getIsAdmin());
    $stmt->execute();

    return $user;
  }

  public function findAll(): array|bool
  {
    $stmt = $this->connection->prepare("SELECT id, nome, email, telefone, isAdmin FROM users");
    $stmt->execute();
    $rows = $stmt->fetchAll();

    $users = [];

    foreach ($rows as $row) {
      $user = new Users(
        $row["id"],
        $row["nome"],
        $row["email"],
        $row["telefone"],
        $row["isAdmin"],
      );

      $users[] = $user;
    }

    return $users;
  }

  public function findByName(string $nome): ?Users
  {
    $stmt = $this->connection->prepare("SELECT id, nome, email, telefone, isAdmin FROM users WHERE nome LIKE :nome");
    $stmt->bindValue(":nome", $nome, PDO::PARAM_STR);
    $stmt->execute();

    $row = $stmt->fetch();

    if (!$row) return null;

    $user = new Users(
      id: $row["id"],
      nome: $row["nome"],
      email: $row["email"],
      telefone: $row["telefone"],
      isAdmin: $row["isAdmin"]
    );

    return $user;
  }

  public function findUserById(string $id): ?Users
  {
    $stmt = $this->connection->prepare("SELECT * FROM users WHERE id = :id");
    $stmt->bindValue(':id', $id, PDO::PARAM_INT);
    $stmt->execute();

    $row = $stmt->fetch();

    if (!$row) {
      return null;
    }

    $user = new Users(
      id: $row["id"],
      nome: $row["nome"],
      email: $row["email"],
      telefone: $row["telefone"],
      isAdmin: $row["isAdmin"]
    );

    return $user;
  }
}
