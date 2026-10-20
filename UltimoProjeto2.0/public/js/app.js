/* UniAnimal app.js v7 */
(function () {
  'use strict';
  document.addEventListener('DOMContentLoaded', init);

  function init() {
    navScroll(); mobileMenu(); activeLinks();
    counters(); flashDismiss(); starsHover();
    ecgAnim(); sidebarToggle(); anchors();
  }

  function navScroll() {
    const nav = document.getElementById('navbar');
    if (!nav) return;
    const fn = () => nav.classList.toggle('scrolled', window.scrollY > 24);
    window.addEventListener('scroll', fn, { passive: true });
    fn();
  }

  function mobileMenu() {
    const burger = document.getElementById('nav-burger');
    const links  = document.getElementById('nav-links');
    if (!burger || !links) return;
    burger.addEventListener('click', function () {
      const open = links.classList.toggle('open');
      burger.setAttribute('aria-expanded', open);
      const [a,b,c] = burger.querySelectorAll('span');
      a.style.transform = open ? 'rotate(45deg) translate(5px,5px)' : '';
      b.style.opacity   = open ? '0' : '1';
      c.style.transform = open ? 'rotate(-45deg) translate(5px,-5px)' : '';
    });
    document.addEventListener('click', e => {
      if (!document.querySelector('.nav')?.contains(e.target)) {
        links.classList.remove('open');
        burger.setAttribute('aria-expanded','false');
        burger.querySelectorAll('span').forEach(s=>{s.style.transform='';s.style.opacity='1';});
      }
    });
  }

  function activeLinks() {
    const p = window.location.pathname;
    document.querySelectorAll('.nav__link[href]').forEach(a => {
      if (a.getAttribute('href') === p) a.classList.add('active');
    });
  }

  function counters() {
    if (!('IntersectionObserver' in window)) return;
    const obs = new IntersectionObserver(es => {
      es.forEach(e => { if (e.isIntersecting) { run(e.target); obs.unobserve(e.target); }});
    }, { threshold: 0.5 });
    document.querySelectorAll('[data-count]').forEach(el => obs.observe(el));
  }
  function run(el) {
    if (el.dataset.done) return; el.dataset.done = '1';
    const t = parseInt(el.dataset.count, 10);
    const s = el.dataset.suffix || '';
    const d = 1700, t0 = performance.now();
    const tick = now => {
      const p = Math.min((now-t0)/d,1);
      el.textContent = Math.floor((1-Math.pow(1-p,3))*t)+s;
      if (p<1) requestAnimationFrame(tick);
    };
    requestAnimationFrame(tick);
  }

  function flashDismiss() {
    document.querySelectorAll('.flash').forEach(el => {
      if (getComputedStyle(el).position !== 'fixed') return;
      setTimeout(() => {
        el.style.transition = 'opacity .4s ease, transform .4s ease';
        el.style.opacity = '0';
        el.style.transform = 'translateX(-50%) translateY(-8px)';
        setTimeout(() => el.remove(), 420);
      }, 5000);
    });
  }

  function starsHover() {
    document.querySelectorAll('.stars-wrap').forEach(g => {
      const ls = g.querySelectorAll('label');
      ls.forEach((lb,i) => lb.addEventListener('mouseenter', () => {
        ls.forEach((l,j) => { l.style.color = j>=i ? '#F59E0B' : ''; l.style.filter = j>=i ? 'none' : ''; });
      }));
      g.addEventListener('mouseleave', () => ls.forEach(l => { l.style.color=''; l.style.filter=''; }));
    });
  }

  function ecgAnim() {
    document.querySelectorAll('.monitor__ecg polyline').forEach(line => {
      line.style.strokeDasharray  = '300';
      line.style.strokeDashoffset = '300';
      line.style.animation = 'ecgAnim 2.2s linear infinite';
    });
  }

  function sidebarToggle() {
    const btn  = document.getElementById('sidebar-btn');
    const side = document.getElementById('sidebar');
    if (!btn || !side) return;
    btn.addEventListener('click', () => side.classList.toggle('open'));
    document.addEventListener('click', e => {
      if (side.classList.contains('open') && !side.contains(e.target) && !btn.contains(e.target)) {
        side.classList.remove('open');
      }
    });
  }

  function anchors() {
    document.querySelectorAll('a[href^="#"]').forEach(a => {
      a.addEventListener('click', function(e) {
        const t = document.querySelector(this.getAttribute('href'));
        if (t) { e.preventDefault(); window.scrollTo({ top: t.offsetTop-72, behavior:'smooth' }); }
      });
    });
  }
})();
