@extends('layouts.app')
@section('title','Depoimentos')

@section('content')
<div class="page-top">
  <div class="ctr">
    <div class="sec-head reveal">
      <p class="kicker">💬 Avaliações reais</p>
      <h1 class="sec-title sec-title--xxl" id="dep-h">O que nossos clientes<br><em>dizem</em> sobre nós</h1>
      <p class="sec-sub">Histórias reais de tutores, criadores e veterinários que já confiam na UniAnimal.</p>
    </div>
    <div class="dep-grid">
      @forelse($depoimentos as $dep)
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
            <div class="dep-card__role">{{ $dep->created_at->format('d/m/Y') }}</div>
          </div>
        </div>
      </article>
      @empty
      <div style="grid-column:1/-1;text-align:center;padding:var(--s20);background:var(--bg-2);border:1px dashed var(--b2);border-radius:var(--r5)" class="reveal">
        <div style="font-size:3rem;margin-bottom:var(--s4)" aria-hidden="true">💬</div>
        <h2 style="font-family:var(--f-display);font-size:var(--tx-xl);font-weight:700;letter-spacing:-.025em;margin-bottom:var(--s2)">Seja o primeiro!</h2>
        <p style="color:var(--txt-2);margin-bottom:var(--s6);letter-spacing:-.005em">Ainda não há depoimentos. Compartilhe sua experiência.</p>
        <a href="{{ route('depoimentos.novo') }}" class="btn btn-p">✍️ Deixar depoimento</a>
      </div>
      @endforelse
    </div>
    @if($depoimentos->count())
    <div style="text-align:center;margin-top:var(--s12);padding-top:var(--s12);border-top:1px solid var(--b)" class="reveal">
      <p style="font-size:var(--tx-md);color:var(--txt-2);margin-bottom:var(--s5);letter-spacing:-.008em">Usou a UniAnimal? Conta sua experiência!</p>
      <a href="{{ route('depoimentos.novo') }}" class="btn btn-p btn-lg">✍️ Deixar meu depoimento</a>
    </div>
    @endif
  </div>
</div>
@endsection