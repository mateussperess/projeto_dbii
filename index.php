<?php

use Controller\AuthController;
use Controller\CategoriaController;
use Controller\EmprestimoController;
use Controller\LivroController;
use Controller\UserController;
use Error\APIException;
use Http\Request;
use Http\Response;

require_once "src/config.php";

ini_set('display_errors', 1);
ini_set('display_startup_erros', 1);
date_default_timezone_set('America/Sao_Paulo');
error_reporting(E_ALL);

$uri = $_SERVER["REQUEST_URI"];
$method = $_SERVER["REQUEST_METHOD"];
$body = file_get_contents("php://input");
$request = new Request($uri, $method, $body);

switch ($request->getResource()) {
  case 'users':
    $userController = new UserController();
    $userController->proccessRequest($request);
    break;

  case "categorias":
    $categoriasController = new CategoriaController();
    $categoriasController->proccessRequest($request);
    break;

  case "livros":
    $livroController = new LivroController();
    $livroController->proccessRequest($request);
    break;

  case "auth":
    $authController = new AuthController();
    $authController->proccessRequest($request);
    break;

  case "emprestimos":
    $emprestimoController = new EmprestimoController();
    $emprestimoController->processRequest($request);
    break;

  case null:
    $endpoints = [
      "/api/auth" => [
        "POST" => [
          "description" => "Gerar token de autenticação (login)",
          "auth" => false,
          "body" => "required",
          "fields" => ["email", "senha"]
        ],
        "DELETE" => [
          "description" => "Logout (invalidar token)",
          "auth" => true,
          "body" => "not required"
        ]
      ],
      "/api/users" => [
        "POST" => [
          "description" => "Criar novo usuário",
          "auth" => false,
          "body" => "required",
          "fields" => ["nome", "email", "senha", "telefone"]
        ],
        "GET" => [
          "description" => "Listar todos os usuários",
          "auth" => true,
          "admin_only" => true,
          "params" => "?nome=fulano (opcional)"
        ]
      ],
      "/api/users/{id}" => [
        "GET" => [
          "description" => "Buscar usuário específico",
          "auth" => true,
          "example" => "/api/users/1",
          "notes" => "Admin ou próprio usuário"
        ],
        "PUT" => [
          "description" => "Atualizar dados do usuário",
          "auth" => true,
          "body" => "required",
          "fields" => ["nome", "telefone"],
          "notes" => "Admin ou próprio usuário"
        ],
        "PATCH" => [
          "description" => "Atualizar status de administrador",
          "auth" => true,
          "admin_only" => true,
          "body" => "required",
          "fields" => ["isAdmin (0 ou 1)"]
        ]
      ],
      "/api/categorias" => [
        "GET" => [
          "description" => "Listar todas as categorias",
          "auth" => false,
          "params" => "?descricao=terror (opcional)"
        ],
        "POST" => [
          "description" => "Criar nova categoria",
          "auth" => true,
          "admin_only" => true,
          "body" => "required",
          "fields" => ["descricao"]
        ]
      ],
      "/api/categorias/{id}" => [
        "PUT" => [
          "description" => "Atualizar categoria",
          "auth" => true,
          "admin_only" => true,
          "body" => "required",
          "fields" => ["descricao"],
          "example" => "/api/categorias/1"
        ],
        "DELETE" => [
          "description" => "Deletar categoria",
          "auth" => true,
          "admin_only" => true,
          "example" => "/api/categorias/1"
        ]
      ],
      "/api/livros" => [
        "GET" => [
          "description" => "Listar todos os livros",
          "auth" => false,
          "params" => "?titulo=exemplo (opcional)"
        ],
        "POST" => [
          "description" => "Criar novo livro",
          "auth" => true,
          "admin_only" => true,
          "body" => "required",
          "fields" => ["titulo", "autor", "descricao", "ano", "n_paginas", "id_genero"]
        ]
      ],
      "/api/livros/{id}" => [
        "GET" => [
          "description" => "Buscar livro específico",
          "auth" => false,
          "example" => "/api/livros/1"
        ],
        "PUT" => [
          "description" => "Atualizar livro",
          "auth" => true,
          "admin_only" => true,
          "body" => "required",
          "fields" => ["titulo", "autor", "descricao", "ano", "n_paginas", "id_genero"],
          "example" => "/api/livros/1"
        ],
        "DELETE" => [
          "description" => "Deletar livro",
          "auth" => true,
          "admin_only" => true,
          "example" => "/api/livros/1"
        ]
      ],
      "/api/emprestimos" => [
        "GET" => [
          "description" => "Listar empréstimos",
          "auth" => true,
          "params" => "?user_id=1 (obrigatório para usuários comuns) | ?mode=all (admin visualiza todos)"
        ],
        "POST" => [
          "description" => "Criar ou devolver empréstimo",
          "auth" => true,
          "body" => "required",
          "fields" => ["id_livro", "devolver (0 ou 1, opcional)"],
          "notes" => "devolver=1 para devolução, devolver=0 ou omitido para empréstimo"
        ]
      ]
    ];

    Response::send([
      "message" => "API de Biblioteca - Endpoints disponíveis",
      "endpoints" => $endpoints
    ]);
    break;

  default:
    throw new APIException("Resource not found!", 406);
}
