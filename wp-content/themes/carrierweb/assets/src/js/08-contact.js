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
