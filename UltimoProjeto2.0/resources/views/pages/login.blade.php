@extends('layouts.app')
@section('title','Entrar')

@section('content')
<div class="auth-page">
  <div class="auth-left">
    <div class="auth-left__img"><img src="{{ asset('images/Animais/pug.jpg') }}" alt="" loading="eager"></div>
    <div class="auth-left__veil" aria-hidden="true"></div>
    <div class="auth-left__body">
      <p class="kicker auth-left__kicker">Monitoramento Pet</p>
      <h1 class="auth-left__title">Cuide do seu pet com <em>inteligência</em></h1>
      <p class="auth-left__sub">Monitore saúde, batimentos cardíacos e bem-estar do seu animal em tempo real, de qualquer lugar.</p>
      <div class="auth-left__stats">
        <div><div class="auth-stat__n">500+</div><div class="auth-stat__k">Pets monitorados</div></div>
        <div><div class="auth-stat__n">24/7</div><div class="auth-stat__k">Disponível</div></div>
        <div><div class="auth-stat__n">98%</div><div class="auth-stat__k">Satisfação</div></div>
      </div>
    </div>
  </div>
  <div class="auth-right">
    <div class="auth-box">
      <div class="auth-tabs" role="tablist">
        <button class="auth-tab act" role="tab" aria-selected="true">Entrar</button>
        <a href="{{ route('registo') }}" class="auth-tab" role="tab" aria-selected="false">Criar conta</a>
      </div>
      <h2 class="auth-box__title">Bem-vindo de volta</h2>
      <p class="auth-box__sub">Acesse o painel e monitore seu pet.</p>

      @if($errors->any())<div class="form-errors" role="alert">@foreach($errors->all() as $e)<p>• {{ $e }}</p>@endforeach</div>@endif

      <form method="POST" action="{{ route('login.post') }}" novalidate>
        @csrf
        <div class="form-group">
          <label for="email" class="form-label">E-mail</label>
          <div class="form-icon">
            <span class="form-icon__i" aria-hidden="true">✉️</span>
            <input type="email" id="email" name="email" class="form-input form-input--pl {{ $errors->has('email') ? 'form-input--err' : '' }}" value="{{ old('email') }}" placeholder="seu@email.com" required autocomplete="email" autofocus>
          </div>
          @error('email')<p class="form-err">{{ $message }}</p>@enderror
        </div>
        <div class="form-group">
          <label for="password" class="form-label" style="display:flex;justify-content:space-between">
            <span>Senha</span>
          </label>
          <div class="form-icon">
            <span class="form-icon__i" aria-hidden="true">🔒</span>
            <input type="password" id="password" name="password" class="form-input form-input--pl {{ $errors->has('password') ? 'form-input--err' : '' }}" placeholder="••••••••" required autocomplete="current-password">
          </div>
          @error('password')<p class="form-err">{{ $message }}</p>@enderror
        </div>
        <div style="display:flex;align-items:center;gap:var(--s2);margin-bottom:var(--s5)">
          <input type="checkbox" name="remember" id="rem" style="width:15px;height:15px;accent-color:var(--t)">
          <label for="rem" style="font-size:var(--tx-sm);color:var(--txt-2);cursor:pointer;letter-spacing:-.005em">Lembrar de mim</label>
        </div>
        <button type="submit" class="btn btn-p btn-fw btn-lg">→ Entrar na conta</button>
      </form>

      <div class="auth-divider" style="display:flex;align-items:center;gap:12px;margin:var(--s5) 0;color:var(--txt-3);font-size:var(--tx-xs)">
        <span style="flex:1;height:1px;background:var(--b2)"></span>OU<span style="flex:1;height:1px;background:var(--b2)"></span>
      </div>
      <a href="{{ route('auth.google') }}" class="btn btn-g btn-fw btn-lg" style="display:flex;align-items:center;justify-content:center;gap:8px">
        <x-icone-marca nome="google" :tamanho="20" />Entrar com Google
      </a>

      <div class="auth-demo">Demo: rafael.nogueira@petmail.com / <strong>password</strong></div>
      <div class="auth-box__foot">Não tem conta? <a href="{{ route('registo') }}">Cadastre-se gratuitamente</a></div>
    </div>
  </div>
</div>
@endsection
