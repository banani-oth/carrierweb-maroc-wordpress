/**
 * Progression de lecture et bouton de retour en haut.
 */
(function () {
  'use strict';

  var BACK_TO_TOP_AT = 600;

  function init() {
    var bar = document.querySelector('[data-cw-scroll-progress]');
    var backToTop = document.querySelector('[data-cw-back-to-top]');

    if (!bar && !backToTop) return;

    var ticking = false;

    function update() {
      var scrollTop = window.scrollY;
      var height = document.documentElement.scrollHeight - window.innerHeight;
      var pct = height > 0 ? (scrollTop / height) * 100 : 0;

      if (bar) bar.style.setProperty('--cw-progress', pct.toFixed(2) + '%');

      if (backToTop) {
        var show = scrollTop > BACK_TO_TOP_AT;

        // `hidden` sort l'élément du flux ; les classes gèrent le fondu.
        backToTop.classList.toggle('hidden', !show);
        backToTop.classList.toggle('flex', show);
        backToTop.classList.toggle('opacity-0', !show);
        backToTop.classList.toggle('pointer-events-none', !show);
      }

      ticking = false;
    }

    window.addEventListener(
      'scroll',
      function () {
        if (!ticking) {
          ticking = true;
          window.requestAnimationFrame(update);
        }
      },
      { passive: true }
    );

    window.addEventListener('resize', update, { passive: true });

    if (backToTop) {
      backToTop.addEventListener('click', function () {
        var reduced = window.matchMedia('(prefers-reduced-motion: reduce)').matches;

        window.scrollTo({ top: 0, behavior: reduced ? 'auto' : 'smooth' });
      });
    }

    update();
  }

  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', init);
  } else {
    init();
  }
})();
