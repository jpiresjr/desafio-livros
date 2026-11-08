# Desafio Técnico — Cadastro de Livros

Aplicação web de cadastro de livros desenvolvida com **PHP 8.4, Symfony 7.4, Twig e Bootstrap**. O sistema permite gerenciar livros, autores, assuntos e consultar um relatório agrupado por autor, alimentado por uma **VIEW SQL**.

## 1. Tecnologias

- PHP 8.4 e Symfony 7.4 - linguagem e framework utilizados no desenvolvimento
- Twig e Bootstrap - renderização das páginas no servidor e construção de uma interface responsiva.
- Doctrine ORM e Doctrine Migrations - mapeiamento das entidades e os relacionamentos; versionamento do schema e a criação da VIEW
- MySQL 8 - Banco de dados relacional
- Nginx - servidor web
- Docker e Docker Compose - configuração e execução do ambiente de desenvolvimento
- KoolReport (relatório) - geração e apresentação do relatório de livros agrupados por autor
- PHPUnit - execução dos testes automatizados

O código Symfony está em `app/`. Os serviços Docker são definidos em `docker-compose.yml`.

## 2. Funcionalidades

- **Livros:** listagem, cadastro, edição e exclusão; título, editora, edição, ano de publicação e valor em reais (R$).
- **Autores:** listagem, cadastro, edição e exclusão.
- **Assuntos:** listagem, cadastro, edição e exclusão.
- **Relacionamentos:** um livro pode ter vários autores e vários assuntos; um autor ou assunto pode estar associado a vários livros.
- **Formulários:** validação de dados, incluindo a seleção de pelo menos um autor e um assunto para cada livro.
- **Relatório:** informações de livros, autores e assuntos agrupadas por autor, apresentadas com KoolReport e consultadas por meio da VIEW `vw_relatorio_livros`.

Como um livro pode ter mais de um autor, ele pode aparecer em mais de um grupo do relatório.

## 3. Modelo de dados

| Tabela | Finalidade |
| --- | --- |
| `Livro` | Dados dos livros |
| `Autor` | Dados dos autores |
| `Assunto` | Dados dos assuntos |
| `Livro_Autor` | Associação muitos-para-muitos entre livros e autores |
| `Livro_Assunto` | Associação muitos-para-muitos entre livros e assuntos |

As entidades são mapeadas pelo Doctrine ORM. A estrutura do banco e a VIEW do relatório são versionadas por migrations em `app/migrations/`.

## 4. Instalação e execução

### Pré-requisitos

- Docker
- Docker Compose

### 4.1. Configurar a conexão com o banco

No diretório `app/`, configure o arquivo `.env.local` com a variável `DATABASE_URL`:

```dotenv
DATABASE_URL="mysql://livros_user:SUA_SENHA@db:3306/livros?serverVersion=8.0.32&charset=utf8mb4"
```

Substitua `SUA_SENHA` pelo valor de `MYSQL_PASSWORD` definido no serviço `db` do `docker-compose.yml`.

> O arquivo `.env.local` é uma configuração local. Verifique se ele está excluído do versionamento pelo `.gitignore`.

### 4.2. Subir os containers

Na raiz do projeto:

```bash
sudo docker compose up -d --build
```

### 4.3. Instalar as dependências PHP, se necessário

Se o diretório `app/vendor/` ainda não existir e o Composer estiver disponível no container:

```bash
sudo docker compose exec app composer install
```

### 4.4. Executar as migrations

```bash
sudo docker compose exec app php bin/console doctrine:migrations:migrate --no-interaction
```

As migrations criam ou atualizam as tabelas e a VIEW usada pelo relatório.

### 4.5. Abrir a aplicação

**http://localhost:8000**

A partir da tela inicial, utilize a navegação para acessar os cadastros e o relatório.

Para encerrar os serviços sem remover o volume:

```bash
sudo docker compose down
```

## 5. Testes automatizados

O projeto utiliza PHPUnit. A configuração do Doctrine aplica o sufixo `_test%env(default::TEST_TOKEN)%` ao nome do banco no ambiente de testes. Sem `TEST_TOKEN`, a conexão baseada em `livros` utiliza o banco **`livros_test`**.

### 5.1. Preparar o banco de testes

```bash
sudo docker compose exec app php bin/console doctrine:database:create --env=test
sudo docker compose exec app php bin/console doctrine:migrations:migrate --env=test --no-interaction
```

Se o banco de testes já existir, não é necessário criá-lo novamente.

### 5.2. Executar os testes

Testes do controller de livros:

```bash
sudo docker compose exec app php bin/phpunit tests/Controller/LivroControllerTest.php
```

Testes das entidades:

```bash
sudo docker compose exec app php bin/phpunit tests/Entity
```

Todos os testes:

```bash
sudo docker compose exec app php bin/phpunit
```

**Limitação conhecida:** os testes de cadastro e edição ainda podem deixar registros no banco `livros_test`; a limpeza automática não foi concluída. Não execute os testes contra o banco de desenvolvimento ou produção.











