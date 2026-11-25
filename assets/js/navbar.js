/**
 * DREAM SPARK — Advanced navbar effects
 * Sliding gold pill behind links · magnetic WhatsApp/CTA · CTA fill from the cursor side ·
 * reading-progress line · mobile menu reveals from the toggle button.
 * Scroll state (is-scrolled / is-hidden) and menu open/close stay in main.js.
 */
(function () {
  "use strict";
  const nav = document.querySelector("[data-xn]");
  if (!nav) return;
  const fine = matchMedia("(hover: hover) and (pointer: fine)").matches;
  const reduced = matchMedia("(prefers-reduced-motion: reduce)").matches;

  /* ---------- sliding pill ---------- */
  const links = nav.querySelector("[data-xn-links]");
  const pill = nav.querySelector("[data-xn-pill]");
  const active = () => links?.querySelector(".xn__link.is-active");
  function moveTo(el, instant) {
    if (!pill || !el) { if (pill) pill.style.opacity = "0"; return; }
    if (instant) pill.style.transition = "none";
    pill.style.width = `${el.offsetWidth}px`;
    pill.style.transform = `translateX(${el.offsetLeft}px)`;
    pill.style.opacity = "1";
    if (instant) { void pill.offsetWidth; pill.style.transition = ""; }
  }
  if (links) {
    links.addEventListener("pointerover", (e) => { const a = e.target.closest(".xn__link"); if (a) moveTo(a); });
    links.addEventListener("focusin", (e) => { const a = e.target.closest(".xn__link"); if (a) moveTo(a); });
    links.addEventListener("pointerleave", () => moveTo(active()));
    links.addEventListener("focusout", () => setTimeout(() => { if (!links.contains(document.activeElement)) moveTo(active()); }, 0));
    const place = () => moveTo(active(), true);
    place(); document.fonts?.ready.then(place); addEventListener("resize", place);
    // logo shrink on scroll changes layout → re-measure after the transition
    new MutationObserver(() => setTimeout(place, 650)).observe(nav, { attributes: true, attributeFilter: ["class"] });
  }

  /* ---------- magnetic buttons ---------- */
  if (fine && !reduced) {
    nav.querySelectorAll("[data-xn-mag]").forEach((el) => {
      const k = parseFloat(el.dataset.xnMag) || 0.3;
      let raf = 0;
      el.addEventListener("pointermove", (e) => {
        const r = el.getBoundingClientRect();
        const x = (e.clientX - r.left - r.width / 2) * k, y = (e.clientY - r.top - r.height / 2) * k;
        cancelAnimationFrame(raf);
        raf = requestAnimationFrame(() => { el.style.transition = "transform .2s ease-out"; el.style.transform = `translate(${x}px, ${y}px)`; });
        if (el.classList.contains("xn__cta")) { el.style.setProperty("--ox", `${e.clientX - r.left}px`); el.style.setProperty("--oy", `${e.clientY - r.top}px`); }
      });
      el.addEventListener("pointerleave", () => { cancelAnimationFrame(raf); el.style.transition = "transform .7s cubic-bezier(.22,1,.36,1)"; el.style.transform = ""; });
    });
  }

  /* ---------- reading progress ---------- */
  let ticking = false;
  function progress() {
    const max = document.documentElement.scrollHeight - innerHeight;
    nav.style.setProperty("--xn-p", max > 0 ? (scrollY / max).toFixed(4) : 0);
    ticking = false;
  }
  addEventListener("scroll", () => { if (!ticking) { ticking = true; requestAnimationFrame(progress); } }, { passive: true });
  progress();

  /* ---------- mobile menu grows out of the toggle ---------- */
  const toggle = document.getElementById("navToggle");
  const menu = document.getElementById("mobileMenu");
  toggle?.addEventListener("pointerdown", () => {
    const r = toggle.getBoundingClientRect();
    menu?.style.setProperty("--cx", `${r.left + r.width / 2}px`);
    menu?.style.setProperty("--cy", `${r.top + r.height / 2}px`);
  });
  menu?.style.setProperty("--xn-h", getComputedStyle(nav).getPropertyValue("--xn-h") || "76px");
})();
