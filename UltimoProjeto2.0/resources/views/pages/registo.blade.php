@extends('layouts.app')
@section('title','Criar Conta')

@section('content')
<div class="auth-page">
  <div class="auth-left">
    <div class="auth-left__img"><img src="{{ asset('images/Animais/animais-domesticos.jpg') }}" alt="" loading="eager"></div>
    <div class="auth-left__veil" aria-hidden="true"></div>
    <div class="auth-left__body">
      <p class="kicker auth-left__kicker">Junte-se a nós</p>
      <h1 class="auth-left__title">Proteja seu pet com <em>tecnologia</em></h1>
      <p class="auth-left__sub">Cadastre-se gratuitamente e comece a monitorar hoje mesmo.</p>
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
        <a href="{{ route('login') }}" class="auth-tab" role="tab" aria-selected="false">Entrar</a>
        <button class="auth-tab act" role="tab" aria-selected="true">Criar conta</button>
      </div>
      <h2 class="auth-box__title">Criar sua conta</h2>
      <p class="auth-box__sub">Junte-se à comunidade UniAnimal.</p>

      @if($errors->any())<div class="form-errors" role="alert">@foreach($errors->all() as $e)<p>• {{ $e }}</p>@endforeach</div>@endif

      <form method="POST" action="{{ route('registo.post') }}" novalidate>
        @csrf
        <div class="form-group">
          <label for="name" class="form-label">Nome completo *</label>
          <div class="form-icon">
            <span class="form-icon__i" aria-hidden="true">👤</span>
            <input type="text" id="name" name="name" class="form-input form-input--pl {{ $errors->has('name') ? 'form-input--err' : '' }}" value="{{ old('name') }}" placeholder="Seu nome completo" required autocomplete="name" autofocus>
          </div>
          @error('name')<p class="form-err">{{ $message }}</p>@enderror
        </div>
        <div class="form-group">
          <label for="email" class="form-label">E-mail *</label>
          <div class="form-icon">
            <span class="form-icon__i" aria-hidden="true">✉️</span>
            <input type="email" id="email" name="email" class="form-input form-input--pl {{ $errors->has('email') ? 'form-input--err' : '' }}" value="{{ old('email') }}" placeholder="seu@email.com" required autocomplete="email">
          </div>
          @error('email')<p class="form-err">{{ $message }}</p>@enderror
        </div>
        <div class="form-2col">
          <div class="form-group">
            <label for="password" class="form-label">Senha *</label>
            <input type="password" id="password" name="password" class="form-input {{ $errors->has('password') ? 'form-input--err' : '' }}" placeholder="Mín. 8 caracteres" required autocomplete="new-password">
            @error('password')<p class="form-err">{{ $message }}</p>@enderror
          </div>
          <div class="form-group">
            <label for="password_confirmation" class="form-label">Confirmar *</label>
            <input type="password" id="password_confirmation" name="password_confirmation" class="form-input" placeholder="Repita a senha" required autocomplete="new-password">
          </div>
        </div>
        <div style="margin-bottom:var(--s5)">
          <label style="display:flex;align-items:flex-start;gap:var(--s2);font-size:var(--tx-sm);color:var(--txt-2);cursor:pointer;line-height:1.5;letter-spacing:-.005em">
            <input type="checkbox" name="termos" required style="width:15px;height:15px;accent-color:var(--t);margin-top:2px;flex-shrink:0">
            Concordo com os <a href="#" style="color:var(--t-ink)">Termos</a> e <a href="#" style="color:var(--t-ink)">Privacidade</a>.
          </label>
        </div>
        <button type="submit" class="btn btn-p btn-fw btn-lg">🚀 Criar minha conta</button>
      </form>

      <div class="auth-divider" style="display:flex;align-items:center;gap:12px;margin:var(--s5) 0;color:var(--txt-3);font-size:var(--tx-xs)">
        <span style="flex:1;height:1px;background:var(--b2)"></span>OU<span style="flex:1;height:1px;background:var(--b2)"></span>
      </div>
      <a href="{{ route('auth.google') }}" class="btn btn-g btn-fw btn-lg" style="display:flex;align-items:center;justify-content:center;gap:8px">
        <x-icone-marca nome="google" :tamanho="20" />Cadastrar com Google
      </a>
      <div class="auth-box__foot">Já tem conta? <a href="{{ route('login') }}">Fazer login →</a></div>
    </div>
  </div>
</div>
@endsection