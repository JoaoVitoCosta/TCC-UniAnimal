<?php $__env->startSection('title', isset($animal->id) ? 'Editar Animal' : 'Novo Animal'); ?>
<?php $__env->startSection('page_title', isset($animal->id) ? 'Editar: '.$animal->nome : 'Cadastrar Animal'); ?>

<?php $__env->startSection('content'); ?>
<div style="max-width:700px">
  <?php if($errors->any()): ?>
  <div class="form-errors reveal" role="alert">
    <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $e): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><p>• <?php echo e($e); ?></p><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
  </div>
  <?php endif; ?>

  <div class="form-sheet reveal">
    <form method="POST" action="<?php echo e(isset($animal->id) ? route('animais.update', $animal) : route('animais.store')); ?>" novalidate>
      <?php echo csrf_field(); ?>
      <?php if(isset($animal->id)): ?> <?php echo method_field('PUT'); ?> <?php endif; ?>

      <div class="form-2col">
        <div class="form-group">
          <label class="form-label">Nome do animal *</label>
          <input name="nome" class="form-input <?php echo e($errors->has('nome') ? 'form-input--err' : ''); ?>"
            value="<?php echo e(old('nome', $animal->nome ?? '')); ?>" placeholder="Ex: Thor, Mimi..." required>
          <?php $__errorArgs = ['nome'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="form-err"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
        </div>
        <div class="form-group">
          <label class="form-label">Espécie *</label>
          <select name="especie" class="form-select" required>
            <option value="">Selecione</option>
            <?php $__currentLoopData = ['Cachorro','Gato','Coelho','Cavalo','Bovino','Outro']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $esp): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <option value="<?php echo e($esp); ?>" <?php echo e(old('especie', $animal->especie ?? '') == $esp ? 'selected' : ''); ?>><?php echo e($esp); ?></option>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
          </select>
          <?php $__errorArgs = ['especie'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="form-err"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
        </div>
      </div>

      <div class="form-2col">
        <div class="form-group">
          <label class="form-label">Raça</label>
          <input name="raca" class="form-input" value="<?php echo e(old('raca', $animal->raca ?? '')); ?>" placeholder="Ex: Labrador, Persa...">
        </div>
        <div class="form-group">
          <label class="form-label">Porte *</label>
          <select name="porte" class="form-select <?php echo e($errors->has('porte') ? 'form-input--err' : ''); ?>" required>
            <option value="">Selecione</option>
            <option value="pequeno" <?php echo e(old('porte', $animal->porte ?? '') == 'pequeno' ? 'selected' : ''); ?>>🐱 Pequeno (até 15kg)</option>
            <option value="medio"   <?php echo e(old('porte', $animal->porte ?? '') == 'medio'   ? 'selected' : ''); ?>>🐕 Médio (15–50kg)</option>
            <option value="grande"  <?php echo e(old('porte', $animal->porte ?? '') == 'grande'  ? 'selected' : ''); ?>>🐴 Grande (acima de 50kg)</option>
          </select>
          <?php $__errorArgs = ['porte'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="form-err"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
        </div>
      </div>

      <div class="form-2col">
        <div class="form-group">
          <label class="form-label">Idade (anos)</label>
          <input type="number" name="idade" class="form-input" value="<?php echo e(old('idade', $animal->idade ?? '')); ?>" min="0" max="50" placeholder="Ex: 3">
        </div>
        <div class="form-group">
          <label class="form-label">Peso (kg)</label>
          <input type="number" step="0.1" name="peso" class="form-input" value="<?php echo e(old('peso', $animal->peso ?? '')); ?>" min="0.1" max="999" placeholder="Ex: 25.5">
        </div>
      </div>

      <div class="form-group">
        <label class="form-label">Código da coleira *</label>
        <input name="codigo_coleira" class="form-input <?php echo e($errors->has('codigo_coleira') ? 'form-input--err' : ''); ?>"
          value="<?php echo e(old('codigo_coleira', $animal->coleira->codigo ?? '')); ?>"
          placeholder="Ex: COL-2026-0001" required>
        <?php $__errorArgs = ['codigo_coleira'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="form-err"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
        <p style="font-size:.72rem;color:var(--txt-3);margin-top:4px">
          Código único gravado na coleira UniAnimal do seu pet. Ele habilita o rastreamento por GPS e o monitoramento de bateria.
        </p>
      </div>

      <div style="display:flex;gap:12px;margin-top:8px">
        <button type="submit" class="btn btn-p btn-lg" style="flex:1">
          <?php if(isset($animal->id)): ?><span class="ico" aria-hidden="true">💾</span>Salvar alterações <?php else: ?><span class="ico" aria-hidden="true">✅</span>Cadastrar animal <?php endif; ?>
        </button>
        <a href="<?php echo e(route('animais.index')); ?>" class="btn btn-g btn-lg">Cancelar</a>
      </div>
    </form>
  </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.dashboard', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\44648743806\Desktop\UltimoProjeto2.0\resources\views/pages/animais/form.blade.php ENDPATH**/ ?>