@extends('layouts.dashboard')
@section('title','Animais')
@section('page_title','Gestão de Animais')

@section('content')
<div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:var(--s6)">
  <p style="font-size:.8rem;color:var(--txt-3)">{{ $animais->total() }} animais cadastrados</p>
  <a href="{{ route('animais.create') }}" class="btn btn-p"><span class="ico" aria-hidden="true">➕</span>Novo Animal</a>
</div>

{{-- Filtros --}}
<form method="GET" class="panel" style="padding:16px;margin-bottom:16px">
  <div style="display:flex;flex-wrap:wrap;gap:12px;align-items:flex-end">
    <div>
      <label class="form-label" style="font-size:.72rem">Busca</label>
      <input name="busca" value="{{ request('busca') }}" class="form-input" placeholder="🔍 Nome do animal..." style="padding:6px 12px;font-size:.8rem;width:200px">
    </div>
    <div>
      <label class="form-label" style="font-size:.72rem">Status</label>
      <select name="status" class="form-select" style="padding:6px 12px;font-size:.8rem">
        <option value="">Todos</option>
        <option value="saudavel" {{ request('status')=='saudavel'?'selected':'' }}>✅ Saudável</option>
        <option value="atencao"  {{ request('status')=='atencao'?'selected':'' }}>⚠️ Atenção</option>
        <option value="emergencia" {{ request('status')=='emergencia'?'selected':'' }}>🚨 Emergência</option>
      </select>
    </div>
    <div>
      <label class="form-label" style="font-size:.72rem">Porte</label>
      <select name="porte" class="form-select" style="padding:6px 12px;font-size:.8rem">
        <option value="">Todos</option>
        <option value="pequeno" {{ request('porte')=='pequeno'?'selected':'' }}>Pequeno</option>
        <option value="medio"   {{ request('porte')=='medio'?'selected':'' }}>Médio</option>
        <option value="grande"  {{ request('porte')=='grande'?'selected':'' }}>Grande</option>
      </select>
    </div>
    <button class="btn btn-g btn-sm">Filtrar</button>
    @if(request()->hasAny(['busca','status','porte']))
      <a href="{{ route('animais.index') }}" class="btn btn-g btn-sm"><span class="ico" aria-hidden="true">✕</span>Limpar</a>
    @endif
  </div>
</form>

<div class="panel">
  <div style="overflow-x:auto">
    <table class="data-table">
      <thead>
        <tr>
          <th>Animal</th>
          <th>Espécie / Raça</th>
          <th>Porte</th>
          <th>Idade / Peso</th>
          <th>Status</th>
          <th>Coleira</th>
          <th>Ações</th>
        </tr>
      </thead>
      <tbody>
        @forelse($animais as $animal)
        <tr>
          <td>
            <div style="display:flex;align-items:center;gap:8px">
              <span style="font-size:1.3rem">{{ $animal->especie_emoji }}</span>
              <span style="font-weight:600">{{ $animal->nome }}</span>
            </div>
          </td>
          <td style="color:var(--txt-2)">{{ $animal->especie }}@if($animal->raca) · {{ $animal->raca }}@endif</td>
          <td><span class="chip chip-g">{{ $animal->porte_label }}</span></td>
          <td style="color:var(--txt-2)">{{ $animal->idade ?? '—' }} anos / {{ $animal->peso ? $animal->peso.'kg' : '—' }}</td>
          <td><span class="chip chip-{{ $animal->status_color }}">{{ $animal->status_label }}</span></td>
          <td>
            @if($animal->coleira)
              <span style="display:flex;align-items:center;gap:8px;font-size:.72rem;color:var(--t-ink)">
                <span class="ico" aria-hidden="true">🔋</span>{{ $animal->coleira->bateria_percentual }}% · {{ $animal->coleira->status_label }}
              </span>
            @else
              <span style="color:var(--txt-4);font-size:.72rem">Sem coleira</span>
            @endif
          </td>
          <td>
            <div class="panel__actions">
              <a href="{{ route('animais.show', $animal) }}" class="btn btn-g btn-sm">Ver</a>
              <a href="{{ route('animais.edit', $animal) }}" class="btn btn-g btn-sm">Editar</a>
              <form method="POST" action="{{ route('animais.destroy', $animal) }}" onsubmit="return confirm('Excluir {{ $animal->nome }}?')">
                @csrf @method('DELETE')
                <button class="btn btn-sm" style="background:var(--err-bg);color:var(--err)">✕</button>
              </form>
            </div>
          </td>
        </tr>
        @empty
        <tr><td colspan="7" style="text-align:center;padding:32px;color:var(--txt-3)">
          Nenhum animal encontrado. <a href="{{ route('animais.create') }}" style="color:var(--t-ink)">Cadastrar →</a>
        </td></tr>
        @endforelse
      </tbody>
    </table>
  </div>
  <div style="margin-top:16px">{{ $animais->links() }}</div>
</div>
@endsection
