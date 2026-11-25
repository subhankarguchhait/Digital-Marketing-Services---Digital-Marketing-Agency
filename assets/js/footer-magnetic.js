/**
 * DREAM SPARK — Magnetic footer
 * - [data-mag="strength"] elements are pulled toward the cursor (inner [data-mag-inner] moves a bit more)
 * - giant wordmark letters light up and lift near the cursor
 * - services marquee drifts on its own and speeds up / reverses with scroll velocity
 * - copy-to-clipboard, live Kolkata clock, back-to-top
 * One requestAnimationFrame loop, running only while the footer is on screen.
 * Touch devices and "reduce motion" get a calm, static version.
 */
(function () {
  "use strict";
  const footer = document.querySelector("[data-mf]");
  if (!footer) return;

  const reduced = matchMedia("(prefers-reduced-motion: reduce)").matches;
  const finePointer = matchMedia("(hover: hover) and (pointer: fine)").matches;
  const lerp = (a, b, t) => a + (b - a) * t;
  footer.classList.add("is-js");
  if (!finePointer) footer.classList.add("is-touch");

  /* ---------- entrance + hide the floating back-to-top while the footer shows ---------- */
  let visible = false;
  new IntersectionObserver(([en]) => {
    visible = en.isIntersecting;
    document.body.classList.toggle("mf-in-view", visible);
    if (visible) { footer.classList.add("is-in"); start(); }
  }, { threshold: 0.05 }).observe(footer);

  /* ---------- pointer state ---------- */
  const pointer = { x: -9999, y: -9999, inside: false };
  if (finePointer && !reduced) {
    footer.addEventListener("pointermove", (e) => { pointer.x = e.clientX; pointer.y = e.clientY; pointer.inside = true; start(); }, { passive: true });
    footer.addEventListener("pointerleave", () => { pointer.inside = false; pointer.x = pointer.y = -9999; });
  }

  /* ---------- magnetic elements ---------- */
  const magnets = (finePointer && !reduced) ? [...footer.querySelectorAll("[data-mag]")].map((el) => ({
    el, inner: el.querySelector("[data-mag-inner]"), k: parseFloat(el.dataset.mag) || 0.3, x: 0, y: 0, ix: 0, iy: 0,
  })) : [];

  function updateMagnets() {
    let moving = false;
    for (const m of magnets) {
      const r = m.el.getBoundingClientRect();
      // use the untransformed centre so the element doesn't chase itself
      const cx = r.left + r.width / 2 - m.x, cy = r.top + r.height / 2 - m.y;
      const dx = pointer.x - cx, dy = pointer.y - cy;
      const reach = Math.max(r.width, r.height) * 0.75 + 50;          // field radius around the element
      const inField = pointer.inside && Math.hypot(dx, dy) < reach;
      const tx = inField ? dx * m.k : 0, ty = inField ? dy * m.k : 0;
      m.x = lerp(m.x, tx, 0.16); m.y = lerp(m.y, ty, 0.16);
      m.ix = lerp(m.ix, tx * 0.45, 0.16); m.iy = lerp(m.iy, ty * 0.45, 0.16);
      if (Math.abs(m.x - tx) > 0.05 || Math.abs(m.y - ty) > 0.05) moving = true;
      m.el.style.transform = `translate3d(${m.x.toFixed(2)}px, ${m.y.toFixed(2)}px, 0)`;
      if (m.inner) m.inner.style.transform = `translate3d(${m.ix.toFixed(2)}px, ${m.iy.toFixed(2)}px, 0)`;
    }
    return moving;
  }

  /* ---------- giant wordmark: spotlight + lift per letter ---------- */
  const letters = (finePointer && !reduced) ? [...footer.querySelectorAll("[data-mf-word] span:not(.is-space)")].map((el) => ({ el, lift: 0, tilt: 0 })) : [];
  function updateLetters() {
    let moving = false;
    for (const l of letters) {
      const r = l.el.getBoundingClientRect();
      const lx = pointer.x - r.left, ly = pointer.y - r.top + l.lift;
      l.el.style.setProperty("--lx", `${lx.toFixed(1)}px`);
      l.el.style.setProperty("--ly", `${ly.toFixed(1)}px`);
      const dx = pointer.x - (r.left + r.width / 2), dy = pointer.y - (r.top + l.lift + r.height / 2);
      const d = Math.hypot(dx, dy);
      const force = pointer.inside ? Math.max(0, 1 - d / (r.height * 1.6)) : 0;
      const tLift = force * r.height * 0.14, tTilt = force * Math.max(-1, Math.min(1, dx / r.width)) * -6;
      l.lift = lerp(l.lift, tLift, 0.12); l.tilt = lerp(l.tilt, tTilt, 0.12);
      if (Math.abs(l.lift - tLift) > 0.05) moving = true;
      l.el.style.setProperty("--lift", l.lift.toFixed(2));
      l.el.style.setProperty("--tilt", l.tilt.toFixed(2));
    }
    return moving;
  }

  /* ---------- marquee driven by time + scroll velocity ---------- */
  const marquee = footer.querySelector("[data-mf-marquee]");
  const track = marquee?.querySelector(".mf__track");
  let mx = 0, dir = -1, speed = 0, lastY = scrollY, hoverSlow = 1;
  const useJsMarquee = track && !reduced;
  if (useJsMarquee) {
    marquee.addEventListener("pointerenter", () => (hoverSlow = 0.25));
    marquee.addEventListener("pointerleave", () => (hoverSlow = 1));
  }
  function updateMarquee(dt) {
    const y = scrollY, v = y - lastY; lastY = y;
    if (v !== 0) dir = v > 0 ? -1 : 1;                          // scroll down → left, scroll up → right
    speed = lerp(speed, Math.min(Math.abs(v), 60) * 0.9, 0.08); // scroll boost, eased
    const half = track.scrollWidth / 2;
    mx += dir * (0.045 * dt + speed) * hoverSlow;
    if (mx <= -half) mx += half; else if (mx > 0) mx -= half;
    track.style.transform = `translate3d(${mx.toFixed(2)}px, 0, 0) skewX(${(dir * -speed * 0.15).toFixed(2)}deg)`;
  }

  /* ---------- fit the giant wordmark exactly to the footer width ---------- */
  const word = footer.querySelector("[data-mf-word]");
  function fitWord() {
    if (!word) return;
    word.style.fontSize = "";
    const avail = word.clientWidth - parseFloat(getComputedStyle(word).paddingLeft) * 2;
    const used = [...word.children].reduce((w, c) => w + c.getBoundingClientRect().width, 0);
    if (used > 0) word.style.fontSize = `${Math.min(260, parseFloat(getComputedStyle(word).fontSize) * (avail / used) * 0.98)}px`;
  }
  fitWord();
  document.fonts?.ready.then(fitWord);
  let rt; addEventListener("resize", () => { clearTimeout(rt); rt = setTimeout(fitWord, 150); });

  /* ---------- the single animation loop ---------- */
  let raf = 0, last = 0;
  function frame(t) {
    const dt = Math.min(64, t - (last || t)); last = t;
    let busy = false;
    if (magnets.length && updateMagnets()) busy = true;
    if (letters.length && updateLetters()) busy = true;
    if (useJsMarquee) { updateMarquee(dt); busy = true; }
    raf = (visible && (busy || pointer.inside)) ? requestAnimationFrame(frame) : 0;
    if (!raf) last = 0;
  }
  function start() { if (!raf && visible) raf = requestAnimationFrame(frame); }
  addEventListener("scroll", start, { passive: true });
  document.addEventListener("visibilitychange", () => { if (!document.hidden) start(); });

  /* ---------- copy to clipboard ---------- */
  footer.addEventListener("click", async (e) => {
    const btn = e.target.closest("[data-copy]");
    if (!btn) return;
    const text = btn.dataset.copy, label = btn.querySelector("span");
    let ok = false;
    try { await navigator.clipboard.writeText(text); ok = true; }
    catch {
      const ta = Object.assign(document.createElement("textarea"), { value: text });
      ta.style.cssText = "position:fixed;opacity:0"; document.body.append(ta); ta.select();
      try { ok = document.execCommand("copy"); } catch { ok = false; }
      ta.remove();
    }
    btn.classList.toggle("is-done", ok);
    label.textContent = ok ? "Copied" : "Press ⌘C";
    clearTimeout(btn._t); btn._t = setTimeout(() => { btn.classList.remove("is-done"); label.textContent = "Copy"; }, 1800);
  });

  /* ---------- back to top ---------- */
  footer.querySelector("[data-mf-top]")?.addEventListener("click", () => scrollTo({ top: 0, behavior: reduced ? "auto" : "smooth" }));

  /* ---------- live studio clock (Asia/Kolkata) ---------- */
  const clock = footer.querySelector("[data-mf-clock]");
  if (clock) {
    const fmt = new Intl.DateTimeFormat("en-IN", { timeZone: "Asia/Kolkata", hour: "numeric", minute: "2-digit", hour12: true });
    const tick = () => { clock.textContent = fmt.format(new Date()).toUpperCase(); };
    tick(); setInterval(tick, 15000);
  }
})();
