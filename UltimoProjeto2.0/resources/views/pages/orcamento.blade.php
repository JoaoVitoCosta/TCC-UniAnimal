@extends('layouts.app')
@section('title','Orçamento Atacado')

@section('content')
<div class="page-top-sm">
  <div class="ctr">
    <div style="max-width:720px;margin:0 auto">

      {{-- HEADER PREMIUM --}}
      <div style="text-align:center;margin-bottom:var(--s8)" class="reveal">
        <div style="display:inline-flex;align-items:center;gap:8px;background:rgba(0,229,168,.08);border:1px solid rgba(0,229,168,.2);border-radius:var(--rf);padding:6px 16px;margin-bottom:var(--s5)">
          <span class="dot-live" aria-hidden="true"></span>
          <span style="font-size:.75rem;font-weight:600;color:var(--t-ink);letter-spacing:.08em;text-transform:uppercase">WhatsApp direto</span>
        </div>
        <p class="kicker" style="margin-left:auto;margin-right:auto">💬 Orçamento Exclusivo</p>
        <h1 class="sec-title sec-title--xl" style="margin-top:var(--s3)">Compras em <em>grande quantidade</em></h1>
        <p class="sec-sub" style="margin:var(--s3) auto 0">Atendimento personalizado para distribuidores, pet shops e clínicas veterinárias.</p>
      </div>

      {{-- AVISO ATACADO --}}
      <div class="reveal" style="background:linear-gradient(135deg,rgba(0,229,168,.06),rgba(0,196,255,.04));border:1px solid rgba(0,229,168,.2);border-radius:var(--r4);padding:var(--s6);margin-bottom:var(--s6);display:flex;gap:16px;align-items:flex-start">
        <div style="font-size:1.8rem;flex-shrink:0">📦</div>
        <div>
          <h3 style="font-size:1rem;font-weight:700;color:var(--t-ink);margin-bottom:4px">Orçamento exclusivo para compras em atacado</h3>
          <p style="font-size:.85rem;color:var(--txt-2);line-height:1.6">Este formulário é destinado <strong>exclusivamente para compras em lote</strong> — pet shops, distribuidores, clínicas e criadores. Para compras individuais, acesse nossa <a href="{{ route('produtos') }}" style="color:var(--t-ink)">loja online →</a></p>
          <div style="display:flex;flex-wrap:wrap;gap:8px;margin-top:12px">
            @foreach(['✅ Preços especiais','✅ Frete negociado','✅ Suporte dedicado','✅ Personalização'] as $b)
            <span style="background:var(--t-dim2);border:1px solid var(--bt);border-radius:var(--rf);padding:3px 12px;font-size:.72rem;color:var(--t-ink);font-weight:600">{{ $b }}</span>
            @endforeach
          </div>
        </div>
      </div>

      {{-- FORMULÁRIO --}}
      <div class="form-sheet reveal">
        <div style="display:flex;align-items:center;gap:10px;margin-bottom:var(--s6);padding-bottom:var(--s4);border-bottom:1px solid var(--b)">
          <div style="width:36px;height:36px;background:#25D366;border-radius:var(--r2);display:flex;align-items:center;justify-content:center;font-size:1.2rem">💬</div>
          <div>
            <div style="font-weight:700;font-size:.95rem">Solicitar via WhatsApp</div>
            <div style="font-size:.72rem;color:var(--txt-3)">Resposta em até 2 horas úteis</div>
          </div>
        </div>

        <div id="orcamento-form">

          {{-- LINHA 1 --}}
          <div class="form-2col">
            <div class="form-group">
              <label for="nome" class="form-label">Nome do responsável *</label>
              <input type="text" id="nome" class="form-input" placeholder="Seu nome completo" required>
              <p class="form-err" id="err-nome" style="display:none">Informe seu nome.</p>
            </div>
            <div class="form-group">
              <label for="telefone" class="form-label">Telefone / WhatsApp *</label>
              <input type="tel" id="telefone" class="form-input phone-mask" placeholder="(00) 00000-0000" required>
              <p class="form-err" id="err-tel" style="display:none">Informe o telefone.</p>
            </div>
          </div>

          {{-- LINHA 2 --}}
          <div class="form-2col">
            <div class="form-group">
              <label for="pet" class="form-label">Nome / tipo do pet *</label>
              <input type="text" id="pet" class="form-input" placeholder="Ex: cães, gatos, cavalos..." required>
              <p class="form-err" id="err-pet" style="display:none">Informe o pet.</p>
            </div>
            <div class="form-group">
              <label for="porte" class="form-label">Porte do animal *</label>
              <select id="porte" class="form-select" required>
                <option value="">Selecione o porte</option>
                <option value="Pequeno (até 15kg)">🐱 Pequeno — até 15kg · UniCollar Mini</option>
                <option value="Médio (15–50kg)">🐕 Médio — 15–50kg · UniCollar Pro</option>
                <option value="Grande (acima 50kg)">🐴 Grande — acima 50kg · UniCollar Max</option>
              </select>
              <p class="form-err" id="err-porte" style="display:none">Selecione o porte.</p>
            </div>
          </div>

          {{-- LINHA 3 --}}
          <div class="form-2col">
            <div class="form-group">
              <label for="servico" class="form-label">Serviço desejado *</label>
              <select id="servico" class="form-select" required>
                <option value="">Selecione o serviço</option>
                <option value="Coleira UniAnimal + GPS">📍 Coleira UniAnimal + GPS</option>
                <option value="Monitoramento Cardíaco ECG">❤️ Monitoramento Cardíaco ECG</option>
                <option value="Plano SaaS Veterinário">🖥️ Plano SaaS Veterinário</option>
                <option value="Pacote Completo IoT">🌐 Pacote Completo IoT</option>
                <option value="Suporte e Manutenção">🔧 Suporte e Manutenção</option>
              </select>
              <p class="form-err" id="err-servico" style="display:none">Selecione o serviço.</p>
            </div>
            <div class="form-group">
              <label for="quantidade" class="form-label">Quantidade pretendida *</label>
              <input type="number" id="quantidade" class="form-input" placeholder="Ex: 50" min="5" required>
              <p style="font-size:.7rem;color:var(--txt-3);margin-top:4px">Mínimo 5 unidades para orçamento atacado</p>
              <p class="form-err" id="err-qtd" style="display:none">Informe a quantidade (mín. 5).</p>
            </div>
          </div>

          {{-- OBSERVAÇÕES --}}
          <div class="form-group">
            <label for="obs" class="form-label">Observações adicionais</label>
            <textarea id="obs" class="form-input" rows="4" placeholder="Descreva suas necessidades, prazo, condições de entrega, negociação especial..."></textarea>
          </div>

          {{-- BOTÃO --}}
          <button type="button" id="btn-whatsapp" class="btn btn-fw btn-lg" style="background:linear-gradient(135deg,#25D366,#128C7E);color:#fff;border:none;font-weight:700;font-size:1rem;gap:10px;position:relative;overflow:hidden">
            <span id="btn-text" style="display:flex;align-items:center;gap:10px;justify-content:center">
              <svg width="22" height="22" viewBox="0 0 24 24" fill="currentColor"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/></svg>
              Solicitar Orçamento via WhatsApp
            </span>
            <span id="btn-loading" style="display:none">⏳ Gerando mensagem...</span>
          </button>

          <p style="text-align:center;font-size:.72rem;color:var(--txt-3);margin-top:var(--s3)">
            🔒 Seus dados são usados apenas para o atendimento. Não compartilhamos com terceiros.
          </p>
        </div>
      </div>

    </div>
  </div>
</div>
@endsection

@push('scripts')
<script>
// Máscara de telefone
document.querySelector('.phone-mask')?.addEventListener('input', function() {
  let v = this.value.replace(/\D/g,'').slice(0,11);
  if(v.length > 6) v = '('+v.slice(0,2)+') '+v.slice(2,7)+'-'+v.slice(7);
  else if(v.length > 2) v = '('+v.slice(0,2)+') '+v.slice(2);
  this.value = v;
});

// Envio via WhatsApp
document.getElementById('btn-whatsapp').addEventListener('click', function() {
  const get = id => document.getElementById(id)?.value?.trim() || '';

  const nome      = get('nome');
  const telefone  = get('telefone');
  const pet       = get('pet');
  const porte     = get('porte');
  const servico   = get('servico');
  const quantidade= get('quantidade');
  const obs       = get('obs') || 'Nenhuma';

  // Validações
  let ok = true;
  const show = (id, cond) => {
    const el = document.getElementById(id);
    if(el) el.style.display = cond ? 'block' : 'none';
    if(cond) ok = false;
  };

  show('err-nome',   !nome);
  show('err-tel',    !telefone);
  show('err-pet',    !pet);
  show('err-porte',  !porte);
  show('err-servico',!servico);
  show('err-qtd',    !quantidade || Number(quantidade) < 5);

  if(!ok) return;

  // Loading
  document.getElementById('btn-text').style.display = 'none';
  document.getElementById('btn-loading').style.display = 'flex';
  this.disabled = true;

  // Monta mensagem formatada
  const msg = `Olá! Gostaria de solicitar um *orçamento para compra em grande quantidade* — UniAnimal.

👤 *Cliente:* ${nome}
🐾 *Pet / Animal:* ${pet}
📏 *Porte:* ${porte}
🩺 *Serviço desejado:* ${servico}
📦 *Quantidade desejada:* ${quantidade} unidades
📱 *Meu telefone:* ${telefone}
📝 *Observações:* ${obs}

Aguardo retorno. Obrigado! 🐾`;

  const numero = '5519989393961';
  const url = `https://wa.me/${numero}?text=${encodeURIComponent(msg)}`;

  setTimeout(() => {
    window.open(url, '_blank');
    document.getElementById('btn-text').style.display = 'flex';
    document.getElementById('btn-loading').style.display = 'none';
    this.disabled = false;
    this.style.background = 'linear-gradient(135deg,#00E5A8,#00C4FF)';
    this.querySelector('#btn-text').innerHTML = '✅ WhatsApp aberto! Aguardamos sua mensagem.';
  }, 1200);
});
</script>
@endpush