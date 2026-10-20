<?php $__env->startSection('title', 'Localização — ' . $animal->nome); ?>
<?php $__env->startSection('page_title'); ?><span class="ico" aria-hidden="true">📍</span>Localização de <?php echo e($animal->nome); ?><?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
<?php $c = $animal->coleira; ?>

<?php if(! $c): ?>
  <div class="empty-state">
    <p class="empty-state__ico">📡</p>
    <p>Este animal ainda não possui uma coleira cadastrada.</p>
    <a href="<?php echo e(route('animais.edit', $animal)); ?>" class="btn btn-p btn-sm">Cadastrar coleira</a>
  </div>
<?php else: ?>

<div class="cards-grid">
  <div class="stat-card">
    <span class="stat-card__ico">🔋</span>
    <div class="stat-card__value"><?php echo e($c->bateria_percentual); ?>%</div>
    <div class="stat-card__label">Bateria (<?php echo e($c->bateria_status_label); ?>)</div>
  </div>
  <div class="stat-card">
    <span class="stat-card__ico">📡</span>
    <div class="stat-card__value"><?php echo e($c->status_label); ?></div>
    <div class="stat-card__label">Status da Coleira</div>
  </div>
  <div class="stat-card">
    <span class="stat-card__ico">🎯</span>
    <div class="stat-card__value"><?php echo e($c->precisao ? $c->precisao.'m' : '—'); ?></div>
    <div class="stat-card__label">Precisão do GPS</div>
  </div>
  <div class="stat-card">
    <span class="stat-card__ico">🕐</span>
    <div class="stat-card__value" style="font-size:1rem"><?php echo e(ucfirst($c->localizacao_atualizada_em?->diffForHumans() ?? '—')); ?></div>
    <div class="stat-card__label">Última atualização</div>
  </div>
</div>

<section class="dp-section">
  <div class="dp-section__head">
    <h2>Última posição conhecida</h2>
    <button id="btn-simular" class="btn btn-g btn-sm" data-id="<?php echo e($animal->id); ?>"><span class="ico" aria-hidden="true">📡</span>Simular nova posição</button>
  </div>

  <?php if($c->latitude && $c->longitude): ?>
    <div class="panel" style="padding:12px">
      <p style="font-size:.85rem;color:var(--txt-2)">
        Latitude: <strong><?php echo e($c->latitude); ?></strong> &nbsp;·&nbsp;
        Longitude: <strong><?php echo e($c->longitude); ?></strong>
      </p>
      <a href="https://www.google.com/maps?q=<?php echo e($c->latitude); ?>,<?php echo e($c->longitude); ?>" target="_blank" rel="noopener" class="btn btn-p btn-sm" style="margin-top:10px">
        Abrir no mapa →
      </a>
    </div>
  <?php else: ?>
    <p style="color:var(--txt-3)">Nenhuma posição registrada ainda.</p>
  <?php endif; ?>
</section>


<?php
  // localizacoes já vem da mais recente para a mais antiga: pega as 10
  // últimas e inverte, para a rota ser desenhada na ordem do percurso.
  $rota = $c->localizacoes->take(10)->reverse()->values()->map(fn ($l) => [
    'lat'  => (float) $l->latitude,
    'lng'  => (float) $l->longitude,
    'hora' => $l->registrado_em?->format('d/m H:i'),
  ]);
?>
<section class="dp-section">
  <div class="dp-section__head"><h2><span class="ico" aria-hidden="true">🗺️</span>Rota das últimas <?php echo e($rota->count()); ?> posições</h2></div>
  <div class="iot-card">
    <?php if($rota->count()): ?>
      <div id="mapa-rota" class="mapa-rota" data-rota='<?php echo json_encode($rota, 15, 512) ?>' role="img" aria-label="Mapa com a rota das últimas posições da coleira"></div>
      <div class="mapa-legenda">
        <span><i class="mapa-legenda__ponto mapa-legenda__ponto--inicio"></i> Início (<?php echo e($rota->first()['hora']); ?>)</span>
        <span><i class="mapa-legenda__ponto"></i> Pontos intermediários</span>
        <span><i class="mapa-legenda__ponto mapa-legenda__ponto--atual"></i> Posição atual (<?php echo e($rota->last()['hora']); ?>)</span>
      </div>
    <?php else: ?>
      <p style="color:var(--txt-3)">Ainda não há posições para desenhar a rota.</p>
    <?php endif; ?>
  </div>
</section>


<section class="dp-section">
  <div class="dp-section__head"><h2><span class="ico" aria-hidden="true">❤️</span>Histórico de batimentos</h2></div>
  <?php echo $__env->make('partials.bpm-monitor', ['animal' => $animal], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
</section>


<section class="dp-section">
  <div class="dp-section__head"><h2><span class="ico" aria-hidden="true">📋</span>Referência dos dados</h2></div>

  <?php echo $__env->make('partials.bpm-referencia', ['refAtual' => $animal->faixaReferencia()['chave'] ?? null], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

  <div class="iot-card bpm-ref">
    <h3 class="bpm-ref__title"><span class="ico" aria-hidden="true">🔋</span>Bateria e precisão do GPS</h3>
    <div class="bpm-ref__scroll">
      <table class="data-table">
        <thead><tr><th>Dado</th><th>Bom</th><th>Atenção</th><th>Crítico</th></tr></thead>
        <tbody>
          <tr>
            <td><strong>Bateria da coleira</strong></td>
            <td><span class="badge badge--ok">Normal: acima de 35%</span></td>
            <td><span class="badge badge--warn">Baixa: 16% a 35%</span></td>
            <td><span class="badge badge--err">Crítica: 15% ou menos</span></td>
          </tr>
          <tr>
            <td><strong>Precisão do GPS</strong></td>
            <td><span class="badge badge--ok">Até 5 m</span></td>
            <td><span class="badge badge--warn">6 a 15 m</span></td>
            <td><span class="badge badge--err">Acima de 15 m</span></td>
          </tr>
        </tbody>
      </table>
    </div>
    <p class="bpm-info bpm-ref__nota">
      A precisão indica o raio de erro da posição: quanto menor, mais exata. Em locais fechados ou com muitos prédios o GPS
      tende a ficar menos preciso. Com bateria baixa ou crítica, recarregue a coleira para não perder o monitoramento.
    </p>
  </div>
</section>

<section class="dp-section">
  <div class="dp-section__head"><h2>Histórico de localização</h2></div>
  <div class="panel">
    <div style="overflow-x:auto">
      <table class="data-table">
        <thead><tr><th>Data/Hora</th><th>Latitude</th><th>Longitude</th><th>Precisão</th></tr></thead>
        <tbody>
          <?php $__empty_1 = true; $__currentLoopData = $animal->coleira->localizacoes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $loc): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
          <tr>
            <td style="color:var(--txt-3);font-size:.78rem"><?php echo e($loc->registrado_em->format('d/m/Y H:i')); ?></td>
            <td><?php echo e($loc->latitude); ?></td>
            <td><?php echo e($loc->longitude); ?></td>
            <td><?php echo e($loc->precisao ? $loc->precisao.' m' : '—'); ?></td>
          </tr>
          <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
          <tr><td colspan="4" style="text-align:center;color:var(--txt-3);padding:20px">Sem histórico ainda.</td></tr>
          <?php endif; ?>
        </tbody>
      </table>
    </div>
  </div>
</section>

<?php endif; ?>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('head'); ?>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/leaflet/1.9.4/leaflet.min.css">
<?php $__env->stopPush(); ?>

<?php $__env->startPush('scripts'); ?>
<script src="https://cdnjs.cloudflare.com/ajax/libs/leaflet/1.9.4/leaflet.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/4.4.1/chart.umd.min.js"></script>
<script src="<?php echo e(asset('js/batimentos.js')); ?>"></script>
<script>
// Mapa com a rota das últimas posições da coleira (Leaflet + OpenStreetMap).
(function () {
  const div = document.getElementById('mapa-rota');
  if (!div || !window.L) return;

  const rota = JSON.parse(div.dataset.rota || '[]');
  const pontos = rota.map(p => [p.lat, p.lng]);
  const css = nome => getComputedStyle(document.documentElement).getPropertyValue(nome).trim();

  const mapa = L.map(div, { scrollWheelZoom: false });
  L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
    maxZoom: 19,
    attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a>',
  }).addTo(mapa);

  // Caminho percorrido
  L.polyline(pontos, { color: css('--t') || '#00E5A8', weight: 4, opacity: .85 }).addTo(mapa);

  // Um marcador por posição: início (verde), intermediários e posição atual (destacada)
  rota.forEach((p, i) => {
    const inicio = i === 0;
    const atual  = i === rota.length - 1;
    const cor = atual ? (css('--err') || '#F04B58') : inicio ? (css('--ok') || '#10D078') : (css('--t2') || '#00C4FF');

    L.circleMarker([p.lat, p.lng], {
      radius: atual ? 9 : inicio ? 7 : 5,
      color: '#fff', weight: 2, fillColor: cor, fillOpacity: 1,
    })
      .bindTooltip(`${i + 1}º ponto · ${p.hora}` + (atual ? ' — posição atual' : inicio ? ' — início' : ''))
      .addTo(mapa);
  });

  if (pontos.length === 1) mapa.setView(pontos[0], 17);
  else mapa.fitBounds(pontos, { padding: [30, 30] });
})();

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
<?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.dashboard', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\44648743806\Desktop\UltimoProjeto2.0\resources\views/pages/coleira/show.blade.php ENDPATH**/ ?>