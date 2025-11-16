<?php 

namespace Repository;

use Database\Database;
use Model\Users;

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
}