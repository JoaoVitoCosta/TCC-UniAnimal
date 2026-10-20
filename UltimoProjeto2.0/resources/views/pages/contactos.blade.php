@extends('layouts.app')
@section('title','Contato')

@section('content')
<div class="page-top">
  <div class="ctr">
    <div class="g2a">
      <div class="reveal">
        <p class="kicker">📞 Fale conosco</p>
        <h1 class="sec-title sec-title--xl">Entre em <em>contato</em></h1>
        <p style="color:var(--txt-2);font-size:var(--tx-lg);line-height:1.72;margin-bottom:var(--s8);letter-spacing:-.008em">Resposta em até 24 horas úteis por qualquer canal abaixo.</p>
        <a href="tel:+5519989393961" class="ci"><div class="ci__icon" aria-hidden="true">📞</div><div><div class="ci__label">Telefone</div><div class="ci__val">(19) 98939-3961</div></div></a>
        <a href="https://wa.me/5519989393961" target="_blank" rel="noopener" class="ci"><div class="ci__icon" aria-hidden="true"><x-icone-marca nome="whatsapp" :tamanho="22" /></div><div><div class="ci__label">WhatsApp</div><div class="ci__val">(19) 98939-3961</div></div></a>
        <a href="mailto:unianimal@gmail.com" class="ci"><div class="ci__icon" aria-hidden="true">✉️</div><div><div class="ci__label">E-mail</div><div class="ci__val">unianimal@gmail.com</div></div></a>
        <div class="ci"><div class="ci__icon" aria-hidden="true">📍</div><div><div class="ci__label">Localização</div><div class="ci__val">Tambaú, São Paulo — Brasil</div></div></div>
        <div class="ci"><div class="ci__icon" aria-hidden="true">🕐</div><div><div class="ci__label">Horário</div><div class="ci__val">Seg–Sex: 8h–18h | Sáb: 9h–13h</div></div></div>
        <img src="{{ asset('images/Animais/cachorro1.webp') }}" alt="Cachorro com coleira UniAnimal" class="img-r" style="margin-top:var(--s8);aspect-ratio:16/9" loading="lazy">
      </div>
      <div class="reveal d2">
        @if($errors->any())<div class="form-errors" role="alert">@foreach($errors->all() as $e)<p>• {{ $e }}</p>@endforeach</div>@endif
        <div class="form-sheet">
          <h2 style="font-family:var(--f-display);font-size:var(--tx-xl);font-weight:700;letter-spacing:-.025em;margin-bottom:5px">Envie uma mensagem</h2>
          <p style="font-size:var(--tx-sm);color:var(--txt-2);margin-bottom:var(--s6);letter-spacing:-.005em">Respondemos em até 24 horas úteis.</p>
          <form method="POST" action="{{ route('contactos.store') }}" novalidate>
            @csrf
            <div class="form-2col">
              <div class="form-group">
                <label for="nome" class="form-label">Nome *</label>
                <input type="text" id="nome" name="nome" class="form-input {{ $errors->has('nome') ? 'form-input--err' : '' }}" value="{{ old('nome') }}" placeholder="Seu nome" required>
                @error('nome')<p class="form-err">{{ $message }}</p>@enderror
              </div>
              <div class="form-group">
                <label for="email" class="form-label">E-mail *</label>
                <input type="email" id="email" name="email" class="form-input {{ $errors->has('email') ? 'form-input--err' : '' }}" value="{{ old('email') }}" placeholder="seu@email.com" required>
                @error('email')<p class="form-err">{{ $message }}</p>@enderror
              </div>
            </div>
            <div class="form-2col">
              <div class="form-group">
                <label for="tel" class="form-label">Telefone</label>
                <input type="tel" id="tel" name="telefone" class="form-input" value="{{ old('telefone') }}" placeholder="(00) 00000-0000">
              </div>
              <div class="form-group">
                <label for="ass" class="form-label">Assunto</label>
                <input type="text" id="ass" name="assunto" class="form-input" value="{{ old('assunto') }}" placeholder="Ex: Dúvida sobre produto">
              </div>
            </div>
            <div class="form-group">
              <label for="msg" class="form-label">Mensagem *</label>
              <textarea id="msg" name="mensagem" class="form-input {{ $errors->has('mensagem') ? 'form-input--err' : '' }}" placeholder="Descreva sua dúvida..." required rows="5">{{ old('mensagem') }}</textarea>
              @error('mensagem')<p class="form-err">{{ $message }}</p>@enderror
            </div>
            <button type="submit" class="btn btn-p btn-fw btn-lg">📨 Enviar mensagem</button>
          </form>
        </div>
      </div>
    </div>
  </div>
</div>
@endsection