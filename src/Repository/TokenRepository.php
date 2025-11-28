<?php

namespace Repository;

use Database\Database;
use Model\Token;
use PDO;
use PDOException;

class TokenRepository
{
  private $connection;

  public function __construct()
  {
    $this->connection = Database::getConnection();
  }

  public function insert(Token $token): Token|bool
  {
    try {
      
      $stmt = $this->connection->prepare("INSERT INTO token (token, expiredAt, id_user) VALUES (:token, :expiredAt, :id_user)");
      //$stmt = $this->connection->prepare("INSERT INTO token (token, expiredAt, id_user) VALUES ('teste', 10, 1)");

      
      $stmt->bindValue(":token", $token->getToken(), PDO::PARAM_STR);
      $stmt->bindValue(":expiredAt", $token->getExpiredAt(), PDO::PARAM_INT);
      $stmt->bindValue(":id_user", $token->getIdUser(), PDO::PARAM_INT);

      $stmt->execute();

      $lastId = $this->connection->lastInsertId();
      $token->setId($lastId);

      return $token;
    } catch (PDOException $e) {
      throw new PDOException("Erro ao inserir token no banco de dados: " . $e->getMessage(), 500);
    }
  }

  public function update(Token $token): Token|bool
  {
    try {
      $stmt = $this->connection->prepare("UPDATE token SET token = :token, expiredAt = :expiredAt WHERE id_user = :id_user");
      $stmt->bindValue(":token", $token->getToken(), PDO::PARAM_STR);
      $stmt->bindValue(":expiredAt", $token->getExpiredAt(), PDO::PARAM_INT);
      $stmt->bindValue(":id_user", $token->getIdUser(), PDO::PARAM_INT);

      $stmt->execute();

      return $token;
    } catch (PDOException $e) {
      throw new PDOException("Erro ao atualizar token no banco de dados: " . $e->getMessage(), 500);
    }
  }

  public function findAll() : array|bool {
    $stmt = $this->connection->prepare("SELECT * FROM token");
    $stmt->execute();
    
    $rows = $stmt->fetchAll();

    $tokens = [];

    foreach ($rows as $row) {
      $token = new token(
        $row["id"],
        $row["token"],
        $row["expiredAt"],
        $row["id_user"],
      );

      $tokens = $token;
    }

    return $tokens;
  }

  public function findByUser(string $userId): ?Token {
    $stmt = $this->connection->prepare("SELECT * FROM token WHERE id_user LIKE :id_user");
    $stmt->bindValue(":id_user", $userId, PDO::PARAM_STR);
    $stmt->execute();

    $row = $stmt->fetch();

    if(!$row) return null;

    $token = new token(
        $row["id"],
        $row["token"],
        $row["expiredAt"],
        $row["id_user"],
      );

    return $token;
  }

  public function findByToken(string $token): ?Token {
    $stmt = $this->connection->prepare("SELECT * FROM token WHERE token LIKE :token");
    $stmt->bindValue(":token", $token, PDO::PARAM_STR);
    $stmt->execute();

    $row = $stmt->fetch();

    if(!$row) return null;

    $token = new token(
        $row["id"],
        $row["token"],
        $row["expiredAt"],
        $row["id_user"],
      );

    return $token;
  }
}
