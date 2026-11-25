/**
 * Dream Spark — motion layer
 * Preloader, word-by-word headline reveal, scroll reveals (IntersectionObserver),
 * counters, skill bars, parallax and the spark-particle canvas.
 * Respects prefers-reduced-motion and pauses work that is off-screen.
 */
(function () {
  "use strict";
  const root = document.documentElement;
  const reduced = matchMedia("(prefers-reduced-motion: reduce)").matches;
  if (reduced) root.classList.add("reduced");

  /* ---------- word splitting for .reveal-words headlines ---------- */
  function splitWords(el) {
    let i = 0;
    const walk = (node) => {
      [...node.childNodes].forEach((n) => {
        if (n.nodeType === 3) {
          const frag = document.createDocumentFragment();
          n.textContent.split(/(\s+)/).forEach((part) => {
            if (!part) return;
            if (/^\s+$/.test(part)) { frag.append(" "); return; }
            const w = document.createElement("span"); w.className = "w";
            const inner = document.createElement("span"); inner.textContent = part; inner.style.setProperty("--wi", i++);
            w.append(inner); frag.append(w);
          });
          n.replaceWith(frag);
        } else if (n.nodeType === 1 && n.tagName !== "BR") walk(n);
      });
    };
    walk(el);
  }
  document.querySelectorAll(".reveal-words").forEach(splitWords);

  /* ---------- counters & bars ---------- */
  function countUp(el) {
    const target = parseFloat(el.dataset.count);
    if (isNaN(target) || el.dataset.done) return;
    el.dataset.done = "1";
    const dec = (el.dataset.count.split(".")[1] || "").length;
    const pre = el.dataset.prefix || "", suf = el.dataset.suffix || "";
    const fmt = (v) => pre + v.toLocaleString("en-IN", { minimumFractionDigits: dec, maximumFractionDigits: dec }) + suf;
    if (reduced) { el.textContent = fmt(target); return; }
    const t0 = performance.now(), dur = 2000;
    (function step(t) {
      const p = Math.min(1, (t - t0) / dur);
      el.textContent = fmt(target * (1 - Math.pow(1 - p, 4)));
      if (p < 1) requestAnimationFrame(step);
    })(t0);
  }
  const fill = (el) => {
    el.querySelectorAll(".counter").forEach(countUp);
    if (el.classList.contains("counter")) countUp(el);
    el.querySelectorAll(".bar__fill").forEach((b) => (b.style.width = b.dataset.w + "%"));
  };

  /* ---------- reveal on scroll ---------- */
  const SEL = ".reveal, .reveal-clip, .reveal-card, .reveal-words, .process__list, .bar, .stat, .counter";
  const io = "IntersectionObserver" in window && !reduced
    ? new IntersectionObserver((entries) => entries.forEach((en) => {
        if (!en.isIntersecting) return;
        en.target.classList.add("is-in"); fill(en.target); io.unobserve(en.target);
      }), { threshold: 0.15, rootMargin: "0px 0px -6% 0px" })
    : null;
  function observeAll(includeHero) {
    document.querySelectorAll(SEL).forEach((el) => {
      if (el.dataset.obs) return;
      if (!includeHero && el.closest(".hero, .page-hero")) return;
      el.dataset.obs = "1";
      if (io) io.observe(el); else { el.classList.add("is-in"); fill(el); }
    });
  }
  observeAll(false);

  /* ---------- preloader ---------- */
  const pre = document.getElementById("preloader");
  const started = performance.now();
  let done = false;
  function lift() {
    if (done) return; done = true;
    setTimeout(() => { pre?.classList.add("is-done"); observeAll(true); }, reduced ? 0 : Math.max(0, 800 - (performance.now() - started)));
  }
  if (document.readyState === "complete") lift(); else addEventListener("load", lift);
  setTimeout(lift, 3500); // never block on slow images

  /* ---------- parallax ---------- */
  let queued = false;
  function parallax() {
    if (reduced || queued) return;
    queued = true;
    requestAnimationFrame(() => {
      queued = false;
      document.querySelectorAll("[data-parallax]").forEach((el) => {
        const r = el.parentElement.getBoundingClientRect();
        if (r.bottom < 0 || r.top > innerHeight) return;
        el.style.transform = `translate3d(0, ${(-r.top * parseFloat(el.dataset.parallax)).toFixed(1)}px, 0)`;
      });
      document.querySelectorAll("[data-parallax-inner]").forEach((el) => {
        const r = el.parentElement.getBoundingClientRect();
        if (r.bottom < 0 || r.top > innerHeight) return;
        const off = (r.top + r.height / 2 - innerHeight / 2) * parseFloat(el.dataset.parallaxInner);
        el.style.transform = `translate3d(0, ${(-off - r.height * 0.06).toFixed(1)}px, 0)`;
      });
    });
  }
  addEventListener("scroll", parallax, { passive: true });
  addEventListener("resize", parallax);
  parallax();

  /* ---------- spark particles ---------- */
  class Sparks {
    constructor(canvas) {
      this.c = canvas; this.ctx = canvas.getContext("2d");
      const small = innerWidth < 720;
      this.count = Math.round((parseInt(canvas.dataset.particles, 10) || 50) * (small ? 0.5 : 1));
      this.colors = ["255,106,43", "255,179,71", "201,164,92", "230,207,157"];
      this.mouse = { x: -999, y: -999 };
      this.resize();
      this.ps = Array.from({ length: this.count }, () => this.spawn(true));
      new ResizeObserver(() => this.resize()).observe(canvas);
      const host = canvas.parentElement;
      host.addEventListener("pointermove", (e) => { const r = canvas.getBoundingClientRect(); this.mouse.x = e.clientX - r.left; this.mouse.y = e.clientY - r.top; });
      host.addEventListener("pointerleave", () => { this.mouse.x = this.mouse.y = -999; });
      new IntersectionObserver(([en]) => { this.visible = en.isIntersecting; if (this.visible) this.loop(); }).observe(canvas);
      if (reduced) this.draw();
    }
    resize() {
      const dpr = Math.min(devicePixelRatio || 1, 2);
      this.w = this.c.clientWidth; this.h = this.c.clientHeight;
      this.c.width = this.w * dpr; this.c.height = this.h * dpr;
      this.ctx.setTransform(dpr, 0, 0, dpr, 0, 0);
    }
    spawn(initial) {
      return { x: Math.random() * this.w, y: initial ? Math.random() * this.h : this.h + 10, r: Math.random() * 1.8 + 0.4,
        vy: Math.random() * 0.55 + 0.15, vx: (Math.random() - 0.5) * 0.25, a: Math.random() * 0.6 + 0.2,
        tw: Math.random() * Math.PI * 2, col: this.colors[(Math.random() * this.colors.length) | 0] };
    }
    draw() {
      const { ctx } = this;
      ctx.clearRect(0, 0, this.w, this.h);
      ctx.globalCompositeOperation = "lighter";
      for (const p of this.ps) {
        const dx = p.x - this.mouse.x, dy = p.y - this.mouse.y, d2 = dx * dx + dy * dy;
        if (d2 < 14000) { const f = (14000 - d2) / 14000; p.x += (dx / 40) * f; p.y += (dy / 40) * f; }
        p.y -= p.vy; p.x += p.vx + Math.sin(p.tw) * 0.15; p.tw += 0.02;
        if (p.y < -10 || p.x < -10 || p.x > this.w + 10) Object.assign(p, this.spawn(false));
        const al = p.a * (0.6 + Math.sin(p.tw * 2) * 0.4);
        ctx.beginPath(); ctx.fillStyle = `rgba(${p.col},${al * 0.18})`; ctx.arc(p.x, p.y, p.r * 4, 0, 6.283); ctx.fill();
        ctx.beginPath(); ctx.fillStyle = `rgba(${p.col},${al})`; ctx.arc(p.x, p.y, p.r, 0, 6.283); ctx.fill();
      }
      ctx.globalCompositeOperation = "source-over";
    }
    loop() {
      if (!this.visible || reduced || this.running) return;
      this.running = true;
      const tick = () => { if (!this.visible || document.hidden) { this.running = false; return; } this.draw(); requestAnimationFrame(tick); };
      requestAnimationFrame(tick);
    }
  }
  document.querySelectorAll("canvas[data-particles]").forEach((c) => new Sparks(c));
  document.addEventListener("visibilitychange", () => { if (!document.hidden) dispatchEvent(new Event("scroll")); });

  window.DSAnim = { observeAll, splitWords };
})();
