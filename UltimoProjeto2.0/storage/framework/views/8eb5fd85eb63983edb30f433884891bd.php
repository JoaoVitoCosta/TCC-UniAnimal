<?php $__env->startSection('title', $animal->nome); ?>
<?php $__env->startSection('page_title'); ?><span class="ico" aria-hidden="true"><?php echo e($animal->especie_emoji); ?></span><?php echo e($animal->nome); ?><?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
<div class="gm">
  <div style="display:flex;flex-direction:column;gap:var(--s4)">

    
    <div class="panel">
      <div class="panel__head">
        <span style="color:var(--t-ink)"><span class="ico" aria-hidden="true">📋</span>Ficha do Animal</span>
        <div class="panel__actions">
          <a href="<?php echo e(route('animais.edit', $animal)); ?>" class="btn btn-g btn-sm">Editar</a>
          <span class="chip chip-<?php echo e($animal->status_color); ?>"><?php echo e($animal->status_label); ?></span>
        </div>
      </div>
      <div style="display:grid;grid-template-columns:repeat(3,1fr);gap:16px;padding:8px 0">
        <div><p style="font-size:.72rem;color:var(--txt-3);margin-bottom:3px">ESPÉCIE / RAÇA</p><p><?php echo e($animal->especie); ?><?php if($animal->raca): ?> · <?php echo e($animal->raca); ?><?php endif; ?></p></div>
        <div><p style="font-size:.72rem;color:var(--txt-3);margin-bottom:3px">PORTE</p><p><?php echo e($animal->porte_label); ?></p></div>
        <div><p style="font-size:.72rem;color:var(--txt-3);margin-bottom:3px">IDADE</p><p><?php echo e($animal->idade ?? '—'); ?> anos</p></div>
        <div><p style="font-size:.72rem;color:var(--txt-3);margin-bottom:3px">PESO</p><p><?php echo e($animal->peso ? $animal->peso.'kg' : '—'); ?></p></div>
        <div><p style="font-size:.72rem;color:var(--txt-3);margin-bottom:3px">CÓDIGO DA COLEIRA</p><p><?php echo e($animal->coleira->codigo ?? '—'); ?></p></div>
        <div><p style="font-size:.72rem;color:var(--txt-3);margin-bottom:3px">COLEIRA</p>
          <p><?php if($animal->coleira): ?><span class="ico" aria-hidden="true">✅</span><?php echo e($animal->coleira->status_label); ?><?php else: ?><span class="ico" aria-hidden="true">⭕</span>Não cadastrada <?php endif; ?></p></div>
      </div>
    </div>

    
    <?php if($animal->coleira): ?>
    <?php
      $c = $animal->coleira;
      $h = $animal->ultimoBatimento;
      $faixa = $animal->faixaBatimento($h?->batimento_cardiaco);
    ?>
    <div class="panel">
      <div class="panel__head">
        <span style="color:var(--t-ink)"><span class="ico" aria-hidden="true">📍</span>Localização, Batimentos &amp; Bateria</span>
        <span style="font-size:.75rem;color:var(--txt-3)">
          <?php echo e(ucfirst($c->localizacao_atualizada_em?->diffForHumans() ?? 'Sem dados ainda')); ?>

        </span>
      </div>
      <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(130px,1fr));gap:12px;margin:12px 0">
        <?php $__currentLoopData = [
          ['📍','Latitude', $c->latitude ?? '—'],
          ['📍','Longitude', $c->longitude ?? '—'],
          ['🎯','Precisão', $c->precisao ? $c->precisao.' m' : '—'],
          ['🔋','Bateria', $c->bateria_percentual.'% ('.$c->bateria_status_label.')'],
        ]; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as [$ic,$lb,$vl]): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <div style="background:var(--bg-3);border-radius:var(--r3);padding:16px;text-align:center;border:1px solid var(--b2)">
          <div style="font-size:1.5rem;margin-bottom:6px"><?php echo e($ic); ?></div>
          <div style="font-size:1.05rem;font-weight:700;color:var(--t-ink)"><?php echo e($vl); ?></div>
          <div style="font-size:.7rem;color:var(--txt-3);margin-top:2px"><?php echo e($lb); ?></div>
        </div>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

        
        <div style="background:var(--bg-3);border-radius:var(--r3);padding:16px;text-align:center;border:1px solid var(--b2)">
          <div style="font-size:1.5rem;margin-bottom:6px">❤️</div>
          <div style="font-size:1.05rem;font-weight:700;color:var(--err)"><?php echo e($h?->batimento_cardiaco ? $h->batimento_cardiaco.' bpm' : '—'); ?></div>
          <div style="font-size:.7rem;color:var(--txt-3);margin-top:2px">
            Último batimento<?php echo e($h?->registrado_em ? ' · ' . $h->registrado_em->diffForHumans() : ''); ?>

          </div>
          <?php if($faixa): ?>
            <span class="badge badge--<?php echo e($faixa[0]); ?>" style="margin-top:6px"><?php echo e($faixa[1]); ?></span>
          <?php endif; ?>
        </div>

        
        <?php
          $t = $animal->ultimaTemperatura;
          $nivelT = \App\Models\Animal::nivelNaFaixa($t?->temperatura, $animal->faixaTemperatura());
          $corT = ['normal' => 'ok', 'alerta' => 'warn', 'emergencia' => 'err'][$nivelT] ?? null;
        ?>
        <div style="background:var(--bg-3);border-radius:var(--r3);padding:16px;text-align:center;border:1px solid var(--b2)">
          <div style="font-size:1.5rem;margin-bottom:6px">🌡️</div>
          <div style="font-size:1.05rem;font-weight:700;color:var(--warn)"><?php echo e($t ? number_format($t->temperatura, 1, ',', '') . ' °C' : '—'); ?></div>
          <div style="font-size:.7rem;color:var(--txt-3);margin-top:2px">
            Temperatura<?php echo e($t?->registrado_em ? ' · ' . $t->registrado_em->diffForHumans() : ''); ?>

          </div>
          <?php if($corT): ?>
            <span class="badge badge--<?php echo e($corT); ?>" style="margin-top:6px"><?php echo e(['ok' => 'Normal', 'warn' => 'Alerta', 'err' => 'Emergência'][$corT]); ?></span>
          <?php endif; ?>
        </div>
      </div>
      <a href="<?php echo e(route('coleira.show', $animal)); ?>" class="btn btn-p btn-sm">Ver mapa e histórico completo →</a>
    </div>
    <?php endif; ?>

  </div>

  
  <div style="display:flex;flex-direction:column;gap:var(--s4)">

    <?php if($animal->coleira): ?>
    <div class="nudge">
      <div class="nudge__icon">📡</div>
      <div class="nudge__title">Simular atualização</div>
      <p class="nudge__sub">Gera uma nova posição/bateria de demonstração (arquitetura preparada para GPS real).</p>
      <button id="btn-simular" class="btn btn-p btn-sm btn-fw" data-id="<?php echo e($animal->id); ?>">Simular agora</button>
    </div>
    <?php endif; ?>

    <div class="nudge">
      <div class="nudge__icon">🐾</div>
      <div class="nudge__title">Meus Animais</div>
      <p class="nudge__sub">Voltar para a lista completa dos seus pets.</p>
      <a href="<?php echo e(route('animais.index')); ?>" class="btn btn-g btn-sm btn-fw">Ver todos →</a>
    </div>

  </div>
</div>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
<script>
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

<?php echo $__env->make('layouts.dashboard', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\44648743806\Desktop\UltimoProjeto2.0\resources\views/pages/animais/show.blade.php ENDPATH**/ ?>