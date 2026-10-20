<!DOCTYPE html>
<html lang="pt-BR" data-theme="light">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta name="theme-color" content="#F0F4F8">
<meta name="csrf-token" content="{{ csrf_token() }}">
<title>@yield('title','Dashboard') — UniAnimal</title>
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

<div class="dash">

  {{-- SIDEBAR --}}
  <aside class="sb" id="sidebar" role="navigation" aria-label="Menu do painel">
    <div class="sb__top">
      <a href="{{ route('home') }}" class="sb__brand">
        <div class="sb__mark" aria-hidden="true">🐾</div>
        <span class="sb__bname">Uni<strong>Animal</strong></span>
      </a>
      <div class="sb__badge">Painel do Tutor</div>
      <div class="sb__user">
        <div class="sb__ava" aria-hidden="true">{{ mb_strtoupper(mb_substr(auth()->user()->name,0,2)) }}</div>
        <div>
          <div class="sb__uname">{{ auth()->user()->name }}</div>
          <div class="sb__urole">Tutor</div>
        </div>
        <div class="sb__status-dot" title="Online"></div>
      </div>
    </div>

    <nav class="sb__nav">
      <p class="sb__section">Principal</p>
      <a href="{{ route('dashboard') }}" class="sb__link {{ request()->routeIs('dashboard') ? 'act' : '' }}">
        <span class="sb__ico" aria-hidden="true">📊</span>Dashboard
        @php $alertas = \App\Models\Alerta::whereIn('animal_id', auth()->user()->tutor?->animais()->pluck('id') ?? [])->abertos()->count(); @endphp
        @if($alertas > 0)<span class="sb__badge-n">{{ $alertas }}</span>@endif
      </a>

      <p class="sb__section">Meus Pets</p>
      <a href="{{ route('animais.index') }}" class="sb__link {{ request()->routeIs('animais.index') || request()->routeIs('animais.show') ? 'act' : '' }}">
        <span class="sb__ico" aria-hidden="true">🐾</span>Meus Animais
      </a>
      <a href="{{ route('animais.create') }}" class="sb__link {{ request()->routeIs('animais.create') ? 'act' : '' }}">
        <span class="sb__ico" aria-hidden="true">➕</span>Novo Animal
      </a>

      <p class="sb__section">Conta</p>
      <a href="{{ route('perfil') }}" class="sb__link {{ request()->routeIs('perfil') ? 'act' : '' }}">
        <span class="sb__ico" aria-hidden="true">👤</span>Perfil &amp; Configurações
      </a>
    </nav>

    <div class="sb__foot">
      <a href="{{ route('home') }}" class="sb__link" style="margin-bottom:8px">
        <span class="sb__ico">🌐</span>Ver site público
      </a>
      <form method="POST" action="{{ route('logout') }}">@csrf
        <button type="submit" class="sb__logout">
          <span class="sb__ico" aria-hidden="true">🚪</span>Sair da conta
        </button>
      </form>
    </div>
  </aside>

  {{-- MAIN CONTENT --}}
  <div class="dm">
    <header class="topbar">
      <div style="display:flex;align-items:center;gap:12px">
        <button id="sidebar-btn" class="btn btn-g btn-sm" aria-label="Menu" aria-expanded="false">
          <svg width="18" height="18" viewBox="0 0 18 18" fill="none">
            <rect x="2" y="4" width="14" height="1.5" rx=".75" fill="currentColor"/>
            <rect x="2" y="8.25" width="14" height="1.5" rx=".75" fill="currentColor"/>
            <rect x="2" y="12.5" width="14" height="1.5" rx=".75" fill="currentColor"/>
          </svg>
        </button>
        <h1 class="topbar__title">@yield('page_title','Dashboard')</h1>
      </div>
      <div class="topbar__actions">
        @if(session('success'))
          <span style="font-size:.8rem;color:var(--ok);display:flex;align-items:center;gap:8px"><span class="ico" aria-hidden="true">✅</span>{{ session('success') }}</span>
        @endif
        @if(session('error'))
          <span style="font-size:.8rem;color:var(--err);display:flex;align-items:center;gap:8px"><span class="ico" aria-hidden="true">⚠️</span>{{ session('error') }}</span>
        @endif

        {{-- Theme Toggle --}}
        <button class="theme-btn" id="themeToggle" aria-label="Alternar tema" title="Alternar tema claro/escuro">
          <span class="theme-btn__icon theme-btn__sun">☀️</span>
          <span class="theme-btn__icon theme-btn__moon">🌙</span>
          <span class="theme-btn__knob"></span>
        </button>
      </div>
    </header>

    <main class="dp" id="main-content">@yield('content')</main>
  </div>

</div>

<script src="{{ asset('js/app.js') }}"></script>
<script src="{{ asset('js/transitions.js') }}"></script>
<script src="{{ asset('js/theme.js') }}"></script>
@stack('scripts')
</body>
</html>
