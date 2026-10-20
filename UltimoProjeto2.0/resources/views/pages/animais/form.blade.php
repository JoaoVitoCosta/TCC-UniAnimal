@extends('layouts.dashboard')
@section('title', isset($animal->id) ? 'Editar Animal' : 'Novo Animal')
@section('page_title', isset($animal->id) ? 'Editar: '.$animal->nome : 'Cadastrar Animal')

@section('content')
<div style="max-width:700px">
  @if($errors->any())
  <div class="form-errors reveal" role="alert">
    @foreach($errors->all() as $e)<p>• {{ $e }}</p>@endforeach
  </div>
  @endif

  <div class="form-sheet reveal">
    <form method="POST" action="{{ isset($animal->id) ? route('animais.update', $animal) : route('animais.store') }}" novalidate>
      @csrf
      @if(isset($animal->id)) @method('PUT') @endif

      <div class="form-2col">
        <div class="form-group">
          <label class="form-label">Nome do animal *</label>
          <input name="nome" class="form-input {{ $errors->has('nome') ? 'form-input--err' : '' }}"
            value="{{ old('nome', $animal->nome ?? '') }}" placeholder="Ex: Thor, Mimi..." required>
          @error('nome')<p class="form-err">{{ $message }}</p>@enderror
        </div>
        <div class="form-group">
          <label class="form-label">Espécie *</label>
          <select name="especie" class="form-select" required>
            <option value="">Selecione</option>
            @foreach(['Cachorro','Gato','Coelho','Cavalo','Bovino','Outro'] as $esp)
            <option value="{{ $esp }}" {{ old('especie', $animal->especie ?? '') == $esp ? 'selected' : '' }}>{{ $esp }}</option>
            @endforeach
          </select>
          @error('especie')<p class="form-err">{{ $message }}</p>@enderror
        </div>
      </div>

      <div class="form-2col">
        <div class="form-group">
          <label class="form-label">Raça</label>
          <input name="raca" class="form-input" value="{{ old('raca', $animal->raca ?? '') }}" placeholder="Ex: Labrador, Persa...">
        </div>
        <div class="form-group">
          <label class="form-label">Porte *</label>
          <select name="porte" class="form-select {{ $errors->has('porte') ? 'form-input--err' : '' }}" required>
            <option value="">Selecione</option>
            <option value="pequeno" {{ old('porte', $animal->porte ?? '') == 'pequeno' ? 'selected' : '' }}>🐱 Pequeno (até 15kg)</option>
            <option value="medio"   {{ old('porte', $animal->porte ?? '') == 'medio'   ? 'selected' : '' }}>🐕 Médio (15–50kg)</option>
            <option value="grande"  {{ old('porte', $animal->porte ?? '') == 'grande'  ? 'selected' : '' }}>🐴 Grande (acima de 50kg)</option>
          </select>
          @error('porte')<p class="form-err">{{ $message }}</p>@enderror
        </div>
      </div>

      <div class="form-2col">
        <div class="form-group">
          <label class="form-label">Idade (anos)</label>
          <input type="number" name="idade" class="form-input" value="{{ old('idade', $animal->idade ?? '') }}" min="0" max="50" placeholder="Ex: 3">
        </div>
        <div class="form-group">
          <label class="form-label">Peso (kg)</label>
          <input type="number" step="0.1" name="peso" class="form-input" value="{{ old('peso', $animal->peso ?? '') }}" min="0.1" max="999" placeholder="Ex: 25.5">
        </div>
      </div>

      <div class="form-group">
        <label class="form-label">Código da coleira *</label>
        <input name="codigo_coleira" class="form-input {{ $errors->has('codigo_coleira') ? 'form-input--err' : '' }}"
          value="{{ old('codigo_coleira', $animal->coleira->codigo ?? '') }}"
          placeholder="Ex: COL-2026-0001" required>
        @error('codigo_coleira')<p class="form-err">{{ $message }}</p>@enderror
        <p style="font-size:.72rem;color:var(--txt-3);margin-top:4px">
          Código único gravado na coleira UniAnimal do seu pet. Ele habilita o rastreamento por GPS e o monitoramento de bateria.
        </p>
      </div>

      <div style="display:flex;gap:12px;margin-top:8px">
        <button type="submit" class="btn btn-p btn-lg" style="flex:1">
          @if(isset($animal->id))<span class="ico" aria-hidden="true">💾</span>Salvar alterações @else<span class="ico" aria-hidden="true">✅</span>Cadastrar animal @endif
        </button>
        <a href="{{ route('animais.index') }}" class="btn btn-g btn-lg">Cancelar</a>
      </div>
    </form>
  </div>
</div>
@endsection
