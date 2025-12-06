<?php

namespace Service;

use Error\APIException;
use Model\User;
use Repository\UserRepository;

class UserService
{
  private UserRepository $repository;

  public function __construct()
  {
    $this->repository = new UserRepository();
  }

  public function getUsers(?string $nome): array|User
  {
    if (!$nome) return $this->repository->findAll();

    $user = $this->repository->findByName($nome);

    if (!$user) throw new APIException("Não existe um usuário com esse nome!",  404);
    return $user;
  }

  public function getUserByIdAuthorized(User $authUser, int $requestId): User
  {
    if (!$authUser->getIsAdmin() && $authUser->getId() !== $requestId) {
      throw new APIException("Acesso negado!", 403);
    }

    return $this->repository->findUserById($requestId);
  }


  public function getUserById(int $id): User
  {
    $user = $this->repository->findUserById($id);
    if (!$user) throw new APIException("User not found!", 404);
    return $user;
  }

  public function getUserByEmail(string $email): ?User
  {
    return $this->repository->findByEmail($email);
  }

  public function insert(string $nome, string $email, string $senha, string $telefone): User
  {
    $user = new User(
      null,
      $nome,
      $email,
      $senha,
      $telefone,
      0
    );

    $this->validateUser($user);

    $this->repository->insert($user);

    return $user;
  }


  public function updateUserAuthorized(User $user, int $requestId, array $body): User
  {
    if (!$user->getIsAdmin() && $user->getId() !== $requestId) {
      throw new APIException("Acesso negado", 403);
    }

    $updateUser = $this->repository->findUserById($requestId);

    if (!$updateUser) {
      throw new APIException("Usuário não encontrado!", 404);
    }

    $updateUser->setNome($body['nome']);
    $updateUser->setTelefone($body['telefone']);

    $this->repository->update($updateUser);

    return $updateUser;
  }

  public function validateUser(User $user)
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

  public function getUsersWithPermission(User $authenticatedUser, ?string $nome): array|User
  {
    if (!$this->isAdmin($authenticatedUser)) {
      if ($nome) {
        throw new APIException("Você não tem permissão para buscar outros usuários!", 403);
      }
      return $this->getUserById($authenticatedUser->getId());
    }

    return $this->getUsers($nome);
  }

  public function updateAdminStatus(User $user, int $requestId, int $is_admin): User
  {
    if (!$user->getIsAdmin()) {
      throw new APIException("Acesso negado!", 403);
    }

    $updateUser = $this->repository->findUserById($requestId);
    if (!$updateUser) {
      throw new APIException("Usuário não encontrado!", 404);
    }

    $updateUser->setIsAdmin($is_admin);
    $this->repository->updateAdminStatus($updateUser);
    return $updateUser;
  }

  public function isAdmin(User $user): bool
  {
    return $user->getIsAdmin() !== 0;
  }
}
