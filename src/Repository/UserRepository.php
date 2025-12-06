<?php

namespace Repository;

use Database\Database;
use Model\User;
use PDO;
use PDOException;

class UserRepository
{
  private $connection;

  public function __construct()
  {
    $this->connection = Database::getConnection();
  }

  public function insert(User $user): User|bool
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
      $user = new User(
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

  public function findByName(string $nome): ?User
  {
    $stmt = $this->connection->prepare("SELECT id, nome, email, telefone, isAdmin FROM users WHERE nome LIKE :nome");
    $stmt->bindValue(":nome", $nome, PDO::PARAM_STR);
    $stmt->execute();

    $row = $stmt->fetch();

    if (!$row) return null;

    $user = new User(
      id: $row["id"],
      nome: $row["nome"],
      email: $row["email"],
      telefone: $row["telefone"],
      isAdmin: $row["isAdmin"]
    );

    return $user;
  }

  public function findUserById(string $id): ?User
  {
    $stmt = $this->connection->prepare("SELECT * FROM users WHERE id = :id");
    $stmt->bindValue(':id', $id, PDO::PARAM_INT);
    $stmt->execute();

    $row = $stmt->fetch();

    if (!$row) {
      return null;
    }

    $user = new User(
      id: $row["id"],
      nome: $row["nome"],
      email: $row["email"],
      telefone: $row["telefone"],
      isAdmin: $row["isAdmin"]
    );

    return $user;
  }

  public function findByEmail(string $email): ?User
  {
    $stmt = $this->connection->prepare("SELECT * FROM users WHERE email LIKE :email");
    $stmt->bindValue(':email', $email, PDO::PARAM_STR);

    $stmt->execute();

    $row = $stmt->fetch();

    if (!$row) return null;

    $user = new User(
      $row["id"],
      $row["nome"],
      $row["email"],
      $row["senha"],
      $row["telefone"],
      $row["isAdmin"],
    );

    return $user;
  }

  public function update(User $user): void
  {
    $stmt = $this->connection->prepare("UPDATE users SET nome = :nome, telefone = :telefone WHERE id = :id");
    $stmt->bindValue(":id", $user->getId(), PDO::PARAM_INT);
    $stmt->bindValue(":nome", $user->getNome(), PDO::PARAM_STR);
    $stmt->bindValue(":telefone", $user->getTelefone(), PDO::PARAM_STR);
    $stmt->execute();
  }

  public function updateAdminStatus(User $user): void
  {
    $stmt = $this->connection->prepare("UPDATE users SET isAdmin = :isAdmin WHERE id = :id");
    $stmt->bindValue(":isAdmin", $user->getIsAdmin(), PDO::PARAM_INT);
    $stmt->bindValue(":id", $user->getId(), PDO::PARAM_INT);
    $stmt->execute();
  }
}
