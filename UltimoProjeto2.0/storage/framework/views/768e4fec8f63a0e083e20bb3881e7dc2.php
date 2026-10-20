<?php $__env->startSection('title', 'Perfil'); ?>
<?php $__env->startSection('page_title'); ?><span class="ico" aria-hidden="true">👤</span>Perfil &amp; Configurações <?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
<div style="max-width:700px">

  <?php if($errors->any()): ?>
  <div class="form-errors reveal" role="alert">
    <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $e): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><p>• <?php echo e($e); ?></p><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
  </div>
  <?php endif; ?>

  <div class="form-sheet reveal">
    <h3 style="margin-bottom:16px;color:var(--t-ink)">Meus dados</h3>
    <form method="POST" action="<?php echo e(route('perfil.update')); ?>" novalidate>
      <?php echo csrf_field(); ?>
      <?php echo method_field('PUT'); ?>

      <div class="form-group">
        <label class="form-label">Nome *</label>
        <input name="nome" class="form-input <?php echo e($errors->has('nome') ? 'form-input--err' : ''); ?>"
          value="<?php echo e(old('nome', $tutor->nome)); ?>" required>
        <?php $__errorArgs = ['nome'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="form-err"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
      </div>

      <div class="form-2col">
        <div class="form-group">
          <label class="form-label">Telefone *</label>
          <input name="telefone" class="form-input <?php echo e($errors->has('telefone') ? 'form-input--err' : ''); ?>"
            value="<?php echo e(old('telefone', $tutor->telefone)); ?>" required>
          <?php $__errorArgs = ['telefone'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="form-err"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
        </div>
        <div class="form-group">
          <label class="form-label">E-mail *</label>
          <input type="email" name="email" class="form-input <?php echo e($errors->has('email') ? 'form-input--err' : ''); ?>"
            value="<?php echo e(old('email', $tutor->email)); ?>" required>
          <?php $__errorArgs = ['email'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="form-err"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
        </div>
      </div>

      <div class="form-group">
        <label class="form-label">Endereço</label>
        <input name="endereco" class="form-input" value="<?php echo e(old('endereco', $tutor->endereco)); ?>">
      </div>

      <button type="submit" class="btn btn-p btn-lg" style="margin-top:8px"><span class="ico" aria-hidden="true">💾</span>Salvar alterações</button>
    </form>
  </div>

  <div class="form-sheet reveal" style="margin-top:20px">
    <h3 style="margin-bottom:12px;color:var(--t-ink)">Preferências</h3>
    <label style="display:flex;align-items:center;gap:10px;cursor:pointer;padding:12px;background:var(--bg-3);border-radius:var(--r2);border:1px solid var(--b2)">
      <div>
        <div style="font-weight:600;color:var(--txt)"><span class="ico" aria-hidden="true">🌓</span>Tema da interface</div>
        <div style="font-size:.75rem;color:var(--txt-3)">Use o botão no topo da página para alternar entre claro e escuro. O padrão é o tema claro.</div>
      </div>
    </label>
  </div>

</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.dashboard', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\44648743806\Desktop\UltimoProjeto2.0\resources\views/pages/perfil.blade.php ENDPATH**/ ?>