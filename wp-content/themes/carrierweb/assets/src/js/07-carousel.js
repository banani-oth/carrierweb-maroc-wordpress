/**
 * Navigation entre les témoignages avec flèches et boutons de sélection.
 * Sans JavaScript, les témoignages restent affichés dans le document.
 */
(function () {
  'use strict';

  function init() {
    document.querySelectorAll('[data-cw-carousel]').forEach(function (carousel) {
      var slides = carousel.querySelectorAll('[data-cw-slide]');

      if (slides.length < 2) return;

      var controls = carousel.querySelector('[data-cw-carousel-controls]');
      var section = carousel.closest('section');
      var dotsWrap = section && section.querySelector('[data-cw-carousel-dots]');
      var dots = dotsWrap ? dotsWrap.querySelectorAll('[data-cw-carousel-dot]') : [];
      var current = 0;

      // Le CSS ne replie les diapos qu'une fois cette classe posée.
      document.documentElement.classList.add('js-carousel-ready');

      if (controls) controls.classList.remove('hidden');
      if (dotsWrap) dotsWrap.classList.remove('hidden');

      function show(index) {
        current = (index + slides.length) % slides.length;

        slides.forEach(function (slide, i) {
          slide.classList.toggle('is-active', i === current);
        });

        dots.forEach(function (dot, i) {
          var active = i === current;

          dot.classList.toggle('bg-cw-red', active);
          dot.classList.toggle('w-6', active);
          dot.classList.toggle('bg-cw-slate-700', !active);
          dot.classList.toggle('w-2', !active);
          dot.setAttribute('aria-current', active ? 'true' : 'false');
        });
      }

      var prev = carousel.querySelector('[data-cw-carousel-prev]');
      var next = carousel.querySelector('[data-cw-carousel-next]');

      if (prev) prev.addEventListener('click', function () { show(current - 1); });
      if (next) next.addEventListener('click', function () { show(current + 1); });

      dots.forEach(function (dot, i) {
        dot.addEventListener('click', function () { show(i); });
      });

      show(0);
    });
  }

  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', init);
  } else {
    init();
  }
})();
