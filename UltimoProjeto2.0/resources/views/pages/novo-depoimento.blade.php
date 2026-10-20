@extends('layouts.app')
@section('title','Novo Depoimento')

@section('content')
<div class="page-top-sm">
  <div class="ctr">
    <div style="max-width:620px;margin:0 auto">
      <div style="text-align:center;margin-bottom:var(--s8)" class="reveal">
        <p class="kicker" style="margin-left:auto;margin-right:auto">✍️ Compartilhe</p>
        <h1 class="sec-title sec-title--xl" style="margin-top:var(--s3)">Deixe seu <em>depoimento</em></h1>
        <p class="sec-sub" style="margin:var(--s3) auto 0">Sua opinião ajuda outros tutores a tomarem a melhor decisão.</p>
      </div>
      @if($errors->any())<div class="form-errors reveal" role="alert">@foreach($errors->all() as $e)<p>• {{ $e }}</p>@endforeach</div>@endif
      <div class="form-sheet reveal">
        <form method="POST" action="{{ route('depoimentos.store') }}" novalidate>
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
          <div class="form-group">
            <label for="tel" class="form-label">Telefone</label>
            <input type="tel" id="tel" name="telefone" class="form-input" value="{{ old('telefone') }}" placeholder="(00) 00000-0000">
          </div>
          <div class="form-group">
            <label class="form-label">Avaliação *</label>
            <div class="stars-wrap" role="radiogroup">
              @for($i=5;$i>=1;$i--)
              <input type="radio" id="s{{ $i }}" name="estrelas" value="{{ $i }}" {{ old('estrelas',5)==$i?'checked':'' }}>
              <label for="s{{ $i }}" aria-label="{{ $i }} estrela{{ $i>1?'s':'' }}">★</label>
              @endfor
            </div>
          </div>
          <div class="form-group">
            <label for="dep-txt" class="form-label">Depoimento * <span style="font-weight:400;text-transform:none;letter-spacing:0">(mín. 20 caracteres)</span></label>
            <textarea id="dep-txt" name="depoimento" class="form-input {{ $errors->has('depoimento') ? 'form-input--err' : '' }}" placeholder="Conte sua experiência com a coleira UniAnimal..." required rows="5">{{ old('depoimento') }}</textarea>
            @error('depoimento')<p class="form-err">{{ $message }}</p>@enderror
          </div>
          <button type="submit" class="btn btn-p btn-fw btn-lg">🚀 Enviar depoimento</button>
        </form>
      </div>
      <p style="text-align:center;margin-top:var(--s5);font-size:var(--tx-sm);color:var(--txt-3);letter-spacing:-.005em">
        Ver os depoimentos? <a href="{{ route('depoimentos') }}" style="color:var(--t-ink);font-weight:600">Ver todos →</a>
      </p>
    </div>
  </div>
</div>
@endsection