<?php $__env->startSection('title','Oferta Especial'); ?>

<?php $__env->startSection('content'); ?>
<div class="page-top">
  <div class="ctr">
    <div style="text-align:center;margin-bottom:var(--s12)" class="reveal">
      <p class="kicker" style="margin-left:auto;margin-right:auto">🔥 Tempo limitado</p>
      <h1 class="sec-title sec-title--xxl" style="margin-top:var(--s3)">A liberdade que eles amam,<br>com a <em>segurança</em> que você precisa.</h1>
      <div style="display:flex;flex-wrap:wrap;gap:var(--s2);justify-content:center;margin-top:var(--s5)">
        <?php $__currentLoopData = ['📍 GPS','💓 Saúde','📊 Monitoramento','🔒 Segurança']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $t): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <span class="chip chip-ok" style="padding:5px 14px"><?php echo e($t); ?></span>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
      </div>
    </div>

    <div class="g2a">
      <div class="reveal">
        <div class="price-box">
          <div class="price-box__tag">🔥 Oferta especial · Tempo limitado</div>
          <div class="price-box__was">De: R$ 899,00</div>
          <div class="price-box__val" aria-label="Por R$799">R$ 799</div>
          <div class="price-box__inst">ou <strong style="color:var(--txt)">12x de R$ 41,66</strong> sem juros<br>ou <strong style="color:var(--t-ink)">R$ 650 no Pix</strong> (19% de desconto)</div>
          <a href="<?php echo e(route('orcamento')); ?>" class="btn btn-p btn-fw btn-lg">🛒 Comprar agora</a>
          <div class="price-box__inc">
            <p style="font-family:var(--f-display);font-size:var(--tx-xs);font-weight:800;text-transform:uppercase;letter-spacing:.08em;color:var(--txt-3);margin-bottom:var(--s3)">O que está incluído</p>
            <?php $__currentLoopData = ['UniCollar Pro (escolha o porte)','Aplicativo iOS & Android','1 ano de monitoramento','Suporte 24/7','Rede de veterinários parceiros','Garantia de 2 anos']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $inc): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <div class="price-box__row"><span class="price-box__ck" aria-hidden="true">✓</span><?php echo e($inc); ?></div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
          </div>
        </div>
      </div>
      <div class="reveal d2">
        <img src="<?php echo e(asset('images/Animais/animais-domesticos.jpg')); ?>" alt="Animais" class="img-r" style="aspect-ratio:4/3" loading="lazy">
      </div>
    </div>

    <div style="margin-top:var(--s16)" class="reveal">
      <div style="text-align:center;margin-bottom:var(--s8)">
        <h2 class="sec-title">Formas de <em>pagamento</em></h2>
      </div>
      <div class="pay-grid">
        <?php $__currentLoopData = [['💳','Cartão','Até 12x sem juros'],['📄','Boleto','Vencimento em 3 dias'],['⚡','Pix','19% de desconto']]; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as [$i,$n,$d]): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <a href="<?php echo e(route('orcamento')); ?>" class="pay-card"><div class="pay-card__icon" aria-hidden="true"><?php echo e($i); ?></div><div class="pay-card__name"><?php echo e($n); ?></div><div class="pay-card__desc"><?php echo e($d); ?></div></a>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
      </div>
    </div>

    <div class="g3" style="margin-top:var(--s12);padding-top:var(--s12);border-top:1px solid var(--b)" class="reveal">
      <?php $__currentLoopData = [['🛡️','Garantia 2 anos','Hardware coberto. Troca sem burocracia.'],['↩️','Devolução 30 dias','Não ficou satisfeito? 100% do valor.'],['🔒','Compra segura','Pagamento criptografado.']]; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as [$i,$t,$d]): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
      <div style="text-align:center;padding:var(--s5)" class="reveal">
        <div style="font-size:2rem;margin-bottom:var(--s3)" aria-hidden="true"><?php echo e($i); ?></div>
        <div style="font-family:var(--f-display);font-size:var(--tx-md);font-weight:700;letter-spacing:-.025em;margin-bottom:6px"><?php echo e($t); ?></div>
        <p style="font-size:var(--tx-sm);color:var(--txt-2);line-height:1.62;letter-spacing:-.005em"><?php echo e($d); ?></p>
      </div>
      <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </div>
  </div>
</div>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\44648743806\Desktop\UltimoProjeto2.0\resources\views/pages/oferta.blade.php ENDPATH**/ ?>