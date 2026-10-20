/* ══════════════════════════════════════════════════════════════
   UniAnimal — Theme Toggle (Dark ↔ Light)
   Persiste via localStorage — Tema padrão: LIGHT
   ══════════════════════════════════════════════════════════════ */

(function () {
  'use strict';

  const KEY = 'unianimal_theme';
  const html = document.documentElement;

  // Aplica tema salvo ou padrão light
  function applyTheme(theme) {
    html.setAttribute('data-theme', theme);
    document.querySelector('meta[name="theme-color"]')?.setAttribute(
      'content',
      theme === 'light' ? '#F0F4F8' : '#090B10'
    );
  }

  // Inicia com tema salvo (padrão: light)
  const saved = localStorage.getItem(KEY) || 'light';
  applyTheme(saved);

  // Toggle ao clicar no botão
  document.addEventListener('DOMContentLoaded', function () {
    const btn = document.getElementById('themeToggle');
    if (!btn) return;

    btn.addEventListener('click', function () {
      const current = html.getAttribute('data-theme') || 'dark';
      const next    = current === 'dark' ? 'light' : 'dark';
      applyTheme(next);
      localStorage.setItem(KEY, next);
    });
  });
})();
