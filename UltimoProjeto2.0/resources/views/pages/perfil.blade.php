@extends('layouts.dashboard')
@section('title', 'Perfil')
@section('page_title')<span class="ico" aria-hidden="true">👤</span>Perfil &amp; Configurações @endsection

@section('content')
<div style="max-width:700px">

  @if($errors->any())
  <div class="form-errors reveal" role="alert">
    @foreach($errors->all() as $e)<p>• {{ $e }}</p>@endforeach
  </div>
  @endif

  <div class="form-sheet reveal">
    <h3 style="margin-bottom:16px;color:var(--t-ink)">Meus dados</h3>
    <form method="POST" action="{{ route('perfil.update') }}" novalidate>
      @csrf
      @method('PUT')

      <div class="form-group">
        <label class="form-label">Nome *</label>
        <input name="nome" class="form-input {{ $errors->has('nome') ? 'form-input--err' : '' }}"
          value="{{ old('nome', $tutor->nome) }}" required>
        @error('nome')<p class="form-err">{{ $message }}</p>@enderror
      </div>

      <div class="form-2col">
        <div class="form-group">
          <label class="form-label">Telefone *</label>
          <input name="telefone" class="form-input {{ $errors->has('telefone') ? 'form-input--err' : '' }}"
            value="{{ old('telefone', $tutor->telefone) }}" required>
          @error('telefone')<p class="form-err">{{ $message }}</p>@enderror
        </div>
        <div class="form-group">
          <label class="form-label">E-mail *</label>
          <input type="email" name="email" class="form-input {{ $errors->has('email') ? 'form-input--err' : '' }}"
            value="{{ old('email', $tutor->email) }}" required>
          @error('email')<p class="form-err">{{ $message }}</p>@enderror
        </div>
      </div>

      <div class="form-group">
        <label class="form-label">Endereço</label>
        <input name="endereco" class="form-input" value="{{ old('endereco', $tutor->endereco) }}">
      </div>

      <button type="submit" class="btn btn-p btn-lg" style="margin-top:8px"><span class="ico" aria-hidden="true">💾</span>Salvar alterações</button>
    </form>
  </div>

  <div class="form-sheet reveal" style="margin-top:20px">
    <h3 style="margin-bottom:12px;color:var(--t-ink)">Preferências</h3>
    <label style="display:flex;align-items:center;gap:10px;cursor:pointer;padding:12px;background:var(--bg-3);border-radius:var(--r2);border:1px solid var(--b2)">
      <div>
        <div style="font-weight:600;color:var(--txt)"><span class="ico" aria-hidden="true">🌓</span>Tema da interface</div>
        <div style="font-size:.75rem;color:var(--txt-3)">Use o botão no topo da página para alternar entre claro e escuro. O padrão é o tema claro.</div>
      </div>
    </label>
  </div>

</div>
@endsection
