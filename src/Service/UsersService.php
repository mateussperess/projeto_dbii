<?php 

namespace Service;

use ArrayAccess;
use Model\Users;
use Repository\UsersRepository;

class UsersService {
  private UsersRepository $repository;

  public function __construct() {
    $this->repository = new UsersRepository();
  }

  public function getUsers(?string $nome): array {
    if(!$nome) {
      return $this->repository->findAll();
    } else {
      return $this->repository->findAll();
    }
  }

  public function insert($data)
  {
    $result = $this->repository->insert(new Users(null, $data['nome'], $data['email'], $data['senha'], $data['telefone'], $data['isAdmin']));

    if (!$result) return [];

    return $result;
  }

}