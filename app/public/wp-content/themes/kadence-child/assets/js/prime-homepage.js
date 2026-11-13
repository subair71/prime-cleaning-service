/* Homepage navigation and form presentation. Pricing remains in the existing plugin. */
document.addEventListener("DOMContentLoaded", function () {
  "use strict";
  const toggle = document.querySelector(".prime-menu-toggle");
  const navigation = document.getElementById("prime-home-navigation");
  if (toggle && navigation) {
    const mobile = window.matchMedia("(max-width: 1080px)");
    const close = function (restoreFocus) {
      toggle.setAttribute("aria-expanded", "false");
      navigation.classList.remove("is-open");
      if (restoreFocus) toggle.focus();
    };
    toggle.hidden = false;
    navigation.classList.add("is-enhanced");
    toggle.addEventListener("click", function () {
      const open = toggle.getAttribute("aria-expanded") !== "true";
      toggle.setAttribute("aria-expanded", String(open));
      navigation.classList.toggle("is-open", open);
    });
    navigation.addEventListener("click", function (event) {
      if (event.target.closest("a")) close(false);
    });
    document.addEventListener("keydown", function (event) {
      if (
        event.key === "Escape" &&
        toggle.getAttribute("aria-expanded") === "true"
      )
        close(true);
    });
    document.addEventListener("click", function (event) {
      if (!event.target.closest(".prime-home-header")) close(false);
    });
    const resize = function () {
      close(false);
    };
    if (mobile.addEventListener) mobile.addEventListener("change", resize);
    else mobile.addListener(resize);
  }
  document.querySelectorAll(".prime-quick-quote").forEach(function (form) {
    const service = form.querySelector('[name="prime_service"]');
    const bedrooms = form.querySelector('[name="prime_bedrooms"]');
    const size = form.querySelector(".prime-home-size");
    const officeNote = form.querySelector(".prime-office-note");
    const sync = function () {
      const office = service.value === "office" || service.value === "carpet";
      size.hidden = office;
      bedrooms.disabled = office;
      officeNote.hidden = service.value !== "office";
    };
    service.addEventListener("change", sync);
    sync();
    form.addEventListener("submit", function (event) {
      if (form.dataset.demo === "true") event.preventDefault();
      else {
        event.preventDefault();
        const destination = new URL(form.action, window.location.href);
        new FormData(form).forEach((value, key) => destination.searchParams.set(key, value));
        destination.hash = "estimate";
        window.location.assign(destination.href);
      }
    });
  });
});
