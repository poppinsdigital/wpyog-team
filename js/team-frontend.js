/**
 * WPYog Team — Frontend JS
 * Handles the popup/modal for the popup layout.
 */
(function ($) {
  'use strict';

  // One modal per team wrapper
  $(document).ready(function () {
    initTeamPopups();
  });

  function initTeamPopups() {
    $('.pt-popup-layout').each(function () {
      var $wrap   = $(this);
      var teamId  = $wrap.data('team');
      var members = (window.ptTeamData && window.ptTeamData[teamId]) || [];
      var current = 0;

      if (!members.length) return;

      var $overlay = $wrap.find('.pt-modal-overlay');
      var $modal   = $wrap.find('.pt-modal');

      /* ---- Open modal on card click ---- */
      $wrap.on('click', '.pt-card', function () {
        current = parseInt($(this).data('index'), 10) || 0;
        showMember(current);
        $overlay.fadeIn(180);
        $('body').addClass('pt-modal-open');
        trapFocus($modal[0]);
      });

      /* ---- Close: overlay click or ESC only (no X button) ---- */
      $overlay.on('click', function (e) {
        if ($(e.target).is($overlay)) {
          closeModal();
        }
      });
      $(document).on('keydown.ptModal', function (e) {
        if (!$overlay.is(':visible')) return;
        if (e.key === 'Escape')    closeModal();
        if (e.key === 'ArrowLeft') prevMember();
        if (e.key === 'ArrowRight') nextMember();
      });

      /* ---- Close button ---- */
      $wrap.on('click', '.pt-modal-close', closeModal);

      /* ---- Prev / Next ---- */
      $wrap.on('click', '.pt-modal-prev', prevMember);
      $wrap.on('click', '.pt-modal-next', nextMember);

      /* ---- Scrollbar hover-reveal (class toggle for both WebKit + Firefox) ---- */
      $modal.on('mouseenter', function () {
        $modal.addClass('pt-scrollbar-visible');
      }).on('mouseleave', function () {
        $modal.removeClass('pt-scrollbar-visible');
      });

      /* ---- Touch swipe ---- */
      var touchStartX = 0;
      $modal[0].addEventListener('touchstart', function (e) {
        touchStartX = e.changedTouches[0].screenX;
      }, { passive: true });
      $modal[0].addEventListener('touchend', function (e) {
        var delta = e.changedTouches[0].screenX - touchStartX;
        if (Math.abs(delta) > 50) {
          if (delta < 0) nextMember(); else prevMember();
        }
      }, { passive: true });

      function showMember(idx) {
        var m = members[idx];
        if (!m) return;

        // Animate content out, swap, animate in
        var $inner = $wrap.find('.pt-modal-inner');
        $inner.css({ opacity: 0.4 });

        $wrap.find('.pt-modal-img').attr('src', m.photo).attr('alt', m.name);
        $wrap.find('.pt-modal-designation').text(m.designation || '');
        $wrap.find('.pt-modal-name').text(m.name || '');
        $wrap.find('.pt-modal-subtitle').text(m.subtitle || '');
        $wrap.find('.pt-modal-bio').html(m.bio || '');
        $wrap.find('.pt-modal-contacts').html(m.contacts || '');
        $wrap.find('.pt-modal-social').html(m.social || '');

        // Show/hide empty sections
        toggleSection($wrap.find('.pt-modal-designation'), m.designation);
        toggleSection($wrap.find('.pt-modal-subtitle'), m.subtitle);
        toggleSection($wrap.find('.pt-modal-bio'), m.bio);
        toggleSection($wrap.find('.pt-modal-contacts'), m.contacts);
        toggleSection($wrap.find('.pt-modal-social'), m.social);

        $inner.animate({ opacity: 1 }, 150);

        // Update prev/next visibility
        $wrap.find('.pt-modal-prev').toggle(members.length > 1);
        $wrap.find('.pt-modal-next').toggle(members.length > 1);

        // Scroll content to top
        $wrap.find('.pt-modal-content')[0].scrollTop = 0;
        $wrap.find('.pt-modal-bio')[0].scrollTop = 0;
      }

      function toggleSection($el, content) {
        if (content && content.trim() !== '') {
          $el.show();
        } else {
          $el.hide();
        }
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
        $overlay.fadeOut(160);
        $('body').removeClass('pt-modal-open');
        $(document).off('keydown.ptModal');
      }
    });
  }

  /* Prevent body scroll when modal open */
  $('<style>.pt-modal-open { overflow: hidden !important; }</style>').appendTo('head');

  /* Basic focus trap */
  function trapFocus(modalEl) {
    var focusable = modalEl.querySelectorAll(
      'button, [href], input, select, textarea, [tabindex]:not([tabindex="-1"])'
    );
    if (!focusable.length) return;
    focusable[0].focus();
  }

})(jQuery);
