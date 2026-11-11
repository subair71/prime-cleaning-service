/* Set only existing inputs; the original controller still validates and calculates. */
document.addEventListener("DOMContentLoaded", function () {
  "use strict";
  const form = document.getElementById("pce-estimator-form");
  if (!form) return;
  const parameters = new URLSearchParams(window.location.search);
  const service = parameters.get("prime_service");
  if (!["regular", "deep", "lease", "office"].includes(service)) return;
  const radio = form.querySelector(
    'input[name="service"][value="' + service + '"]',
  );
  if (!radio) return;
  radio.checked = true;
  radio.dispatchEvent(new Event("change", { bubbles: true }));
  const bedrooms = parameters.get("prime_bedrooms");
  const select = document.getElementById("pce_bedrooms");
  if (
    service !== "office" &&
    select &&
    /^[1-6]$/.test(bedrooms || "") &&
    Array.from(select.options).some(function (option) {
      return option.value === bedrooms;
    })
  ) {
    select.value = bedrooms;
    select.dispatchEvent(new Event("change", { bubbles: true }));
  }
});
