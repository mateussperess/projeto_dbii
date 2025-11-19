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
}