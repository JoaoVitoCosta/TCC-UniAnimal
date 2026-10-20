# UniAnimal — App Flutter

App mobile da UniAnimal (coleira inteligente para pets), com a mesma
identidade visual e estrutura de dados do site.

## Como rodar

```bash
flutter pub get
flutter run
```

Login de demonstração: qualquer e-mail e senha (ainda não há backend
ligado — ver seção "Próximos passos").

## Fluxo do app

```
Splash → Login → Meus Animais → Dashboard do pet ⇄ Mapa / Alertas / Histórico
                       ↑                 |
                       └── Novo Animal (código da coleira)
```

## O que foi corrigido nesta rodada

- **Crash garantido**: `HeroPetCard` carregava uma imagem
  (`assets/images/dog.png`) que não existia no projeto — nem a pasta
  `assets/` existia. Substituído por um avatar em emoji, no mesmo
  padrão já usado no resto do app (splash, tela de token).
- **`fl_chart` em `dev_dependencies`**: usado em código de produção
  (`heartbeat_chart.dart`), mas declarado como dependência de
  desenvolvimento no `pubspec.yaml`. Movido para `dependencies`.
- **Import de si mesmo** em `metric_grid.dart` (`import 'metric_grid.dart'`).
  Removido.
- **Bug de navegação**: o menu inferior (Home/Mapa/Alertas) não
  voltava para "Home" ao retornar do Mapa ou dos Alertas com o botão
  de voltar — ficava com o item errado destacado. Corrigido.
- **`history_screen.dart` estava vazio** (0 bytes), sem uso em lugar
  nenhum do app. Implementada por completo.

## O que foi completado (não existia antes)

O app antes só existia como uma tela única, com um pet fixo chamado
"Thor" e dados 100% fixos em cada widget. Para "terminar" o projeto,
adicionei a camada que faltava para ele funcionar como um app de
verdade, com a mesma estrutura de informação do site:

- **`core/models/pet.dart`** — modelo de dados `Pet`, espelhando os
  campos das tabelas `animals`/`coleiras` do backend Laravel do site
  (espécie, raça, porte, status de saúde, bateria, GPS, histórico de
  leituras).
- **`core/data/pet_repository.dart`** — repositório com 6 pets de
  demonstração, usando os **mesmos nomes e dados do banco de dados do
  site** (Belinha, Max, Simba, Amora, Zeca, Bento), para manter o app
  e o site consistentes. Hoje os dados são fixos em memória; os
  comentários no arquivo explicam exatamente que chamadas HTTP
  colocar aqui para ligar à API real, sem precisar mudar nenhuma tela.
- **`screen/login_screen.dart`** — não existia nenhuma tela de login;
  o app pulava direto para "conectar coleira" sem autenticação.
- **`screen/pets_screen.dart`** — tela "Meus Animais" (equivalente à
  página `/animais` do site): lista os pets do tutor, deixa escolher
  qual acompanhar e adicionar um novo pelo código da coleira.
- Dashboard, Mapa, Alertas e o novo Histórico agora recebem o `Pet`
  selecionado como parâmetro, em vez de tudo vir hardcoded.

## Próximos passos (não incluídos nesta rodada)

Este ambiente não tinha acesso ao Flutter/Dart nem ao repositório de
pacotes (pub.dev), então não foi possível compilar nem rodar o app
para testar de ponta a ponta como foi feito com o site — revise com
`flutter analyze` e `flutter run` e me avise se algo não compilar.

Para ligar o app ao backend Laravel de verdade, faltaria:

1. Expor uma API autenticada no Laravel (o app não pode usar sessão
   de cookie como o site; precisaria de algo como Laravel Sanctum
   para emitir um token no login).
2. Trocar `PetRepository` por uma implementação que busca os dados
   dessa API em vez do array fixo em memória (a assinatura dos
   métodos já foi pensada para isso — ver comentários no arquivo).
3. Persistir o token de sessão localmente (ex.: pacote
   `shared_preferences`) para manter o usuário logado entre aberturas
   do app.
