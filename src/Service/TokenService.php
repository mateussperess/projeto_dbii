<?php

namespace Service;

use Error\APIException;
use Model\Token;
use Model\User;
use Repository\TokenRepository;

class TokenService
{
  private TokenRepository $tokenRepository;

  private const TOKEN_EXPIRATION = 3600; // 1 horinha


  public function __construct()
  {
    $this->tokenRepository = new TokenRepository();
  }

  public function insert(string $token, int $expiresAt, int $userID): Token
  {
    $token = new Token(
      null,
      $token,
      $expiresAt,
      $userID
    );

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

  public function tokenIsValid(?Token $token): bool
  {
    if (!$token) {
      throw new APIException("Token Invalido, necessário autenticação", 400);
    }

    if ($token->getExpiredAt() < time()) {
      throw new APIException("Token Expirado, necessário autenticação", 400);
    }

    return true;
  }

  public function getUserByValidToken(string $token): User
  {
    $userToken = $this->tokenRepository->findByToken($token);
    $this->tokenIsValid($userToken);

    $userService = new UserService();
    return $userService->getUserById($userToken->getIdUser());
  }

  public function deleteTokenByUser(User $user): bool
  { 
    return $this->tokenRepository->deleteByUserId($user->getId());
  }

  private function generateToken(string $email): string
  {
    $timeStamp = time();
    $tompero = "hehehehe";

    $data_to_hash = $email . $timeStamp . $tompero;
    return hash('sha256', $data_to_hash);
  }
}
