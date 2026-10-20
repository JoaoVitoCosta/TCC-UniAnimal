<?php $__env->startSection('title','Contato'); ?>

<?php $__env->startSection('content'); ?>
<div class="page-top">
  <div class="ctr">
    <div class="g2a">
      <div class="reveal">
        <p class="kicker">📞 Fale conosco</p>
        <h1 class="sec-title sec-title--xl">Entre em <em>contato</em></h1>
        <p style="color:var(--txt-2);font-size:var(--tx-lg);line-height:1.72;margin-bottom:var(--s8);letter-spacing:-.008em">Resposta em até 24 horas úteis por qualquer canal abaixo.</p>
        <a href="tel:+5519989393961" class="ci"><div class="ci__icon" aria-hidden="true">📞</div><div><div class="ci__label">Telefone</div><div class="ci__val">(19) 98939-3961</div></div></a>
        <a href="https://wa.me/5519989393961" target="_blank" rel="noopener" class="ci"><div class="ci__icon" aria-hidden="true"><?php if (isset($component)) { $__componentOriginalf8901d640d6a9c957b5a2e11b0e3bf32 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalf8901d640d6a9c957b5a2e11b0e3bf32 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.icone-marca','data' => ['nome' => 'whatsapp','tamanho' => 22]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('icone-marca'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['nome' => 'whatsapp','tamanho' => 22]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalf8901d640d6a9c957b5a2e11b0e3bf32)): ?>
<?php $attributes = $__attributesOriginalf8901d640d6a9c957b5a2e11b0e3bf32; ?>
<?php unset($__attributesOriginalf8901d640d6a9c957b5a2e11b0e3bf32); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalf8901d640d6a9c957b5a2e11b0e3bf32)): ?>
<?php $component = $__componentOriginalf8901d640d6a9c957b5a2e11b0e3bf32; ?>
<?php unset($__componentOriginalf8901d640d6a9c957b5a2e11b0e3bf32); ?>
<?php endif; ?></div><div><div class="ci__label">WhatsApp</div><div class="ci__val">(19) 98939-3961</div></div></a>
        <a href="mailto:unianimal@gmail.com" class="ci"><div class="ci__icon" aria-hidden="true">✉️</div><div><div class="ci__label">E-mail</div><div class="ci__val">unianimal@gmail.com</div></div></a>
        <div class="ci"><div class="ci__icon" aria-hidden="true">📍</div><div><div class="ci__label">Localização</div><div class="ci__val">Tambaú, São Paulo — Brasil</div></div></div>
        <div class="ci"><div class="ci__icon" aria-hidden="true">🕐</div><div><div class="ci__label">Horário</div><div class="ci__val">Seg–Sex: 8h–18h | Sáb: 9h–13h</div></div></div>
        <img src="<?php echo e(asset('images/Animais/cachorro1.webp')); ?>" alt="Cachorro com coleira UniAnimal" class="img-r" style="margin-top:var(--s8);aspect-ratio:16/9" loading="lazy">
      </div>
      <div class="reveal d2">
        <?php if($errors->any()): ?><div class="form-errors" role="alert"><?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $e): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><p>• <?php echo e($e); ?></p><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?></div><?php endif; ?>
        <div class="form-sheet">
          <h2 style="font-family:var(--f-display);font-size:var(--tx-xl);font-weight:700;letter-spacing:-.025em;margin-bottom:5px">Envie uma mensagem</h2>
          <p style="font-size:var(--tx-sm);color:var(--txt-2);margin-bottom:var(--s6);letter-spacing:-.005em">Respondemos em até 24 horas úteis.</p>
          <form method="POST" action="<?php echo e(route('contactos.store')); ?>" novalidate>
            <?php echo csrf_field(); ?>
            <div class="form-2col">
              <div class="form-group">
                <label for="nome" class="form-label">Nome *</label>
                <input type="text" id="nome" name="nome" class="form-input <?php echo e($errors->has('nome') ? 'form-input--err' : ''); ?>" value="<?php echo e(old('nome')); ?>" placeholder="Seu nome" required>
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
                <label for="email" class="form-label">E-mail *</label>
                <input type="email" id="email" name="email" class="form-input <?php echo e($errors->has('email') ? 'form-input--err' : ''); ?>" value="<?php echo e(old('email')); ?>" placeholder="seu@email.com" required>
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
            <div class="form-2col">
              <div class="form-group">
                <label for="tel" class="form-label">Telefone</label>
                <input type="tel" id="tel" name="telefone" class="form-input" value="<?php echo e(old('telefone')); ?>" placeholder="(00) 00000-0000">
              </div>
              <div class="form-group">
                <label for="ass" class="form-label">Assunto</label>
                <input type="text" id="ass" name="assunto" class="form-input" value="<?php echo e(old('assunto')); ?>" placeholder="Ex: Dúvida sobre produto">
              </div>
            </div>
            <div class="form-group">
              <label for="msg" class="form-label">Mensagem *</label>
              <textarea id="msg" name="mensagem" class="form-input <?php echo e($errors->has('mensagem') ? 'form-input--err' : ''); ?>" placeholder="Descreva sua dúvida..." required rows="5"><?php echo e(old('mensagem')); ?></textarea>
              <?php $__errorArgs = ['mensagem'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="form-err"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
            </div>
            <button type="submit" class="btn btn-p btn-fw btn-lg">📨 Enviar mensagem</button>
          </form>
        </div>
      </div>
    </div>
  </div>
</div>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\44648743806\Desktop\UltimoProjeto2.0\resources\views/pages/contactos.blade.php ENDPATH**/ ?>