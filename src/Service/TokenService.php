<?php 

namespace Service;

use Error\APIException;
use Model\Token;
use Repository\TokenRepository;
use Service\UserService;

class TokenService
{
  private TokenRepository $repository;
  private UserService $userService;

  public function __construct()
  {
    $this->repository = new TokenRepository();
  }

  public function insert(string $token, int $expired_at, int $userID ): Token {
    $token = new Token(
      null,
      $token,
      $expired_at,
      $userID
    );

    $this->validateToken($token);
    $this->repository->insert($token);
    return $token;
  }

  public function update(string $token, int $expired_at, int $userID ): Token {
    $token = new Token(
      null,
      $token,
      $expired_at,
      $userID
    );

    $this->validateToken($token);
    $this->repository->update($token);
    return $token;
  }

  public function verifyLogin(string $email, string $senha): Token{
    $this->userService = new UserService;
    
    $user = $this->userService->getUserByEmail($email);

    if (!$user) {
        throw new APIException("User not found!", 404);
    }else{

        if(!password_verify($senha, $user->getSenha())){
            throw new APIException("Falha de Autenticação!", 404);
        }
        
        $token ='';
        $newToken = $this->generateToken($user->getEmail());
        if($this->tokenExists($user->getId())){
            $token = $this->update($newToken, time(), $user->getId());
        }else{
            $token = $this->insert($newToken, time(), $user->getId());
        }
        
        return $token;
    }



  }

  private function generateToken($email) : string{
    $userEmail = $email;
    $timeStamp = time();
    $tompero = "hehehehe";

    $data_to_hash = $userEmail.$timeStamp.$tompero;

    $token = hash('sha256', $data_to_hash);

    return $token;
  }

  private function tokenExists($userId) : bool{
    $token = $this->repository->findByUser($userId);

    if (!$token){return false;};
    return true;
  }
  

  
  public function validateToken(Token $token) {

    if($token == null){
        throw new APIException("Token Invalido!", 400);
        return false;
    }
  }

  public function tokenIsValid(string $token): bool {

    if($token == null){
        throw new APIException("Token Invalido!", 400);
        return false;
    }

    $searchToken = $this->repository->findByToken($token);

    if(!$searchToken){
        throw new APIException("Token Invalido, necessário autenticação", 400);
        return false;
    }else{
        if($searchToken->getExpiredAt()< time()){
            throw new APIException("Token Expirado, necessário autenticação", 400);
        return false;
        }
        return true;
    }
  }
}