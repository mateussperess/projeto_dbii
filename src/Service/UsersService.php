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

  public function getUsers(?string $nome): array|Users
  {
    if (!$nome) return $this->repository->findAll();

    $user = $this->repository->findByName($nome);

    if (!$user) throw new APIException("Não existe um usuário com esse nome!",  404);
    return $user;
  }

  public function getUserById(int $id): Users
  {
    $user = $this->repository->findUserById($id);
    if (!$user) throw new APIException("User not found!", 404);
    return $user;
  }

  public function insert(string $nome, string $email, string $senha, string $telefone, int $isAdmin): Users
  {
    $user = new Users(
      null,
      $nome,
      $email,
      $senha,
      $telefone,
      $isAdmin
    );

    $this->validateUser($user);

    $this->repository->insert($user);

    return $user;
  }


  public function updateUser(int $id, string $nome, string $email, string $senha, string $telefone, int $isAdmin): Users {
    $user = new Users(
      $id,
      $nome,
      $email, 
      $senha,
      $telefone,
      $isAdmin
    );

    $user = $this->getUserById($id);
    $user->setNome($nome);
    $user->setEmail($email);
    $user->setSenha($senha);
    $user->setTelefone($telefone);
    $user->setIsAdmin($isAdmin);

    $this->validateUser($user);

    $this->repository->update($user);
    return $user;
  }

  public function validateUser(Users $user)
  {
    if (strlen(trim($user->getNome())) < 5)
      throw new APIException("Nome de usuário muito curto!", 400);

    if (!filter_var($user->getEmail(), FILTER_VALIDATE_EMAIL))
      throw new APIException("Email inválido!", 400);

    $userEmailAlreadyExists = $this->repository->findByEmail($user->getEmail());
    if ($userEmailAlreadyExists) {
      if ($userEmailAlreadyExists->getId() !== $user->getId()) {
        throw new APIException("Este email já está em uso!", 409);
      }
    }
  }
}
