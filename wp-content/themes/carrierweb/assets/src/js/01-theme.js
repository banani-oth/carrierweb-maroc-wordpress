/**
 * Choix du thème clair ou sombre, mémorisé dans le navigateur.
 * Le libellé du bouton annonce toujours la prochaine action.
 */
(function () {
  'use strict';

  var STORAGE_KEY = 'cw-theme';

  function currentTheme() {
    return document.documentElement.dataset.theme === 'light' ? 'light' : 'dark';
  }

  /** Le libellé doit annoncer l'action, pas l'état courant. */
  function syncLabel(button) {
    var next = currentTheme() === 'dark' ? 'light' : 'dark';
    var label = next === 'light' ? button.dataset.labelLight : button.dataset.labelDark;

    if (label) {
      button.setAttribute('aria-label', label);
      button.setAttribute('title', label);
    }
  }

  function init() {
    var buttons = document.querySelectorAll('[data-cw-theme-toggle]');

    if (!buttons.length) return;

    buttons.forEach(function (button) {
      syncLabel(button);

      button.addEventListener('click', function () {
        var next = currentTheme() === 'dark' ? 'light' : 'dark';

        document.documentElement.dataset.theme = next;

        try {
          localStorage.setItem(STORAGE_KEY, next);
        } catch (e) {
          /* Stockage indisponible (navigation privée) : on continue sans persister. */
        }

        // Le header et le menu mobile ont chacun leur bouton : garder les deux à jour.
        buttons.forEach(syncLabel);
      });
    });
  }

  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', init);
  } else {
    init();
  }
})();
