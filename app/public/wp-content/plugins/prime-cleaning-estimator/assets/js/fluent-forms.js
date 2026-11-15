/* Only integration state lives here. Fluent Forms renders, validates and submits fields. */
(function ($) {
  'use strict';
  $(function () {
    document.querySelectorAll('.prime-fluent-form form').forEach(form => {
      const key = form.elements.request_key;
      if (key && !key.value) {
        const bytes = new Uint8Array(24);
        crypto.getRandomValues(bytes);
        key.value = Array.from(bytes, b => b.toString(16).padStart(2, '0')).join('');
      }
      const date = form.elements.preferred_date;
      if (date && window.primeFluentForms) date.min = window.primeFluentForms.today;
    });
    $(document.body).on('fluentform_submission_success', function (_, event) {
      const form = event.form && event.form[0];
      if (!form || !form.closest('.prime-fluent-form')) return;
      document.dispatchEvent(new CustomEvent('prime:enquiry-saved', { detail: { form } }));
    });
  });
})(jQuery);
