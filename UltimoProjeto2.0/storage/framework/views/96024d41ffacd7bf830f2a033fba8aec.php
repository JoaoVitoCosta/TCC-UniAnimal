
<?php $dash = '–'; ?>
<div class="iot-card bpm-ref">
  <div class="bpm-ref__head">
    <svg class="bpm-ref__icone" width="26" height="26" viewBox="0 0 24 24" aria-hidden="true">
      <rect x="1" y="3" width="22" height="18" rx="4" fill="currentColor"/>
      <path d="M4 12.5h3.6l1.7-3.6 2.6 7 2.1-5 1.2 1.6H20" fill="none" stroke="var(--bg-2)" stroke-width="1.9" stroke-linecap="round" stroke-linejoin="round"/>
    </svg>
    <h3 class="bpm-ref__title">Orientação de batimentos</h3>
  </div>
  <p class="bpm-ref__sub">Frequência cardíaca em repouso (bpm) de animais adultos.</p>

  <div class="bpm-ref__scroll">
    <table class="bpm-tabela">
      <thead>
        <tr>
          <th>Animal</th>
          <th class="bpm-tabela__normal">Normal</th>
          <th class="bpm-tabela__alerta">Alerta</th>
          <th class="bpm-tabela__emergencia">Emergência</th>
        </tr>
      </thead>
      <tbody>
        <?php $__currentLoopData = \App\Models\Animal::REFERENCIA_BPM; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $chave => $r): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
          <tr data-ref="<?php echo e($chave); ?>" class="<?php echo \Illuminate\Support\Arr::toCssClasses(['bpm-ref__atual' => ($refAtual ?? null) === $chave]); ?>">
            <td><?php echo e($r['label']); ?><span class="bpm-tabela__estrela" aria-label="(este animal)"> ★</span></td>
            <td><?php echo e($r['min']); ?><?php echo e($dash); ?><?php echo e($r['max']); ?></td>
            <td>
              <?php echo e($r['emergencia_min']); ?><?php echo e($dash); ?><?php echo e($r['min'] - 1); ?><br>
              <?php echo e($r['max'] + 1); ?><?php echo e($dash); ?><?php echo e($r['emergencia_max']); ?>

            </td>
            <td>
              &lt; <?php echo e($r['emergencia_min']); ?><br>
              &gt; <?php echo e($r['emergencia_max']); ?>

            </td>
          </tr>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
      </tbody>
    </table>
  </div>

  <ul class="bpm-legenda">
    <li>
      <svg width="20" height="20" viewBox="0 0 24 24" aria-hidden="true" class="bpm-legenda__ico bpm-legenda__ico--normal">
        <circle cx="12" cy="12" r="11" fill="currentColor"/><path d="M7 12.5l3.2 3.2L17 9" fill="none" stroke="#fff" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round"/>
      </svg>
      <span><strong class="bpm-tabela__normal">Normal:</strong> Dentro do esperado em repouso. Continue acompanhando.</span>
    </li>
    <li>
      <svg width="20" height="20" viewBox="0 0 24 24" aria-hidden="true" class="bpm-legenda__ico bpm-legenda__ico--alerta">
        <circle cx="12" cy="12" r="11" fill="currentColor"/><rect x="10.8" y="10.5" width="2.4" height="7" rx="1.2" fill="#fff"/><circle cx="12" cy="7.4" r="1.5" fill="#fff"/>
      </svg>
      <span><strong class="bpm-tabela__alerta">Alerta:</strong> Deixe o pet descansar em local calmo e confira de novo em alguns minutos. Se continuar fora da faixa, fale com o veterinário.</span>
    </li>
    <li>
      <svg width="20" height="20" viewBox="0 0 24 24" aria-hidden="true" class="bpm-legenda__ico bpm-legenda__ico--emergencia">
        <path d="M10.3 2.9a2 2 0 013.4 0l9.2 16a2 2 0 01-1.7 3H2.8a2 2 0 01-1.7-3z" fill="currentColor"/><rect x="10.8" y="8" width="2.4" height="7" rx="1.2" fill="#fff"/><circle cx="12" cy="18" r="1.5" fill="#fff"/>
      </svg>
      <span><strong class="bpm-tabela__emergencia">Emergência:</strong> Procure atendimento veterinário imediatamente, principalmente se houver apatia, desmaio, falta de ar ou gengivas pálidas/arroxeadas.</span>
    </li>
  </ul>

  <p class="bpm-ref__nota">
    Valores aproximados, apenas para orientação. Filhotes, exercício, calor e estresse aumentam a frequência. Em caso de dúvida, consulte um médico-veterinário.
  </p>
</div>
<?php /**PATH C:\Users\44648743806\Desktop\UltimoProjeto2.0\resources\views/partials/bpm-referencia.blade.php ENDPATH**/ ?>