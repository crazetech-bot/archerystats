/**
 * PopupEngine v1.0 — SportDNS
 * Behavior-based popup system: triggers, templates, frequency caps.
 * Zero dependencies. ES5-compatible (no bundler needed).
 */
(function (win, doc) {
  'use strict';

  /* ═══════════════════════════════════════════════════
   * 1. STORAGE ADAPTER
   *    Persists show counts + timestamps across sessions
   * ═══════════════════════════════════════════════════ */
  var Store = {
    ns: 'sdn_pu_',
    _get: function (key) {
      try { return JSON.parse(localStorage.getItem(this.ns + key)); } catch (e) { return null; }
    },
    _set: function (key, val) {
      try { localStorage.setItem(this.ns + key, JSON.stringify(val)); } catch (e) {}
    },
    getCount:    function (id) { return this._get('c_' + id) || 0; },
    incCount:    function (id) { this._set('c_' + id, this.getCount(id) + 1); },
    getLastShown: function (id) { return this._get('l_' + id) || 0; },
    setLastShown: function (id) { this._set('l_' + id, Date.now()); },
    reset:       function (id) { this._set('c_' + id, 0); this._set('l_' + id, 0); },
  };

  /* ═══════════════════════════════════════════════════
   * 2. FREQUENCY GUARD
   *    Enforces cooldowns + per-session + lifetime caps
   * ═══════════════════════════════════════════════════ */
  var Session = {};   // per-page-load session tracking

  function canShow(id, cfg) {
    var total = Store.getCount(id);
    var last  = Store.getLastShown(id);
    var now   = Date.now();
    // Lifetime cap
    if (cfg.maxTotal > 0 && total >= cfg.maxTotal) return false;
    // Cooldown (time since last shown)
    if (cfg.cooldown > 0 && last > 0 && (now - last) < cfg.cooldown) return false;
    // Per-session cap (resets on page load)
    if (cfg.maxPerSession > 0 && (Session[id] || 0) >= cfg.maxPerSession) return false;
    return true;
  }

  function recordShow(id) {
    Store.incCount(id);
    Store.setLastShown(id);
    Session[id] = (Session[id] || 0) + 1;
  }

  /* ═══════════════════════════════════════════════════
   * 3. TRIGGER ENGINE
   *    Six behavior-based triggers, each returns a
   *    destroy() function for cleanup.
   * ═══════════════════════════════════════════════════ */
  var Triggers = {

    /**
     * EXIT INTENT
     * Desktop : fires when cursor leaves viewport from the top
     * Mobile  : fires on rapid upward scroll from near the top
     * cfg: { threshold:px, delay:ms, mobileSeconds:number }
     */
    exitIntent: function (cfg, cb) {
      var fired = false;
      var delay = cfg.delay || 0;
      var threshold = cfg.threshold || 25;

      function onLeave(e) {
        if (fired || e.clientY > threshold) return;
        fired = true;
        doc.removeEventListener('mouseleave', onLeave);
        win.removeEventListener('scroll', onScroll);
        setTimeout(cb, delay);
      }

      // Mobile: detect rapid upward scroll while near top of page
      var lastY = win.pageYOffset;
      var samples = [];
      function onScroll() {
        if (fired) return;
        var y = win.pageYOffset;
        samples.push(y - lastY);
        if (samples.length > 6) samples.shift();
        var avg = samples.reduce(function (a, b) { return a + b; }, 0) / samples.length;
        if (avg < -18 && y < 250) {
          fired = true;
          doc.removeEventListener('mouseleave', onLeave);
          win.removeEventListener('scroll', onScroll);
          setTimeout(cb, delay);
        }
        lastY = y;
      }

      if (win.innerWidth >= 768) {
        doc.addEventListener('mouseleave', onLeave);
      }
      win.addEventListener('scroll', onScroll, { passive: true });

      return function () {
        doc.removeEventListener('mouseleave', onLeave);
        win.removeEventListener('scroll', onScroll);
      };
    },

    /**
     * SCROLL DEPTH
     * Fires when user has scrolled past a percentage of the page.
     * cfg: { percent:0-100, delay:ms }
     */
    scrollDepth: function (cfg, cb) {
      var fired = false;
      var target = cfg.percent || 50;
      var delay  = cfg.delay  || 0;

      function onScroll() {
        if (fired) return;
        var scrolled = win.pageYOffset + win.innerHeight;
        var total    = doc.documentElement.scrollHeight;
        if (total <= win.innerHeight) return; // page not tall enough
        if ((scrolled / total) * 100 >= target) {
          fired = true;
          win.removeEventListener('scroll', onScroll);
          setTimeout(cb, delay);
        }
      }

      win.addEventListener('scroll', onScroll, { passive: true });
      return function () { win.removeEventListener('scroll', onScroll); };
    },

    /**
     * TIME ON PAGE
     * Fires after N seconds of being on the page.
     * cfg: { seconds:number, delay:ms }
     * Cooldown rule: respects visibility — pauses when tab is hidden.
     */
    timeOnPage: function (cfg, cb) {
      var seconds  = cfg.seconds || 30;
      var delay    = cfg.delay   || 0;
      var elapsed  = 0;
      var interval;
      var fired    = false;

      function tick() {
        if (!doc.hidden) elapsed += 1;
        if (elapsed >= seconds && !fired) {
          fired = true;
          clearInterval(interval);
          setTimeout(cb, delay);
        }
      }

      interval = setInterval(tick, 1000);
      return function () { clearInterval(interval); };
    },

    /**
     * INACTIVITY
     * Fires when user has not moved mouse / touched / typed for N seconds.
     * Resets on any interaction. Skips if user is mid-input.
     * cfg: { seconds:number }
     */
    inactivity: function (cfg, cb) {
      var ms     = (cfg.seconds || 20) * 1000;
      var fired  = false;
      var timer  = null;
      var events = ['mousemove', 'keydown', 'touchstart', 'click', 'scroll'];

      function isInInput() {
        var el = doc.activeElement;
        return el && (el.tagName === 'INPUT' || el.tagName === 'TEXTAREA' || el.isContentEditable);
      }

      function reset() {
        clearTimeout(timer);
        timer = setTimeout(function () {
          if (fired || isInInput()) return;
          fired = true;
          events.forEach(function (ev) { doc.removeEventListener(ev, reset, true); });
          cb();
        }, ms);
      }

      events.forEach(function (ev) { doc.addEventListener(ev, reset, true); });
      reset();

      return function () {
        clearTimeout(timer);
        events.forEach(function (ev) { doc.removeEventListener(ev, reset, true); });
      };
    },

    /**
     * PAGE SPECIFIC
     * Immediately fires (after optional delay) if current URL matches.
     * cfg: { patterns: ['/path', /regex/], delay:ms }
     */
    pageSpecific: function (cfg, cb) {
      var path     = win.location.pathname;
      var patterns = cfg.patterns || [];
      var match    = patterns.some(function (p) {
        return (p instanceof RegExp) ? p.test(path) : (path === p || path.indexOf(p) === 0);
      });
      if (match) setTimeout(cb, cfg.delay || 0);
      return function () {};
    },

    /**
     * DEVICE SPECIFIC
     * Fires immediately if current device class matches.
     * cfg: { mobile:bool, tablet:bool, desktop:bool, delay:ms }
     */
    deviceSpecific: function (cfg, cb) {
      var w = win.innerWidth;
      var match =
        (cfg.mobile  && w < 768) ||
        (cfg.tablet  && w >= 768 && w < 1024) ||
        (cfg.desktop && w >= 1024);
      if (match) setTimeout(cb, cfg.delay || 0);
      return function () {};
    },
  };

  /* ═══════════════════════════════════════════════════
   * 4. TEMPLATE BUILDERS
   *    Each returns an HTML string injected into the popup wrapper.
   * ═══════════════════════════════════════════════════ */
  var Templates = {

    modal: function (id, p) {
      return '<div class="sdn-pu__overlay"></div>' +
        '<div class="sdn-pu__box sdn-pu__box--modal" style="' + vars(p) + '" role="document">' +
          closeBtn() +
          '<div class="sdn-pu__inner">' + p.html + '</div>' +
        '</div>';
    },

    slideIn: function (id, p) {
      return '<div class="sdn-pu__box sdn-pu__box--slide-in" style="' + vars(p) + '" role="document">' +
          closeBtn() +
          '<div class="sdn-pu__inner">' + p.html + '</div>' +
        '</div>';
    },

    banner: function (id, p) {
      var pos = p.position === 'bottom' ? 'bottom' : 'top';
      return '<div class="sdn-pu__box sdn-pu__box--banner sdn-pu__box--banner-' + pos + '" style="' + vars(p) + '" role="document">' +
          '<div class="sdn-pu__inner sdn-pu__inner--banner">' + p.html + '</div>' +
          closeBtn() +
        '</div>';
    },

    fullScreen: function (id, p) {
      return '<div class="sdn-pu__overlay"></div>' +
        '<div class="sdn-pu__box sdn-pu__box--fullscreen" style="' + vars(p) + '" role="document">' +
          closeBtn() +
          '<div class="sdn-pu__inner sdn-pu__inner--fullscreen">' + p.html + '</div>' +
        '</div>';
    },

    stickyBar: function (id, p) {
      var pos = p.position === 'bottom' ? 'bottom' : 'top';
      return '<div class="sdn-pu__box sdn-pu__box--sticky sdn-pu__box--sticky-' + pos + '" style="' + vars(p) + '" role="document">' +
          '<div class="sdn-pu__inner sdn-pu__inner--sticky">' + p.html + '</div>' +
          closeBtn() +
        '</div>';
    },
  };

  function closeBtn() {
    return '<button class="sdn-pu__close" data-pu-close aria-label="Close popup">' +
      '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" aria-hidden="true">' +
      '<path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg></button>';
  }

  function vars(p) {
    var out = [];
    if (p.accentColor) out.push('--pu-accent:' + p.accentColor);
    if (p.bgColor)     out.push('--pu-bg:'     + p.bgColor);
    if (p.textColor)   out.push('--pu-text:'   + p.textColor);
    return out.join(';');
  }

  /* ═══════════════════════════════════════════════════
   * 5. DOM RENDERER
   *    Creates, animates and manages popup DOM elements.
   * ═══════════════════════════════════════════════════ */
  function renderPopup(id, popup) {
    var tplFn = Templates[popup.format] || Templates.modal;
    var el    = doc.createElement('div');

    el.id        = 'sdn-pu-' + id;
    el.className = 'sdn-pu sdn-pu--' + popup.format;
    el.setAttribute('role', 'dialog');
    el.setAttribute('aria-modal', 'true');
    el.setAttribute('aria-labelledby', 'pu-title-' + id);

    el.innerHTML = tplFn(id, popup);
    doc.body.appendChild(el);

    // Close buttons
    el.querySelectorAll('[data-pu-close]').forEach(function (btn) {
      btn.addEventListener('click', function () { PopupEngine.hide(id); });
    });

    // Overlay click (only for modal/fullscreen)
    if (popup.closeOnOverlay !== false) {
      var overlay = el.querySelector('.sdn-pu__overlay');
      if (overlay) overlay.addEventListener('click', function () { PopupEngine.hide(id); });
    }

    // Two-frame animate-in (ensures CSS transition fires)
    requestAnimationFrame(function () {
      requestAnimationFrame(function () { el.classList.add('sdn-pu--visible'); });
    });

    // Scroll lock for blocking formats
    if (popup.format === 'modal' || popup.format === 'fullScreen') {
      doc.body.classList.add('sdn-pu-lock');
    }

    // Sticky bar: nudge body content down/up
    if (popup.format === 'stickyBar') {
      setTimeout(function () {
        var bar = el.querySelector('.sdn-pu__box');
        if (!bar) return;
        var h = bar.offsetHeight;
        if (popup.position !== 'bottom') {
          doc.body.style.paddingTop = (parseFloat(doc.body.style.paddingTop) || 0) + h + 'px';
          el.dataset.nudgeTop = h;
        } else {
          doc.body.style.paddingBottom = (parseFloat(doc.body.style.paddingBottom) || 0) + h + 'px';
          el.dataset.nudgeBottom = h;
        }
      }, 360);
    }

    // Focus first interactive element
    setTimeout(function () {
      var first = el.querySelector('button, [href], input, [tabindex]:not([tabindex="-1"])');
      if (first) first.focus();
    }, 400);

    if (typeof popup.onShow === 'function') popup.onShow(el, popup);
    return el;
  }

  /* ═══════════════════════════════════════════════════
   * 6. POPUP ENGINE — PUBLIC API
   * ═══════════════════════════════════════════════════ */
  var PopupEngine = {
    _reg:      {},   // registered popup configs
    _destroys: {},   // trigger cleanup functions

    /**
     * DEFAULTS — override per popup via register()
     */
    defaults: {
      format:        'modal',
      cooldown:      86400000,  // 24 h in ms (0 = no cooldown)
      maxTotal:      0,         // 0 = unlimited
      maxPerSession: 1,
      accentColor:   '#f59e0b',
      bgColor:       '#ffffff',
      textColor:     '#0f172a',
      closeOnOverlay: true,
      closeOnEscape:  true,
      position:      'top',     // for banner/stickyBar
      html:          '',
      onShow:        null,
      onHide:        null,
    },

    /**
     * register(id, config)
     * Registers a popup and arms its triggers.
     */
    register: function (id, config) {
      var cfg = {};
      var def = this.defaults;
      for (var k in def) { if (def.hasOwnProperty(k)) cfg[k] = def[k]; }
      for (var k in config) { if (config.hasOwnProperty(k)) cfg[k] = config[k]; }

      this._reg[id] = cfg;
      this._armTriggers(id, cfg);
    },

    _armTriggers: function (id, cfg) {
      var self = this;
      var fns  = [];

      (cfg.triggers || []).forEach(function (tCfg) {
        var fn = Triggers[tCfg.type];
        if (!fn) return;
        fns.push(fn(tCfg, function () { self.show(id); }));
      });

      this._destroys[id] = fns;
    },

    /**
     * show(id)
     * Displays the popup if frequency rules allow.
     */
    show: function (id) {
      if (doc.getElementById('sdn-pu-' + id)) return;  // already showing

      var popup = this._reg[id];
      if (!popup) { console.warn('PopupEngine: unknown id "' + id + '"'); return; }
      if (!canShow(id, popup)) return;

      // Only one modal/fullscreen at a time
      if (popup.format === 'modal' || popup.format === 'fullScreen') {
        var blocking = doc.querySelector('.sdn-pu--modal, .sdn-pu--fullScreen');
        if (blocking) return;
      }

      recordShow(id);
      var el = renderPopup(id, popup);

      // Escape key
      if (popup.closeOnEscape) {
        var self = this;
        function onEsc(e) {
          if (e.key === 'Escape') {
            self.hide(id);
            doc.removeEventListener('keydown', onEsc);
          }
        }
        doc.addEventListener('keydown', onEsc);
      }
    },

    /**
     * hide(id)
     * Animates out and removes popup from DOM.
     */
    hide: function (id) {
      var el = doc.getElementById('sdn-pu-' + id);
      if (!el) return;

      var popup = this._reg[id];
      if (typeof popup.onHide === 'function') popup.onHide(el, popup);

      el.classList.remove('sdn-pu--visible');
      el.classList.add('sdn-pu--hiding');

      // Restore scroll lock / padding
      if (popup.format === 'modal' || popup.format === 'fullScreen') {
        doc.body.classList.remove('sdn-pu-lock');
      }
      if (popup.format === 'stickyBar') {
        var nudgeTop = parseFloat(el.dataset.nudgeTop) || 0;
        var nudgeBot = parseFloat(el.dataset.nudgeBottom) || 0;
        if (nudgeTop) doc.body.style.paddingTop    = Math.max(0, (parseFloat(doc.body.style.paddingTop)    || 0) - nudgeTop) + 'px';
        if (nudgeBot) doc.body.style.paddingBottom = Math.max(0, (parseFloat(doc.body.style.paddingBottom) || 0) - nudgeBot) + 'px';
      }

      setTimeout(function () { if (el.parentNode) el.parentNode.removeChild(el); }, 380);
    },

    /**
     * destroy(id)
     * Hides popup and tears down all its trigger listeners.
     */
    destroy: function (id) {
      this.hide(id);
      (this._destroys[id] || []).forEach(function (fn) { if (fn) fn(); });
      delete this._reg[id];
      delete this._destroys[id];
    },

    destroyAll: function () {
      var self = this;
      Object.keys(this._reg).forEach(function (id) { self.destroy(id); });
    },

    /**
     * resetFrequency(id)
     * Clears stored count + timestamp so popup can show again.
     */
    resetFrequency: function (id) { Store.reset(id); },

    /**
     * forceShow(id)
     * Shows popup regardless of frequency rules (useful for testing).
     */
    forceShow: function (id) {
      var popup = this._reg[id];
      if (!popup) return;
      if (doc.getElementById('sdn-pu-' + id)) return;
      renderPopup(id, popup);
    },
  };

  // Expose globally
  win.PopupEngine = PopupEngine;

})(window, document);
