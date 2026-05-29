/**
 * WPYog Team — Admin JS  v1.0.15
 */
(function ($) {
  'use strict';

  /* =========================================================
     Copy shortcode button
  ========================================================= */
  $(document).on('click', '.pt-copy-sc', function (e) {
    e.preventDefault();
    var text = $(this).data('sc') || $(this).attr('data-clipboard-text') || '';
    if (!text) return;
    if (navigator.clipboard) {
      navigator.clipboard.writeText(text).then(function () { flashCopy($(e.currentTarget)); });
    } else {
      var ta = document.createElement('textarea');
      ta.value = text;
      document.body.appendChild(ta);
      ta.select();
      document.execCommand('copy');
      document.body.removeChild(ta);
      flashCopy($(e.currentTarget));
    }
  });
  function flashCopy($btn) {
    var orig = $btn.text();
    $btn.text('Copied!').prop('disabled', true);
    setTimeout(function () { $btn.text(orig).prop('disabled', false); }, 1800);
  }

  /* =========================================================
     Repeater rows (contact details / social icons)
  ========================================================= */
  $(document).on('click', '.pt-add-row', function (e) {
    e.preventDefault();
    var tableId = $(this).data('table');
    var $table  = $('#' + tableId);
    var $tmpl   = $table.find('.pt-template-row').first();
    if (!$tmpl.length) return;

    var $newRow = $tmpl.clone().removeClass('pt-template-row screen-reader-text');
    $newRow.find('input, textarea').val('');
    $newRow.find('select').prop('selectedIndex', 0);

    // Remove any Select2 state carried over from the cloned template row.
    // Select2 adds 'select2-hidden-accessible' class + data attributes to the <select>.
    // If we don't clean these up, initIconSelects() guard will skip reinit and the
    // cloned row will render as a plain <select> instead of a Select2 dropdown.
    $newRow.find('select.pt-icon-select').each(function () {
      $(this)
        .removeClass('select2-hidden-accessible')
        .removeAttr('data-select2-id')
        .removeAttr('aria-hidden')
        .removeData();
    });
    // Also remove any stray Select2 container elements cloned into the row
    $newRow.find('.select2-container').remove();

    $table.find('tbody').append($newRow);
    initIconSelects($newRow);
  });

  $(document).on('click', '.pt-remove-row', function (e) {
    e.preventDefault();
    var $tbody   = $(this).closest('tbody');
    var $row     = $(this).closest('tr');
    var realRows = $tbody.find('tr:not(.pt-template-row)').length;
    if (realRows <= 1) {
      $row.find('input, textarea').val('');
      $row.find('select').prop('selectedIndex', 0);
      // Reset Select2 visually
      $row.find('select.pt-icon-select').each(function () {
        if ($(this).hasClass('select2-hidden-accessible')) {
          $(this).val('').trigger('change.select2');
        }
      });
    } else {
      $row.remove();
    }
  });

  // Make repeater tables sortable
  function initRepeaterSort() {
    $('.pt-repeater-table tbody').sortable({
      handle: '.pt-drag-col',
      items: 'tr:not(.pt-template-row)',
      placeholder: 'pt-row-placeholder',
      tolerance: 'pointer',
    });
  }

  /* =========================================================
     Icon select2 on member details page
  ========================================================= */
  function initIconSelects($ctx) {
    ($ctx || $(document)).find('select.pt-icon-select').each(function () {
      // Skip if already a fully-initialised Select2 instance
      if ($(this).hasClass('select2-hidden-accessible')) return;
      $(this).select2({
        width: '100%',
        templateResult: function (state) {
          if (!state.id) return state.text;
          return $('<span><i class="pt-icon-' + state.id + '" style="margin-right:6px;vertical-align:middle;"></i>' + state.text + '</span>');
        },
        templateSelection: function (state) {
          if (!state.id) return state.text;
          return $('<span><i class="pt-icon-' + state.id + '" style="margin-right:6px;vertical-align:middle;"></i>' + state.text + '</span>');
        },
      });
    });
  }

  /* =========================================================
     Team member picker + hidden select sync
  ========================================================= */

  var $addSelect    = $('#pt-member-add-select');   // search/add dropdown (no name)
  var $hiddenSelect = $('#pt-memberlist-hidden');   // <select multiple name="memberlist[]">
  var $memberList   = $('#pt-members-list');        // sortable <ul>

  /**
   * Sync the visible list to the hidden <select multiple>.
   *
   * CRITICAL: browsers submit <select multiple> options in DOM order (the order
   * the <option> elements appear in the HTML), NOT the order they were selected.
   * So simply toggling .selected is not enough — we must REBUILD the options in
   * the exact drag order so the server receives them in the right sequence.
   */
  function syncHiddenSelect() {
    // Gather IDs in current visual order
    var ids = [];
    $memberList.find('li[data-id]').each(function () {
      ids.push(String($(this).data('id')));
    });

    // Rebuild <option> list in that order so POST preserves it
    $hiddenSelect.empty();
    ids.forEach(function (id) {
      $hiddenSelect.append(
        $('<option>', { value: id, selected: true }).text(id)
      );
    });
  }

  // Run sync on any interaction inside the metabox and before form submits
  $(document).on('click', '#pt_team_details', syncHiddenSelect);
  $('form#post').on('submit', syncHiddenSelect);

  // ---- Add member ----

  function addMemberToList(id, img, title) {
    id    = String(id);
    img   = img   || (window.wpyogTeam && window.wpyogTeam.defaultImage) || '';
    title = title || id;

    // Don't add duplicates
    if ($memberList.find('li[data-id="' + id + '"]').length) return;

    // Remove placeholder
    $memberList.find('.pt-no-members-msg').remove();

    var $li = $(
      '<li data-id="' + id + '">' +
        '<span class="dashicons dashicons-move pt-drag-handle"></span>' +
        '<img width="32" height="32" src="' + img + '" alt="">' +
        '<span class="pt-member-name">' + title + '</span>' +
        '<span class="pt-remove-member dashicons dashicons-no-alt" data-id="' + id + '" title="Remove"></span>' +
      '</li>'
    );
    $memberList.append($li);

    // Disable in add-select
    $addSelect.find('option[value="' + id + '"]').prop('disabled', true);

    syncHiddenSelect();
  }

  // ---- Remove member ----

  $(document).on('click', '.pt-remove-member', function () {
    var id = String($(this).data('id'));
    $(this).closest('li').remove();

    if ($memberList.find('li[data-id]').length === 0) {
      $memberList.append('<li class="pt-no-members-msg">No members selected. Use the search above to add members.</li>');
    }

    // Re-enable in add-select
    $addSelect.find('option[value="' + id + '"]').prop('disabled', false);
    if ($addSelect.hasClass('select2-hidden-accessible')) {
      $addSelect.trigger('change.select2');
    }

    syncHiddenSelect();
  });

  // ---- Init Select2 on the add-select (enhanced search) ----

  if ($addSelect.length) {
    $addSelect.select2({
      width: '100%',
      allowClear: true,
      placeholder: $addSelect.data('placeholder') || 'Search and add a member…',
      templateResult: function (opt) {
        if (!opt.id) return opt.text;
        var img = $(opt.element).data('img') || (window.wpyogTeam && window.wpyogTeam.defaultImage) || '';
        return $(
          '<span style="display:flex;align-items:center;gap:8px;">' +
          '<img src="' + img + '" width="28" height="28" style="border-radius:3px;object-fit:cover;flex-shrink:0;" onerror="this.style.display=\'none\'">' +
          '<span>' + opt.text + '</span></span>'
        );
      },
    });

    // Listen for select2:select event
    $addSelect.on('select2:select', function (e) {
      var data  = e.params.data;
      var img   = $(data.element).data('img') || '';
      addMemberToList(data.id, img, data.text);
      $addSelect.val(null).trigger('change.select2');
    });

    // ALSO listen to plain change event (fallback if select2:select doesn't fire)
    $addSelect.on('change', function () {
      var val = $(this).val();
      if (!val) return;
      var $opt = $(this).find('option[value="' + val + '"]');
      var img  = $opt.data('img') || '';
      addMemberToList(val, img, $opt.text());
      $(this).val('').trigger('change.select2');
    });
  }

  // ---- Make members list sortable ----

  $memberList.sortable({
    handle: '.pt-drag-handle',
    items: 'li:not(.pt-no-members-msg)',
    tolerance: 'pointer',
    stop: function () { syncHiddenSelect(); },
  });

  /* =========================================================
     Layout option radio highlight
  ========================================================= */
  $(document).on('change', 'input[name="layout"]', function () {
    $('.pt-layout-option').removeClass('is-selected');
    $(this).closest('.pt-layout-option').addClass('is-selected');
  });

  /* =========================================================
     Init on DOM ready
  ========================================================= */
  $(function () {
    initRepeaterSort();
    initIconSelects();
    syncHiddenSelect(); // run once on load to ensure initial state is correct
  });

})(jQuery);
