<?php

namespace Service;

use Error\APIException;
use Model\Token;

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
            throw new APIException("Falha de Autenticação!", 404);
        }

        return $this->tokenService->generateOrUpdateToken($user);
    }
}
