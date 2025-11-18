<?php 

namespace Repository;

use Database\Database;
use Model\Users;
use PDO;

class UsersRepository {
  private $connection;

  public function __construct() {
    $this->connection = Database::getConnection();
  }

  public function findAll(): array {
    $sql = "SELECT * FROM users";
    $stmt = $this->connection->prepare($sql);
    $stmt->execute();

    $users = [];

    foreach ($users as $user) {
      $user = new Users(
        $user["id"],
        $user["nome"],
        $user["email"],
        $user["telefone"],
      );
      $users[] = $user;
    }

    return $users;
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
    return false;

    return $user;
  }
}