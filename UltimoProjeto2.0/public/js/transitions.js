/* UniAnimal transitions.js v7 */
(function(){
  'use strict';
  const veil=document.getElementById('page-veil');
  const bar=document.getElementById('progress-bar');
  const dot=document.getElementById('cursor-dot');
  let bw=0,bt=null;

  function barStart(){bw=0;if(bar){bar.style.opacity='1';bar.classList.remove('is-done')}clearInterval(bt);bt=setInterval(()=>{bw=Math.min(bw+(90-bw)*.08,90);if(bar)bar.style.width=bw+'%'},40)}
  function barEnd(){clearInterval(bt);if(bar){bar.style.width='100%';setTimeout(()=>bar.classList.add('is-done'),280)}}

  function reveal(){
    if(!veil)return;
    veil.classList.remove('is-visible');
    requestAnimationFrame(()=>requestAnimationFrame(()=>{veil.classList.add('is-hidden');barEnd()}));
  }
  function hide(cb){if(!veil){cb();return}veil.classList.remove('is-hidden');veil.classList.add('is-visible');setTimeout(cb,400)}

  document.addEventListener('click',function(e){
    const a=e.target.closest('a[href]');
    if(!a)return;
    const h=a.getAttribute('href');
    if(!h||h.startsWith('#')||h.startsWith('javascript')||h.startsWith('mailto:')||h.startsWith('tel:')||h.startsWith('http')||a.hasAttribute('download')||a.target==='_blank'||e.metaKey||e.ctrlKey||e.shiftKey||e.altKey)return;
    e.preventDefault();barStart();hide(()=>{window.location.href=h});
  });

  document.addEventListener('submit',()=>barStart());

  /* Cursor dot */
  if(dot&&window.matchMedia('(pointer:fine)').matches){
    let cx=0,cy=0,dx=0,dy=0;
    dot.classList.add('is-active');
    document.addEventListener('mousemove',e=>{cx=e.clientX;cy=e.clientY});
    document.addEventListener('mouseleave',()=>dot.classList.remove('is-active'));
    document.addEventListener('mouseenter',()=>dot.classList.add('is-active'));
    document.querySelectorAll('a,button,[role="button"]').forEach(el=>{
      el.addEventListener('mouseenter',()=>dot.classList.add('is-hover'));
      el.addEventListener('mouseleave',()=>dot.classList.remove('is-hover'));
    });
    (function loop(){dx+=(cx-dx)*.18;dy+=(cy-dy)*.18;dot.style.left=dx+'px';dot.style.top=dy+'px';requestAnimationFrame(loop)})();
  }

  /* Button ripple */
  document.querySelectorAll('.btn').forEach(btn=>{
    btn.addEventListener('click',function(e){
      const r=this.getBoundingClientRect();
      const sz=Math.max(r.width,r.height)*2;
      const x=e.clientX-r.left-sz/2,y=e.clientY-r.top-sz/2;
      const rpl=document.createElement('span');
      rpl.className='btn-ripple';
      rpl.style.cssText=`width:${sz}px;height:${sz}px;left:${x}px;top:${y}px`;
      this.appendChild(rpl);setTimeout(()=>rpl.remove(),520);
    });
  });

  /* Scroll reveal */
  if('IntersectionObserver' in window){
    const obs=new IntersectionObserver(es=>{
      es.forEach(e=>{if(e.isIntersecting){e.target.classList.add('v');obs.unobserve(e.target)}});
    },{threshold:.07,rootMargin:'0px 0px -44px 0px'});
    document.querySelectorAll('.reveal,.reveal-l,.reveal-r').forEach(el=>obs.observe(el));
  }else{
    document.querySelectorAll('.reveal,.reveal-l,.reveal-r').forEach(el=>el.classList.add('v'));
  }

  /* Init */
  if(document.readyState==='loading')document.addEventListener('DOMContentLoaded',reveal);
  else reveal();
  window.addEventListener('pageshow',e=>{if(e.persisted&&veil){veil.classList.remove('is-visible');veil.classList.add('is-hidden')}});
})();
