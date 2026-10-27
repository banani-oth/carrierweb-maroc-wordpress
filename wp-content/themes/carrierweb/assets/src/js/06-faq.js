/**
 * Accordéons FAQ avec un seul panneau ouvert à la fois.
 * Sans JavaScript, toutes les réponses restent visibles.
 */
(function () {
  'use strict';

  function init() {
    var groups = document.querySelectorAll('[data-cw-faq]');

    if (!groups.length) return;

    // À partir d'ici, le CSS a le droit de replier les panneaux.
    document.documentElement.classList.add('js-faq-ready');

    groups.forEach(function (group) {
      var items = group.querySelectorAll('.faq-item');

      items.forEach(function (item) {
        var toggle = item.querySelector('[data-cw-faq-toggle]');

        if (!toggle) return;

        // État initial : tous les panneaux sont repliés.
        toggle.setAttribute('aria-expanded', 'false');
        item.classList.remove('open');

        toggle.addEventListener('click', function () {
          var isOpen = toggle.getAttribute('aria-expanded') === 'true';

          // Un seul panneau ouvert à la fois.
          items.forEach(function (other) {
            var otherToggle = other.querySelector('[data-cw-faq-toggle]');

            if (otherToggle) otherToggle.setAttribute('aria-expanded', 'false');
            other.classList.remove('open');
          });

          if (!isOpen) {
            toggle.setAttribute('aria-expanded', 'true');
            item.classList.add('open');
          }
        });
      });
    });
  }

  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', init);
  } else {
    init();
  }
})();
