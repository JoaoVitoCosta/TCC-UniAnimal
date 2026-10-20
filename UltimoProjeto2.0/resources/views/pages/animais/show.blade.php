@extends('layouts.dashboard')
@section('title', $animal->nome)
@section('page_title')<span class="ico" aria-hidden="true">{{ $animal->especie_emoji }}</span>{{ $animal->nome }}@endsection

@section('content')
<div class="gm">
  <div style="display:flex;flex-direction:column;gap:var(--s4)">

    {{-- FICHA DO ANIMAL --}}
    <div class="panel">
      <div class="panel__head">
        <span style="color:var(--t-ink)"><span class="ico" aria-hidden="true">📋</span>Ficha do Animal</span>
        <div class="panel__actions">
          <a href="{{ route('animais.edit', $animal) }}" class="btn btn-g btn-sm">Editar</a>
          <span class="chip chip-{{ $animal->status_color }}">{{ $animal->status_label }}</span>
        </div>
      </div>
      <div style="display:grid;grid-template-columns:repeat(3,1fr);gap:16px;padding:8px 0">
        <div><p style="font-size:.72rem;color:var(--txt-3);margin-bottom:3px">ESPÉCIE / RAÇA</p><p>{{ $animal->especie }}@if($animal->raca) · {{ $animal->raca }}@endif</p></div>
        <div><p style="font-size:.72rem;color:var(--txt-3);margin-bottom:3px">PORTE</p><p>{{ $animal->porte_label }}</p></div>
        <div><p style="font-size:.72rem;color:var(--txt-3);margin-bottom:3px">IDADE</p><p>{{ $animal->idade ?? '—' }} anos</p></div>
        <div><p style="font-size:.72rem;color:var(--txt-3);margin-bottom:3px">PESO</p><p>{{ $animal->peso ? $animal->peso.'kg' : '—' }}</p></div>
        <div><p style="font-size:.72rem;color:var(--txt-3);margin-bottom:3px">CÓDIGO DA COLEIRA</p><p>{{ $animal->coleira->codigo ?? '—' }}</p></div>
        <div><p style="font-size:.72rem;color:var(--txt-3);margin-bottom:3px">COLEIRA</p>
          <p>@if($animal->coleira)<span class="ico" aria-hidden="true">✅</span>{{ $animal->coleira->status_label }}@else<span class="ico" aria-hidden="true">⭕</span>Não cadastrada @endif</p></div>
      </div>
    </div>

    {{-- LOCALIZAÇÃO / BATERIA --}}
    @if($animal->coleira)
    @php
      $c = $animal->coleira;
      $h = $animal->ultimoBatimento;
      $faixa = $animal->faixaBatimento($h?->batimento_cardiaco);
    @endphp
    <div class="panel">
      <div class="panel__head">
        <span style="color:var(--t-ink)"><span class="ico" aria-hidden="true">📍</span>Localização, Batimentos &amp; Bateria</span>
        <span style="font-size:.75rem;color:var(--txt-3)">
          {{ ucfirst($c->localizacao_atualizada_em?->diffForHumans() ?? 'Sem dados ainda') }}
        </span>
      </div>
      <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(130px,1fr));gap:12px;margin:12px 0">
        @foreach([
          ['📍','Latitude', $c->latitude ?? '—'],
          ['📍','Longitude', $c->longitude ?? '—'],
          ['🎯','Precisão', $c->precisao ? $c->precisao.' m' : '—'],
          ['🔋','Bateria', $c->bateria_percentual.'% ('.$c->bateria_status_label.')'],
        ] as [$ic,$lb,$vl])
        <div style="background:var(--bg-3);border-radius:var(--r3);padding:16px;text-align:center;border:1px solid var(--b2)">
          <div style="font-size:1.5rem;margin-bottom:6px">{{ $ic }}</div>
          <div style="font-size:1.05rem;font-weight:700;color:var(--t-ink)">{{ $vl }}</div>
          <div style="font-size:.7rem;color:var(--txt-3);margin-top:2px">{{ $lb }}</div>
        </div>
        @endforeach

        {{-- Último batimento cardíaco enviado pela coleira (tabela leituras_saude) --}}
        <div style="background:var(--bg-3);border-radius:var(--r3);padding:16px;text-align:center;border:1px solid var(--b2)">
          <div style="font-size:1.5rem;margin-bottom:6px">❤️</div>
          <div style="font-size:1.05rem;font-weight:700;color:var(--err)">{{ $h?->batimento_cardiaco ? $h->batimento_cardiaco.' bpm' : '—' }}</div>
          <div style="font-size:.7rem;color:var(--txt-3);margin-top:2px">
            Último batimento{{ $h?->registrado_em ? ' · ' . $h->registrado_em->diffForHumans() : '' }}
          </div>
          @if($faixa)
            <span class="badge badge--{{ $faixa[0] }}" style="margin-top:6px">{{ $faixa[1] }}</span>
          @endif
        </div>

        {{-- Última temperatura corporal enviada pela coleira --}}
        @php
          $t = $animal->ultimaTemperatura;
          $nivelT = \App\Models\Animal::nivelNaFaixa($t?->temperatura, $animal->faixaTemperatura());
          $corT = ['normal' => 'ok', 'alerta' => 'warn', 'emergencia' => 'err'][$nivelT] ?? null;
        @endphp
        <div style="background:var(--bg-3);border-radius:var(--r3);padding:16px;text-align:center;border:1px solid var(--b2)">
          <div style="font-size:1.5rem;margin-bottom:6px">🌡️</div>
          <div style="font-size:1.05rem;font-weight:700;color:var(--warn)">{{ $t ? number_format($t->temperatura, 1, ',', '') . ' °C' : '—' }}</div>
          <div style="font-size:.7rem;color:var(--txt-3);margin-top:2px">
            Temperatura{{ $t?->registrado_em ? ' · ' . $t->registrado_em->diffForHumans() : '' }}
          </div>
          @if($corT)
            <span class="badge badge--{{ $corT }}" style="margin-top:6px">{{ ['ok' => 'Normal', 'warn' => 'Alerta', 'err' => 'Emergência'][$corT] }}</span>
          @endif
        </div>
      </div>
      <a href="{{ route('coleira.show', $animal) }}" class="btn btn-p btn-sm">Ver mapa e histórico completo →</a>
    </div>
    @endif

  </div>

  {{-- SIDEBAR DIREITA --}}
  <div style="display:flex;flex-direction:column;gap:var(--s4)">

    @if($animal->coleira)
    <div class="nudge">
      <div class="nudge__icon">📡</div>
      <div class="nudge__title">Simular atualização</div>
      <p class="nudge__sub">Gera uma nova posição/bateria de demonstração (arquitetura preparada para GPS real).</p>
      <button id="btn-simular" class="btn btn-p btn-sm btn-fw" data-id="{{ $animal->id }}">Simular agora</button>
    </div>
    @endif

    <div class="nudge">
      <div class="nudge__icon">🐾</div>
      <div class="nudge__title">Meus Animais</div>
      <p class="nudge__sub">Voltar para a lista completa dos seus pets.</p>
      <a href="{{ route('animais.index') }}" class="btn btn-g btn-sm btn-fw">Ver todos →</a>
    </div>

  </div>
</div>
@endsection

@push('scripts')
<script>
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
