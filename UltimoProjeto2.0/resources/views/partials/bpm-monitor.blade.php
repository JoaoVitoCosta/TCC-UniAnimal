{{--
  Card de monitoramento de frequência cardíaca (resumo + gráfico).
  Os dados são carregados por public/js/batimentos.js.

  Parâmetros:
    $animal  (opcional) — animal fixo (tela de detalhes). Sem ele, o script
                          usa o <select id="bpm-animal"> da página (dashboard).

  data-ref = faixa de referência do animal (Animal::faixaReferencia()):
  {"chave","min","max"} ou null quando a espécie não tem referência.
--}}
<div class="iot-card bpm-card" id="bpm-monitor"
     data-url="{{ url('/api/dashboard/batimentos') }}"
     @isset($animal) data-animal-id="{{ $animal->id }}" data-ref='@json($animal->faixaReferencia())' @endisset>
  <div class="bpm-resumo">
    <div class="iot-vital"><div class="iot-vital__val" id="bpm-atual">—</div><div class="iot-vital__unit">bpm</div><div class="iot-vital__label">Última leitura</div><span class="badge bpm-faixa" id="bpm-faixa" hidden></span></div>
    <div class="iot-vital"><div class="iot-vital__val" id="bpm-media">—</div><div class="iot-vital__unit">bpm</div><div class="iot-vital__label">Média</div></div>
    <div class="iot-vital"><div class="iot-vital__val" id="bpm-min">—</div><div class="iot-vital__unit">bpm</div><div class="iot-vital__label">Mínima</div></div>
    <div class="iot-vital"><div class="iot-vital__val" id="bpm-max">—</div><div class="iot-vital__unit">bpm</div><div class="iot-vital__label">Máxima</div></div>
  </div>

  <div class="iot-chart-wrap bpm-chart">
    <canvas id="bpm-chart" aria-label="Gráfico de batimentos cardíacos"></canvas>
    <p class="bpm-vazio" id="bpm-vazio" hidden>Ainda não há leituras de batimento para este animal.</p>
  </div>

  <div class="iot-card__foot">
    <span class="bpm-info">Últimas 20 leituras da coleira · atualiza a cada 10 s</span>
    <span class="bpm-info" id="bpm-hora"></span>
  </div>
</div>
