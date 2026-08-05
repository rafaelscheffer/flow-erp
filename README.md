<p align="center">
  <img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="320" alt="Laravel Logo">
</p>

<h1 align="center">FlowERP</h1>

<p align="center">
  Um ERP modular open-source construído com Laravel, pensado para qualidade de produção — não um projeto de estudo.
</p>

<p align="center">
  <img src="https://img.shields.io/badge/PHP-8.4-777BB4?logo=php&logoColor=white" alt="PHP 8.4">
  <img src="https://img.shields.io/badge/Laravel-12-FF2D20?logo=laravel&logoColor=white" alt="Laravel 12">
  <img src="https://img.shields.io/badge/PostgreSQL-16-4169E1?logo=postgresql&logoColor=white" alt="PostgreSQL 16">
  <img src="https://img.shields.io/badge/Redis-7-DC382D?logo=redis&logoColor=white" alt="Redis 7">
  <img src="https://img.shields.io/badge/Docker-Compose-2496ED?logo=docker&logoColor=white" alt="Docker Compose">
  <img src="https://img.shields.io/badge/license-MIT-blue" alt="License MIT">
</p>

---

## Sobre o projeto

**FlowERP** é um sistema de gestão empresarial (ERP) desenvolvido do zero com Laravel, aplicando arquitetura modular inspirada em Domain-Driven Design. O objetivo é entregar uma base sólida para os principais processos de uma empresa — clientes, fornecedores, produtos, estoque, compras, vendas e financeiro — com o rigor de engenharia de um software comercial: tipagem estrita, separação clara de responsabilidades, auditoria de dados e testes automatizados.

> **Status:** projeto em desenvolvimento ativo. A infraestrutura (Docker, banco de dados, cache, fila, e-mail) já está funcional e o esqueleto dos módulos de negócio foi criado; as regras de negócio de cada módulo estão sendo implementadas incrementalmente.

## Arquitetura

O projeto segue uma separação entre **infraestrutura/cross-cutting concerns** e **módulos de domínio**, evitando o modelo tradicional de Controllers "gordos" do Laravel:

```
app/
├── Actions/         # Casos de uso (regra de negócio isolada e reutilizável)
├── DTOs/            # Objetos de transferência de dados entre camadas
├── Enums/           # Enums tipados (status, eventos, etc.)
├── Events/          # Eventos de domínio
├── Exceptions/       # Exceções customizadas
├── Http/            # Controllers "finos" — apenas orquestram Actions/Services
├── Jobs/            # Processamento assíncrono (fila)
├── Listeners/       # Reação a eventos de domínio
├── Models/          # Modelos compartilhados (ex.: User)
├── Observers/       # Observers de Eloquent
├── Policies/        # Autorização
├── Repositories/    # Abstração de acesso a dados
├── Rules/           # Regras de validação customizadas
├── Services/        # Serviços de domínio transversais
└── Traits/          # Comportamentos reutilizáveis (ex.: Auditable)

Modules/
├── Administration/  # Usuários, papéis, permissões, log de auditoria
├── Customers/       # Clientes PF/PJ (CPF/CNPJ)
├── Suppliers/       # Fornecedores
├── Products/        # Produtos, variantes, SKU/NCM/EAN
├── Inventory/       # Estoque e movimentações (histórico imutável)
├── Purchases/       # Compras
├── Sales/           # Vendas (orçamento → pedido → nota, com efeitos em estoque/financeiro)
├── Financial/        # Plano de contas, centros de custo, contas a pagar/receber, fluxo de caixa
└── Reports/         # Relatórios e exportação (CSV/Excel)
```

Cada módulo é autocontido — com seus próprios `Models`, `Services`, `Repositories`, `Policies`, `DTOs`, `Actions`, `Requests`, `Resources`, `Events`, `Migrations`, `Routes` e `Tests` — e é registrado via um `ServiceProvider` próprio (`Modules/{Nome}/Providers/{Nome}ServiceProvider.php`), carregando suas migrations e rotas de forma independente.

**Princípios seguidos:**
- Controllers não contêm regra de negócio — apenas delegam para Actions/Services.
- Movimentações de estoque nunca são editadas diretamente; toda alteração gera um novo registro de histórico.
- Efeitos colaterais entre módulos (ex.: venda impactando estoque e financeiro) são propagados via eventos, não por acoplamento direto.
- Alterações relevantes em modelos auditáveis são registradas automaticamente (trait `Auditable` + `AuditLogService`).
- Tudo roda via Docker — não há dependência de PHP/Composer instalados localmente.

## Tecnologias

| Camada | Tecnologia |
|---|---|
| Linguagem | PHP 8.4 |
| Framework | Laravel 12 |
| Banco de dados | PostgreSQL 16 |
| Cache, sessão e fila | Redis 7 |
| E-mail (ambiente local) | Mailpit |
| Front-end (build) | Vite, Tailwind CSS v4 |
| Testes | PHPUnit, Mockery, Faker |
| Qualidade de código | Laravel Pint (PSR-12) |
| Infraestrutura | Docker & Docker Compose |
| Servidor web | Nginx |

## Arquitetura de containers

O ambiente é totalmente orquestrado via `docker-compose.yml`, com os seguintes serviços:

- **app** — PHP-FPM, executa a aplicação Laravel.
- **nginx** — servidor web, expõe a aplicação em `http://localhost:8080`.
- **postgres** — banco de dados principal, porta `5432`.
- **redis** — cache, sessão e driver de fila, porta `6379`.
- **mailpit** — captura de e-mails em desenvolvimento, UI em `http://localhost:8025`.
- **queue** — worker dedicado (`queue:work`) para processamento assíncrono.
- **scheduler** — executa o agendador do Laravel (`schedule:work`) para tarefas recorrentes.

## Como executar o projeto

### Pré-requisitos
- [Docker](https://www.docker.com/) e Docker Compose

### Passos

```bash
# 1. Clonar o repositório
git clone <url-do-repositorio> flow-erp
cd flow-erp

# 2. Copiar o arquivo de ambiente
cp .env.example .env

# 3. Subir os containers
docker compose up -d --build

# 4. Instalar dependências PHP (dentro do container)
docker compose exec app composer install

# 5. Gerar a chave da aplicação
docker compose exec app php artisan key:generate

# 6. Executar as migrations
docker compose exec app php artisan migrate

# 7. Instalar dependências de front-end e compilar assets
docker compose exec app npm install
docker compose exec app npm run build
```

A aplicação estará disponível em **http://localhost:8080** e o Mailpit em **http://localhost:8025**.

### Executando os testes

```bash
docker compose exec app php artisan test
```

### Padrão de código

```bash
docker compose exec app ./vendor/bin/pint
```

## Roadmap

- [x] Infraestrutura Docker (app, nginx, postgres, redis, mailpit, queue, scheduler)
- [x] Estrutura modular (`Modules/`) e padrão de `ServiceProvider` por módulo
- [x] Sistema de auditoria (`AuditLogService` + trait `Auditable`)
- [ ] Módulo de Administração — usuários, autenticação, 2FA, papéis e permissões
- [ ] Módulo de Clientes e Fornecedores
- [ ] Módulo de Produtos e Estoque
- [ ] Módulo de Compras e Vendas
- [ ] Módulo Financeiro
- [ ] Relatórios e exportação (CSV/Excel)
- [ ] API REST versionada (`api/v1`) com Sanctum e documentação Swagger

## Licença

Este projeto é open-source, licenciado sob a [licença MIT](https://opensource.org/licenses/MIT).
