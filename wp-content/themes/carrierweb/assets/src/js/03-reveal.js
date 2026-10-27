/**
 * Apparition des sections au défilement.
 * Le contenu reste visible lorsque JavaScript ou IntersectionObserver est indisponible.
 */
(function () {
  'use strict';

  var root = document.documentElement;

  function revealAll() {
    document.querySelectorAll('[data-reveal]').forEach(function (el) {
      el.classList.add('is-visible');
    });
  }

  function init() {
    // Pas d'IntersectionObserver (très anciens navigateurs) : tout est visible.
    if (!('IntersectionObserver' in window)) {
      revealAll();
      return;
    }

    if (window.matchMedia('(prefers-reduced-motion: reduce)').matches) {
      revealAll();
      return;
    }

    // À partir d'ici seulement, le CSS a le droit de masquer.
    root.classList.add('js-reveal-ready');

    var observer = new IntersectionObserver(
      function (entries) {
        entries.forEach(function (entry) {
          if (!entry.isIntersecting) return;

          entry.target.classList.add('is-visible');
          // Une seule fois : l'animation ne rejoue pas au défilement inverse.
          observer.unobserve(entry.target);
        });
      },
      { threshold: 0.15, rootMargin: '0px 0px -60px 0px' }
    );

    document.querySelectorAll('[data-reveal]').forEach(function (el) {
      var delay = el.getAttribute('data-reveal-delay');

      if (delay) el.style.setProperty('--reveal-delay', delay + 'ms');

      observer.observe(el);
    });
  }

  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', init);
  } else {
    init();
  }
})();
