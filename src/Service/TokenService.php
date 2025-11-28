<?php

namespace Service;

use Error\APIException;
use Model\Token;
use Model\User;
use Repository\TokenRepository;
use Service\UserService;

class TokenService
{
  private TokenRepository $tokenRepository;
  private UserService $userService;

  private const TOKEN_EXPIRATION = 3600; // 1 horinha


  public function __construct()
  {
    $this->tokenRepository = new TokenRepository();
  }

  public function insert(string $token, int $expired_at, int $userID): Token
  {
    $token = new Token(
      null,
      $token,
      $expired_at,
      $userID
    );

    $this->validateToken($token);
    $this->tokenRepository->insert($token);
    return $token;
  }

  public function update(string $newToken, int $expiresAt, int $userID): Token
  {
    $token = $this->tokenRepository->findByUser($userID);
    $token->setToken($newToken);
    $token->setExpiredAt($expiresAt);

    $this->tokenRepository->update($token);
    return $token;
  }

  public function generateOrUpdateToken(User $user): Token
  {
      $generatedToken = $this->generateToken($user->getEmail());
      $expirationTime = time() + self::TOKEN_EXPIRATION;

      if ($this->tokenExists($user->getId())) {
        return $this->update($generatedToken, $expirationTime, $user->getId());
      }
        
      return $this->insert($generatedToken, $expirationTime, $user->getId());
  }

  private function tokenExists($userId): bool
  {
    $token = $this->tokenRepository->findByUser($userId);

    if (!$token) {
      return false;
    };
    return true;
  }

  public function validateToken(Token $token)
  {
    if ($token == null) {
      throw new APIException("Token Invalido!", 400);
    }
  }

  public function tokenIsValid(string $token): bool
  {
    $searchToken = $this->tokenRepository->findByToken($token);

    if (!$searchToken) {
      throw new APIException("Token Invalido, necessário autenticação", 400);
    }

    if ($searchToken->getExpiredAt() < time()) {
      throw new APIException("Token Expirado, necessário autenticação", 400);
    }

    return true;
  }

  public function getUserByValidToken(string $token): User
  {
    $this->tokenIsValid($token);

    $tokenData = $this->tokenRepository->findByToken($token);
    $userService = new UserService();
    return $userService->getUserById($tokenData->getIdUser());
  }

  private function generateToken(string $email): string
  {
    $timeStamp = time();
    $tompero = "hehehehe";

    $data_to_hash = $email . $timeStamp . $tompero;
    return hash('sha256', $data_to_hash);
  }
}
