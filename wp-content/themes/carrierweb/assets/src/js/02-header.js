/**
 * En-tête : compression au défilement, menus au clavier et navigation mobile.
 * L’ouverture au survol reste gérée par le CSS.
 */
(function () {
  'use strict';

  var SCROLL_THRESHOLD = 20;

  /* ── Compression du header ────────────────────────────────────────── */
  function initScroll() {
    var pill = document.querySelector('[data-cw-header]');

    if (!pill) return;

    var ticking = false;

    function update() {
      pill.classList.toggle('is-scrolled', window.scrollY > SCROLL_THRESHOLD);
      ticking = false;
    }

    // rAF : le handler de scroll ne fait qu'planifier, jamais mesurer/écrire.
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

    update();
  }

  /* ── Largeur disponible : aucun lien ne déborde sur les actions ───── */
  function initFit() {
    var pill = document.querySelector('[data-cw-header]');
    var brand = document.querySelector('[data-cw-header-brand]');
    var nav = document.querySelector('[data-cw-header-nav]');
    var actions = document.querySelector('[data-cw-header-actions]');
    if (!pill || !brand || !nav || !actions) return;

    document.documentElement.classList.add('cw-header-js');
    var ticking = false;
    var previous = null;

    function update() {
      ticking = false;
      var expanded = false;
      // Use the same desktop threshold in every language; Arabic labels are shorter.
      if (window.matchMedia('(min-width: 90rem)').matches) {
        // Measure the desktop contents without painting a second navigation
        // or counting the compact menu button in the desktop action width.
        pill.setAttribute('data-cw-header-measuring', '');
        try {
          var style = window.getComputedStyle(pill);
          var gap = parseFloat(style.columnGap) || 0;
          var available = pill.clientWidth - (parseFloat(style.paddingLeft) || 0) - (parseFloat(style.paddingRight) || 0);
          var required = brand.getBoundingClientRect().width + nav.getBoundingClientRect().width + actions.getBoundingClientRect().width + gap * 2;
          expanded = required + 8 <= available;
        } finally {
          pill.removeAttribute('data-cw-header-measuring');
        }
      }
      pill.classList.toggle('is-expanded', expanded);
      pill.setAttribute('data-cw-header-layout', expanded ? 'desktop' : 'compact');
      if (expanded !== previous) {
        previous = expanded;
        document.dispatchEvent(new CustomEvent('cw:header-layout', { detail: { expanded: expanded } }));
      }
    }

    function schedule() {
      if (!ticking) {
        ticking = true;
        window.requestAnimationFrame(update);
      }
    }

    window.addEventListener('resize', schedule, { passive: true });
    if (window.ResizeObserver) {
      var observer = new window.ResizeObserver(schedule);
      observer.observe(pill);
      observer.observe(brand);
    }
    if (window.MutationObserver) {
      var mutations = new window.MutationObserver(schedule);
      mutations.observe(nav, { childList: true, characterData: true, subtree: true });
      mutations.observe(actions, { childList: true, characterData: true, subtree: true });
    }
    if (document.fonts) {
      document.fonts.ready.then(schedule);
      if (document.fonts.addEventListener) document.fonts.addEventListener('loadingdone', schedule);
    }
    schedule();
  }

  /* ── Menus déroulants : accès clavier ─────────────────────────────── */
  function initDropdowns() {
    var groups = document.querySelectorAll('[data-cw-dropdown]');

    groups.forEach(function (group) {
      var trigger = group.querySelector('[data-cw-dropdown-trigger]');

      if (!trigger) return;

      function open() {
        group.classList.add('is-open');
        trigger.setAttribute('aria-expanded', 'true');
      }

      function close() {
        group.classList.remove('is-open');
        trigger.setAttribute('aria-expanded', 'false');
      }

      // focusin/focusout couvrent le panneau entier, pas seulement le lien.
      group.addEventListener('focusin', open);
      group.addEventListener('focusout', function (event) {
        if (!group.contains(event.relatedTarget)) close();
      });

      group.addEventListener('mouseenter', open);
      group.addEventListener('mouseleave', close);

      group.addEventListener('keydown', function (event) {
        if (event.key === 'Escape') {
          close();
          trigger.focus();
        }
      });
    });
  }

  /* ── Menu mobile ──────────────────────────────────────────────────── */
  function initMobileMenu() {
    var menu = document.querySelector('[data-cw-mobile-menu]');
    var openBtn = document.querySelector('[data-cw-menu-open]');
    var closeBtn = document.querySelector('[data-cw-menu-close]');

    if (!menu || !openBtn) return;

    var lastFocused = null;

    function focusable() {
      return Array.prototype.filter.call(
        menu.querySelectorAll('a[href], button:not([disabled])'),
        function (el) { return el.offsetParent !== null; }
      );
    }

    function open() {
      lastFocused = document.activeElement;
      menu.classList.add('open');
      openBtn.setAttribute('aria-expanded', 'true');
      // Empêche le défilement de la page derrière le menu plein écran.
      document.body.style.overflow = 'hidden';

      var items = focusable();
      if (items.length) items[0].focus();
    }

    function close(restoreFocus) {
      menu.classList.remove('open');
      openBtn.setAttribute('aria-expanded', 'false');
      document.body.style.overflow = '';

      if (restoreFocus !== false && lastFocused && lastFocused.offsetParent !== null) lastFocused.focus();
    }

    document.addEventListener('cw:header-layout', function (event) {
      if (!event.detail.expanded || !menu.classList.contains('open')) return;
      close(false);
      var firstLink = document.querySelector('[data-cw-header-nav] a[href]');
      if (firstLink) firstLink.focus();
    });

    openBtn.addEventListener('click', open);
    if (closeBtn) closeBtn.addEventListener('click', close);

    // Naviguer referme le menu (sinon il reste ouvert au retour arrière).
    menu.addEventListener('click', function (event) {
      if (event.target.closest('a[href]')) close();
    });

    document.addEventListener('keydown', function (event) {
      if (!menu.classList.contains('open')) return;

      if (event.key === 'Escape') {
        close();
        return;
      }

      // Piège à focus : obligatoire pour un dialogue aria-modal.
      if (event.key === 'Tab') {
        var items = focusable();
        if (!items.length) return;

        var first = items[0];
        var last = items[items.length - 1];

        if (event.shiftKey && document.activeElement === first) {
          event.preventDefault();
          last.focus();
        } else if (!event.shiftKey && document.activeElement === last) {
          event.preventDefault();
          first.focus();
        }
      }
    });

    /* Sections dépliables du menu mobile. */
    menu.querySelectorAll('[data-cw-mobile-toggle]').forEach(function (toggle) {
      var item = toggle.closest('[data-cw-mobile-item]');
      var panel = item && item.querySelector('[data-cw-mobile-panel]');

      if (!panel) return;

      toggle.addEventListener('click', function () {
        var expanded = toggle.getAttribute('aria-expanded') === 'true';

        toggle.setAttribute('aria-expanded', expanded ? 'false' : 'true');
        panel.classList.toggle('hidden', expanded);

        var label = expanded ? toggle.dataset.labelExpand : toggle.dataset.labelCollapse;
        if (label) toggle.setAttribute('aria-label', label);
      });
    });
  }

  function init() {
    initScroll();
    initFit();
    initDropdowns();
    initMobileMenu();
  }

  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', init);
  } else {
    init();
  }
})();
