<?php $__env->startSection('title','Produtos'); ?>

<?php $__env->startSection('content'); ?>
<div class="page-top sec-solid">
  <div class="ctr">
    <div class="sec-head reveal">
      <p class="kicker">🛒 Catálogo</p>
      <h1 class="sec-title sec-title--xxl">Escolha o produto <em>ideal</em><br>para o seu pet</h1>
      <p class="sec-sub">Três modelos com a mesma tecnologia UniAnimal, projetados para cada tipo de animal.</p>
    </div>
    <div class="prod-grid">
      <?php $__currentLoopData = [
        ['Pequeno','UniCollar Mini','R$899','R$799','ou 12x de R$66,58','pequeno',false,['Pescoço 15–35cm','Até 15kg','7 dias · IP67','GPS + ECG + Temp']],
        ['Médio',  'UniCollar Pro', 'R$999','R$879','ou 12x de R$73,25','medio', true, ['Pescoço 30–55cm','15–50kg','10 dias · IP68','GPS + ECG + Sono + IA']],
        ['Grande', 'UniCollar Max', 'R$1.199','R$1.099','ou 12x de R$91,58','grande',false,['Pescoço 50–100cm','Acima 50kg','14 dias · IP69K','GPS Rural 5km + ECG']],
      ]; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as [$porte,$name,$was,$price,$inst,$slug,$feat,$feats]): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
      <?php
        // Preço e parcela vêm da tabela produtos (mesma fonte da home).
        if ($p = ($produtos[$slug] ?? null)) { $price = $p->preco_formatado; $inst = $p->parcelamento; }
      ?>
      <article class="prod-card <?php echo e($feat ? 'prod-card--feat' : ''); ?> reveal <?php echo e($feat ? '' : 'd1'); ?>" aria-label="<?php echo e($name); ?>">
        <?php if($feat): ?><div class="prod-card__ribbon">⭐ Mais vendido</div><?php endif; ?>
        <div class="prod-card__body">
          <div class="prod-card__tier"><?php echo e($porte); ?> porte</div>
          <div class="prod-card__name"><?php echo e($name); ?></div>
          <div class="prod-card__was">De: <?php echo e($was); ?></div>
          <div class="prod-card__price"><?php echo e($price); ?></div>
          <div class="prod-card__inst"><?php echo e($inst); ?></div>
          <ul class="prod-card__feats">
            <?php $__currentLoopData = $feats; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $f): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <li class="prod-card__feat"><div class="prod-card__dot" aria-hidden="true"></div><?php echo e($f); ?></li>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
          </ul>
          <a href="<?php echo e(route('orcamento')); ?>?porte=<?php echo e($slug); ?>" class="btn <?php echo e($feat ? 'btn-p' : 'btn-g'); ?> btn-fw">Escolher produto</a>
        </div>
      </article>
      <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </div>
    <div style="text-align:center;margin-top:var(--s8);padding:var(--s4) var(--s8);background:var(--t-dim);border:1px solid var(--bt);border-radius:var(--r4);color:var(--t-ink);font-size:var(--tx-sm);font-weight:500;display:flex;align-items:center;justify-content:center;gap:var(--s3)" class="reveal">
      📐 Regulagem de tamanho inclusa — adapta-se a diferentes pescoços dentro de cada categoria.
    </div>
  </div>
</div>

<section class="sec sec-alt" aria-labelledby="promo-h">
  <div class="ctr">
    <div class="g2">
      <div class="reveal">
        <p class="kicker">🔥 Oferta especial</p>
        <h2 class="sec-title" id="promo-h">Aproveite nossa<br><em>oferta exclusiva</em></h2>
        <p style="color:var(--txt-2);line-height:1.72;margin-bottom:var(--s8);letter-spacing:-.008em">Por tempo limitado, UniCollar Pro com desconto especial. Até 12x sem juros no cartão ou 19% off no Pix.</p>
        <a href="<?php echo e(route('oferta')); ?>" class="btn btn-p btn-lg">🛒 Ver oferta especial</a>
      </div>
      <div class="reveal d2">
        <img src="<?php echo e(asset('images/Animais/animais-domesticos.jpg')); ?>" alt="Animais domésticos" class="img-r" style="aspect-ratio:4/3" loading="lazy">
      </div>
    </div>
  </div>
</section>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\44648743806\Desktop\UltimoProjeto2.0\resources\views/pages/produtos.blade.php ENDPATH**/ ?>