<?php $__env->startSection('title', 'Dashboard'); ?>
<?php $__env->startSection('page_title', 'Minha Dashboard'); ?>

<?php $__env->startSection('content'); ?>

<div class="cards-grid">
  <div class="stat-card">
    <span class="stat-card__ico">🐾</span>
    <div class="stat-card__value"><?php echo e($stats['total_animais']); ?></div>
    <div class="stat-card__label">Meus Animais</div>
  </div>
  <div class="stat-card">
    <span class="stat-card__ico">📡</span>
    <div class="stat-card__value"><?php echo e($stats['coleiras_ativas']); ?></div>
    <div class="stat-card__label">Coleiras Ativas</div>
  </div>
  <div class="stat-card">
    <span class="stat-card__ico">🔋</span>
    <div class="stat-card__value"><?php echo e($stats['bateria_media'] ? round($stats['bateria_media']) . '%' : '—'); ?></div>
    <div class="stat-card__label">Bateria Média</div>
  </div>
  <div class="stat-card <?php echo e($stats['alertas_ativos'] > 0 ? 'stat-card--alert' : ''); ?>">
    <span class="stat-card__ico">⚠️</span>
    <div class="stat-card__value"><?php echo e($stats['alertas_ativos']); ?></div>
    <div class="stat-card__label">Alertas Ativos</div>
  </div>
</div>

<?php if($animais->isNotEmpty()): ?>
<section class="dp-section">
  <div class="dp-section__head">
    <h2><span class="ico" aria-hidden="true">❤️</span>Frequência Cardíaca</h2>
    <select id="bpm-animal" class="bpm-select" aria-label="Escolher animal">
      <?php $__currentLoopData = $animais; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $animal): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <option value="<?php echo e($animal->id); ?>" data-ref='<?php echo json_encode($animal->faixaReferencia(), 15, 512) ?>'><?php echo e($animal->especie_emoji); ?> <?php echo e($animal->nome); ?></option>
      <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </select>
  </div>

  
  <?php echo $__env->make('partials.bpm-monitor', ['animal' => null], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
  <?php echo $__env->make('partials.bpm-referencia', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
</section>
<?php endif; ?>

<section class="dp-section">
  <div class="dp-section__head">
    <h2>Meus Animais</h2>
    <a href="<?php echo e(route('animais.create')); ?>" class="btn btn-p btn-sm"><span class="ico" aria-hidden="true">➕</span>Novo Animal</a>
  </div>

  <?php if($animais->isEmpty()): ?>
    <div class="empty-state">
      <p class="empty-state__ico">🐾</p>
      <p>Você ainda não cadastrou nenhum animal.</p>
      <a href="<?php echo e(route('animais.create')); ?>" class="btn btn-p btn-sm">Cadastrar meu primeiro animal</a>
    </div>
  <?php else: ?>
    <div class="animais-grid">
      <?php $__currentLoopData = $animais; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $animal): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <a href="<?php echo e(route('animais.show', $animal)); ?>" class="animal-card">
          <div class="animal-card__head">
            <span class="animal-card__emoji"><?php echo e($animal->especie_emoji); ?></span>
            <span class="badge badge--<?php echo e($animal->status_color); ?>"><?php echo e($animal->status_label); ?></span>
          </div>
          <h3 class="animal-card__nome"><?php echo e($animal->nome); ?></h3>
          <p class="animal-card__meta"><?php echo e($animal->especie); ?> · <?php echo e($animal->porte_label); ?></p>

          <?php if($animal->coleira): ?>
            <div class="animal-card__coleira">
              <span><span class="ico" aria-hidden="true">🔋</span><?php echo e($animal->coleira->bateria_percentual); ?>%</span>
              <span><span class="ico" aria-hidden="true">📡</span><?php echo e($animal->coleira->status_label); ?></span>
            </div>
          <?php else: ?>
            <div class="animal-card__coleira animal-card__coleira--sem">Sem coleira cadastrada</div>
          <?php endif; ?>
        </a>
      <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </div>
  <?php endif; ?>
</section>

<?php if($alertas->isNotEmpty()): ?>
<section class="dp-section">
  <div class="dp-section__head"><h2><span class="ico" aria-hidden="true">⚠️</span>Alertas abertos</h2></div>
  <ul class="alertas-list">
    <?php $__currentLoopData = $alertas; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $alerta): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
      <li class="alertas-list__item alertas-list__item--<?php echo e($alerta->cor); ?>">
        <span class="badge badge--<?php echo e($alerta->cor); ?>"><?php echo e($alerta->nivel_label); ?></span>
        <div>
          <strong><?php echo e($alerta->animal->nome); ?> · <?php echo e($alerta->titulo); ?></strong>
          <span class="alertas-list__msg"><?php echo e($alerta->mensagem); ?></span>
          <span class="alertas-list__hora">Desde <?php echo e($alerta->created_at->format('d/m H:i')); ?></span>
        </div>
        <a href="<?php echo e(route('coleira.show', $alerta->animal)); ?>" class="btn btn-g btn-sm">Ver detalhes</a>
      </li>
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
  </ul>
</section>
<?php endif; ?>

<?php $__env->stopSection(); ?>


<?php $__env->startPush('scripts'); ?>
<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/4.4.1/chart.umd.min.js"></script>
<script src="<?php echo e(asset('js/batimentos.js')); ?>"></script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.dashboard', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\44648743806\Desktop\UltimoProjeto2.0\resources\views/pages/dashboard.blade.php ENDPATH**/ ?>