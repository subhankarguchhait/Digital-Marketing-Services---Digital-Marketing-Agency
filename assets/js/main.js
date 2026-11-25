/**
 * Dream Spark — interactions
 * Navigation, mobile menu, scroll progress, back-to-top, custom cursor, magnetic buttons,
 * tilt cards, page transitions, testimonial slider, brand filters and brand modal.
 */
(function () {
  "use strict";
  const root = document.documentElement;
  const fine = matchMedia("(hover: hover) and (pointer: fine)").matches;
  const reduced = matchMedia("(prefers-reduced-motion: reduce)").matches;

  /* ---------- navigation ---------- */
  const nav = document.getElementById("nav");
  const toggle = document.getElementById("navToggle");
  const menu = document.getElementById("mobileMenu");
  const progress = document.querySelector(".scroll-progress");
  const toTop = document.querySelector(".to-top");
  let lastY = scrollY;
  function onScroll() {
    const y = scrollY;
    nav?.classList.toggle("is-scrolled", y > 40);
    nav?.classList.toggle("is-hidden", y > 500 && y > lastY && !menu?.classList.contains("is-open"));
    lastY = y;
    const max = root.scrollHeight - innerHeight;
    progress?.style.setProperty("--p", max > 0 ? (y / max).toFixed(4) : 0);
    toTop?.classList.toggle("is-visible", y > 700);
  }
  addEventListener("scroll", onScroll, { passive: true });
  onScroll();

  function setMenu(open) {
    if (!menu) return;
    menu.classList.toggle("is-open", open);
    menu.setAttribute("aria-hidden", String(!open));
    toggle.setAttribute("aria-expanded", String(open));
    toggle.setAttribute("aria-label", open ? "Close menu" : "Open menu");
    document.body.classList.toggle("is-locked", open);
    if (open) menu.querySelector("a")?.focus();
  }
  toggle?.addEventListener("click", () => setMenu(!menu.classList.contains("is-open")));
  menu?.addEventListener("click", (e) => { if (e.target.closest("a")) setMenu(false); });
  addEventListener("keydown", (e) => { if (e.key === "Escape" && menu?.classList.contains("is-open")) { setMenu(false); toggle.focus(); } });
  addEventListener("resize", () => { if (innerWidth > 1024 && menu?.classList.contains("is-open")) setMenu(false); });
  toTop?.addEventListener("click", () => scrollTo({ top: 0, behavior: reduced ? "auto" : "smooth" }));

  /* ---------- custom cursor (desktop only) ---------- */
  const cursor = document.querySelector(".cursor");
  if (fine && cursor && !reduced) {
    root.classList.add("has-cursor");
    const dot = cursor.querySelector(".cursor__dot"), ring = cursor.querySelector(".cursor__ring");
    let mx = innerWidth / 2, my = innerHeight / 2, rx = mx, ry = my;
    addEventListener("pointermove", (e) => { mx = e.clientX; my = e.clientY; dot.style.transform = `translate(${mx}px, ${my}px)`; cursor.classList.remove("is-hidden"); }, { passive: true });
    document.addEventListener("pointerleave", () => cursor.classList.add("is-hidden"));
    addEventListener("pointerdown", () => cursor.classList.add("is-down"));
    addEventListener("pointerup", () => cursor.classList.remove("is-down"));
    document.addEventListener("pointerover", (e) => cursor.classList.toggle("is-hover", !!e.target.closest("a, button, .brand-card, .service-card, input, select, textarea, label")));
    (function loop() { rx += (mx - rx) * 0.16; ry += (my - ry) * 0.16; ring.style.transform = `translate(${rx}px, ${ry}px)`; requestAnimationFrame(loop); })();
  }

  /* ---------- magnetic buttons & tilt cards ---------- */
  if (fine && !reduced) {
    document.querySelectorAll(".magnetic").forEach((el) => {
      el.addEventListener("pointermove", (e) => {
        const r = el.getBoundingClientRect();
        el.style.setProperty("--tx", `${((e.clientX - r.left) / r.width - 0.5) * 18}px`);
        el.style.setProperty("--ty", `${((e.clientY - r.top) / r.height - 0.5) * 14}px`);
      });
      el.addEventListener("pointerleave", () => { el.style.setProperty("--tx", "0px"); el.style.setProperty("--ty", "0px"); });
    });
    document.querySelectorAll(".tilt, .service-card").forEach((el) => {
      el.addEventListener("pointermove", (e) => {
        const r = el.getBoundingClientRect(), px = (e.clientX - r.left) / r.width, py = (e.clientY - r.top) / r.height;
        el.style.setProperty("--ry", `${(px - 0.5) * 8}deg`); el.style.setProperty("--rx", `${(0.5 - py) * 8}deg`);
        el.style.setProperty("--mx", `${px * 100}%`); el.style.setProperty("--my", `${py * 100}%`);
      });
      el.addEventListener("pointerleave", () => { el.style.setProperty("--rx", "0deg"); el.style.setProperty("--ry", "0deg"); });
    });
  }

  /* ---------- page transitions ---------- */
  const curtain = document.createElement("div");
  curtain.className = "page-curtain";
  document.body.append(curtain);
  document.addEventListener("click", (e) => {
    const a = e.target.closest("a[href]");
    if (!a || reduced || e.defaultPrevented || e.metaKey || e.ctrlKey || e.shiftKey || a.target === "_blank" || a.hasAttribute("download")) return;
    const url = new URL(a.href, location.href);
    if (url.origin !== location.origin || url.pathname.includes("/admin/")) return;
    if (url.pathname === location.pathname && url.hash) return;
    if (!/(\.php|\/)$/.test(url.pathname)) return;
    e.preventDefault();
    curtain.classList.add("is-active");
    setTimeout(() => (location.href = url.href), 520);
  });
  addEventListener("pageshow", () => curtain.classList.remove("is-active"));

  /* ---------- testimonial slider ---------- */
  document.querySelectorAll("[data-slider]").forEach((slider) => {
    const slides = [...slider.querySelectorAll(".t-slide")];
    const dots = [...slider.querySelectorAll("[data-go]")];
    if (slides.length < 2) return;
    let cur = 0, timer;
    const go = (i) => {
      cur = (i + slides.length) % slides.length;
      slides.forEach((s, k) => { s.classList.toggle("is-active", k === cur); s.setAttribute("aria-hidden", String(k !== cur)); });
      dots.forEach((d, k) => d.classList.toggle("is-active", k === cur));
    };
    const start = () => { clearInterval(timer); if (!reduced) timer = setInterval(() => go(cur + 1), 7000); };
    slider.querySelector("[data-prev]")?.addEventListener("click", () => { go(cur - 1); start(); });
    slider.querySelector("[data-next]")?.addEventListener("click", () => { go(cur + 1); start(); });
    dots.forEach((d) => d.addEventListener("click", () => { go(+d.dataset.go); start(); }));
    slider.addEventListener("mouseenter", () => clearInterval(timer));
    slider.addEventListener("mouseleave", start);
    start();
  });

  /* ---------- brand filters ---------- */
  const filterBar = document.querySelector("[data-brand-filters]");
  filterBar?.addEventListener("click", (e) => {
    const b = e.target.closest("[data-filter]");
    if (!b) return;
    filterBar.querySelectorAll("[data-filter]").forEach((x) => { x.classList.toggle("is-active", x === b); x.setAttribute("aria-selected", String(x === b)); });
    document.querySelectorAll("[data-brand-grid] .brand-card").forEach((c) => c.classList.toggle("is-filtered-out", b.dataset.filter !== "all" && c.dataset.category !== b.dataset.filter));
  });

  /* ---------- brand modal ---------- */
  const modal = document.getElementById("brandModal");
  const icons = { instagram: "fa-brands fa-instagram", facebook: "fa-brands fa-facebook-f", linkedin: "fa-brands fa-linkedin-in", youtube: "fa-brands fa-youtube", x: "fa-brands fa-x-twitter" };
  let lastFocus = null;
  function openBrand(d) {
    const q = (k) => modal.querySelector(`[data-m="${k}"]`);
    lastFocus = document.activeElement;
    q("cover").src = d.cover || ""; q("cover").hidden = !d.cover;
    q("logo").src = d.logo || ""; q("logo").hidden = !d.logo; q("logo").alt = `${d.name} logo`;
    q("category").textContent = d.category || "";
    q("name").textContent = d.name || "";
    q("description").textContent = d.description || "";
    q("services").replaceChildren(...(d.services || []).map((s) => { const li = document.createElement("li"); li.textContent = s; return li; }));
    q("services-title").hidden = !(d.services || []).length;
    q("website").hidden = !d.website; if (d.website) q("website").href = d.website;
    q("social").replaceChildren(...Object.entries(d.social || {}).filter(([k, v]) => v && icons[k]).map(([k, v]) => {
      const a = document.createElement("a"); a.href = v; a.target = "_blank"; a.rel = "noopener"; a.setAttribute("aria-label", k);
      const i = document.createElement("i"); i.className = icons[k]; a.append(i); return a;
    }));
    modal.hidden = false;
    document.body.classList.add("is-locked");
    modal.querySelector(".brand-modal__close").focus();
    if (d.slug) history.replaceState(null, "", `?brand=${encodeURIComponent(d.slug)}`);
  }
  function closeBrand() {
    if (!modal || modal.hidden) return;
    modal.hidden = true;
    document.body.classList.remove("is-locked");
    history.replaceState(null, "", location.pathname);
    lastFocus?.focus?.();
  }
  if (modal) {
    document.addEventListener("click", (e) => {
      const card = e.target.closest("[data-brand]");
      if (card) { try { openBrand(JSON.parse(card.dataset.brand)); } catch (err) { console.error(err); } }
      if (e.target.closest("[data-close]")) closeBrand();
    });
    document.addEventListener("keydown", (e) => {
      if (e.key === "Escape") closeBrand();
      if (e.key === "Tab" && !modal.hidden) { // keep focus inside the dialog
        const f = [...modal.querySelectorAll("a[href]:not([hidden]), button")].filter((x) => x.offsetParent);
        if (!f.length) return;
        if (e.shiftKey && document.activeElement === f[0]) { e.preventDefault(); f[f.length - 1].focus(); }
        else if (!e.shiftKey && document.activeElement === f[f.length - 1]) { e.preventDefault(); f[0].focus(); }
      }
    });
    const slug = new URLSearchParams(location.search).get("brand");
    if (slug) {
      const card = [...document.querySelectorAll("[data-brand]")].find((c) => { try { return JSON.parse(c.dataset.brand).slug === slug; } catch { return false; } });
      if (card) setTimeout(() => openBrand(JSON.parse(card.dataset.brand)), 900);
    }
  }
})();
