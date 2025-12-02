<?php

namespace Http;

use Error\APIException;
use Model\User;
use Service\TokenService;

class Request
{
  private string $resource;
  private ?string $id;
  private ?string $subCollection;
  private string $method;
  private array $body;
  private array $query;

  public function __construct(string $uri, string $method, ?string $rawBody)
  {
    $path = parse_url($uri, PHP_URL_PATH);

    //cria uma expressão regular com um padrão que admite
    //qualquer texto (para o início da URL) até encontrar /api/
    //tudo o que vier depois será capturado pela subexpressão
    //especificada por (.*) e referenciada por $1. 
    $pattern = "/.*\/api\/(.*)$/";

    //por exemplo, para http://localhost/xyz/api/students/123 
    //$route recebe students/123
    $route = preg_replace($pattern, "$1", $path);

    //cria um array com os segmentos de $route separados por /
    $segments = explode('/', $route);

    $this->resource = $segments[0]; //o primeiro segmento é o recurso
    $this->id = isset($segments[1]) && $segments[1] !== '' ? $segments[1] : null; //o segundo segmento é id (se não houver, nulo)
    $this->subCollection = isset($segments[2]) && $segments[2] !== '' ? $segments[2] : null; //o terceito segmento é a subcoleção (se não houver, nulo)

    //obtém o método (verbo) HTTP da requisição
    $this->method = $method;

    //cria um array com os parâmetros da querystring
    $this->query = [];
    $queryString = parse_url($uri, PHP_URL_QUERY) ?? ""; //pega só a querystring
    parse_str($queryString, $this->query); //gera um array associativo

    //verifica o corpo da requisição
    if ($rawBody) {
      //decodifica o corpo que deve vir no formato JSON
      //gera um array associativo
      $this->body = json_decode($rawBody, true) ?? [];

      //caso não venha em JSON ou seja um JSON inválido, gera uma exceção
      if (json_last_error() !== JSON_ERROR_NONE)
        throw new APIException("Invalid request body!", 400);
    } else {
      //se não houver um corpo, devolve um array vazio
      $this->body = [];
    }
  }
  public function getAuthenticatedUser(): ?User
  {
    return $this->validateToken();
  }

  private function validateToken(): ?User
  {
    $headers = getallheaders();
    $authHeader = $headers["Authorization"] ?? $headers["authorization"] ?? null;

    if (!isset($authHeader) || !preg_match('/Bearer\s+(.+)/', $authHeader, $matches)) {
      throw new APIException("Formato de token inválido!", 401);
    }

    $token = $matches[1];

    $tokenService = new TokenService();
    return $tokenService->getUserByValidToken($token);
  }
  
  // Getters
  public function getResource(): string
  {
    return $this->resource;
  }

  public function getId(): ?string
  {
    return $this->id;
  }

  public function getSubCollection(): ?string
  {
    return $this->subCollection;
  }

  public function getMethod(): string
  {
    return $this->method;
  }

  public function getQuery(): array
  {
    return $this->query;
  }

  public function getBody(): array
  {
    return $this->body;
  }
}
