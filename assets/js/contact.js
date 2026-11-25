/**
 * Contact form: instant validation, AJAX submission (fetch), loading state,
 * duplicate-submit prevention and success/error messages.
 * The server (actions/contact-submit.php) re-validates everything.
 */
(function () {
  "use strict";
  const form = document.getElementById("contactForm");
  if (!form) return;

  const RULES = {
    name: (v) => (v.length < 2 ? "Please enter your full name." : /^[\p{L}\p{M} .'\-]+$/u.test(v) ? "" : "Please use letters only in your name."),
    email: (v) => (/^[^\s@]+@[^\s@]+\.[^\s@]{2,}$/.test(v) ? "" : "Please enter a valid email address."),
    phone: (v) => { const d = v.replace(/\D/g, ""); return /^[+()\d\s-]{7,20}$/.test(v) && d.length >= 7 && d.length <= 15 ? "" : "Please enter a valid phone number."; },
    industry: (v) => (v ? "" : "Please choose your industry."),
    service: (v) => (v ? "" : "Please choose a service."),
    subject: (v) => (v.length < 3 ? "Please add a short subject." : ""),
    message: (v) => (v.length < 10 ? "Please tell us a little more (at least 10 characters)." : "")
  };
  const btn = form.querySelector("button[type=submit]");
  const status = form.querySelector(".form__status");
  let sending = false;

  function setError(name, msg) {
    const input = form.elements[name];
    const field = input?.closest?.(".field");
    const err = field ? field.querySelector(".field__err") : form.querySelector(`[data-err="${name}"]`);
    field?.classList.toggle("is-invalid", !!msg);
    if (input && input.setAttribute) input.setAttribute("aria-invalid", msg ? "true" : "false");
    if (err) err.textContent = msg || "";
  }
  const val = (n) => String(form.elements[n]?.value || "").trim();
  function validate() {
    let first = null;
    Object.entries(RULES).forEach(([k, rule]) => { const m = rule(val(k)); setError(k, m); if (m && !first) first = k; });
    const c = form.elements.consent.checked ? "" : "Please confirm we may contact you.";
    setError("consent", c); if (c && !first) first = "consent";
    if (first) form.elements[first]?.focus();
    return !first;
  }
  function show(type, msg) { status.className = "form__status" + (type ? " is-" + type : ""); status.textContent = msg || ""; }

  form.addEventListener("focusout", (e) => { const r = RULES[e.target.name]; if (r) setError(e.target.name, r(String(e.target.value || "").trim())); });
  form.addEventListener("input", (e) => { const r = RULES[e.target.name]; if (r && e.target.closest(".field")?.classList.contains("is-invalid")) setError(e.target.name, r(String(e.target.value || "").trim())); });

  form.addEventListener("submit", async (e) => {
    e.preventDefault();
    if (sending) return;                       // duplicate-click protection
    show("", "");
    if (!validate()) { show("error", "Please correct the highlighted fields."); return; }
    sending = true; btn.disabled = true; btn.classList.add("is-loading");
    try {
      const res = await fetch(form.action, { method: "POST", body: new FormData(form), headers: { "X-Requested-With": "XMLHttpRequest", Accept: "application/json" }, credentials: "same-origin" });
      const data = await res.json().catch(() => ({ ok: false, message: "Unexpected server response. Please try again." }));
      Object.keys(RULES).concat("consent").forEach((k) => setError(k, ""));
      if (data.errors) Object.entries(data.errors).forEach(([k, m]) => setError(k, m));
      if (data.ok) {
        show("success", data.message);
        form.reset();
        form.querySelectorAll("select").forEach((s) => (s.selectedIndex = 0));
        btn.querySelector(".btn__label").textContent = "Sent ✓";
        setTimeout(() => { btn.querySelector(".btn__label").textContent = "Send enquiry"; }, 6000);
      } else {
        show("error", data.message || "Something went wrong. Please try again.");
      }
    } catch (err) {
      show("error", "Network error — please check your connection and try again.");
    } finally {
      sending = false; btn.disabled = false; btn.classList.remove("is-loading");
    }
  });
})();
