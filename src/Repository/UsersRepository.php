<?php

namespace Repository;

use Database\Database;
use Error\APIException;
use Model\Users;
use PDO;
use PDOException;

class UsersRepository
{
  private $connection;

  public function __construct()
  {
    $this->connection = Database::getConnection();
  }

  public function insert(Users $user): Users|bool
  {
    try {
      $stmt = $this->connection->prepare("INSERT INTO users (nome, email, senha, telefone, isAdmin) VALUES (:nome, :email, :senha, :telefone, :isAdmin)");
      $stmt->bindValue(":nome", $user->getNome(), PDO::PARAM_STR);
      $stmt->bindValue(":email", $user->getEmail(), PDO::PARAM_STR);
      $stmt->bindValue(":senha", password_hash($user->getSenha(), PASSWORD_DEFAULT), PDO::PARAM_STR);
      $stmt->bindValue(":telefone", $user->getTelefone(), PDO::PARAM_STR);
      $stmt->bindValue(":isAdmin", $user->getIsAdmin(), PDO::PARAM_INT);

      $stmt->execute();

      $lastId = $this->connection->lastInsertId();
      $user->setId($lastId);

      return $user;
    } catch (PDOException $e) {
      throw new PDOException("Erro ao inserir usuário no banco de dados", 500);
    }
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

  public function findByEmail(string $email): ?Users
  {
    $stmt = $this->connection->prepare("SELECT * FROM users WHERE email LIKE :email");
    $stmt->bindValue(':email', $email, PDO::PARAM_STR);

    $stmt->execute();

    $row = $stmt->fetch();

    if (!$row) return null;

    $user = new Users(
      $row["id"],
      $row["nome"],
      $row["email"],
      $row["telefone"],
      $row["isAdmin"],
    );

    return $user;
  }

  public function update(Users $user): void {
    $stmt = $this->connection->prepare("UPDATE users SET nome = :nome, email = :email, senha = :senha, telefone = :telefone, isAdmin = :isAdmin WHERE id = :id");
    $stmt->bindValue(":id", $user->getId(), PDO::PARAM_INT);
    $stmt->bindValue(":nome", $user->getNome(), PDO::PARAM_STR);
    $stmt->bindValue(":email", $user->getEmail(), PDO::PARAM_STR);
    $stmt->bindValue(":senha", $user->getSenha(), PDO::PARAM_STR);
    $stmt->bindValue(":telefone", $user->getTelefone(), PDO::PARAM_STR);
    $stmt->bindValue(":isAdmin", $user->getIsAdmin(), PDO::PARAM_INT);
    $stmt->execute();
  }
}
