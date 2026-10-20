<!DOCTYPE html>
<html lang="pt-BR" data-theme="light">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">
<meta name="description" content="<?php echo $__env->yieldContent('meta_desc','UniAnimal — Coleira inteligente com GPS e monitoramento de saúde animal 24h.'); ?>">
<meta name="theme-color" content="#F0F4F8">
<title><?php echo $__env->yieldContent('title','UniAnimal'); ?> — Coleira Inteligente</title>
<link rel="icon" type="image/svg+xml" href="data:image/svg+xml,<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 100 100'><text y='.9em' font-size='90'>🐾</text></svg>">
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link rel="stylesheet" href="<?php echo e(asset('css/reset.css')); ?>">
<link rel="stylesheet" href="<?php echo e(asset('css/style.css')); ?>">
<link rel="stylesheet" href="<?php echo e(asset('css/transitions.css')); ?>">
<link rel="stylesheet" href="<?php echo e(asset('css/dashboard.css')); ?>">
<?php echo $__env->yieldPushContent('head'); ?>
</head>
<body>

<div id="page-veil"    aria-hidden="true"></div>
<div id="progress-bar" aria-hidden="true"></div>
<div id="cursor-dot"   aria-hidden="true"></div>


<nav class="nav" id="navbar" role="navigation" aria-label="Navegação principal">
  <div class="nav__inner">

    <a href="<?php echo e(route('home')); ?>" class="nav__logo" aria-label="UniAnimal — início">
      <div class="nav__logo-mark" aria-hidden="true">🐾</div>
      <span class="nav__logo-name">Uni<strong>Animal</strong></span>
    </a>

    <div class="nav__links" id="nav-links" role="menubar">
      <a href="<?php echo e(route('sobre')); ?>"        class="nav__link <?php echo e(request()->routeIs('sobre')        ? 'active' : ''); ?>">Sobre</a>
      <a href="<?php echo e(route('diferenciais')); ?>" class="nav__link <?php echo e(request()->routeIs('diferenciais') ? 'active' : ''); ?>">Recursos</a>
      <a href="<?php echo e(route('depoimentos')); ?>"  class="nav__link <?php echo e(request()->routeIs('depoimentos*') ? 'active' : ''); ?>">Depoimentos</a>
      <a href="<?php echo e(route('contactos')); ?>"    class="nav__link <?php echo e(request()->routeIs('contactos')    ? 'active' : ''); ?>">Contato</a>
      <div class="nav__sep" aria-hidden="true"></div>
      <a href="<?php echo e(route('produtos')); ?>" class="nav__cta nav__link">Comprar</a>
      <div class="nav__sep" aria-hidden="true"></div>
      <?php if(auth()->guard()->check()): ?>
        <a href="<?php echo e(route('dashboard')); ?>" class="nav__link <?php echo e(request()->routeIs('dashboard') ? 'active' : ''); ?>">Dashboard</a>
        <form method="POST" action="<?php echo e(route('logout')); ?>" style="display:inline"><?php echo csrf_field(); ?>
          <button type="submit" class="nav__link" style="font-family:inherit">Sair</button>
        </form>
      <?php else: ?>
        <a href="<?php echo e(route('login')); ?>"   class="nav__link">Entrar</a>
        <a href="<?php echo e(route('registo')); ?>" class="nav__out nav__link">Criar conta</a>
      <?php endif; ?>

      
      <button class="theme-btn" id="themeToggle" aria-label="Alternar tema" title="Alternar tema claro/escuro" style="margin-left:8px">
        <span class="theme-btn__icon theme-btn__sun">☀️</span>
        <span class="theme-btn__icon theme-btn__moon">🌙</span>
        <span class="theme-btn__knob"></span>
      </button>
    </div>

    <button class="nav__burger" id="nav-burger" aria-label="Menu" aria-expanded="false" aria-controls="nav-links">
      <span></span><span></span><span></span>
    </button>
  </div>
</nav>

<?php if(session('success')): ?>
<div class="flash flash-ok" role="alert">
  <svg width="15" height="15" viewBox="0 0 16 16" fill="currentColor" aria-hidden="true"><path d="M8 1a7 7 0 110 14A7 7 0 018 1zm3.03 4.47L7 9.5 5.47 7.97a.75.75 0 00-1.06 1.06l2 2a.75.75 0 001.06 0l4.5-4.5a.75.75 0 00-1.06-1.06z"/></svg>
  <?php echo e(session('success')); ?>

</div>
<?php endif; ?>
<?php if(session('error')): ?>
<div class="flash flash-err" role="alert">
  <svg width="15" height="15" viewBox="0 0 16 16" fill="currentColor" aria-hidden="true"><path d="M8 1a7 7 0 110 14A7 7 0 018 1zm0 3.5a.75.75 0 00-.75.75v3.5a.75.75 0 001.5 0v-3.5A.75.75 0 008 4.5zm0 6.5a.875.875 0 100 1.75A.875.875 0 008 11z"/></svg>
  <?php echo e(session('error')); ?>

</div>
<?php endif; ?>

<main id="main-content"><?php echo $__env->yieldContent('content'); ?></main>

<footer class="footer" role="contentinfo">
  <div class="ctr">
    <div class="footer__grid">
      <div>
        <a href="<?php echo e(route('home')); ?>" class="footer__logo">
          <div class="footer__mark" aria-hidden="true">🐾</div>
          <span class="footer__bname">Uni<strong>Animal</strong></span>
        </a>
        <p class="footer__desc">Plataforma SaaS veterinária premium com monitoramento inteligente de saúde animal. GPS em tempo real, alertas automáticos e coleira IoT para tutores e produtores de todo o Brasil.</p>
        <div class="footer__soc" aria-label="Redes sociais">
          <a href="#" class="footer__soc-a" aria-label="Instagram"><?php if (isset($component)) { $__componentOriginalf8901d640d6a9c957b5a2e11b0e3bf32 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalf8901d640d6a9c957b5a2e11b0e3bf32 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.icone-marca','data' => ['nome' => 'instagram']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('icone-marca'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['nome' => 'instagram']); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalf8901d640d6a9c957b5a2e11b0e3bf32)): ?>
<?php $attributes = $__attributesOriginalf8901d640d6a9c957b5a2e11b0e3bf32; ?>
<?php unset($__attributesOriginalf8901d640d6a9c957b5a2e11b0e3bf32); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalf8901d640d6a9c957b5a2e11b0e3bf32)): ?>
<?php $component = $__componentOriginalf8901d640d6a9c957b5a2e11b0e3bf32; ?>
<?php unset($__componentOriginalf8901d640d6a9c957b5a2e11b0e3bf32); ?>
<?php endif; ?></a>
          <a href="#" class="footer__soc-a" aria-label="Facebook"><?php if (isset($component)) { $__componentOriginalf8901d640d6a9c957b5a2e11b0e3bf32 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalf8901d640d6a9c957b5a2e11b0e3bf32 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.icone-marca','data' => ['nome' => 'facebook']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('icone-marca'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['nome' => 'facebook']); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalf8901d640d6a9c957b5a2e11b0e3bf32)): ?>
<?php $attributes = $__attributesOriginalf8901d640d6a9c957b5a2e11b0e3bf32; ?>
<?php unset($__attributesOriginalf8901d640d6a9c957b5a2e11b0e3bf32); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalf8901d640d6a9c957b5a2e11b0e3bf32)): ?>
<?php $component = $__componentOriginalf8901d640d6a9c957b5a2e11b0e3bf32; ?>
<?php unset($__componentOriginalf8901d640d6a9c957b5a2e11b0e3bf32); ?>
<?php endif; ?></a>
          <a href="https://wa.me/5519989393961" class="footer__soc-a" aria-label="WhatsApp" target="_blank" rel="noopener"><?php if (isset($component)) { $__componentOriginalf8901d640d6a9c957b5a2e11b0e3bf32 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalf8901d640d6a9c957b5a2e11b0e3bf32 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.icone-marca','data' => ['nome' => 'whatsapp']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('icone-marca'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['nome' => 'whatsapp']); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalf8901d640d6a9c957b5a2e11b0e3bf32)): ?>
<?php $attributes = $__attributesOriginalf8901d640d6a9c957b5a2e11b0e3bf32; ?>
<?php unset($__attributesOriginalf8901d640d6a9c957b5a2e11b0e3bf32); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalf8901d640d6a9c957b5a2e11b0e3bf32)): ?>
<?php $component = $__componentOriginalf8901d640d6a9c957b5a2e11b0e3bf32; ?>
<?php unset($__componentOriginalf8901d640d6a9c957b5a2e11b0e3bf32); ?>
<?php endif; ?></a>
          <a href="#" class="footer__soc-a" aria-label="YouTube"><?php if (isset($component)) { $__componentOriginalf8901d640d6a9c957b5a2e11b0e3bf32 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalf8901d640d6a9c957b5a2e11b0e3bf32 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.icone-marca','data' => ['nome' => 'youtube']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('icone-marca'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['nome' => 'youtube']); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalf8901d640d6a9c957b5a2e11b0e3bf32)): ?>
<?php $attributes = $__attributesOriginalf8901d640d6a9c957b5a2e11b0e3bf32; ?>
<?php unset($__attributesOriginalf8901d640d6a9c957b5a2e11b0e3bf32); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalf8901d640d6a9c957b5a2e11b0e3bf32)): ?>
<?php $component = $__componentOriginalf8901d640d6a9c957b5a2e11b0e3bf32; ?>
<?php unset($__componentOriginalf8901d640d6a9c957b5a2e11b0e3bf32); ?>
<?php endif; ?></a>
        </div>
      </div>
      <nav aria-label="Produto">
        <p class="footer__col-title">Produto</p>
        <div class="footer__links">
          <a href="<?php echo e(route('sobre')); ?>"        class="footer__link">Sobre</a>
          <a href="<?php echo e(route('diferenciais')); ?>" class="footer__link">Recursos</a>
          <a href="<?php echo e(route('produtos')); ?>"     class="footer__link">Produtos</a>
          <a href="<?php echo e(route('oferta')); ?>"       class="footer__link">Oferta especial</a>
        </div>
      </nav>
      <nav aria-label="Plataforma">
        <p class="footer__col-title">Plataforma</p>
        <div class="footer__links">
          <a href="<?php echo e(route('animais.index')); ?>" class="footer__link">Meus Animais</a>
          <a href="<?php echo e(route('dashboard')); ?>"      class="footer__link">Dashboard</a>
          <a href="<?php echo e(route('orcamento')); ?>"           class="footer__link">Orçamento Atacado</a>
          <a href="<?php echo e(route('depoimentos')); ?>"         class="footer__link">Depoimentos</a>
          <a href="<?php echo e(route('contactos')); ?>"           class="footer__link">Contato</a>
        </div>
      </nav>
      <address style="font-style:normal">
        <p class="footer__col-title">Contato</p>
        <div class="footer__ct">
          
          <div class="footer__ct-r"><span>📞</span><a href="tel:+5519989393961" style="color:inherit">(19) 98939-3961</a></div>
          <div class="footer__ct-r"><span><?php if (isset($component)) { $__componentOriginalf8901d640d6a9c957b5a2e11b0e3bf32 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalf8901d640d6a9c957b5a2e11b0e3bf32 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.icone-marca','data' => ['nome' => 'whatsapp','tamanho' => 16]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('icone-marca'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['nome' => 'whatsapp','tamanho' => 16]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalf8901d640d6a9c957b5a2e11b0e3bf32)): ?>
<?php $attributes = $__attributesOriginalf8901d640d6a9c957b5a2e11b0e3bf32; ?>
<?php unset($__attributesOriginalf8901d640d6a9c957b5a2e11b0e3bf32); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalf8901d640d6a9c957b5a2e11b0e3bf32)): ?>
<?php $component = $__componentOriginalf8901d640d6a9c957b5a2e11b0e3bf32; ?>
<?php unset($__componentOriginalf8901d640d6a9c957b5a2e11b0e3bf32); ?>
<?php endif; ?></span><a href="https://wa.me/5519989393961" target="_blank" rel="noopener" style="color:inherit">WhatsApp</a></div>
          <div class="footer__ct-r"><span>✉️</span><a href="mailto:unianimal@gmail.com" style="color:inherit">unianimal@gmail.com</a></div>
          <div class="footer__ct-r"><span>📍</span><span>Tambaú, São Paulo — Brasil</span></div>
          <div class="footer__ct-r"><span>🕐</span><span>Seg–Sex: 8h–18h</span></div>
        </div>
      </address>
    </div>
    <div class="footer__bot">
      <p class="footer__copy">&copy; <?php echo e(date('Y')); ?> <a href="<?php echo e(route('home')); ?>">UniAnimal</a>. Todos os direitos reservados.</p>
      <p class="footer__copy">Feito com ❤️ para o bem-estar animal · SaaS Veterinário Premium</p>
    </div>
  </div>
</footer>

<script src="<?php echo e(asset('js/app.js')); ?>"></script>
<script src="<?php echo e(asset('js/transitions.js')); ?>"></script>
<script src="<?php echo e(asset('js/theme.js')); ?>"></script>
<?php echo $__env->yieldPushContent('scripts'); ?>


<div vw class="enabled">
  <div vw-access-button class="active"></div>
  <div vw-plugin-wrapper>
    <div class="vw-plugin-top-wrapper"></div>
  </div>
</div>
<script src="https://vlibras.gov.br/app/vlibras-plugin.js"></script>
<script>
  new window.VLibras.Widget('https://vlibras.gov.br/app');
</script>
</body>
</html>
<?php /**PATH C:\Users\44648743806\Desktop\UltimoProjeto2.0\resources\views/layouts/app.blade.php ENDPATH**/ ?>