/**
 * Vidéo de démonstration : lecture dans le viewport et pause à la sortie.
 * Les contrôles restent disponibles. La préférence de réduction des animations désactive la lecture automatique.
 */
(function () {
  'use strict';

  function init() {
    var section = document.querySelector('[data-cw-video-section]');
    var video = document.querySelector('[data-cw-video]');

    if (!section || !video || !('IntersectionObserver' in window)) return;

    // Lecture muette en boucle, avec contrôles accessibles.
    video.controls = true;
    if (window.matchMedia && window.matchMedia("(prefers-reduced-motion: reduce)").matches) return;
    video.loop = true;
    video.muted = true;

    var observer = new IntersectionObserver(
      function (entries) {
        entries.forEach(function (entry) {
          if (entry.isIntersecting) {
            // play() peut être rejeté (politique d'autoplay) : on rend alors
            // les contrôles pour que la vidéo reste lançable à la main.
            var p = video.play();

            if (p && typeof p.catch === 'function') {
              p.catch(function () { video.controls = true; });
            }
          } else {
            video.pause();
          }
        });
      },
      { threshold: 0.25 }
    );

    observer.observe(section);
  }

  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', init);
  } else {
    init();
  }
})();
