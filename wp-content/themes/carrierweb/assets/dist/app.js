/*!
 * CarrierWeb Maroc — JavaScript du thème (FICHIER GÉNÉRÉ).
 * Source : assets/src/js/ — régénérer avec `npm run build:js`.
 * Modules : 01-theme.js, 02-header.js, 03-reveal.js, 04-scroll-progress.js, 05-video.js, 06-faq.js, 07-carousel.js, 08-contact.js, 09-auth.js
 */

/* ── 01-theme.js ── */
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


/* ── 02-header.js ── */
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


/* ── 03-reveal.js ── */
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


/* ── 04-scroll-progress.js ── */
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


/* ── 05-video.js ── */
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


/* ── 06-faq.js ── */
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


/* ── 07-carousel.js ── */
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


/* ── 08-contact.js ── */
/** Contact form enhancement. Native POST remains available with JavaScript disabled. */
(function () {
  'use strict';

  function init() {
    var form = document.querySelector('[data-cw-contact-form]');
    var success = document.querySelector('[data-cw-contact-success]');
    if (!form || !success) return;

    var button = form.querySelector('button[type="submit"]');
    var label = form.querySelector('[data-cw-submit-label]');
    var error = form.querySelector('[data-cw-contact-error]');
    var messages = {};
    var submitting = false;
    try { messages = JSON.parse(form.dataset.cwContactMessages || '{}'); } catch (_) { /* Server-rendered fallback remains available. */ }

    function showError(code) {
      if (!error) return;
      error.textContent = messages[code] || messages.unknown || 'The response could not be confirmed. Contact us directly before retrying.';
      error.classList.remove('hidden');
      error.focus();
    }

    form.addEventListener('submit', function (event) {
      if (!form.checkValidity()) return;
      event.preventDefault();
      if (submitting) return;
      submitting = true;
      if (error) error.classList.add('hidden');
      if (button) button.disabled = true;
      if (label && button && button.dataset.labelSending) label.textContent = button.dataset.labelSending;

      fetch(form.action, {
        method: 'POST',
        body: new FormData(form),
        headers: { 'X-Requested-With': 'XMLHttpRequest' },
        credentials: 'same-origin',
      })
        .then(function (response) {
          // A login page/proxy error returning HTTP 200 is not a successful enquiry.
          return response.json().then(function (result) {
            if (!response.ok || !result || result.ok !== true || result.code !== 'sent') {
              var failure = new Error(result && result.code ? result.code : 'unknown');
              failure.code = result && result.code ? result.code : 'unknown';
              throw failure;
            }
            return result;
          });
        })
        .then(function () {
          form.classList.add('hidden');
          success.classList.remove('hidden');
          success.setAttribute('tabindex', '-1');
          success.focus();
          success.scrollIntoView({ behavior: window.matchMedia('(prefers-reduced-motion: reduce)').matches ? 'auto' : 'smooth', block: 'center' });
        })
        .catch(function (failure) {
          // Keep every field. A lost response may follow an accepted message: never replay automatically.
          showError(failure && failure.code ? failure.code : 'unknown');
        })
        .finally(function () {
          submitting = false;
          if (button) button.disabled = false;
          if (label && button && button.dataset.labelIdle) label.textContent = button.dataset.labelIdle;
        });
    });
  }

  if (document.readyState === 'loading') document.addEventListener('DOMContentLoaded', init);
  else init();
})();


/* ── 09-auth.js ── */
/**
 * Point d’entrée conservé pour l’ordre de compilation des modules.
 * Les pages d’accès utilisent les liens du portail et du formulaire de contact.
 */
