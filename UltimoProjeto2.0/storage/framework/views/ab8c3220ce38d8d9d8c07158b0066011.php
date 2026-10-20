<?php $__env->startSection('title','Entrar'); ?>

<?php $__env->startSection('content'); ?>
<div class="auth-page">
  <div class="auth-left">
    <div class="auth-left__img"><img src="<?php echo e(asset('images/Animais/pug.jpg')); ?>" alt="" loading="eager"></div>
    <div class="auth-left__veil" aria-hidden="true"></div>
    <div class="auth-left__body">
      <p class="kicker auth-left__kicker">Monitoramento Pet</p>
      <h1 class="auth-left__title">Cuide do seu pet com <em>inteligência</em></h1>
      <p class="auth-left__sub">Monitore saúde, batimentos cardíacos e bem-estar do seu animal em tempo real, de qualquer lugar.</p>
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
        <button class="auth-tab act" role="tab" aria-selected="true">Entrar</button>
        <a href="<?php echo e(route('registo')); ?>" class="auth-tab" role="tab" aria-selected="false">Criar conta</a>
      </div>
      <h2 class="auth-box__title">Bem-vindo de volta</h2>
      <p class="auth-box__sub">Acesse o painel e monitore seu pet.</p>

      <?php if($errors->any()): ?><div class="form-errors" role="alert"><?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $e): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><p>• <?php echo e($e); ?></p><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?></div><?php endif; ?>

      <form method="POST" action="<?php echo e(route('login.post')); ?>" novalidate>
        <?php echo csrf_field(); ?>
        <div class="form-group">
          <label for="email" class="form-label">E-mail</label>
          <div class="form-icon">
            <span class="form-icon__i" aria-hidden="true">✉️</span>
            <input type="email" id="email" name="email" class="form-input form-input--pl <?php echo e($errors->has('email') ? 'form-input--err' : ''); ?>" value="<?php echo e(old('email')); ?>" placeholder="seu@email.com" required autocomplete="email" autofocus>
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
        <div class="form-group">
          <label for="password" class="form-label" style="display:flex;justify-content:space-between">
            <span>Senha</span>
          </label>
          <div class="form-icon">
            <span class="form-icon__i" aria-hidden="true">🔒</span>
            <input type="password" id="password" name="password" class="form-input form-input--pl <?php echo e($errors->has('password') ? 'form-input--err' : ''); ?>" placeholder="••••••••" required autocomplete="current-password">
          </div>
          <?php $__errorArgs = ['password'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="form-err"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
        </div>
        <div style="display:flex;align-items:center;gap:var(--s2);margin-bottom:var(--s5)">
          <input type="checkbox" name="remember" id="rem" style="width:15px;height:15px;accent-color:var(--t)">
          <label for="rem" style="font-size:var(--tx-sm);color:var(--txt-2);cursor:pointer;letter-spacing:-.005em">Lembrar de mim</label>
        </div>
        <button type="submit" class="btn btn-p btn-fw btn-lg">→ Entrar na conta</button>
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
<?php endif; ?>Entrar com Google
      </a>

      <div class="auth-demo">Demo: rafael.nogueira@petmail.com / <strong>password</strong></div>
      <div class="auth-box__foot">Não tem conta? <a href="<?php echo e(route('registo')); ?>">Cadastre-se gratuitamente</a></div>
    </div>
  </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\44648743806\Desktop\UltimoProjeto2.0\resources\views/pages/login.blade.php ENDPATH**/ ?>