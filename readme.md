# BIBLIOTECA

Este é um exemplo de uso do padrão Controller-Service-Repository para a construção de uma API Rest usando PHP puro. Para fins didáticos, alguns pontos foram simplificados ou abstraídos. Da mesma forma, algumas soluções foram adotadas a fim de permitir a problematização de aspectos importantes do projeto durante as aulas, além da discussão das mudanças necessárias para a sua evolução ou para a implementação de outras técnicas/princípios.

## Entidades e Relações

* Usuário (nome, email, senha, telefone, isAdmin)
* Livro (título, autor, descrição, ano_publicacao, paginas, categoria, isAlocated, n_alocated)
* Categoria (descrição)
* Empréstimo (usuário, livro, data_inicio, data_entrega)

* Um usuário pode possuir vários empréstimos.
* Um livro pertence a uma categoria.
* Uma categoria pode ter vários livros.
* Um livro só pode ser emprestado se não estiver alocado.
* Um empréstimo vincula diretamente um livro a um usuário.

## Requisitos

* Deve ser possível cadastrar um usuário (sempre como usuário normal).
* Deve ser possível fazer login (gera token).
* Deve ser possível fazer logout (invalida token).
* Deve ser possível obter os dados do usuário logado.
* Deve ser possível atualizar parcialmente os dados do usuário.
* Deve ser possível listar livros.
* Deve ser possível obter os dados de um livro pelo id.
* Deve ser possível filtrar livros por categoria.
* Deve ser possível listar categorias.
* Deve ser possível criar, alterar e excluir livros (somente admin).
* Deve ser possível criar, alterar e excluir categorias (somente admin).
* Deve ser possível visualizar todos usuários (admin).
* Deve ser possível visualizar empréstimos (usuário vê os seus; admin vê todos).
* Deve ser possível criar empréstimos.
* Deve ser possível devolver livros.
* Deve ser possível cancelar empréstimos (admin).
* Deve ser possível deletar usuários (admin, com restrições).

## Regras de Negócio

### Usuário Normal
* Um usuário pode se cadastrar apenas como usuário normal (isAdmin = 0).
* Um usuário pode ver somente os seus dados.
* Um usuário pode atualizar somente:
  * nome
  * telefone
* Um usuário pode visualizar:
  * Todos os livros cadastrados
  * Um livro específico
  * Suas categorias
  * Seus próprios empréstimos (ativos ou devolvidos)
* Um usuário pode criar um empréstimo desde que:
  * O livro não esteja alocado
  * Não possua empréstimos em atraso
* Um usuário pode devolver um livro que tenha emprestado.
* Um usuário pode fazer logout, invalidando seu token atual.
* Um usuário não pode excluir sua própria conta.

### Empréstimos (Usuário Normal)
* Empréstimos ativos: `data_entrega IS NULL`
* Empréstimos devolvidos: `data_entrega IS NOT NULL`
* Empréstimos atrasados:
  * `data_entrega IS NULL`
  * `data_inicio + 14 dias < hoje`
* Ao emprestar:
  * `isAlocated = 1`
  * `n_alocated` é incrementado
* Ao devolver:
  * `isAlocated = 0`
  * `data_entrega = data/hora atual`

### Usuário Administrador
* Pode visualizar todos os usuários cadastrados.
* Pode visualizar todos os livros.
* Pode visualizar todos os empréstimos.
* Pode criar, editar e deletar livros:
  * Não pode haver empréstimos vinculados ao livro.
* Pode criar, editar e deletar categorias:
  * Não pode haver livros associados à categoria.
* Pode alterar o nível de acesso de um usuário (isAdmin).
* Pode cancelar um empréstimo:
  * Atualiza `isAlocated` do livro para 0
  * Define `data_entrega` com data/hora atual
* Pode deletar usuários, com restrições:
  * Não pode deletar a si próprio
  * Pode deletar usuários com empréstimos ativos, liberando os livros desses empréstimos
* Pode visualizar empréstimos de usuários específicos.
* Pode criar empréstimos em nome de outros usuários.
* Pode editar qualquer dado de qualquer usuário.

## Regras Gerais do Sistema

### Validação de Cadastro
* email deve ser único e válido (FILTER_VALIDATE_EMAIL).
* senha deve ter no mínimo 6 caracteres e ser armazenada com hash.
* nome é obrigatório e deve ter ao menos 3 caracteres.

### Autenticação
* Cada token expira após 24 horas.
* Token inválido ou expirado retorna 401 (Unauthorized).
* Rotas de administrador acessadas por usuários comuns retornam 403 (Forbidden).
* Cada usuário pode ter apenas um token ativo.
* Logout remove o token atual.

### Empréstimos
* Prazo padrão: 14 dias.
* Um livro só pode ser emprestado se `isAlocated = 0`.
* Ao emprestar: `isAlocated = 1`.
* Ao devolver: `isAlocated = 0`.
* Um empréstimo é atrasado quando:
  * `data_entrega IS NULL`
  * `data_inicio + 14 dias < hoje`

### Livros
* `isAlocated` indica se o livro está emprestado.
* `n_alocated` representa quantas vezes o livro foi emprestado.
* Ambos devem ser atualizados corretamente durante empréstimos.

### Categorias
* Uma categoria pode ter vários livros.
* Ao excluir categorias, não pode haver livros associados.

### Integridade e Cascatas
* Ao deletar um usuário:
  * Verificar se ele não possui empréstimos ativos.
  * Seus tokens devem ser removidos.
* Ao deletar um livro:
  * Verificar se não há empréstimos vinculados.
* Ao deletar uma categoria:
  * Verificar se não há livros relacionados.

## Detalhes Importantes

* Deve ser usado o servidor APACHE com o módulo de reescrita de URLs ativado.
* No arquivo `.htaccess`, há uma regra simples que redireciona todas as requisições para o `index.php`.
* No arquivo `index.php` há um roteador rudimentar que, baseado no recurso, chama o controller adequado.
* No `index.php` é carregado um arquivo de configurações que registra um autoload simples e funções para tratamento de erros e exceções.
* Antes de chamar o controller específico, é criado um objeto `Request` com todos os dados da requisição.
* A classe `Response` possui um método estático para padronizar o envio das respostas em JSON.
* A classe `APIException` define uma exceção personalizada da API.
* A conexão com o banco de dados adota um Singleton.
* O script `setup.php` prepara o banco de dados e insere dados de exemplo.
* Nos arquivos de teste podem existir exemplos de requisições para testar as rotas e regras de negócio.

## Endpoint

### Autenticação
* POST /auth/register
* POST /auth/login
* POST /auth/logout

### Usuário
* GET /auth/me
* GET /users/me
* PUT /users/me
* PATCH /users/me
* GET /users/me/emprestimos

### Usuário Admin
* GET /users
* GET /users/:id
* PUT /users/:id
* PATCH /users/:id
* DELETE /users/:id
* GET /users/:id/emprestimos

### Livros
* GET /livros
* GET /livros?categoria=id
* GET /livros/:id
* POST /livros
* PUT /livros/:id
* PATCH /livros/:id
* DELETE /livros/:id

### Categorias
* GET /categorias
* POST /categorias
* PUT /categorias/:id
* DELETE /categorias/:id

### Empréstimos
* GET /emprestimos
* GET /emprestimos/:id
* POST /emprestimos
* PATCH /emprestimos/:id
* POST /emprestimos/:id/cancelar

## TO-DO

* Ao excluir ou editar categorias, verificar se há livros associados.
* Criar funcionalidade de renovação de empréstimos.
* Implementar auditoria de operações administrativas.
* Criar filtro avançado de livros (autor, ano, categoria).
* Ao deletar usuário com empréstimos ativos, garantir liberação dos livros.
