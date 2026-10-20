<?php $__env->startSection('title','Animais'); ?>
<?php $__env->startSection('page_title','Gestão de Animais'); ?>

<?php $__env->startSection('content'); ?>
<div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:var(--s6)">
  <p style="font-size:.8rem;color:var(--txt-3)"><?php echo e($animais->total()); ?> animais cadastrados</p>
  <a href="<?php echo e(route('animais.create')); ?>" class="btn btn-p"><span class="ico" aria-hidden="true">➕</span>Novo Animal</a>
</div>


<form method="GET" class="panel" style="padding:16px;margin-bottom:16px">
  <div style="display:flex;flex-wrap:wrap;gap:12px;align-items:flex-end">
    <div>
      <label class="form-label" style="font-size:.72rem">Busca</label>
      <input name="busca" value="<?php echo e(request('busca')); ?>" class="form-input" placeholder="🔍 Nome do animal..." style="padding:6px 12px;font-size:.8rem;width:200px">
    </div>
    <div>
      <label class="form-label" style="font-size:.72rem">Status</label>
      <select name="status" class="form-select" style="padding:6px 12px;font-size:.8rem">
        <option value="">Todos</option>
        <option value="saudavel" <?php echo e(request('status')=='saudavel'?'selected':''); ?>>✅ Saudável</option>
        <option value="atencao"  <?php echo e(request('status')=='atencao'?'selected':''); ?>>⚠️ Atenção</option>
        <option value="emergencia" <?php echo e(request('status')=='emergencia'?'selected':''); ?>>🚨 Emergência</option>
      </select>
    </div>
    <div>
      <label class="form-label" style="font-size:.72rem">Porte</label>
      <select name="porte" class="form-select" style="padding:6px 12px;font-size:.8rem">
        <option value="">Todos</option>
        <option value="pequeno" <?php echo e(request('porte')=='pequeno'?'selected':''); ?>>Pequeno</option>
        <option value="medio"   <?php echo e(request('porte')=='medio'?'selected':''); ?>>Médio</option>
        <option value="grande"  <?php echo e(request('porte')=='grande'?'selected':''); ?>>Grande</option>
      </select>
    </div>
    <button class="btn btn-g btn-sm">Filtrar</button>
    <?php if(request()->hasAny(['busca','status','porte'])): ?>
      <a href="<?php echo e(route('animais.index')); ?>" class="btn btn-g btn-sm"><span class="ico" aria-hidden="true">✕</span>Limpar</a>
    <?php endif; ?>
  </div>
</form>

<div class="panel">
  <div style="overflow-x:auto">
    <table class="data-table">
      <thead>
        <tr>
          <th>Animal</th>
          <th>Espécie / Raça</th>
          <th>Porte</th>
          <th>Idade / Peso</th>
          <th>Status</th>
          <th>Coleira</th>
          <th>Ações</th>
        </tr>
      </thead>
      <tbody>
        <?php $__empty_1 = true; $__currentLoopData = $animais; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $animal): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
        <tr>
          <td>
            <div style="display:flex;align-items:center;gap:8px">
              <span style="font-size:1.3rem"><?php echo e($animal->especie_emoji); ?></span>
              <span style="font-weight:600"><?php echo e($animal->nome); ?></span>
            </div>
          </td>
          <td style="color:var(--txt-2)"><?php echo e($animal->especie); ?><?php if($animal->raca): ?> · <?php echo e($animal->raca); ?><?php endif; ?></td>
          <td><span class="chip chip-g"><?php echo e($animal->porte_label); ?></span></td>
          <td style="color:var(--txt-2)"><?php echo e($animal->idade ?? '—'); ?> anos / <?php echo e($animal->peso ? $animal->peso.'kg' : '—'); ?></td>
          <td><span class="chip chip-<?php echo e($animal->status_color); ?>"><?php echo e($animal->status_label); ?></span></td>
          <td>
            <?php if($animal->coleira): ?>
              <span style="display:flex;align-items:center;gap:8px;font-size:.72rem;color:var(--t-ink)">
                <span class="ico" aria-hidden="true">🔋</span><?php echo e($animal->coleira->bateria_percentual); ?>% · <?php echo e($animal->coleira->status_label); ?>

              </span>
            <?php else: ?>
              <span style="color:var(--txt-4);font-size:.72rem">Sem coleira</span>
            <?php endif; ?>
          </td>
          <td>
            <div class="panel__actions">
              <a href="<?php echo e(route('animais.show', $animal)); ?>" class="btn btn-g btn-sm">Ver</a>
              <a href="<?php echo e(route('animais.edit', $animal)); ?>" class="btn btn-g btn-sm">Editar</a>
              <form method="POST" action="<?php echo e(route('animais.destroy', $animal)); ?>" onsubmit="return confirm('Excluir <?php echo e($animal->nome); ?>?')">
                <?php echo csrf_field(); ?> <?php echo method_field('DELETE'); ?>
                <button class="btn btn-sm" style="background:var(--err-bg);color:var(--err)">✕</button>
              </form>
            </div>
          </td>
        </tr>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
        <tr><td colspan="7" style="text-align:center;padding:32px;color:var(--txt-3)">
          Nenhum animal encontrado. <a href="<?php echo e(route('animais.create')); ?>" style="color:var(--t-ink)">Cadastrar →</a>
        </td></tr>
        <?php endif; ?>
      </tbody>
    </table>
  </div>
  <div style="margin-top:16px"><?php echo e($animais->links()); ?></div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.dashboard', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\44648743806\Desktop\UltimoProjeto2.0\resources\views/pages/animais/index.blade.php ENDPATH**/ ?>