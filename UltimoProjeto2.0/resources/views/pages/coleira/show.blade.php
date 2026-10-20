@extends('layouts.dashboard')
@section('title', 'Localização — ' . $animal->nome)
@section('page_title')<span class="ico" aria-hidden="true">📍</span>Localização de {{ $animal->nome }}@endsection

@section('content')
@php $c = $animal->coleira; @endphp

@if(! $c)
  <div class="empty-state">
    <p class="empty-state__ico">📡</p>
    <p>Este animal ainda não possui uma coleira cadastrada.</p>
    <a href="{{ route('animais.edit', $animal) }}" class="btn btn-p btn-sm">Cadastrar coleira</a>
  </div>
@else

<div class="cards-grid">
  <div class="stat-card">
    <span class="stat-card__ico">🔋</span>
    <div class="stat-card__value">{{ $c->bateria_percentual }}%</div>
    <div class="stat-card__label">Bateria ({{ $c->bateria_status_label }})</div>
  </div>
  <div class="stat-card">
    <span class="stat-card__ico">📡</span>
    <div class="stat-card__value">{{ $c->status_label }}</div>
    <div class="stat-card__label">Status da Coleira</div>
  </div>
  <div class="stat-card">
    <span class="stat-card__ico">🎯</span>
    <div class="stat-card__value">{{ $c->precisao ? $c->precisao.'m' : '—' }}</div>
    <div class="stat-card__label">Precisão do GPS</div>
  </div>
  <div class="stat-card">
    <span class="stat-card__ico">🕐</span>
    <div class="stat-card__value" style="font-size:1rem">{{ ucfirst($c->localizacao_atualizada_em?->diffForHumans() ?? '—') }}</div>
    <div class="stat-card__label">Última atualização</div>
  </div>
</div>

<section class="dp-section">
  <div class="dp-section__head">
    <h2>Última posição conhecida</h2>
    <button id="btn-simular" class="btn btn-g btn-sm" data-id="{{ $animal->id }}"><span class="ico" aria-hidden="true">📡</span>Simular nova posição</button>
  </div>

  @if($c->latitude && $c->longitude)
    <div class="panel" style="padding:12px">
      <p style="font-size:.85rem;color:var(--txt-2)">
        Latitude: <strong>{{ $c->latitude }}</strong> &nbsp;·&nbsp;
        Longitude: <strong>{{ $c->longitude }}</strong>
      </p>
      <a href="https://www.google.com/maps?q={{ $c->latitude }},{{ $c->longitude }}" target="_blank" rel="noopener" class="btn btn-p btn-sm" style="margin-top:10px">
        Abrir no mapa →
      </a>
    </div>
  @else
    <p style="color:var(--txt-3)">Nenhuma posição registrada ainda.</p>
  @endif
</section>

{{-- ROTA DAS ÚLTIMAS 10 POSIÇÕES (GPS) --}}
@php
  // localizacoes já vem da mais recente para a mais antiga: pega as 10
  // últimas e inverte, para a rota ser desenhada na ordem do percurso.
  $rota = $c->localizacoes->take(10)->reverse()->values()->map(fn ($l) => [
    'lat'  => (float) $l->latitude,
    'lng'  => (float) $l->longitude,
    'hora' => $l->registrado_em?->format('d/m H:i'),
  ]);
@endphp
<section class="dp-section">
  <div class="dp-section__head"><h2><span class="ico" aria-hidden="true">🗺️</span>Rota das últimas {{ $rota->count() }} posições</h2></div>
  <div class="iot-card">
    @if($rota->count())
      <div id="mapa-rota" class="mapa-rota" data-rota='@json($rota)' role="img" aria-label="Mapa com a rota das últimas posições da coleira"></div>
      <div class="mapa-legenda">
        <span><i class="mapa-legenda__ponto mapa-legenda__ponto--inicio"></i> Início ({{ $rota->first()['hora'] }})</span>
        <span><i class="mapa-legenda__ponto"></i> Pontos intermediários</span>
        <span><i class="mapa-legenda__ponto mapa-legenda__ponto--atual"></i> Posição atual ({{ $rota->last()['hora'] }})</span>
      </div>
    @else
      <p style="color:var(--txt-3)">Ainda não há posições para desenhar a rota.</p>
    @endif
  </div>
</section>

{{-- HISTÓRICO DE BATIMENTOS --}}
<section class="dp-section">
  <div class="dp-section__head"><h2><span class="ico" aria-hidden="true">❤️</span>Histórico de batimentos</h2></div>
  @include('partials.bpm-monitor', ['animal' => $animal])
</section>

{{-- TABELAS DE REFERÊNCIA DOS DADOS --}}
<section class="dp-section">
  <div class="dp-section__head"><h2><span class="ico" aria-hidden="true">📋</span>Referência dos dados</h2></div>

  @include('partials.bpm-referencia', ['refAtual' => $animal->faixaReferencia()['chave'] ?? null])

  <div class="iot-card bpm-ref">
    <h3 class="bpm-ref__title"><span class="ico" aria-hidden="true">🔋</span>Bateria e precisão do GPS</h3>
    <div class="bpm-ref__scroll">
      <table class="data-table">
        <thead><tr><th>Dado</th><th>Bom</th><th>Atenção</th><th>Crítico</th></tr></thead>
        <tbody>
          <tr>
            <td><strong>Bateria da coleira</strong></td>
            <td><span class="badge badge--ok">Normal: acima de 35%</span></td>
            <td><span class="badge badge--warn">Baixa: 16% a 35%</span></td>
            <td><span class="badge badge--err">Crítica: 15% ou menos</span></td>
          </tr>
          <tr>
            <td><strong>Precisão do GPS</strong></td>
            <td><span class="badge badge--ok">Até 5 m</span></td>
            <td><span class="badge badge--warn">6 a 15 m</span></td>
            <td><span class="badge badge--err">Acima de 15 m</span></td>
          </tr>
        </tbody>
      </table>
    </div>
    <p class="bpm-info bpm-ref__nota">
      A precisão indica o raio de erro da posição: quanto menor, mais exata. Em locais fechados ou com muitos prédios o GPS
      tende a ficar menos preciso. Com bateria baixa ou crítica, recarregue a coleira para não perder o monitoramento.
    </p>
  </div>
</section>

<section class="dp-section">
  <div class="dp-section__head"><h2>Histórico de localização</h2></div>
  <div class="panel">
    <div style="overflow-x:auto">
      <table class="data-table">
        <thead><tr><th>Data/Hora</th><th>Latitude</th><th>Longitude</th><th>Precisão</th></tr></thead>
        <tbody>
          @forelse($animal->coleira->localizacoes as $loc)
          <tr>
            <td style="color:var(--txt-3);font-size:.78rem">{{ $loc->registrado_em->format('d/m/Y H:i') }}</td>
            <td>{{ $loc->latitude }}</td>
            <td>{{ $loc->longitude }}</td>
            <td>{{ $loc->precisao ? $loc->precisao.' m' : '—' }}</td>
          </tr>
          @empty
          <tr><td colspan="4" style="text-align:center;color:var(--txt-3);padding:20px">Sem histórico ainda.</td></tr>
          @endforelse
        </tbody>
      </table>
    </div>
  </div>
</section>

@endif
@endsection

@push('head')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/leaflet/1.9.4/leaflet.min.css">
@endpush

@push('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/leaflet/1.9.4/leaflet.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/4.4.1/chart.umd.min.js"></script>
<script src="{{ asset('js/batimentos.js') }}"></script>
<script>
// Mapa com a rota das últimas posições da coleira (Leaflet + OpenStreetMap).
(function () {
  const div = document.getElementById('mapa-rota');
  if (!div || !window.L) return;

  const rota = JSON.parse(div.dataset.rota || '[]');
  const pontos = rota.map(p => [p.lat, p.lng]);
  const css = nome => getComputedStyle(document.documentElement).getPropertyValue(nome).trim();

  const mapa = L.map(div, { scrollWheelZoom: false });
  L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
    maxZoom: 19,
    attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a>',
  }).addTo(mapa);

  // Caminho percorrido
  L.polyline(pontos, { color: css('--t') || '#00E5A8', weight: 4, opacity: .85 }).addTo(mapa);

  // Um marcador por posição: início (verde), intermediários e posição atual (destacada)
  rota.forEach((p, i) => {
    const inicio = i === 0;
    const atual  = i === rota.length - 1;
    const cor = atual ? (css('--err') || '#F04B58') : inicio ? (css('--ok') || '#10D078') : (css('--t2') || '#00C4FF');

    L.circleMarker([p.lat, p.lng], {
      radius: atual ? 9 : inicio ? 7 : 5,
      color: '#fff', weight: 2, fillColor: cor, fillOpacity: 1,
    })
      .bindTooltip(`${i + 1}º ponto · ${p.hora}` + (atual ? ' — posição atual' : inicio ? ' — início' : ''))
      .addTo(mapa);
  });

  if (pontos.length === 1) mapa.setView(pontos[0], 17);
  else mapa.fitBounds(pontos, { padding: [30, 30] });
})();

document.getElementById('btn-simular')?.addEventListener('click', async function() {
  const id = this.dataset.id;
  this.textContent = '⏳ Simulando...';
  this.disabled = true;
  try {
    await fetch(`/api/animais/${id}/coleira/simular`);
    location.reload();
  } catch (e) {
    this.textContent = 'Erro ao simular';
    this.disabled = false;
  }
});
</script>
@endpush
