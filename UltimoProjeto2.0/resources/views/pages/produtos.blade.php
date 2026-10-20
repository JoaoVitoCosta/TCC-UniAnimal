@extends('layouts.app')
@section('title','Produtos')

@section('content')
<div class="page-top sec-solid">
  <div class="ctr">
    <div class="sec-head reveal">
      <p class="kicker">🛒 Catálogo</p>
      <h1 class="sec-title sec-title--xxl">Escolha o produto <em>ideal</em><br>para o seu pet</h1>
      <p class="sec-sub">Três modelos com a mesma tecnologia UniAnimal, projetados para cada tipo de animal.</p>
    </div>
    <div class="prod-grid">
      @foreach([
        ['Pequeno','UniCollar Mini','R$899','R$799','ou 12x de R$66,58','pequeno',false,['Pescoço 15–35cm','Até 15kg','7 dias · IP67','GPS + ECG + Temp']],
        ['Médio',  'UniCollar Pro', 'R$999','R$879','ou 12x de R$73,25','medio', true, ['Pescoço 30–55cm','15–50kg','10 dias · IP68','GPS + ECG + Sono + IA']],
        ['Grande', 'UniCollar Max', 'R$1.199','R$1.099','ou 12x de R$91,58','grande',false,['Pescoço 50–100cm','Acima 50kg','14 dias · IP69K','GPS Rural 5km + ECG']],
      ] as [$porte,$name,$was,$price,$inst,$slug,$feat,$feats])
      @php
        // Preço e parcela vêm da tabela produtos (mesma fonte da home).
        if ($p = ($produtos[$slug] ?? null)) { $price = $p->preco_formatado; $inst = $p->parcelamento; }
      @endphp
      <article class="prod-card {{ $feat ? 'prod-card--feat' : '' }} reveal {{ $feat ? '' : 'd1' }}" aria-label="{{ $name }}">
        @if($feat)<div class="prod-card__ribbon">⭐ Mais vendido</div>@endif
        <div class="prod-card__body">
          <div class="prod-card__tier">{{ $porte }} porte</div>
          <div class="prod-card__name">{{ $name }}</div>
          <div class="prod-card__was">De: {{ $was }}</div>
          <div class="prod-card__price">{{ $price }}</div>
          <div class="prod-card__inst">{{ $inst }}</div>
          <ul class="prod-card__feats">
            @foreach($feats as $f)
            <li class="prod-card__feat"><div class="prod-card__dot" aria-hidden="true"></div>{{ $f }}</li>
            @endforeach
          </ul>
          <a href="{{ route('orcamento') }}?porte={{ $slug }}" class="btn {{ $feat ? 'btn-p' : 'btn-g' }} btn-fw">Escolher produto</a>
        </div>
      </article>
      @endforeach
    </div>
    <div style="text-align:center;margin-top:var(--s8);padding:var(--s4) var(--s8);background:var(--t-dim);border:1px solid var(--bt);border-radius:var(--r4);color:var(--t-ink);font-size:var(--tx-sm);font-weight:500;display:flex;align-items:center;justify-content:center;gap:var(--s3)" class="reveal">
      📐 Regulagem de tamanho inclusa — adapta-se a diferentes pescoços dentro de cada categoria.
    </div>
  </div>
</div>

<section class="sec sec-alt" aria-labelledby="promo-h">
  <div class="ctr">
    <div class="g2">
      <div class="reveal">
        <p class="kicker">🔥 Oferta especial</p>
        <h2 class="sec-title" id="promo-h">Aproveite nossa<br><em>oferta exclusiva</em></h2>
        <p style="color:var(--txt-2);line-height:1.72;margin-bottom:var(--s8);letter-spacing:-.008em">Por tempo limitado, UniCollar Pro com desconto especial. Até 12x sem juros no cartão ou 19% off no Pix.</p>
        <a href="{{ route('oferta') }}" class="btn btn-p btn-lg">🛒 Ver oferta especial</a>
      </div>
      <div class="reveal d2">
        <img src="{{ asset('images/Animais/animais-domesticos.jpg') }}" alt="Animais domésticos" class="img-r" style="aspect-ratio:4/3" loading="lazy">
      </div>
    </div>
  </div>
</section>
@endsection