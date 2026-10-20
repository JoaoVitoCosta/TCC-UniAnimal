/* ══════════════════════════════════════════════════════════════
   UNIANIMAL — Gráfico de frequência cardíaca (Chart.js)
   Usado pelo partial resources/views/partials/bpm-monitor.blade.php.
   - Dashboard: o animal vem do <select id="bpm-animal">.
   - Detalhes do animal: o animal vem de data-animal-id no card.
   ══════════════════════════════════════════════════════════════ */
(function () {
  const card = document.getElementById('bpm-monitor');
  if (!card || !window.Chart) return;

  const select = document.getElementById('bpm-animal');
  const el = id => document.getElementById(id);
  const css = nome => getComputedStyle(document.documentElement).getPropertyValue(nome).trim();
  let chart = null;

  const animalId = () => select ? select.value : card.dataset.animalId;

  // Faixa de referência do animal exibido ({chave, min, max} ou null).
  function refAtual() {
    const bruto = select ? select.selectedOptions[0]?.dataset.ref : card.dataset.ref;
    try { return JSON.parse(bruto || 'null'); } catch { return null; }
  }

  function cores() {
    return { linha: css('--err') || '#F04B58', texto: css('--txt-3') || '#888', grade: css('--b2') || 'rgba(128,128,128,.15)' };
  }

  function criarGrafico() {
    const c = cores();
    chart = new Chart(el('bpm-chart'), {
      type: 'line',
      data: { labels: [], datasets: [{
        label: 'Batimentos (bpm)',
        data: [],
        borderColor: c.linha,
        backgroundColor: c.linha + '22',
        fill: true,
        tension: 0.35,
        pointRadius: 3,
        pointHoverRadius: 6,
        borderWidth: 2,
      }]},
      options: {
        responsive: true,
        maintainAspectRatio: false,
        animation: { duration: 400 },
        interaction: { mode: 'index', intersect: false },
        plugins: {
          legend: { display: false },
          tooltip: { callbacks: { label: ctx => ` ${ctx.parsed.y} bpm` } },
        },
        scales: {
          x: { ticks: { color: c.texto, maxRotation: 0, autoSkip: true }, grid: { display: false } },
          y: { ticks: { color: c.texto }, grid: { color: c.grade }, suggestedMin: 50, suggestedMax: 150 },
        },
      },
    });
  }

  function aplicarTema() {
    if (!chart) return;
    const c = cores();
    chart.data.datasets[0].borderColor = c.linha;
    chart.data.datasets[0].backgroundColor = c.linha + '22';
    chart.options.scales.x.ticks.color = c.texto;
    chart.options.scales.y.ticks.color = c.texto;
    chart.options.scales.y.grid.color = c.grade;
    chart.update('none');
  }

  async function carregar() {
    try {
      const resp = await fetch(`${card.dataset.url}/${animalId()}`, { headers: { Accept: 'application/json' } });
      if (!resp.ok) return;
      const { batimentos } = await resp.json();
      const valores = batimentos.map(b => b.bpm).filter(v => v !== null);

      el('bpm-vazio').hidden = valores.length > 0;
      el('bpm-chart').style.visibility = valores.length ? 'visible' : 'hidden';

      chart.data.labels = batimentos.map(b => b.registrado_em
        ? new Date(b.registrado_em).toLocaleTimeString('pt-BR', { hour: '2-digit', minute: '2-digit' })
        : '');
      chart.data.datasets[0].data = batimentos.map(b => b.bpm);
      chart.update();

      el('bpm-atual').textContent = valores.length ? valores[valores.length - 1] : '—';
      el('bpm-media').textContent = valores.length ? Math.round(valores.reduce((a, b) => a + b, 0) / valores.length) : '—';
      el('bpm-min').textContent   = valores.length ? Math.min(...valores) : '—';
      el('bpm-max').textContent   = valores.length ? Math.max(...valores) : '—';
      el('bpm-hora').textContent  = 'Atualizado às ' + new Date().toLocaleTimeString('pt-BR');
      avaliarFaixa(valores.length ? valores[valores.length - 1] : null);
    } catch (e) {
      console.error('Erro ao carregar batimentos:', e);
    }
  }

  // Destaca na tabela de referência a linha que vale para o animal exibido
  // e diz se a última leitura está dentro da faixa normal dele.
  function avaliarFaixa(ultimo) {
    const ref = refAtual();
    document.querySelectorAll('.bpm-ref tr[data-ref]').forEach(tr => {
      tr.classList.toggle('bpm-ref__atual', tr.dataset.ref === ref?.chave);
    });

    const badge = el('bpm-faixa');
    if (!ref || ultimo === null) { badge.hidden = true; return; }

    // Mesmos 3 níveis da tabela "Orientação de batimentos" (Animal::faixaBatimento)
    const [classe, texto] = (ultimo < ref.emergencia_min || ultimo > ref.emergencia_max) ? ['err', 'Emergência']
                          : (ultimo < ref.min || ultimo > ref.max) ? ['warn', 'Alerta']
                          : ['ok', 'Normal'];
    badge.className = `badge badge--${classe} bpm-faixa`;
    badge.textContent = texto;
    badge.hidden = false;
  }

  criarGrafico();
  carregar();
  select?.addEventListener('change', carregar);
  setInterval(carregar, 10000);

  // Acompanha o botão de tema claro/escuro.
  new MutationObserver(aplicarTema).observe(document.documentElement, { attributes: true, attributeFilter: ['data-theme'] });
})();
