# Projeto Laravel - Desafio Técnico UEX Tecnologia

Este é um projeto desenvolvido em **Laravel** como parte de um desafio técnico para a empresa **UEX Tecnologia**. Abaixo, você encontrará as instruções necessárias para configurar, executar e utilizar o sistema, além de descrições detalhadas dos endpoints disponíveis.

## Configuração Inicial
Para executar o projeto, siga os passos abaixo:

1. Clone o repositório para o seu ambiente local.
2. Instale as dependências com o comando:
   ```bash
   composer install
   ```
3. Crie o arquivo `.env` baseado no `.env.example` e configure as seguintes variáveis obrigatórias:

   ```env
   APP_URL_FRONT=<URL do front-end>
   GOOGLE_API_KEY=<Sua chave da API Google>

   DB_CONNECTION=<tipo de conexão>
   DB_HOST=<host do banco de dados>
   DB_PORT=<porta do banco de dados>
   DB_DATABASE=<nome do banco de dados>
   DB_USERNAME=<usuário do banco>
   DB_PASSWORD=<senha do banco>
   ```
4. Execute as migrações para configurar o banco de dados:
   ```bash
   php artisan migrate
   ```
5. Inicie o servidor local:
   ```bash
   php artisan serve
   ```

## Endpoints Disponíveis

### Autenticação

#### Registro de Usuário
**POST** `/api/auth/register`

Permite que novos usuários se registrem informando:
- **email** (string, obrigatório)
- **password** (string, obrigatório)
- **password_confirmation** (string, obrigatório)

#### Login
**POST** `/api/auth/login`

Autentica o usuário e gera um token de acesso ao informar:
- **email** (string, obrigatório)
- **password** (string, obrigatório)

#### Esqueceu a Senha
**POST** `/api/auth/forgot-password`

Envia um email com um token para redefinição de senha ao informar:
- **email** (string, obrigatório)

#### Redefinir Senha
**POST** `/api/auth/reset-password`

Redefine a senha de um usuário ao informar:
- **email** (string, obrigatório)
- **password** (string, obrigatório)
- **token** (string, obrigatório, enviado por email)

#### Logout
**POST** `/api/logout`

Remove todos os tokens de acesso do usuário logado ao enviar o token válido no cabeçalho de autenticação.

#### Informações do Usuário
**GET** `/api/me`

Retorna as informações da conta do usuário autenticado.

#### Deletar Conta
**DELETE** `/api/delete-account`

Permite ao usuário deletar sua própria conta ao informar:
- **password** (string, obrigatório)

### Contatos

#### Listar Contatos
**GET** `/api/contacts`

Retorna uma lista paginada dos contatos do usuário autenticado. Suporte a filtros e ordenações:
- **page**: número da página (opcional)
- **per_page**: quantidade de itens por página (opcional)
- **order_by**: campo de ordenação, `name` ou `cpf` (opcional)
- **order_type**: tipo de ordenação, `asc` ou `desc` (opcional)
- **search**: busca pelo nome ou CPF do contato (opcional)

#### Criar Contato
**POST** `/api/contacts`

Permite criar um novo contato informando os seguintes campos:
- **name** (string, obrigatório)
- **cpf** (string, obrigatório, único por usuário)
- **phone** (string, obrigatório)
- **number** (integer, obrigatório)
- **address** (string, obrigatório)
- **cep** (string, obrigatório)
- **district** (string, obrigatório)
- **city** (string, obrigatório)
- **state** (string, obrigatório)
- **country** (string, obrigatório)
- **complement** (string, opcional)

#### Visualizar Contato
**GET** `/api/contacts/{id}`

Retorna as informações de um contato específico pelo **id**.

#### Atualizar Contato
**PUT** `/api/contacts/{id}`

Permite atualizar as informações de um contato específico informando o **id** e os campos abaixo:
- **cpf** (string, obrigatório)
- **phone** (string, obrigatório)
- **number** (integer, obrigatório)
- **address** (string, obrigatório)
- **cep** (string, obrigatório)
- **district** (string, obrigatório)
- **city** (string, obrigatório)
- **state** (string, obrigatório)
- **country** (string, obrigatório)
- **complement** (string, opcional)

#### Deletar Contato
**DELETE** `/api/contacts/{id}`

Permite deletar um contato específico informando o **id**.

---
#### Pesquisar endereço
**GET** `/api/addresses`

Permite pesquisar um endereços informando o **CEP**.
- **CEP** (string, obrigatório)

---

### Observações
Certifique-se de que todas as requisições protegidas por autenticação incluam o token de acesso no cabeçalho como:

```json
Authorization: Bearer {seu_token_de_acesso}
```

Dúvidas ou sugestões? Entre em contato!

