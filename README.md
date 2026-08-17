<p align="center">
  <img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="320" alt="Laravel Logo">
</p>

<h1 align="center">FlowERP</h1>

<p align="center">
  Um ERP modular open-source construído com Laravel, pensado para qualidade de produção — não um projeto de estudo.
</p>

<p align="center">
  <a href="https://github.com/rafaelscheffer/flow-erp/actions/workflows/ci.yml">
    <img src="https://github.com/rafaelscheffer/flow-erp/actions/workflows/ci.yml/badge.svg" alt="CI">
  </a>
  <img src="https://img.shields.io/badge/PHP-8.4-777BB4?logo=php&logoColor=white" alt="PHP 8.4">
  <img src="https://img.shields.io/badge/Laravel-12-FF2D20?logo=laravel&logoColor=white" alt="Laravel 12">
  <img src="https://img.shields.io/badge/Filament-4-F59E0B?logo=laravel&logoColor=white" alt="Filament 4">
  <img src="https://img.shields.io/badge/PostgreSQL-16-4169E1?logo=postgresql&logoColor=white" alt="PostgreSQL 16">
  <img src="https://img.shields.io/badge/Redis-7-DC382D?logo=redis&logoColor=white" alt="Redis 7">
  <img src="https://img.shields.io/badge/Docker-Compose-2496ED?logo=docker&logoColor=white" alt="Docker Compose">
  <img src="https://img.shields.io/badge/license-MIT-blue" alt="License MIT">
</p>

---

## Sobre o projeto

**FlowERP** é um sistema de gestão empresarial (ERP) desenvolvido do zero com Laravel, aplicando arquitetura modular inspirada em Domain-Driven Design. Cobre os principais processos de uma empresa — clientes, fornecedores, produtos, estoque, compras, vendas e financeiro — com o rigor de engenharia de um software comercial: tipagem estrita, separação clara de responsabilidades, auditoria de dados e uma suíte de testes automatizados cobrindo cada módulo.

> **Status:** todos os módulos de negócio do escopo original estão implementados, testados e documentados — Administração, Clientes, Produtos, Estoque, Compras, Vendas, Financeiro e Relatórios — junto com uma API REST versionada, importação de CSV, processamento assíncrono via fila e integração contínua.

## Funcionalidades

- **Administração** — usuários, autenticação com 2FA (app authenticator e e-mail), papéis e permissões granulares (Spatie Permission), log de auditoria automático em todos os modelos relevantes.
- **Clientes e Fornecedores** — cadastro PF/PJ com validação de CPF/CNPJ, endereço e contato.
- **Produtos** — catálogo com marcas, categorias (hierárquicas), coleções, variantes (grade de cor/tamanho), SKU/EAN/NCM, preços e limites de estoque.
- **Estoque** — movimentações imutáveis (histórico nunca é editado, só acrescido), saldo consolidado por localização, reservas e transferências entre depósitos.
- **Compras** — pedidos de compra com recebimento, gerando movimentação de estoque e conta a pagar automaticamente.
- **Vendas** — pedidos com confirmação que debita estoque e gera conta a receber, via eventos de domínio.
- **Financeiro** — plano de contas, centros de custo, contas a pagar/receber e fluxo de caixa com widgets de KPI.
- **Relatórios** — 7 relatórios filtráveis com exportação CSV, um por área de negócio.
- **Dashboard** — KPIs de vendas, compras, financeiro e estoque baixo, cada widget controlado pela permissão do próprio recurso.
- **API REST v1** — endpoints versionados (`/api/v1`) para todos os módulos de negócio, autenticados via Sanctum com tokens de escopo granular, documentados com Swagger/OpenAPI.
- **Importação de CSV** — upsert em lote para Clientes, Produtos e Fornecedores, processado em fila.
- **Processamento assíncrono** — fila (Redis) e agendador (`schedule:work`) rodando como serviços dedicados; um job diário envia um resumo de contas vencidas por e-mail para quem tem permissão financeira.

## Arquitetura

O projeto segue uma separação entre **infraestrutura/cross-cutting concerns** e **módulos de domínio**, evitando o modelo tradicional de Controllers "gordos" do Laravel:

```
app/
├── Actions/         # Casos de uso (regra de negócio isolada e reutilizável)
├── DTOs/            # Objetos de transferência de dados entre camadas
├── Enums/           # Enums tipados (status, eventos, etc.)
├── Events/          # Eventos de domínio
├── Exceptions/      # Exceções customizadas
├── Filament/        # Componentes Filament reutilizáveis entre módulos (ex.: BaseImporter)
├── Http/            # Controllers "finos" — apenas orquestram Actions/Services
├── Jobs/            # Processamento assíncrono (fila)
├── Mail/            # E-mails transacionais
├── Models/          # Modelos compartilhados (ex.: User)
├── Notifications/   # Notificações (banco de dados, e-mail)
├── Observers/       # Observers de Eloquent
├── Policies/        # Autorização
├── Repositories/    # Abstração de acesso a dados
├── Rules/           # Regras de validação customizadas (ex.: ValidCpf, ValidCnpj)
├── Services/        # Serviços de domínio transversais
└── Traits/          # Comportamentos reutilizáveis (ex.: Auditable)

Modules/
├── Administration/  # Usuários, papéis, permissões, log de auditoria
├── Customers/       # Clientes PF/PJ (CPF/CNPJ)
├── Products/        # Produtos, variantes, SKU/NCM/EAN
├── Inventory/       # Estoque e movimentações (histórico imutável)
├── Purchases/       # Fornecedores e pedidos de compra
├── Sales/           # Vendas (pedido → confirmação, com efeitos em estoque/financeiro)
├── Financial/       # Plano de contas, centros de custo, contas a pagar/receber, fluxo de caixa
└── Reports/         # Relatórios filtráveis com exportação CSV
```

Cada módulo é autocontido — com seus próprios `Models`, `Services`, `Repositories`, `Policies`, `DTOs`, `Actions`, `Requests`, `Resources`, `Events`, `Migrations`, `Routes` e `Tests` — e é registrado via um `ServiceProvider` próprio (`Modules/{Nome}/Providers/{Nome}ServiceProvider.php`), carregando suas migrations e rotas de forma independente. Fornecedores (`Supplier`) vivem dentro de `Modules/Purchases`, não em um módulo próprio, por decisão explícita de escopo.

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
| Painel administrativo | Filament v4 |
| Banco de dados | PostgreSQL 16 |
| Cache, sessão e fila | Redis 7 |
| Autenticação de API | Laravel Sanctum |
| Documentação de API | Swagger / OpenAPI (`darkaonline/l5-swagger`) |
| Autorização | Spatie Laravel Permission |
| E-mail (ambiente local) | Mailpit |
| Front-end (build) | Vite, Tailwind CSS v4 |
| Testes | PHPUnit, Mockery, Faker |
| Qualidade de código | Laravel Pint (PSR-12) |
| Integração contínua | GitHub Actions |
| Infraestrutura | Docker & Docker Compose |
| Servidor web | Nginx |

## Arquitetura de containers

O ambiente é totalmente orquestrado via `docker-compose.yml`, com os seguintes serviços:

- **app** — PHP-FPM, executa a aplicação Laravel.
- **nginx** — servidor web, expõe a aplicação em `http://localhost:8080`.
- **postgres** — banco de dados principal, porta `5432`.
- **redis** — cache, sessão e driver de fila, porta `6379`.
- **mailpit** — captura de e-mails em desenvolvimento, UI em `http://localhost:8025`.
- **queue** — worker dedicado (`queue:work`) para processamento assíncrono (exportação/importação de CSV, e-mails).
- **scheduler** — executa o agendador do Laravel (`schedule:work`) para tarefas recorrentes (ex.: resumo diário de contas vencidas).

## Como executar o projeto

### Pré-requisitos
- [Docker](https://www.docker.com/) e Docker Compose

### Passos

```bash
# 1. Clonar o repositório
git clone https://github.com/rafaelscheffer/flow-erp.git
cd flow-erp

# 2. Copiar o arquivo de ambiente
cp .env.example .env

# 3. Subir os containers
docker compose up -d --build

# 4. Instalar dependências PHP (dentro do container)
docker compose exec app composer install

# 5. Gerar a chave da aplicação
docker compose exec app php artisan key:generate

# 6. Executar as migrations e popular os dados iniciais
docker compose exec app php artisan migrate --seed

# 7. Instalar dependências de front-end e compilar assets
docker compose exec app npm install
docker compose exec app npm run build
```

A aplicação estará disponível em **http://localhost:8080**, com o painel administrativo em `/admin` e o Mailpit em **http://localhost:8025**.

O seeder cria um usuário administrador de desenvolvimento:

```
E-mail: admin@flowerp.test
Senha:  password
```

### API REST

A API versionada fica em `/api/v1`, autenticada via Sanctum. Um token é obtido em `POST /api/v1/auth/token` (e-mail/senha), com escopo de habilidades igual ou menor que as permissões reais do usuário. A documentação interativa (Swagger) está disponível em **http://localhost:8080/api/documentation**.

### Executando os testes

```bash
docker compose exec app php artisan test
```

### Padrão de código

```bash
docker compose exec app ./vendor/bin/pint
```

Ambos os comandos também rodam automaticamente no GitHub Actions a cada push/pull request para `main` (veja `.github/workflows/ci.yml`).

## Roadmap

- [x] Infraestrutura Docker (app, nginx, postgres, redis, mailpit, queue, scheduler)
- [x] Estrutura modular (`Modules/`) e padrão de `ServiceProvider` por módulo
- [x] Sistema de auditoria (`AuditLogService` + trait `Auditable`)
- [x] Módulo de Administração — usuários, autenticação, 2FA, papéis e permissões
- [x] Módulo de Clientes e Fornecedores
- [x] Módulo de Produtos e Estoque
- [x] Módulo de Compras e Vendas
- [x] Módulo Financeiro (plano de contas, centros de custo, AP/AR, fluxo de caixa)
- [x] Relatórios com exportação CSV
- [x] Dashboard com KPIs por módulo
- [x] API REST versionada (`api/v1`) com Sanctum e documentação Swagger
- [x] Importação de CSV (Clientes, Produtos, Fornecedores)
- [x] Processamento assíncrono via fila + agendador (resumo diário de contas vencidas)
- [x] Integração contínua (GitHub Actions: estilo de código + testes)

## Licença

Este projeto é open-source, licenciado sob a [licença MIT](https://opensource.org/licenses/MIT).
