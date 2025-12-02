<?php

namespace Service;

use Error\APIException;
use Model\Token;
use Model\User;

class AuthService
{
    private UserService $userService;
    private TokenService $tokenService;

    public function __construct()
    {
        $this->userService = new UserService();
        $this->tokenService = new TokenService();
    }

    public function login(string $email, string $senha): Token
    {
        $user = $this->userService->getUserByEmail($email);

        if (empty($user) || !password_verify($senha, $user->getSenha())) {
            throw new APIException("Falha de Autenticação!", 400);
        }

        return $this->tokenService->generateOrUpdateToken($user);
    }

    public function logout(User $user): void
    {   
        if (!$this->tokenService->deleteTokenByUser($user)) {
            throw new APIException("Erro ao fazer o logout, tente novamente!", 400);
        }
    }
}
