<?php

namespace Service;

use ArrayAccess;
use Error\APIException;
use Model\Users;
use Repository\UsersRepository;

class UsersService
{
  private UsersRepository $repository;

  public function __construct()
  {
    $this->repository = new UsersRepository();
  }

  public function getUsers(?string $nome): array
  {
    if (!$nome) {
      return $this->repository->findAll();
    } else {
      $user = $this->repository->findByName($nome);
      if (!$user) throw new APIException("Não existe um usuário com esse nome!",  404);
      return $this->repository->findByName($nome);
    }
  }

  public function insert($data): array|bool
  {
    $result = $this->repository->insert(new Users(null, $data['nome'], $data['email'], $data['senha'], $data['telefone'], $data['isAdmin']));

    if (!$result) return [];

    return $result;
  }
}
