@extends('layouts.app')
@section('title','Sobre')

@section('content')
<div class="page-top">
  <div class="ctr">
    <div class="g2">
      <div class="reveal">
        <p class="kicker">🏢 Nossa história</p>
        <h1 class="sec-title sec-title--xl">Sobre a <em>UniAnimal</em></h1>
        <p style="font-size:var(--tx-lg);color:var(--txt-2);line-height:1.72;margin-bottom:var(--s5);letter-spacing:-.008em">A UniAnimal disponibiliza uma <strong style="color:var(--txt)">coleira inteligente</strong> capaz de monitorar a saúde de animais de pequeno a grande porte, com localização em tempo real e alertas automáticos.</p>
        <p style="font-size:var(--tx-md);color:var(--txt-2);line-height:1.72;margin-bottom:var(--s8);letter-spacing:-.008em">Nascemos da paixão por animais e tecnologia. Acreditamos que cada tutor merece tranquilidade e informação completa sobre o bem-estar do seu companheiro.</p>
        <a href="{{ route('produtos') }}" class="btn btn-p">🛒 Conhecer produtos</a>
      </div>
      <div class="reveal d2" style="position:relative">
        <img src="{{ asset('images/Animais/dog-cat-together.jpg') }}" alt="Cachorro e gato" class="img-r" style="aspect-ratio:4/3" loading="lazy">
        <div style="position:absolute;bottom:-18px;right:-18px;background:var(--t-grad);border-radius:var(--r3);padding:var(--s4) var(--s6);text-align:center;box-shadow:var(--sh-t2)">
          <strong style="display:block;font-family:var(--f-display);font-size:1.75rem;font-weight:800;color:#090B10;line-height:1;letter-spacing:-.04em">2024</strong>
          <span style="font-size:var(--tx-xs);font-weight:700;text-transform:uppercase;letter-spacing:.07em;color:rgba(9,11,16,.65)">Fundação</span>
        </div>
      </div>
    </div>
  </div>
</div>

<section class="sec sec-alt" aria-labelledby="val-h">
  <div class="ctr">
    <div class="sec-head reveal">
      <h2 class="sec-title" id="val-h">Nossos <em>pilares</em></h2>
      <p class="sec-sub">O que nos move a criar a melhor tecnologia pet do mercado.</p>
    </div>
    <div class="val-grid">
      @foreach([['🎯','Missão','Democratizar o acesso à monitorização de saúde animal para todos os tutores e produtores rurais.'],['🔭','Visão','Ser a plataforma líder em saúde animal conectada na América Latina, integrando IA e IoT no cuidado pet.'],['💎','Valores','Bem-estar animal em primeiro lugar, inovação constante, transparência total e responsabilidade ambiental.']] as [$i,$t,$d])
      <div class="val-card reveal">
        <div class="val-card__icon" aria-hidden="true">{{ $i }}</div>
        <div class="val-card__title">{{ $t }}</div>
        <p class="val-card__text">{{ $d }}</p>
      </div>
      @endforeach
    </div>
  </div>
</section>

<section class="sec sec-solid" aria-labelledby="anim-h">
  <div class="ctr">
    <div class="sec-head reveal">
      <h2 class="sec-title" id="anim-h">Para todos os seus <em>animais</em></h2>
      <p class="sec-sub">Tecnologia validada para diversas espécies, com precisão e confiabilidade.</p>
    </div>
    <div class="img-gal reveal">
      @foreach([['cachorro1.webp','Cão'],['gato1.webp','Gato'],['cavalo.jpg','Cavalo'],['vaca.jpg','Bovino']] as [$img,$alt])
      <img src="{{ asset('images/Animais/'.$img) }}" alt="{{ $alt }}" class="img-gal-item" loading="lazy">
      @endforeach
    </div>
    <div style="text-align:center;margin-top:var(--s10)" class="reveal">
      <a href="{{ route('diferenciais') }}" class="btn btn-p btn-lg">Ver nossos diferenciais →</a>
    </div>
  </div>
</section>
@endsection