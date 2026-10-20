<!DOCTYPE html>
<html lang="pt-BR" data-theme="light">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta name="csrf-token" content="{{ csrf_token() }}">
<meta name="description" content="@yield('meta_desc','UniAnimal — Coleira inteligente com GPS e monitoramento de saúde animal 24h.')">
<meta name="theme-color" content="#F0F4F8">
<title>@yield('title','UniAnimal') — Coleira Inteligente</title>
<link rel="icon" type="image/svg+xml" href="data:image/svg+xml,<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 100 100'><text y='.9em' font-size='90'>🐾</text></svg>">
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link rel="stylesheet" href="{{ asset('css/reset.css') }}">
<link rel="stylesheet" href="{{ asset('css/style.css') }}">
<link rel="stylesheet" href="{{ asset('css/transitions.css') }}">
<link rel="stylesheet" href="{{ asset('css/dashboard.css') }}">
@stack('head')
</head>
<body>

<div id="page-veil"    aria-hidden="true"></div>
<div id="progress-bar" aria-hidden="true"></div>
<div id="cursor-dot"   aria-hidden="true"></div>

{{-- NAVBAR --}}
<nav class="nav" id="navbar" role="navigation" aria-label="Navegação principal">
  <div class="nav__inner">

    <a href="{{ route('home') }}" class="nav__logo" aria-label="UniAnimal — início">
      <div class="nav__logo-mark" aria-hidden="true">🐾</div>
      <span class="nav__logo-name">Uni<strong>Animal</strong></span>
    </a>

    <div class="nav__links" id="nav-links" role="menubar">
      <a href="{{ route('sobre') }}"        class="nav__link {{ request()->routeIs('sobre')        ? 'active' : '' }}">Sobre</a>
      <a href="{{ route('diferenciais') }}" class="nav__link {{ request()->routeIs('diferenciais') ? 'active' : '' }}">Recursos</a>
      <a href="{{ route('depoimentos') }}"  class="nav__link {{ request()->routeIs('depoimentos*') ? 'active' : '' }}">Depoimentos</a>
      <a href="{{ route('contactos') }}"    class="nav__link {{ request()->routeIs('contactos')    ? 'active' : '' }}">Contato</a>
      <div class="nav__sep" aria-hidden="true"></div>
      <a href="{{ route('produtos') }}" class="nav__cta nav__link">Comprar</a>
      <div class="nav__sep" aria-hidden="true"></div>
      @auth
        <a href="{{ route('dashboard') }}" class="nav__link {{ request()->routeIs('dashboard') ? 'active' : '' }}">Dashboard</a>
        <form method="POST" action="{{ route('logout') }}" style="display:inline">@csrf
          <button type="submit" class="nav__link" style="font-family:inherit">Sair</button>
        </form>
      @else
        <a href="{{ route('login') }}"   class="nav__link">Entrar</a>
        <a href="{{ route('registo') }}" class="nav__out nav__link">Criar conta</a>
      @endauth

      {{-- Theme Toggle público --}}
      <button class="theme-btn" id="themeToggle" aria-label="Alternar tema" title="Alternar tema claro/escuro" style="margin-left:8px">
        <span class="theme-btn__icon theme-btn__sun">☀️</span>
        <span class="theme-btn__icon theme-btn__moon">🌙</span>
        <span class="theme-btn__knob"></span>
      </button>
    </div>

    <button class="nav__burger" id="nav-burger" aria-label="Menu" aria-expanded="false" aria-controls="nav-links">
      <span></span><span></span><span></span>
    </button>
  </div>
</nav>

@if(session('success'))
<div class="flash flash-ok" role="alert">
  <svg width="15" height="15" viewBox="0 0 16 16" fill="currentColor" aria-hidden="true"><path d="M8 1a7 7 0 110 14A7 7 0 018 1zm3.03 4.47L7 9.5 5.47 7.97a.75.75 0 00-1.06 1.06l2 2a.75.75 0 001.06 0l4.5-4.5a.75.75 0 00-1.06-1.06z"/></svg>
  {{ session('success') }}
</div>
@endif
@if(session('error'))
<div class="flash flash-err" role="alert">
  <svg width="15" height="15" viewBox="0 0 16 16" fill="currentColor" aria-hidden="true"><path d="M8 1a7 7 0 110 14A7 7 0 018 1zm0 3.5a.75.75 0 00-.75.75v3.5a.75.75 0 001.5 0v-3.5A.75.75 0 008 4.5zm0 6.5a.875.875 0 100 1.75A.875.875 0 008 11z"/></svg>
  {{ session('error') }}
</div>
@endif

<main id="main-content">@yield('content')</main>

<footer class="footer" role="contentinfo">
  <div class="ctr">
    <div class="footer__grid">
      <div>
        <a href="{{ route('home') }}" class="footer__logo">
          <div class="footer__mark" aria-hidden="true">🐾</div>
          <span class="footer__bname">Uni<strong>Animal</strong></span>
        </a>
        <p class="footer__desc">Plataforma SaaS veterinária premium com monitoramento inteligente de saúde animal. GPS em tempo real, alertas automáticos e coleira IoT para tutores e produtores de todo o Brasil.</p>
        <div class="footer__soc" aria-label="Redes sociais">
          <a href="#" class="footer__soc-a" aria-label="Instagram"><x-icone-marca nome="instagram" /></a>
          <a href="#" class="footer__soc-a" aria-label="Facebook"><x-icone-marca nome="facebook" /></a>
          <a href="https://wa.me/5519989393961" class="footer__soc-a" aria-label="WhatsApp" target="_blank" rel="noopener"><x-icone-marca nome="whatsapp" /></a>
          <a href="#" class="footer__soc-a" aria-label="YouTube"><x-icone-marca nome="youtube" /></a>
        </div>
      </div>
      <nav aria-label="Produto">
        <p class="footer__col-title">Produto</p>
        <div class="footer__links">
          <a href="{{ route('sobre') }}"        class="footer__link">Sobre</a>
          <a href="{{ route('diferenciais') }}" class="footer__link">Recursos</a>
          <a href="{{ route('produtos') }}"     class="footer__link">Produtos</a>
          <a href="{{ route('oferta') }}"       class="footer__link">Oferta especial</a>
        </div>
      </nav>
      <nav aria-label="Plataforma">
        <p class="footer__col-title">Plataforma</p>
        <div class="footer__links">
          <a href="{{ route('animais.index') }}" class="footer__link">Meus Animais</a>
          <a href="{{ route('dashboard') }}"      class="footer__link">Dashboard</a>
          <a href="{{ route('orcamento') }}"           class="footer__link">Orçamento Atacado</a>
          <a href="{{ route('depoimentos') }}"         class="footer__link">Depoimentos</a>
          <a href="{{ route('contactos') }}"           class="footer__link">Contato</a>
        </div>
      </nav>
      <address style="font-style:normal">
        <p class="footer__col-title">Contato</p>
        <div class="footer__ct">
          
          <div class="footer__ct-r"><span>📞</span><a href="tel:+5519989393961" style="color:inherit">(19) 98939-3961</a></div>
          <div class="footer__ct-r"><span><x-icone-marca nome="whatsapp" :tamanho="16" /></span><a href="https://wa.me/5519989393961" target="_blank" rel="noopener" style="color:inherit">WhatsApp</a></div>
          <div class="footer__ct-r"><span>✉️</span><a href="mailto:unianimal@gmail.com" style="color:inherit">unianimal@gmail.com</a></div>
          <div class="footer__ct-r"><span>📍</span><span>Tambaú, São Paulo — Brasil</span></div>
          <div class="footer__ct-r"><span>🕐</span><span>Seg–Sex: 8h–18h</span></div>
        </div>
      </address>
    </div>
    <div class="footer__bot">
      <p class="footer__copy">&copy; {{ date('Y') }} <a href="{{ route('home') }}">UniAnimal</a>. Todos os direitos reservados.</p>
      <p class="footer__copy">Feito com ❤️ para o bem-estar animal · SaaS Veterinário Premium</p>
    </div>
  </div>
</footer>

<script src="{{ asset('js/app.js') }}"></script>
<script src="{{ asset('js/transitions.js') }}"></script>
<script src="{{ asset('js/theme.js') }}"></script>
@stack('scripts')

{{-- Widget de acessibilidade (VLibras) — antes duplicado e "morto" (fora de
     qualquer @section) em 10 views diferentes; agora vive uma única vez,
     de forma funcional, aqui no layout público compartilhado. --}}
<div vw class="enabled">
  <div vw-access-button class="active"></div>
  <div vw-plugin-wrapper>
    <div class="vw-plugin-top-wrapper"></div>
  </div>
</div>
<script src="https://vlibras.gov.br/app/vlibras-plugin.js"></script>
<script>
  new window.VLibras.Widget('https://vlibras.gov.br/app');
</script>
</body>
</html>
