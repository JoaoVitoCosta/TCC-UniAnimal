@extends('layouts.dashboard')

@section('title', 'Dashboard')
@section('page_title', 'Minha Dashboard')

@section('content')

<div class="cards-grid">
  <div class="stat-card">
    <span class="stat-card__ico">🐾</span>
    <div class="stat-card__value">{{ $stats['total_animais'] }}</div>
    <div class="stat-card__label">Meus Animais</div>
  </div>
  <div class="stat-card">
    <span class="stat-card__ico">📡</span>
    <div class="stat-card__value">{{ $stats['coleiras_ativas'] }}</div>
    <div class="stat-card__label">Coleiras Ativas</div>
  </div>
  <div class="stat-card">
    <span class="stat-card__ico">🔋</span>
    <div class="stat-card__value">{{ $stats['bateria_media'] ? round($stats['bateria_media']) . '%' : '—' }}</div>
    <div class="stat-card__label">Bateria Média</div>
  </div>
  <div class="stat-card {{ $stats['alertas_ativos'] > 0 ? 'stat-card--alert' : '' }}">
    <span class="stat-card__ico">⚠️</span>
    <div class="stat-card__value">{{ $stats['alertas_ativos'] }}</div>
    <div class="stat-card__label">Alertas Ativos</div>
  </div>
</div>

@if($animais->isNotEmpty())
<section class="dp-section">
  <div class="dp-section__head">
    <h2><span class="ico" aria-hidden="true">❤️</span>Frequência Cardíaca</h2>
    <select id="bpm-animal" class="bpm-select" aria-label="Escolher animal">
      @foreach($animais as $animal)
        <option value="{{ $animal->id }}" data-ref='@json($animal->faixaReferencia())'>{{ $animal->especie_emoji }} {{ $animal->nome }}</option>
      @endforeach
    </select>
  </div>

  {{-- animal => null: o animal vem do <select> acima, não do último do @foreach --}}
  @include('partials.bpm-monitor', ['animal' => null])
  @include('partials.bpm-referencia')
</section>
@endif

<section class="dp-section">
  <div class="dp-section__head">
    <h2>Meus Animais</h2>
    <a href="{{ route('animais.create') }}" class="btn btn-p btn-sm"><span class="ico" aria-hidden="true">➕</span>Novo Animal</a>
  </div>

  @if($animais->isEmpty())
    <div class="empty-state">
      <p class="empty-state__ico">🐾</p>
      <p>Você ainda não cadastrou nenhum animal.</p>
      <a href="{{ route('animais.create') }}" class="btn btn-p btn-sm">Cadastrar meu primeiro animal</a>
    </div>
  @else
    <div class="animais-grid">
      @foreach($animais as $animal)
        <a href="{{ route('animais.show', $animal) }}" class="animal-card">
          <div class="animal-card__head">
            <span class="animal-card__emoji">{{ $animal->especie_emoji }}</span>
            <span class="badge badge--{{ $animal->status_color }}">{{ $animal->status_label }}</span>
          </div>
          <h3 class="animal-card__nome">{{ $animal->nome }}</h3>
          <p class="animal-card__meta">{{ $animal->especie }} · {{ $animal->porte_label }}</p>

          @if($animal->coleira)
            <div class="animal-card__coleira">
              <span><span class="ico" aria-hidden="true">🔋</span>{{ $animal->coleira->bateria_percentual }}%</span>
              <span><span class="ico" aria-hidden="true">📡</span>{{ $animal->coleira->status_label }}</span>
            </div>
          @else
            <div class="animal-card__coleira animal-card__coleira--sem">Sem coleira cadastrada</div>
          @endif
        </a>
      @endforeach
    </div>
  @endif
</section>

@if($alertas->isNotEmpty())
<section class="dp-section">
  <div class="dp-section__head"><h2><span class="ico" aria-hidden="true">⚠️</span>Alertas abertos</h2></div>
  <ul class="alertas-list">
    @foreach($alertas as $alerta)
      <li class="alertas-list__item alertas-list__item--{{ $alerta->cor }}">
        <span class="badge badge--{{ $alerta->cor }}">{{ $alerta->nivel_label }}</span>
        <div>
          <strong>{{ $alerta->animal->nome }} · {{ $alerta->titulo }}</strong>
          <span class="alertas-list__msg">{{ $alerta->mensagem }}</span>
          <span class="alertas-list__hora">Desde {{ $alerta->created_at->format('d/m H:i') }}</span>
        </div>
        <a href="{{ route('coleira.show', $alerta->animal) }}" class="btn btn-g btn-sm">Ver detalhes</a>
      </li>
    @endforeach
  </ul>
</section>
@endif

@endsection


@push('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/4.4.1/chart.umd.min.js"></script>
<script src="{{ asset('js/batimentos.js') }}"></script>
@endpush
