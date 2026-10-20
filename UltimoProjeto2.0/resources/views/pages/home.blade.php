@extends('layouts.app')
@section('title','Início')
@section('meta_desc','UniAnimal — A liberdade que eles amam, com a segurança que você precisa. GPS, saúde e bem-estar animal em tempo real.')

@section('content')

{{-- HERO --}}
<section class="hero" aria-label="Seção principal">
  <div class="hero__media">
    <video autoplay muted loop playsinline preload="auto" aria-hidden="true">
      <source src="{{ asset('videos/cachorrolambe.mp4') }}" type="video/mp4">
    </video>
  </div>
  <div class="hero__veil"  aria-hidden="true"></div>
  <div class="hero__fade"  aria-hidden="true"></div>

  <div class="hero__body">
    <div>
      <p class="kicker hero__kicker">Coleira inteligente UniAnimal</p>
      <h1 class="hero__title">A liberdade que eles amam, com a <em>segurança</em> que você precisa.</h1>
      <p class="hero__sub">Coleira inteligente com GPS, monitoramento de saúde 24h e alertas em tempo real.</p>
      <ul class="hero__features" aria-label="Destaques">
        @foreach(['GPS em tempo real','Monitor cardíaco ECG','App iOS & Android','Alertas inteligentes'] as $f)
        <li class="hero__feat"><span class="hero__feat-check" aria-hidden="true">✓</span>{{ $f }}</li>
        @endforeach
      </ul>
      <div class="hero__actions">
        <a href="{{ route('produtos') }}"  class="btn btn-p btn-lg">Comprar agora</a>
        <a href="{{ route('sobre') }}"     class="btn btn-g btn-lg">Saiba mais →</a>
      </div>
    </div>

    <div class="monitor reveal" aria-label="Monitor de saúde em tempo real">
      <div class="monitor__head">
        <div class="monitor__ava" aria-hidden="true">🐕</div>
        <div>
          <div class="monitor__name">Thor</div>
          <div class="monitor__breed">Pastor Alemão · 6 anos</div>
        </div>
        <div style="margin-left:auto"><span class="chip chip-ok">Normal</span></div>
      </div>
      <div class="monitor__grid">
        @foreach([['❤️','78 bpm','Batimento'],['🌡️','38.2°C','Temperatura'],['📍','Casa','Localização'],['🔋','87%','Bateria']] as [$i,$v,$k])
        <div class="monitor__cell">
          <div class="monitor__icon" aria-hidden="true">{{ $i }}</div>
          <div class="monitor__val">{{ $v }}</div>
          <div class="monitor__label">{{ $k }}</div>
        </div>
        @endforeach
      </div>
      <div class="monitor__ecg" aria-label="ECG em tempo real">
        <svg viewBox="0 0 280 34" fill="none">
          <polyline fill="none" stroke="#00E5A8" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"
            points="0,18 24,18 32,4 38,30 44,18 58,18 66,7 72,28 78,18 100,18 108,3 114,32 120,18 142,18 150,6 156,30 162,18 182,18 190,4 196,31 202,18 222,18 230,5 236,31 242,18 262,18 270,8 276,32 280,18"/>
        </svg>
      </div>
      <div class="monitor__status">
        <span class="dot-live" aria-hidden="true"></span>
        ECG Live · Atualizado agora
      </div>
    </div>
  </div>

  <div class="hero__scroll" aria-hidden="true">
    <span>scroll</span>
    <div class="hero__scroll-bar"></div>
  </div>
</section>

{{-- STATS --}}
<div class="stats-bar">
  <div class="stats-bar__row">
    @foreach([['5000','+','Animais monitorados'],['99','%','Uptime'],['24','h','Monitorização'],['350','+','Vets parceiros']] as [$n,$s,$l])
    <div class="stats-bar__cell">
      <div class="stats-bar__num" data-count="{{ $n }}" data-suffix="{{ $s }}">{{ $n.$s }}</div>
      <div class="stats-bar__lbl">{{ $l }}</div>
    </div>
    @endforeach
  </div>
</div>

{{-- DADOS DO PET --}}
<section class="photo-sec sec" aria-labelledby="dados-h">
  <div class="photo-sec__bg" aria-hidden="true">
    <img src="{{ asset('images/Animais/pug.jpg') }}" alt="" loading="lazy">
  </div>
  <div class="photo-sec__veil" aria-hidden="true"></div>
  <div class="photo-sec__body">
    <div class="ctr">
      <div class="g2">
        <div class="reveal">
          <p class="kicker">📊 Painel completo</p>
          <h2 class="sec-title sec-title--w" id="dados-h">Dados do seu pet,<br>sempre na <em>palma da mão.</em></h2>
          <p class="sec-sub" style="margin-bottom:var(--s8)">Dashboard completo com histórico, tendências e alertas automáticos para qualquer anomalia detectada.</p>
          <div style="display:flex;flex-direction:column;gap:var(--s3)">
            @foreach([
              ['🔔','Alertas em tempo real','Notificação imediata para qualquer irregularidade detectada.'],
              ['📅','Histórico de 30 dias','Gráficos de evolução disponíveis para compartilhar com o veterinário.'],
              ['📍','Zona segura','Defina uma área e receba alerta se o animal sair do perímetro.'],
            ] as [$i,$t,$d])
            <div class="feat-card">
              <div class="feat-card__icon" aria-hidden="true">{{ $i }}</div>
              <div>
                <div class="feat-card__title">{{ $t }}</div>
                <div class="feat-card__desc">{{ $d }}</div>
              </div>
            </div>
            @endforeach
          </div>
        </div>
        <div class="reveal d2"></div>
      </div>
    </div>
  </div>
</section>

{{-- PRODUTOS --}}
<section class="photo-sec sec" aria-labelledby="prod-h">
  <div class="photo-sec__bg" aria-hidden="true">
    <img src="{{ asset('images/Animais/cachorros-pequenos-.jpg') }}" alt="" loading="lazy">
  </div>
  <div class="photo-sec__veil" style="background:linear-gradient(to bottom,rgba(9,11,16,.96) 0%,rgba(9,11,16,.82) 100%)" aria-hidden="true"></div>
  <div class="photo-sec__body">
    <div class="ctr">
      <div class="sec-head reveal" style="margin-bottom:var(--s12)">
        <h2 class="sec-title sec-title--w" id="prod-h">Coleira para <em>todos os tamanhos.</em></h2>
        <p class="sec-sub">Pequeno, médio ou grande porte — tecnologia que se adapta ao seu animal.</p>
      </div>
      <div class="prod-grid">
        @foreach([
          ['Pequeno','UniCollar Mini','R$899','R$799','ou 12x de R$66,58','pequeno',false,'GPS tempo real|Monitor ECG|App iOS & Android|Alertas de saúde'],
          ['Médio',  'UniCollar Pro', 'R$999','R$879','ou 12x de R$73,25','medio', true, 'GPS tempo real|Monitor ECG|App iOS & Android|Alertas de saúde'],
          ['Grande', 'UniCollar Max', 'R$1.199','R$1.099','ou 12x de R$91,58','grande',false,'GPS longo alcance|Monitor ECG|App iOS & Android|Alertas de saúde'],
        ] as [$porte,$name,$was,$price,$inst,$slug,$feat,$feats])
        @php
          // Preço e parcela vêm da tabela produtos (mesma fonte da página Produtos).
          if ($p = ($produtos[$slug] ?? null)) { $price = $p->preco_formatado; $inst = $p->parcelamento; }
        @endphp
        <article class="prod-card {{ $feat ? 'prod-card--feat' : '' }} reveal {{ $feat ? '' : 'd1' }}">
          @if($feat)<div class="prod-card__ribbon">⭐ Mais vendido</div>@endif
          <div class="prod-card__body">
            <div class="prod-card__tier">{{ $porte }} porte</div>
            <div class="prod-card__name">{{ $name }}</div>
            <div class="prod-card__was">De: {{ $was }}</div>
            <div class="prod-card__price" aria-label="Por {{ $price }}">{{ $price }}</div>
            <div class="prod-card__inst">{{ $inst }}</div>
            <ul class="prod-card__feats">
              @foreach(explode('|',$feats) as $f)
              <li class="prod-card__feat"><div class="prod-card__dot" aria-hidden="true"></div>{{ $f }}</li>
              @endforeach
            </ul>
            <a href="{{ route('orcamento') }}?porte={{ $slug }}" class="btn {{ $feat ? 'btn-p' : 'btn-g' }} btn-fw">Escolher produto</a>
          </div>
        </article>
        @endforeach
      </div>
    </div>
  </div>
</section>

{{-- DEPOIMENTOS --}}
<section class="sec sec-solid" aria-labelledby="dep-h">
  <div class="ctr">
    <div class="sec-head reveal">
      <p class="kicker">💬 Avaliações reais</p>
      <h2 class="sec-title" id="dep-h">O que nossos clientes <em>dizem</em></h2>
      <p class="sec-sub">Tutores e criadores que já confiam na UniAnimal todos os dias.</p>
    </div>

    @if($depoimentos->count())
    <div class="dep-grid" style="margin-bottom:var(--s12)">
      @foreach($depoimentos->take(3) as $dep)
      <article class="dep-card reveal">
        <div class="dep-card__stars" aria-label="{{ $dep->estrelas }} de 5 estrelas">
          @for($i=1;$i<=5;$i++)<span class="dep-card__star" aria-hidden="true">{{ $i<=$dep->estrelas?'★':'☆' }}</span>@endfor
        </div>
        <blockquote><p class="dep-card__text">"{{ $dep->depoimento }}"</p></blockquote>
        <div class="dep-card__sep"></div>
        <div class="dep-card__author">
          <div class="dep-card__ava" aria-hidden="true">{{ mb_strtoupper(mb_substr($dep->nome,0,1)) }}</div>
          <div>
            <div class="dep-card__name">{{ $dep->nome }}</div>
            <div class="dep-card__role">Tutor UniAnimal</div>
          </div>
        </div>
      </article>
      @endforeach
    </div>
    @endif

    {{-- FORM INLINE --}}
    @if($errors->any())<div class="form-errors" role="alert">@foreach($errors->all() as $e)<p>• {{ $e }}</p>@endforeach</div>@endif
    <div class="form-sheet reveal" style="max-width:640px;margin:0 auto">
      <div style="margin-bottom:var(--s6)">
        <p class="kicker">✍️ Compartilhe</p>
        <h3 class="sec-title" style="font-size:var(--tx-2xl);margin-top:var(--s2)">Deixe seu <em>depoimento</em></h3>
      </div>
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
          <label for="dep-txt" class="form-label">Depoimento *</label>
          <textarea id="dep-txt" name="depoimento" class="form-input {{ $errors->has('depoimento') ? 'form-input--err' : '' }}" placeholder="Conte sua experiência com a coleira UniAnimal..." required>{{ old('depoimento') }}</textarea>
          @error('depoimento')<p class="form-err">{{ $message }}</p>@enderror
        </div>
        <button type="submit" class="btn btn-p btn-fw btn-lg">🚀 Enviar depoimento</button>
      </form>
    </div>
  </div>
</section>

{{-- CONTATO --}}
<section class="photo-sec sec" id="contato" aria-labelledby="ct-h">
  <div class="photo-sec__bg" aria-hidden="true">
    <img src="{{ asset('images/Animais/Pug_triste.jpg') }}" alt="" loading="lazy">
  </div>
  <div class="photo-sec__veil" aria-hidden="true"></div>
  <div class="photo-sec__body">
    <div class="ctr">
      <div class="g2">
        <div class="reveal">
          <p class="kicker">📞 Atendimento</p>
          <h2 class="sec-title sec-title--w" id="ct-h">Entre em contato <em>conosco.</em></h2>
          <p class="sec-sub" style="margin-bottom:var(--s8)">Nossa equipe responde rapidamente por qualquer canal.</p>
          <a href="tel:+5519989393961"        class="ci"><div class="ci__icon" aria-hidden="true">📞</div><div><div class="ci__label">Telefone</div><div class="ci__val">(19) 98939-3961</div></div></a>
          <a href="mailto:unianimal@gmail.com" class="ci"><div class="ci__icon" aria-hidden="true">✉️</div><div><div class="ci__label">E-mail</div><div class="ci__val">unianimal@gmail.com</div></div></a>
          <div class="ci"><div class="ci__icon" aria-hidden="true">📍</div><div><div class="ci__label">Localização</div><div class="ci__val">Tambaú, São Paulo — Brasil</div></div></div>
        </div>
        <div class="reveal d2">
          <div class="form-sheet">
            <h3 style="font-family:var(--f-display);font-size:var(--tx-xl);font-weight:700;letter-spacing:-.025em;margin-bottom:var(--s2)">Faça seu orçamento</h3>
            <p style="font-size:var(--tx-sm);color:var(--txt-2);margin-bottom:var(--s5)">Resposta em minutos pelo WhatsApp.</p>
            <a href="https://wa.me/5519989393961" class="btn btn-w btn-fw btn-lg" target="_blank" rel="noopener"><x-icone-marca nome="whatsapp" cor="escuro" :tamanho="22" />Falar no WhatsApp</a>
            <div style="text-align:center;font-size:var(--tx-xs);color:var(--txt-4);margin:var(--s4) 0">ou</div>
            <a href="{{ route('contactos') }}" class="btn btn-g btn-fw">Ver formulário de contato →</a>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>

@endsection