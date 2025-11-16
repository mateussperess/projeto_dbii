<?php 

namespace Service;

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
}