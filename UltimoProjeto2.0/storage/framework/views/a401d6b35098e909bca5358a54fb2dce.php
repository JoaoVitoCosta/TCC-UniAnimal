
<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag;

$__newAttributes = [];
$__propNames = \Illuminate\View\ComponentAttributeBag::extractPropNames((['nome', 'tamanho' => 18, 'cor' => 'marca']));

foreach ($attributes->all() as $__key => $__value) {
    if (in_array($__key, $__propNames)) {
        $$__key = $$__key ?? $__value;
    } else {
        $__newAttributes[$__key] = $__value;
    }
}

$attributes = new \Illuminate\View\ComponentAttributeBag($__newAttributes);

unset($__propNames);
unset($__newAttributes);

foreach (array_filter((['nome', 'tamanho' => 18, 'cor' => 'marca']), 'is_string', ARRAY_FILTER_USE_KEY) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
}

$__defined_vars = get_defined_vars();

foreach ($attributes->all() as $__key => $__value) {
    if (array_key_exists($__key, $__defined_vars)) unset($$__key);
}

unset($__defined_vars, $__key, $__value); ?>

<?php
  $atual = $cor === 'atual';
  $escuro = $cor === 'escuro';
?>

<svg <?php echo e($attributes->merge(['class' => 'icone-marca icone-marca--' . $nome])); ?>

     width="<?php echo e($tamanho); ?>" height="<?php echo e($tamanho); ?>" viewBox="<?php echo e($nome === 'google' ? '0 0 48 48' : '0 0 24 24'); ?>"
     aria-hidden="true" focusable="false" xmlns="http://www.w3.org/2000/svg">
  <?php switch($nome):
    case ('whatsapp'): ?>
      <path fill="<?php echo e($atual ? 'currentColor' : ($escuro ? '#075E54' : '#25D366')); ?>" d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/>
      <?php break; ?>

    <?php case ('instagram'): ?>
      <?php if (! ($atual)): ?>
        <defs>
          <radialGradient id="ig-grad" cx="30%" cy="107%" r="150%">
            <stop offset="0" stop-color="#FDF497"/>
            <stop offset=".05" stop-color="#FDF497"/>
            <stop offset=".45" stop-color="#FD5949"/>
            <stop offset=".6" stop-color="#D6249F"/>
            <stop offset=".9" stop-color="#285AEB"/>
          </radialGradient>
        </defs>
      <?php endif; ?>
      <rect x="1" y="1" width="22" height="22" rx="6.5" fill="<?php echo e($atual ? 'currentColor' : 'url(#ig-grad)'); ?>"/>
      <rect x="5.5" y="5.5" width="13" height="13" rx="4" fill="none" stroke="#fff" stroke-width="1.8"/>
      <circle cx="12" cy="12" r="3.1" fill="none" stroke="#fff" stroke-width="1.8"/>
      <circle cx="16.3" cy="7.7" r="1" fill="#fff"/>
      <?php break; ?>

    <?php case ('facebook'): ?>
      <circle cx="12" cy="12" r="12" fill="<?php echo e($atual ? 'currentColor' : '#0866FF'); ?>"/>
      <path fill="#fff" d="M13.4 24v-8.6h2.9l.45-3.4H13.4V9.86c0-.98.28-1.65 1.68-1.65h1.78V5.17A23.6 23.6 0 0 0 14.27 5c-2.57 0-4.33 1.57-4.33 4.45V12H7.04v3.4h2.9V24z"/>
      <?php break; ?>

    <?php case ('youtube'): ?>
      <rect x="0" y="4" width="24" height="16" rx="4.5" fill="<?php echo e($atual ? 'currentColor' : '#FF0000'); ?>"/>
      <path fill="#fff" d="M9.75 8.5v7l6-3.5z"/>
      <?php break; ?>

    <?php case ('google'): ?>
      <path fill="#EA4335" d="M24 9.5c3.54 0 6.71 1.22 9.21 3.6l6.85-6.85C35.9 2.38 30.47 0 24 0 14.62 0 6.51 5.38 2.56 13.22l7.98 6.19C12.43 13.72 17.74 9.5 24 9.5z"/>
      <path fill="#4285F4" d="M46.98 24.55c0-1.57-.15-3.09-.38-4.55H24v9.02h12.94c-.58 2.96-2.26 5.48-4.78 7.18l7.73 6c4.51-4.18 7.09-10.36 7.09-17.65z"/>
      <path fill="#FBBC05" d="M10.53 28.59c-.48-1.45-.76-2.99-.76-4.59s.27-3.14.76-4.59l-7.98-6.19C.92 16.46 0 20.12 0 24c0 3.88.92 7.54 2.56 10.78l7.97-6.19z"/>
      <path fill="#34A853" d="M24 48c6.48 0 11.93-2.13 15.89-5.81l-7.73-6c-2.15 1.45-4.92 2.3-8.16 2.3-6.26 0-11.57-4.22-13.47-9.91l-7.98 6.19C6.51 42.62 14.62 48 24 48z"/>
      <?php break; ?>
  <?php endswitch; ?>
</svg>
<?php /**PATH C:\Users\44648743806\Desktop\UltimoProjeto2.0\resources\views/components/icone-marca.blade.php ENDPATH**/ ?>