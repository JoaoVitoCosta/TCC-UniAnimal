@extends('layouts.app')
@section('title','Recursos')

@section('content')
<div class="page-top">
  <div class="ctr">
    <div class="sec-head reveal">
      <p class="kicker">⭐ Por que UniAnimal</p>
      <h1 class="sec-title sec-title--xxl" id="dif-h">O melhor sistema de<br><em>monitoramento</em> animal</h1>
      <p class="sec-sub">Tecnologia de ponta combinada com experiência veterinária para garantir o bem-estar do seu animal.</p>
    </div>

    <div class="g2" style="margin-bottom:var(--s20)">
      <div style="position:relative;padding:var(--s5)" class="reveal">
        <div style="position:absolute;inset:-40px;background:radial-gradient(ellipse,rgba(0,229,168,.08) 0%,rgba(0,196,255,.05) 40%,transparent 70%);pointer-events:none" aria-hidden="true"></div>
        <img src="{{ asset('images/Animais/coleira1.jpg') }}" alt="Coleira UniAnimal" class="img-r" loading="lazy">
      </div>
      <div style="display:flex;flex-direction:column;gap:var(--s4)" class="reveal d2">
        @foreach([['🐾','Histórico 24h de saúde','Acesse o histórico completo de saúde do seu animal a qualquer hora: frequência cardíaca, temperatura, nível de atividade e localização.'],['🔒','Segurança de alto nível','Dados protegidos com criptografia nível bancário. Privacidade total garantida, com servidores redundantes e backups automáticos.'],['📈','Colaboração veterinária','Compartilhe relatórios com seu veterinário. Rede com +350 profissionais parceiros prontos para interpretar dados em tempo real.']] as [$i,$t,$d])
        <div class="feat-card">
          <div class="feat-card__icon" aria-hidden="true">{{ $i }}</div>
          <div><div class="feat-card__title">{{ $t }}</div><div class="feat-card__desc">{{ $d }}</div></div>
        </div>
        @endforeach
      </div>
    </div>

    <div class="g4" style="padding-top:var(--s12);border-top:1px solid var(--b)">
      @foreach([['99','%','Precisão GPS'],['7',' dias','Bateria'],['30','s','Tempo de alerta'],['24','/7','Suporte']] as [$n,$s,$l])
      <div class="sc reveal" style="flex-direction:column;align-items:center;text-align:center;padding:var(--s6)">
        <div class="stats-bar__num" data-count="{{ $n }}" data-suffix="{{ $s }}">{{ $n.$s }}</div>
        <div class="stats-bar__lbl" style="margin-top:var(--s2)">{{ $l }}</div>
      </div>
      @endforeach
    </div>
  </div>
</div>

<section class="sec sec-alt" aria-labelledby="feats-h">
  <div class="ctr">
    <div class="sec-head reveal">
      <h2 class="sec-title" id="feats-h">Tudo que você precisa<br>em <em>uma coleira</em></h2>
    </div>
    <div class="g3">
      @foreach([['📍','GPS em tempo real','Localização precisa a cada 10s. Alertas de cerca virtual configurável por você.'],['💓','Monitor cardíaco','Sensor biométrico detecta irregularidades antes que virem problemas sérios.'],['🌡️','Temperatura corporal','Alertas para febre, hipotermia ou calor excessivo.'],['🏃','Nível de atividade','Analise padrões de movimento e sono com inteligência artificial.'],['📱','App intuitivo','Interface limpa para iOS e Android. Tudo na palma da mão.'],['🔔','Alertas inteligentes','Notificações push personalizáveis para o que você quer monitorar.']] as [$i,$t,$d])
      <div class="feat-card reveal">
        <div class="feat-card__icon" aria-hidden="true">{{ $i }}</div>
        <div><div class="feat-card__title">{{ $t }}</div><div class="feat-card__desc">{{ $d }}</div></div>
      </div>
      @endforeach
    </div>
    <div style="text-align:center;margin-top:var(--s12)" class="reveal">
      <a href="{{ route('orcamento') }}" class="btn btn-p btn-lg">💬 Solicitar orçamento</a>
    </div>
  </div>
</section>
@endsection