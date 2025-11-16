<?php

namespace Database;

use Error\APIException;
use PDO;
use PDOException;

class Database
{
  private static string $database = __DIR__ . "/projeto.sqlite";

  private static ?PDO $connection = null;

  public static function getConnection(): PDO
  {
    if (self::$connection === null) {
      try {
        $dsn = "sqlite:" . self::$database;
        self::$connection = new PDO($dsn);

        self::$connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        self::$connection->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);

        self::$connection->exec("PRAGMA foreign_keys = ON;"); // ativa as chaves estrangeiras
      } catch (PDOException $e) {
        throw new APIException("Erro ao conectar no banco de dados: " . $e->getMessage(), 500);
      }
    }

    return self::$connection;
  }
}
