<?php $__env->startSection('title','Criar Conta'); ?>

<?php $__env->startSection('content'); ?>
<div class="auth-page">
  <div class="auth-left">
    <div class="auth-left__img"><img src="<?php echo e(asset('images/Animais/animais-domesticos.jpg')); ?>" alt="" loading="eager"></div>
    <div class="auth-left__veil" aria-hidden="true"></div>
    <div class="auth-left__body">
      <p class="kicker auth-left__kicker">Junte-se a nós</p>
      <h1 class="auth-left__title">Proteja seu pet com <em>tecnologia</em></h1>
      <p class="auth-left__sub">Cadastre-se gratuitamente e comece a monitorar hoje mesmo.</p>
      <div class="auth-left__stats">
        <div><div class="auth-stat__n">500+</div><div class="auth-stat__k">Pets monitorados</div></div>
        <div><div class="auth-stat__n">24/7</div><div class="auth-stat__k">Disponível</div></div>
        <div><div class="auth-stat__n">98%</div><div class="auth-stat__k">Satisfação</div></div>
      </div>
    </div>
  </div>
  <div class="auth-right">
    <div class="auth-box">
      <div class="auth-tabs" role="tablist">
        <a href="<?php echo e(route('login')); ?>" class="auth-tab" role="tab" aria-selected="false">Entrar</a>
        <button class="auth-tab act" role="tab" aria-selected="true">Criar conta</button>
      </div>
      <h2 class="auth-box__title">Criar sua conta</h2>
      <p class="auth-box__sub">Junte-se à comunidade UniAnimal.</p>

      <?php if($errors->any()): ?><div class="form-errors" role="alert"><?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $e): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><p>• <?php echo e($e); ?></p><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?></div><?php endif; ?>

      <form method="POST" action="<?php echo e(route('registo.post')); ?>" novalidate>
        <?php echo csrf_field(); ?>
        <div class="form-group">
          <label for="name" class="form-label">Nome completo *</label>
          <div class="form-icon">
            <span class="form-icon__i" aria-hidden="true">👤</span>
            <input type="text" id="name" name="name" class="form-input form-input--pl <?php echo e($errors->has('name') ? 'form-input--err' : ''); ?>" value="<?php echo e(old('name')); ?>" placeholder="Seu nome completo" required autocomplete="name" autofocus>
          </div>
          <?php $__errorArgs = ['name'];
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
          <div class="form-icon">
            <span class="form-icon__i" aria-hidden="true">✉️</span>
            <input type="email" id="email" name="email" class="form-input form-input--pl <?php echo e($errors->has('email') ? 'form-input--err' : ''); ?>" value="<?php echo e(old('email')); ?>" placeholder="seu@email.com" required autocomplete="email">
          </div>
          <?php $__errorArgs = ['email'];
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
            <label for="password" class="form-label">Senha *</label>
            <input type="password" id="password" name="password" class="form-input <?php echo e($errors->has('password') ? 'form-input--err' : ''); ?>" placeholder="Mín. 8 caracteres" required autocomplete="new-password">
            <?php $__errorArgs = ['password'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="form-err"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
          </div>
          <div class="form-group">
            <label for="password_confirmation" class="form-label">Confirmar *</label>
            <input type="password" id="password_confirmation" name="password_confirmation" class="form-input" placeholder="Repita a senha" required autocomplete="new-password">
          </div>
        </div>
        <div style="margin-bottom:var(--s5)">
          <label style="display:flex;align-items:flex-start;gap:var(--s2);font-size:var(--tx-sm);color:var(--txt-2);cursor:pointer;line-height:1.5;letter-spacing:-.005em">
            <input type="checkbox" name="termos" required style="width:15px;height:15px;accent-color:var(--t);margin-top:2px;flex-shrink:0">
            Concordo com os <a href="#" style="color:var(--t-ink)">Termos</a> e <a href="#" style="color:var(--t-ink)">Privacidade</a>.
          </label>
        </div>
        <button type="submit" class="btn btn-p btn-fw btn-lg">🚀 Criar minha conta</button>
      </form>

      <div class="auth-divider" style="display:flex;align-items:center;gap:12px;margin:var(--s5) 0;color:var(--txt-3);font-size:var(--tx-xs)">
        <span style="flex:1;height:1px;background:var(--b2)"></span>OU<span style="flex:1;height:1px;background:var(--b2)"></span>
      </div>
      <a href="<?php echo e(route('auth.google')); ?>" class="btn btn-g btn-fw btn-lg" style="display:flex;align-items:center;justify-content:center;gap:8px">
        <?php if (isset($component)) { $__componentOriginalf8901d640d6a9c957b5a2e11b0e3bf32 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalf8901d640d6a9c957b5a2e11b0e3bf32 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.icone-marca','data' => ['nome' => 'google','tamanho' => 20]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('icone-marca'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['nome' => 'google','tamanho' => 20]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalf8901d640d6a9c957b5a2e11b0e3bf32)): ?>
<?php $attributes = $__attributesOriginalf8901d640d6a9c957b5a2e11b0e3bf32; ?>
<?php unset($__attributesOriginalf8901d640d6a9c957b5a2e11b0e3bf32); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalf8901d640d6a9c957b5a2e11b0e3bf32)): ?>
<?php $component = $__componentOriginalf8901d640d6a9c957b5a2e11b0e3bf32; ?>
<?php unset($__componentOriginalf8901d640d6a9c957b5a2e11b0e3bf32); ?>
<?php endif; ?>Cadastrar com Google
      </a>
      <div class="auth-box__foot">Já tem conta? <a href="<?php echo e(route('login')); ?>">Fazer login →</a></div>
    </div>
  </div>
</div>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\44648743806\Desktop\UltimoProjeto2.0\resources\views/pages/registo.blade.php ENDPATH**/ ?>