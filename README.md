# api-tads
Trabalho de Técnicas Avançadas de Desenvolvimento de Software - FACOM 2024
# README

## API de Produtos

Este é um projeto de API desenvolvido como parte do Trabalho de Técnicas Avançadas de Desenvolvimento de Software - FACOM 2024. A API é projetada para gerenciar produtos e incluir autenticação via JWT e Keycloak.

### Pré-requisitos

Antes de começar, certifique-se de ter os seguintes requisitos instalados:

- [XAMPP]
- PHP 7.3 ou superior
- Composer

### Configuração do Ambiente

1. **Clone o repositório:**

   ```bash
   git clone https://github.com/larisleal/api-tads
   cd api-tads
   ```

2. **Instale as dependências:**

   Execute o seguinte comando para instalar as dependências do Laravel:

   ```bash
   composer install
   ```

3. **Configure o arquivo `.env`:**

   Renomeie o arquivo `.env.example` para `.env` e configure as variáveis de ambiente:

   ```plaintext
   APP_NAME=Laravel
   APP_ENV=local
   APP_KEY=base64:YOUR_APP_KEY
   APP_DEBUG=true
   APP_URL=http://localhost

   DB_CONNECTION=mysql
   DB_HOST=127.0.0.1
   DB_PORT=3306
   DB_DATABASE=nome_do_banco
   DB_USERNAME=usuario
   DB_PASSWORD=senha

   KEYCLOAK_TOKEN_ENDPOINT=<URL_DO_ENDPOINT_KEYCLOAK>
   KEYCLOAK_CLIENT_ID=<CLIENT_ID>
   JWT_SECRET=<SUA_CHAVE_JWT>
   KEYCLOAK_JWKS_URI=<URL_JWKS>
   ```

4. **Gere a chave de aplicativo:**

   Execute o seguinte comando para gerar a chave de aplicativo:

   ```bash
   php artisan key:generate
   ```

5. **Migre o banco de dados:**

   Execute as migrações para criar as tabelas necessárias:

   ```bash
   php artisan migrate
   ```

### Executando a Aplicação

1. **Inicie o servidor PHP:**

   Se você estiver usando o XAMPP, inicie o Apache e o MySQL através do painel de controle do XAMPP.

2. **Acesse a aplicação:**

   Acesse a API através do seu navegador ou ferramenta como Postman:

   ```
   http://localhost/api-tads/public
   ```

### Acessando o Swagger

Para visualizar a documentação da API utilizando o Swagger, siga os passos abaixo:

1. **Abra seu navegador.**

2. **Acesse a interface do Swagger:**

   ```
   http://127.0.0.1:8000/api/documentation
   ```

   Isso abrirá a interface do Swagger, onde você pode visualizar e interagir com a documentação da API.

### Endpoints

- **Login:**
  - `POST /api/login`
  
- **Produtos:**
  - `GET /api/products` - Listar todos os produtos
  - `POST /api/products` - Criar um novo produto
  - `GET /api/products/{id}` - Obter um produto específico
  - `PUT /api/products/{id}` - Atualizar um produto
  - `DELETE /api/products/{id}` - Remover um produto

### Licença

Este projeto está licenciado sob a MIT License - veja o arquivo [LICENSE](LICENSE) para mais detalhes.