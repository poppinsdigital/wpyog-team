/**
 * WPYog Team — Frontend JS
 * Vanilla JS — no jQuery dependency. Works on all themes including block themes (TT5+).
 */
(function () {
  'use strict';

  /* ---- DOM-ready helper ---- */
  function ready(fn) {
    if (document.readyState !== 'loading') {
      fn();
    } else {
      document.addEventListener('DOMContentLoaded', fn);
    }
  }

  ready(initTeamPopups);

  function initTeamPopups() {
    var wraps = document.querySelectorAll('.pt-popup-layout');
    wraps.forEach(function (wrap) {
      var teamId  = wrap.getAttribute('data-team');
      var members = (window.ptTeamData && window.ptTeamData[teamId]) || [];
      var current = 0;

      if (!members.length) return;

      var overlay = wrap.querySelector('.pt-modal-overlay');
      var modal   = wrap.querySelector('.pt-modal');

      /* ---- Open on card click ---- */
      wrap.addEventListener('click', function (e) {
        var card = e.target.closest('.pt-card');
        if (!card) return;
        current = parseInt(card.getAttribute('data-index'), 10) || 0;
        showMember(current);
        fadeIn(overlay, 180);
        document.body.classList.add('pt-modal-open');
        trapFocus(modal);
      });

      /* ---- Close: backdrop click ---- */
      overlay.addEventListener('click', function (e) {
        if (e.target === overlay) closeModal();
      });

      /* ---- Close: buttons (close / prev / next) via delegation ---- */
      wrap.addEventListener('click', function (e) {
        if (e.target.closest('.pt-modal-close')) { closeModal(); return; }
        if (e.target.closest('.pt-modal-prev'))  { prevMember(); return; }
        if (e.target.closest('.pt-modal-next'))  { nextMember(); return; }
      });

      /* ---- Keyboard ---- */
      document.addEventListener('keydown', function (e) {
        if (!overlayVisible()) return;
        if (e.key === 'Escape')     closeModal();
        if (e.key === 'ArrowLeft')  prevMember();
        if (e.key === 'ArrowRight') nextMember();
      });

      /* ---- Scrollbar hover-reveal ---- */
      modal.addEventListener('mouseenter', function () { modal.classList.add('pt-scrollbar-visible'); });
      modal.addEventListener('mouseleave', function () { modal.classList.remove('pt-scrollbar-visible'); });

      /* ---- Touch swipe ---- */
      var touchStartX = 0;
      modal.addEventListener('touchstart', function (e) {
        touchStartX = e.changedTouches[0].screenX;
      }, { passive: true });
      modal.addEventListener('touchend', function (e) {
        var delta = e.changedTouches[0].screenX - touchStartX;
        if (Math.abs(delta) > 50) {
          if (delta < 0) nextMember(); else prevMember();
        }
      }, { passive: true });

      /* ---- Helpers ---- */

      function overlayVisible() {
        return overlay && overlay.offsetParent !== null;
      }

      function showMember(idx) {
        var m = members[idx];
        if (!m) return;

        var inner = wrap.querySelector('.pt-modal-inner');
        inner.style.opacity = '0.4';
        inner.style.transition = 'opacity 0.15s';

        var img = wrap.querySelector('.pt-modal-img');
        img.setAttribute('src', m.photo || '');
        img.setAttribute('alt', m.name  || '');

        setText('.pt-modal-name',        m.name        || '');
        setText('.pt-modal-designation', m.designation || '');
        setText('.pt-modal-subtitle',    m.subtitle    || '');
        setHtml('.pt-modal-bio',         m.bio         || '');
        setHtml('.pt-modal-contacts',    m.contacts    || '');
        setHtml('.pt-modal-social',      m.social      || '');

        toggleSection('.pt-modal-designation', m.designation);
        toggleSection('.pt-modal-subtitle',    m.subtitle);
        toggleSection('.pt-modal-bio',         m.bio);
        toggleSection('.pt-modal-contacts',    m.contacts);
        toggleSection('.pt-modal-social',      m.social);

        // Animate back in
        requestAnimationFrame(function () {
          requestAnimationFrame(function () {
            inner.style.opacity = '1';
            setTimeout(function () { inner.style.transition = ''; }, 160);
          });
        });

        // Prev/next visibility
        var showNav = members.length > 1;
        wrap.querySelectorAll('.pt-modal-prev, .pt-modal-next').forEach(function (btn) {
          btn.style.display = showNav ? '' : 'none';
        });

        // Scroll content to top
        var content = wrap.querySelector('.pt-modal-content');
        if (content) content.scrollTop = 0;
      }

      function setText(sel, val) {
        var el = wrap.querySelector(sel);
        if (el) el.textContent = val;
      }

      function setHtml(sel, val) {
        var el = wrap.querySelector(sel);
        if (el) el.innerHTML = val;
      }

      function toggleSection(sel, content) {
        var el = wrap.querySelector(sel);
        if (!el) return;
        el.style.display = (content && content.trim() !== '') ? '' : 'none';
      }

      function prevMember() {
        current = (current - 1 + members.length) % members.length;
        showMember(current);
      }

      function nextMember() {
        current = (current + 1) % members.length;
        showMember(current);
      }

      function closeModal() {
        fadeOut(overlay, 160);
        document.body.classList.remove('pt-modal-open');
      }
    });
  }

  /* ---- Fade helpers (CSS-transition based) ---- */
  function fadeIn(el, duration) {
    el.style.opacity    = '0';
    el.style.display    = 'block';
    el.style.transition = 'opacity ' + (duration / 1000) + 's';
    requestAnimationFrame(function () {
      requestAnimationFrame(function () {
        el.style.opacity = '1';
        setTimeout(function () { el.style.transition = ''; }, duration);
      });
    });
  }

  function fadeOut(el, duration) {
    el.style.transition = 'opacity ' + (duration / 1000) + 's';
    el.style.opacity    = '0';
    setTimeout(function () {
      el.style.transition = '';
      el.style.opacity    = '';
      el.style.display    = 'none';
    }, duration);
  }

  /* Prevent body scroll when modal open */
  var style = document.createElement('style');
  style.textContent = '.pt-modal-open { overflow: hidden !important; }';
  document.head.appendChild(style);

  /* Basic focus trap */
  function trapFocus(modalEl) {
    var focusable = modalEl.querySelectorAll(
      'button, [href], input, select, textarea, [tabindex]:not([tabindex="-1"])'
    );
    if (focusable.length) focusable[0].focus();
  }

})();
