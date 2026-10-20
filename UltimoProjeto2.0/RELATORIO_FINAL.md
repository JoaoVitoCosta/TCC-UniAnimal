# UniAnimal — Relatório Final da Transformação

**Data:** 24/08/2026
**Escopo:** Refatoração do projeto Laravel `UltimoProjeto2.0` para padrão comercial, com dashboard exclusiva do cliente, coleira GPS/bateria, login Google e banco de dados normalizado.

---

## 1. Resumo executivo

O projeto era, na prática, um **painel administrativo** (CRUD completo de tutores, listagem global de todos os animais, sem isolamento entre contas) com um módulo de **monitoramento de sinais vitais simulados** (batimento, respiração, temperatura). Não havia nenhuma camada de autorização: qualquer usuário autenticado enxergava e editava os dados de qualquer tutor.

A transformação entregue:

- Elimina o CRUD administrativo de tutores — **cada conta agora é o próprio tutor** (relação 1:1 com `users`), criado automaticamente no cadastro.
- Substitui o monitoramento de sinais vitais por uma **arquitetura de coleira com GPS e bateria** (sem hardware real, apenas a estrutura preparada, como pedido).
- Introduz uma **Policy de autorização** (`AnimalPolicy`) que impede um tutor de acessar dados de outro — a falha de segurança mais importante do projeto original.
- Adiciona **login com Google (Socialite)**.
- Consolida **4 arquivos SQL duplicados** em um único `database.sql`.
- Corrige o **tema claro/escuro**, que antes só funcionava parcialmente (a maioria dos componentes ignorava o tema light).
- Remove um bug real de **código morto duplicado** (widget de acessibilidade fora de qualquer `@section`, presente e sem efeito em 10 views).

---

## 2. Antes × Depois

| Aspecto | Antes | Depois |
|---|---|---|
| Modelo de conta | 1 usuário podia gerenciar N tutores (CRUD completo) | 1 usuário = 1 tutor (criado automaticamente) |
| Autorização | Nenhuma — qualquer usuário via/editava tudo | `AnimalPolicy` + `authorizeResource`, escopo por `tutor_id` em toda query |
| Dashboard | Administrativa (todos os tutores, todos os animais, registros globais) | Exclusiva do cliente (só os próprios animais, alertas e bateria) |
| Monitoramento | Sinais vitais simulados (bpm, respiração, temperatura) | GPS + bateria da coleira (lat/long/precisão/status), com histórico |
| Código da coleira | Inexistente | Campo obrigatório, único, com validação amigável |
| Login | E-mail/senha apenas | E-mail/senha **+ Google (Socialite)** |
| Tema | Dark por padrão; light quebrado em vários componentes | **Light por padrão**; tokens de cor centralizados, corrige contraste em todos os componentes |
| Banco de dados | 4 dumps SQL redundantes, `DB_CONNECTION` inconsistente com `.env` | 1 `database.sql` único, sincronizado com as migrations, MySQL/MariaDB |
| Testes | Só o exemplo padrão do Laravel | Testes de escopo por tutor, autorização e criação automática de perfil |

---

## 3. Bugs corrigidos

1. **Falha de autorização (crítica)** — qualquer usuário autenticado acessava `/tutores`, `/animais/{id}` e `/monitoramento` de qualquer outra pessoa.
2. **Tema claro incompleto** — apenas alguns seletores (`.panel`, `.form-sheet` etc.) tinham override para `data-theme="light"`; a maioria dos componentes usava direto as variáveis dark, resultando em baixo contraste. Corrigido centralizando os tokens em `:root[data-theme="light"]`.
3. **Widget VLibras morto e duplicado** — o mesmo bloco de acessibilidade estava colado, idêntico, em 10 arquivos de view, sempre **fora** de qualquer `@section` (portanto nunca era renderizado, já que essas views usam `@extends`). Consolidado em uma única instância funcional no layout público.
4. **Inconsistência de ambiente de banco** — `.env` usava SQLite enquanto `.env.example` apontava para MySQL.

## 4. Código morto / duplicado removido

- `TutorController`, `TutorRequest`, `TutorRepository`/`TutorRepositoryInterface`, views `pages/tutores/*`, rota `Route::resource('tutores', ...)`.
- `MonitoramentoController`, `MonitoramentoService`, `MonitoramentoRequest`, `Monitoramento` (model + migration + factory), views `pages/monitoramento/*`.
- `Adicional` (model + migration) — não era referenciado fora da ficha do animal e sua função foi absorvida pela nova estrutura de coleira/alertas.
- Coluna `animals.monitorado` (booleana solta) — substituída pela relação real com `coleiras`.
- 4 arquivos SQL redundantes: `BancoNovo_dump.sql`, `banco2_0_dump.sql`, `unianimal_mysql_dump.sql`, `setup_mysql.sql`.
- 10 blocos duplicados do widget VLibras (ver item 3 acima).

## 5. Funcionalidades adicionadas

- **Coleira (GPS + bateria)**: nova entidade 1:1 com o animal, código único obrigatório, latitude/longitude/precisão, percentual e status de bateria, status geral do dispositivo.
- **Histórico de localização**: tabela `localizacoes`, tela dedicada (`/animais/{animal}/coleira`) com última posição, link para abrir no Google Maps e tabela de histórico.
- **Endpoint de simulação** (`/api/animais/{animal}/coleira/simular`): gera uma nova posição/bateria de demonstração — arquitetura pronta para plugar um provedor de GPS real no futuro, sem implementar IoT de verdade (conforme solicitado).
- **Login com Google** via Laravel Socialite, com associação automática a contas existentes pelo e-mail e criação automática do perfil de tutor.
- **Perfil & Configurações** (`/perfil`): tutor edita nome, telefone, e-mail e endereço.
- **Alertas automáticos** na dashboard: bateria baixa/crítica ou coleira sem sinal.

## 6. Estrutura final do banco de dados

Ver `database/database.sql` para o schema completo. Tabelas:

`users` · `tutors` (1:1 com `users`) · `animals` (N:1 com `tutors`) · `coleiras` (1:1 com `animals`) · `localizacoes` (N:1 com `coleiras`) · `depoimentos` · `contatos` · `orcamentos` · `produtos`.

Todas as FKs, índices e constraints estão documentados nas migrations (`database/migrations/`) e replicados fielmente no `database.sql`.

## 7. Arquivos criados

```
app/Models/Coleira.php
app/Models/Localizacao.php
app/Http/Controllers/ColeiraController.php
app/Http/Controllers/PerfilController.php
app/Policies/AnimalPolicy.php
database/migrations/2024_01_02_000001_create_coleiras_table.php
database/migrations/2024_01_02_000002_create_localizacoes_table.php
database/migrations/2024_01_02_000003_add_google_id_to_users_table.php
database/factories/ColeiraFactory.php
database/database.sql
resources/views/pages/coleira/show.blade.php
resources/views/pages/perfil.blade.php
tests/Feature/AnimalScopingTest.php
tests/Feature/RegistroECadastroTest.php
RELATORIO_FINAL.md
```

## 8. Arquivos modificados (principais)

```
app/Models/Animal.php, Tutor.php, User.php
app/Http/Controllers/AnimalController.php, DashboardController.php, AuthController.php
app/Http/Requests/AnimalRequest.php
app/Repositories/AnimalRepository.php, AnimalRepositoryInterface.php
app/Providers/AppServiceProvider.php
routes/web.php
database/migrations/2024_01_01_000006_create_tutores_table.php  (user_id agora único)
database/migrations/2024_01_01_000007_create_animais_table.php  (campo "monitorado" removido)
database/factories/AnimalFactory.php, TutorFactory.php, UserFactory.php
database/seeders/DatabaseSeeder.php
resources/views/layouts/app.blade.php, dashboard.blade.php
resources/views/pages/dashboard.blade.php, login.blade.php, registo.blade.php
resources/views/pages/animais/index.blade.php, show.blade.php, form.blade.php
resources/views/errors/404.blade.php, database.blade.php, generic.blade.php
public/css/style.css (tokens de tema claro), public/css/dashboard.css (novos componentes)
public/js/theme.js (padrão light)
composer.json (laravel/socialite), config/services.php, .env, .env.example
```

## 9. Arquivos removidos

```
app/Http/Controllers/TutorController.php, MonitoramentoController.php
app/Http/Requests/TutorRequest.php, MonitoramentoRequest.php
app/Repositories/TutorRepository.php, TutorRepositoryInterface.php
app/Services/MonitoramentoService.php
app/Models/Monitoramento.php, Adicional.php
database/factories/MonitoramentoFactory.php
database/migrations/2024_01_01_000008_create_monitoramentos_table.php
database/migrations/2024_01_01_000009_create_adicionais_table.php
resources/views/pages/tutores/ (diretório inteiro)
resources/views/pages/monitoramento/ (diretório inteiro)
database/BancoNovo_dump.sql, banco2_0_dump.sql, unianimal_mysql_dump.sql, setup_mysql.sql
database/database.sqlite
```

## 10. Dependências

**Adicionadas:** `laravel/socialite` (^5.16) — login com Google.
**Removidas:** nenhuma (o projeto original não tinha dependências supérfluas no `composer.json`).

## 11. Melhorias de segurança

- `AnimalPolicy` com `authorizeResource` — elimina o acesso cruzado entre tutores (era a falha mais grave do projeto).
- Senhas de contas criadas via Google recebem hash aleatório (`Str::random(32)` + `Hash::make`), evitando login por senha "adivinhável" em contas OAuth.
- `codigo_coleira` validado com `Rule::unique` corretamente ignorando o próprio registro em edições (evita falso positivo/negativo de duplicidade).
- Tratamento de exceções do Socialite com log e mensagem amigável (sem expor stack trace ao usuário).

## 12. Melhorias de performance

- Consultas de animais e coleiras usam eager loading (`with('coleira.ultimaLocalizacao')`) evitando N+1 na dashboard e na listagem.
- Índices adicionados em `coleiras` (`codigo`, `status`, `bateria_status`) e `localizacoes` (`coleira_id + registrado_em` composto) para as consultas de histórico e alerta.

## 13. Melhorias de UX/UI

- Tokens de tema centralizados corrigindo contraste em **todo** o painel no modo claro (antes só parcialmente coberto).
- Dashboard reduzida ao que interessa ao cliente: meus animais, bateria, alertas, localização — sem ruído administrativo.
- Cards de animal, cards de estatística e lista de alertas novos, com estados vazios (`empty-state`) para quando o tutor ainda não tem animais cadastrados.
- Botões "Entrar/Cadastrar com Google" nas telas de autenticação.

## 14. Testes realizados

- `tests/Feature/AnimalScopingTest.php`: confirma que um tutor não vê nem acessa animais de outro tutor (403 esperado), e que visitantes não autenticados são redirecionados ao login.
- `tests/Feature/RegistroECadastroTest.php`: confirma que o registro cria o `Tutor` automaticamente; que o cadastro de animal exige `codigo_coleira`; que a coleira é criada e vinculada corretamente ao salvar o animal.

> ⚠️ **Não foi possível executar `php artisan test`** neste ambiente de análise (não há interpretador PHP disponível no sandbox usado para a refatoração). Recomendo fortemente rodar `composer install && php artisan test` localmente antes de subir para produção.

## 15. Pendências e sugestões para futuras evoluções

- **Executar `composer install`** localmente para baixar o `laravel/socialite` (o sandbox usado aqui não tem acesso ao Packagist) e configurar `GOOGLE_CLIENT_ID`/`GOOGLE_CLIENT_SECRET` no `.env`.
- **Rodar as migrations** (`php artisan migrate --seed`) em um MySQL/MariaDB real e validar o `database.sql` com um `mysql < database.sql` de teste.
- Refino visual mais profundo (item 8 do prompt original) — microinterações, skeleton loading e animações sutis foram parcialmente endereçados via CSS existente; uma repaginada visual completa de todos os componentes (modais, toasters animados, tabelas) é um próximo passo natural, mas ultrapassa o que foi possível validar sem um navegador real neste ambiente.
- Avaliar mover a listagem de animais para paginação client-side com filtros reativos (hoje é server-side simples).
- Implementar de fato a integração de hardware GPS/IoT quando o fornecedor da coleira física estiver definido — a arquitetura (`coleiras` + `localizacoes` + endpoint de ingestão) já está pronta para receber dados reais no lugar do endpoint `/simular`.
- Adicionar rate limiting específico nas rotas de autenticação (`throttle`) como camada extra de proteção contra força bruta.

## 16. Correções pós-entrega — bugs reais encontrados testando a aplicação de verdade

A primeira entrega deste projeto foi validada apenas por leitura de código (sem interpretador PHP disponível no ambiente da época) e continha bugs reais que só apareceram ao efetivamente rodar o site. Depois de feedback do cliente reportando tela branca/500 em produção, foi instalado PHP 8.3 + extensões (sqlite, mysql, mbstring, xml, curl, bcmath) no ambiente de trabalho e o projeto foi executado de verdade: migrations rodadas, seeder executado, servidor local (`php artisan serve`) subido e testado com requisições HTTP reais (`curl`), incluindo login, CRUD completo de animal (criar/editar/apagar), registro de conta nova, logout e a suíte de testes automatizados.

Bugs reais encontrados e corrigidos nesta rodada:

1. **`Controller.php` sem as traits `AuthorizesRequests`/`ValidatesRequests`** — qualquer chamada a `$this->authorize()`/`$this->authorizeResource()` derrubava a aplicação com erro fatal. Corrigido adicionando as traits à classe base.

2. **`Route::resource('animais', ...)` gerava o parâmetro `{animai}` em vez de `{animal}`** — bug crítico e sutil: o inflector (singularizador) do Laravel é em inglês e não reconhece "animais" como plural de "animal" em português; `Str::singular('animais')` retorna `"animai"`. Isso quebrava o route model binding em toda ação que abre um animal específico (show/edit/update/destroy) — provavelmente a causa raiz principal do "rota quebrada" relatado. Corrigido forçando o nome do parâmetro: `Route::resource('animais', AnimalController::class)->parameters(['animais' => 'animal'])`.

3. **`authorizeResource()` depende de `$this->middleware()`, removido do Controller base no Laravel 11+** — mesmo após corrigir o item 1, chamar `authorizeResource()` no construtor do `AnimalController` gerava `Call to undefined method AnimalController::middleware()` em toda requisição a `/animais/*`, pois esse método faz parte do sistema antigo de "controller middleware" que a reforma "routing without controllers" do Laravel 11 removeu da classe base. Corrigido substituindo por chamadas explícitas de `$this->authorize(...)` em cada método do controller (`index`, `create`, `store`, `show`, `edit`, `update`, `destroy`).

4. **`AppServiceProvider::boot()` executava uma query no banco incondicionalmente** — mesmo em comandos artisan que não usam banco (`--version`, `key:generate` etc.), impedindo a aplicação inteira de subir se a conexão MySQL não estivesse disponível no momento do boot. Corrigido restringindo a chamada ao contexto HTTP (`! runningInConsole()`) e envolvendo em try/catch para nunca derrubar a app por causa desse ajuste cosmético de `sql_mode`.

5. **Bug de agrupamento lógico no scope `Coleira::scopeComAlerta()`** — um `orWhere()` sem parênteses "escapava" do filtro `whereIn('animal_id', ...)` do chamador, podendo (em tese) vazar alertas de coleiras de outros tutores na dashboard. Corrigido envolvendo a condição em `where(fn ($q) => ...)`.

6. **Meta tag `csrf-token` ausente no layout público** (`layouts/app.blade.php`) — inconsistência com o layout da dashboard, que já tinha essa tag; adicionada para consistência e para suportar futuras chamadas AJAX nas páginas públicas.

7. **Model `User` sem a trait `HasFactory`** e **teste `ExampleTest` sem `RefreshDatabase`** — ambos quebravam a suíte de testes automatizados (`php artisan test`). Corrigidos.

## 17. Sessão 2026-09-15 — Integração real Flutter ⇄ API ⇄ Banco

A primeira entrega deste projeto (seções 1–16 acima) cobriu **só o site**. Nesta
sessão, o app Flutter (`uni_animal_flutter`) — que até então tinha login
100% simulado (`Future.delayed` + "qualquer e-mail e senha entram") e uma
lista de pets fixa em memória — foi conectado à mesma fonte única de
dados do site, seguindo a arquitetura pedida:

```
Flutter → API (routes/api.php) → Backend Laravel → MySQL "unianimal"
```

### 17.1 Banco de dados

- Banco renomeado: `unianimal_25_08` → **`unianimal`** (`.env`, `.env.example`).
- Removido resíduo morto: arquivo SQLite `petmonitor` na raiz do projeto
  (inconsistência de ambiente já citada na seção 3 deste relatório).
- Tabela `coleiras` reestruturada: `animal_id` passou a ser *nullable* e
  o status ganhou o valor `disponivel`. Antes, o código da coleira era
  **inventado livremente** no cadastro do animal (só validado por
  `unique`); agora ele precisa **já existir no banco como estoque
  provisionado** antes de ser vinculado — pré-condição para o login por
  código funcionar de verdade no app.
- Nova tabela `api_tokens`: sessões do app. Guarda apenas o hash SHA-256
  do token (nunca o texto puro).
- `database/database.sql`, `database/banco_completo.sql` e
  `database/database_dados_demo.sql` atualizados com o novo schema e com
  3 códigos de demonstração ainda não vinculados (`COL-DEMO-0001/2/3`),
  para testar o caminho de erro do login.

### 17.2 Backend / API

Não existia nenhuma API REST antes desta sessão — só rotas web/sessão.
Criado `routes/api.php` (prefixo `/api/v1`):

| Rota | Autenticação | Função |
|---|---|---|
| `POST /api/v1/auth/coleira` | pública, `throttle:10,1` | Login real por código da coleira |
| `POST /api/v1/auth/logout` | token | Revoga o token atual |
| `GET /api/v1/me` | token | Dados do tutor autenticado |
| `GET /api/v1/animais` | token | Lista os animais do tutor |
| `GET /api/v1/animais/{id}` | token | Detalhe de um animal (escopado) |
| `GET /api/v1/animais/{id}/localizacoes` | token | Histórico de GPS da coleira |
| `POST /api/v1/animais` | token | Cadastra animal + vincula coleira |

Autenticação implementada com token opaco próprio (`ApiToken` +
`EnsureApiToken` middleware), não com Sanctum — o pacote não está
instalado e este sandbox não tem acesso ao Packagist para instalá-lo
(mesma limitação já registrada na seção 15 deste relatório para o
Socialite). A implementação é simples e auditável: token de 64
caracteres aleatórios, armazenado como hash SHA-256, expira em 90 dias.
**Recomendação:** migrar para `laravel/sanctum` quando o ambiente tiver
acesso ao Packagist — a troca é isolada em `ApiToken`/`EnsureApiToken`,
não afeta o resto do backend.

Também corrigido: o grupo de middleware `api` (usado por `routes/api.php`)
depende de um rate limiter chamado `api`, que o Laravel 11 só registra
sozinho quando o projeto roda `php artisan install:api`. Como a API foi
habilitada manualmente em `bootstrap/app.php`, isso quebraria **toda**
rota da API em runtime (`Rate limiter [api] is not defined`) — corrigido
registrando o limiter em `AppServiceProvider::boot()`.

`AnimalController` (web) e o novo `Api\AnimalController` (app) agora
usam a mesma regra de negócio: o código da coleira precisa já existir e
estar disponível (`Coleira::estaDisponivel()`); ao editar um animal, a
coleira anterior volta para o estoque (`disponivel`) em vez de ficar
presa. Validação trocada de `unique` para `exists` em `AnimalRequest`.

`JsonResource::withoutWrapping()` habilitado — a API não usa mais o
wrapper `{"data": ...}` padrão do Laravel, simplificando o parsing no
Flutter. `config/cors.php` publicado explicitamente, restrito a `api/*`
(o site usa sessão, não precisa de CORS).

### 17.3 App Flutter

- **Login real**: `login_screen.dart` reescrita — único fluxo é o código
  da coleira, via `AuthService.loginComCodigoDaColeira()` →
  `POST /api/v1/auth/coleira`. O mock de e-mail/senha foi removido, não
  desativado.
- **Cadastro real de animal**: `token_screen.dart` reescrita — chama
  `POST /api/v1/animais` de verdade; erros exibidos são os que o backend
  devolveu, não mensagens inventadas no cliente.
- **Sessão persistida de verdade**: `TokenStorage` (`shared_preferences`)
  guarda o token devolvido pelo backend; `splash_screen.dart` verifica
  essa sessão antes de decidir entre login e dashboard (antes, sempre ia
  para o login após 3s fixos).
- **Dados reais no Dashboard**: `pets_screen.dart` e `dashboard_screen.dart`
  buscam os animais via `AnimalService` (HTTP real), com loading, erro
  (com botão "tentar novamente") e estado vazio tratados — nada mais vem
  de `PetRepository` (deletado).
- **Reconciliação com o backend real**: o backend nunca teve sensores de
  batimento/temperatura/oxigenação (a sessão anterior já tinha
  substituído "sinais vitais" por GPS + bateria — ver seção 1). O app,
  porém, ainda simulava esses três valores. Removidos de `Pet`,
  `metric_grid.dart`, `history_card.dart` e `history_screen.dart`, que
  agora mostram bateria/GPS/porte/peso e histórico real de localização.
  Apagado `heartbeat_chart.dart` (gráfico de um dado inexistente) e a
  dependência `fl_chart`, que ficou sem uso.

### 17.4 O que NÃO foi testado nesta sessão (seja honesto sobre isso)

Este sandbox não tem PHP nem Flutter SDK instalados, e não tem acesso ao
Packagist/pub.dev. Portanto, diferente da rodada anterior (seção 16, que
rodou `php artisan serve` de verdade), **esta sessão foi validada por
leitura e revisão cruzada de código, não por execução real**. Antes de
considerar isso pronto para produção, é necessário rodar localmente:

1. `composer install && php artisan migrate:fresh --seed` — criação do
   banco `unianimal`, incluindo `coleiras` e `api_tokens`.
2. `php artisan serve` e testar com `curl`/Postman:
   - `POST /api/v1/auth/coleira` com um código de demo já vinculado
     (ex.: os que o seeder cria para os tutores de teste) → deve
     devolver token 200.
   - O mesmo endpoint com `COL-DEMO-0001` (não vinculado) → deve
     devolver 422 com mensagem clara, não autenticar.
   - Um `GET /api/v1/animais` sem header `Authorization` → 401.
3. `flutter pub get` (para baixar `http` e `shared_preferences`, que
   ainda não foram instalados neste ambiente) e `flutter run`, apontando
   `ApiConfig.baseUrl` para o backend local (`10.0.2.2:8000` no emulador
   Android por padrão — ajustar para o IP real em dispositivo físico).
4. Fluxo ponta a ponta manual: cadastrar animal no site (ou no app) com
   um código de estoque → login no app com esse mesmo código → dashboard
   mostra os dados reais → alterar bateria/GPS via
   `POST /animais/{id}/coleira/simular` (endpoint de demonstração já
   existente) → conferir que o app reflete a mudança ao puxar
   (`RefreshIndicator`) a lista de animais.

### 17.5 Pendências conhecidas, não resolvidas nesta sessão

- **Tema claro no app**: o Flutter só tem `AppTheme.darkTheme`; não há
  tema claro (o site já tem os dois, com claro como padrão — ver seção
  13). Implementar exigiria tokenizar as cores hoje hardcoded em cada
  tela/componente (`Color(0xff...)` espalhado), fora do escopo desta
  rodada.
- **Mapa real**: `map_screen.dart` desenha um mapa decorativo
  (`FakeMapPainter`), não usa `pet.latitude`/`pet.longitude` reais. Para
  corrigir de verdade é necessário um SDK de mapas (Google Maps/Mapbox)
  com chave de API — decisão de produto/custo que não me cabe tomar
  sozinho.
- Botões "Centralizar" e "Traçar rota" no mapa ainda são no-ops
  (`onPressed: () {}`) — não fingem fazer algo, mas também não fazem
  nada ainda.
- Testes automatizados (Fase 11 do prompt) não foram escritos nesta
  sessão para a API nem para o Flutter — ficou só a validação manual
  descrita em 17.4.

### Validação final da rodada anterior (2026-08-24, registrada na seção 16)

- `php -l` em **100% dos arquivos PHP do projeto**: zero erro de sintaxe.
- `php artisan migrate:fresh --seed`: todas as 10 migrations e o seeder rodam sem erro (testado contra SQLite neste ambiente, por não haver MySQL disponível aqui; o schema é idêntico ao `database.sql` para MySQL/MariaDB).
- `php artisan route:list`: 34 rotas registradas corretamente, parâmetro `{animal}` confirmado.
- `php artisan test`: **8/8 testes passando** (`AnimalScopingTest`, `RegistroECadastroTest`, `ExampleTest`, `Unit/ExampleTest`).
- Fluxo HTTP real via `curl` contra `php artisan serve`, com sessão/cookies/CSRF reais:
  - Todas as páginas públicas (`/`, `/sobre`, `/produtos`, `/depoimentos`, `/contactos`, `/orcamento`, `/diferenciais`, `/oferta`, `/login`, `/registo`) → 200.
  - `/dashboard` e `/animais` sem login → 302 (redireciona para login).
  - Login com usuário do seeder → 302 → dashboard 200.
  - **CRUD de animal completo**: criar (302) → aparece na listagem → editar (302, nome atualizado confirmado) → apagar (302, some da listagem). Zero erros no `laravel.log` em todo o processo.
  - Isolamento entre tutores: tutor A acessando animal do tutor B → **403**, confirmando que a `AnimalPolicy` funciona de fato, não só na leitura do código.
  - Registro de conta nova → tutor criado automaticamente → dashboard mostra estado vazio correto → logout → dashboard bloqueada depois (302 → login).
  - Página 404 customizada renderiza corretamente para rota inexistente.

⚠️ **O que ainda não foi possível validar neste ambiente**: login com Google (Socialite não está instalado, pois o Packagist não é acessível neste sandbox) e o comportamento contra um MySQL/MariaDB real (só SQLite foi testado aqui). Recomendo fortemente repetir pelo menos o fluxo de criar/editar/apagar animal e o login com Google no seu ambiente local antes de considerar o projeto pronto para produção.
