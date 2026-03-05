(function () {
  const dropdowns = document.querySelectorAll('.assigned-to-dropdown');
  const catalogSelect = document.getElementById('task_catalog_id');
  const durationInput = document.getElementById('duration_minutes');
  const durationHelp = document.getElementById('durationHelp');
  const locationFilter = document.getElementById('location_filter');
  const userJobdeskMap = window.userJobdeskMap || {};
  let currentUserId = null;
  let currentLocationId = locationFilter ? parseInt(locationFilter.value, 10) : null;
  const POINT_TO_MINUTES = 30;

  const normalizeId = (value) => {
    const num = parseInt(value, 10);
    return Number.isNaN(num) ? null : num;
  };

  const filterCatalogOptions = () => {
    if (!catalogSelect) return;
    const allowed = currentUserId ? userJobdeskMap[currentUserId] || [] : null;
    let hasEnabled = false;
    Array.from(catalogSelect.options).forEach((opt) => {
      if (!opt.value) {
        opt.hidden = false;
        opt.disabled = false;
        return;
      }
      const jobdeskId = parseInt(opt.getAttribute('data-jobdesk-id'), 10);
      const locationId = normalizeId(opt.getAttribute('data-location-id'));
      const okJobdesk = allowed ? allowed.includes(jobdeskId) : true;
      const okLocation = currentLocationId ? locationId === currentLocationId : true;
      const ok = okJobdesk && okLocation;
      opt.hidden = !ok;
      opt.disabled = !ok;
      if (ok) hasEnabled = true;
    });
    if (!hasEnabled) {
      catalogSelect.value = '';
    }
  };

  const applyCatalogDuration = () => {
    if (!catalogSelect || !durationInput) return;
    const option = catalogSelect.selectedOptions?.[0];
    if (!option || !option.value) {
      durationInput.readOnly = false;
      if (durationHelp) {
        durationHelp.textContent = 'Total menit yang akan dibagi ke slot progres. Due date tetap berlaku sebagai target akhir.';
      }
      return;
    }
    const unit = (option.getAttribute('data-unit') || '').toLowerCase();
    const value = parseFloat(option.getAttribute('data-value') || '0');
    if (unit === 'points') {
      const minutes = Math.round(value * POINT_TO_MINUTES);
      durationInput.value = minutes || '';
      durationInput.readOnly = true;
      if (durationHelp) {
        durationHelp.textContent = `Durasi otomatis: ${value} point x ${POINT_TO_MINUTES} menit = ${minutes} menit.`;
      }
    } else {
      if (value && (!durationInput.value || parseFloat(durationInput.value) <= 0)) {
        durationInput.value = Math.round(value);
      }
      durationInput.readOnly = false;
      if (durationHelp) {
        durationHelp.textContent = 'Total menit yang akan dibagi ke slot progres. Due date tetap berlaku sebagai target akhir.';
      }
    }
  };

  const filterUserList = (list) => {
    if (!locationFilter) return;
    list.querySelectorAll('[data-user-id]').forEach((btn) => {
      const userId = btn.getAttribute('data-user-id');
      if (!userId) {
        btn.classList.remove('d-none');
        return;
      }
      const locId = normalizeId(btn.getAttribute('data-location-id'));
      const show = currentLocationId ? locId === currentLocationId : true;
      btn.classList.toggle('d-none', !show);
    });
  };

  dropdowns.forEach((dropdown) => {
    const hiddenInput = dropdown.querySelector('input[type="hidden"]');
    const toggle = dropdown.querySelector('.assigned-to-toggle');
    const panel = dropdown.querySelector('.assigned-to-panel');
    const filterInput = dropdown.querySelector('.assigned-to-filter');
    const list = dropdown.querySelector('.assigned-to-list');
    if (!hiddenInput || !toggle || !panel || !filterInput || !list) return;

    const initId = hiddenInput.value;
    const currentBtn = initId ? list.querySelector('[data-user-id="' + initId + '"]') : null;
    if (currentBtn) {
      toggle.textContent = currentBtn.getAttribute('data-user-label');
      currentUserId = normalizeId(initId);
      filterCatalogOptions();
      applyCatalogDuration();
    } else {
      toggle.textContent = toggle.getAttribute('data-placeholder') || '-- Pilih user --';
    }

    const closePanel = () => panel.classList.add('d-none');
    const openPanel = () => {
      panel.classList.remove('d-none');
      filterInput.focus();
    };

    toggle.addEventListener('click', () => {
      if (panel.classList.contains('d-none')) {
        openPanel();
      } else {
        closePanel();
      }
    });

    filterInput.addEventListener('input', function () {
      const term = this.value.toLowerCase();
      list.querySelectorAll('[data-user-id]').forEach((btn) => {
        const text = btn.textContent.toLowerCase();
        btn.classList.toggle('d-none', term && !text.includes(term));
      });
    });

    list.addEventListener('click', (e) => {
      const btn = e.target.closest('[data-user-id]');
      if (!btn) return;
      hiddenInput.value = btn.getAttribute('data-user-id');
      toggle.textContent = btn.getAttribute('data-user-label');
      currentUserId = normalizeId(hiddenInput.value);
      filterCatalogOptions();
      applyCatalogDuration();
      closePanel();
    });

    document.addEventListener('click', (e) => {
      if (!dropdown.contains(e.target)) {
        closePanel();
      }
    });

    filterUserList(list);
  });

  if (locationFilter) {
    locationFilter.addEventListener('change', () => {
      currentLocationId = normalizeId(locationFilter.value);
      dropdowns.forEach((dropdown) => {
        const hiddenInput = dropdown.querySelector('input[type="hidden"]');
        const toggle = dropdown.querySelector('.assigned-to-toggle');
        const list = dropdown.querySelector('.assigned-to-list');
        if (!hiddenInput || !toggle || !list) return;
        filterUserList(list);
        const selectedBtn = hiddenInput.value
          ? list.querySelector('[data-user-id="' + hiddenInput.value + '"]')
          : null;
        if (selectedBtn && selectedBtn.classList.contains('d-none')) {
          hiddenInput.value = '';
          currentUserId = null;
          toggle.textContent = toggle.getAttribute('data-placeholder') || '-- Pilih user --';
        }
      });
      filterCatalogOptions();
      applyCatalogDuration();
    });
  }

  if (catalogSelect) {
    catalogSelect.addEventListener('change', () => {
      applyCatalogDuration();
    });
    applyCatalogDuration();
  }

  // Slot repeater
  (function () {
    const slotList = document.getElementById('slotList');
    const addBtn = document.getElementById('addSlotBtn');
    const clearBtn = document.getElementById('clearSlotsBtn');
    const durationInput = document.getElementById('duration_minutes');
    const slotSummary = document.getElementById('slotSummary');
    if (!slotList || !addBtn || !clearBtn) return;
    let idx = parseInt(slotList.getAttribute('data-initial-count') || slotList.querySelectorAll('.slot-row').length || 0);
    let isSyncing = false;

    const getDuration = () => {
      const val = parseFloat(durationInput?.value);
      return isNaN(val) || val <= 0 ? null : val;
    };

    const formatPct = (val, decimals = 2) => {
      if (!isFinite(val)) return '';
      const factor = Math.pow(10, decimals);
      const rounded = Math.round(val * factor) / factor;
      return Math.abs(rounded) < 0.01 ? 0 : rounded;
    };

    const updateSummary = () => {
      if (!slotSummary) return;
      let totalPct = 0;
      let totalMinutes = 0;
      slotList.querySelectorAll('.slot-row').forEach((row) => {
        const pct = parseFloat(row.querySelector('input[name$="[percentage]"]')?.value);
        const min = parseFloat(row.querySelector('input[name$="[minutes]"]')?.value);
        if (!isNaN(pct)) totalPct += pct;
        if (!isNaN(min)) totalMinutes += min;
      });
      totalPct = parseFloat(totalPct.toFixed(2));
      const durationVal = getDuration();
      const pctDiff = 100 - totalPct;
      let remainingPct = Math.abs(pctDiff) < 0.05 ? 0 : pctDiff;
      const displayTotalPct = remainingPct === 0 ? 100 : totalPct;
      let remainingMin = durationVal !== null ? durationVal - totalMinutes : null;
      if (remainingMin !== null && Math.abs(remainingMin) < 0.01) remainingMin = 0;
      slotSummary.textContent = [
        `Total %: ${formatPct(displayTotalPct)} / 100` + (remainingPct ? ` (sisa ${formatPct(remainingPct)})` : ''),
        durationVal !== null
          ? `Total menit: ${totalMinutes} / ${durationVal}` + (remainingMin !== null ? ` (sisa ${remainingMin})` : '')
          : `Total menit: ${totalMinutes}`
      ].join(' | ');
    };

    const syncRow = (row, from) => {
      if (isSyncing) return;
      const pctInput = row.querySelector('input[name$="[percentage]"]');
      const minInput = row.querySelector('input[name$="[minutes]"]');
      if (!pctInput || !minInput) return;
      const durationVal = getDuration();
      isSyncing = true;
      if (from === 'percentage') {
        const pct = parseFloat(pctInput.value);
        if (durationVal && !isNaN(pct)) {
          const minutes = Math.round((pct / 100) * durationVal);
          minInput.value = minutes || '';
        } else if (!durationVal) {
          minInput.value = '';
        }
        row.dataset.lastSource = 'percentage';
      } else if (from === 'minutes') {
        const mins = parseFloat(minInput.value);
        if (durationVal && !isNaN(mins)) {
          const pct = (mins / durationVal) * 100;
          pctInput.value = formatPct(pct) || '';
        } else if (!durationVal) {
          pctInput.value = '';
        }
        row.dataset.lastSource = 'minutes';
      } else if (from === 'duration-change') {
        const pctVal = parseFloat(pctInput.value);
        const minVal = parseFloat(minInput.value);
        if (durationVal && !isNaN(pctVal)) {
          const minutes = Math.round((pctVal / 100) * durationVal);
          minInput.value = minutes || '';
        } else if (durationVal && isNaN(pctVal) && !isNaN(minVal)) {
          const pct = (minVal / durationVal) * 100;
          pctInput.value = formatPct(pct) || '';
        }
      }
      isSyncing = false;
      updateSummary();
    };

    const attachSlotSync = (row) => {
      const pctInput = row.querySelector('input[name$="[percentage]"]');
      const minInput = row.querySelector('input[name$="[minutes]"]');
      if (!pctInput || !minInput) return;
      pctInput.addEventListener('input', () => syncRow(row, 'percentage'));
      minInput.addEventListener('input', () => syncRow(row, 'minutes'));
    };

    slotList.querySelectorAll('.slot-row').forEach((row) => attachSlotSync(row));

    if (durationInput) {
      durationInput.addEventListener('input', () => {
        slotList.querySelectorAll('.slot-row').forEach((row) => syncRow(row, 'duration-change'));
        updateSummary();
      });
    }

    const addSlotRow = () => {
      const row = document.createElement('div');
      row.className = 'row g-2 mb-2 slot-row';
      const showLabel = idx === 0;
      row.innerHTML = `
        <div class="col-md-4">
          ${showLabel ? '<label class="form-label">Nama / Tujuan</label>' : ''}
          <input type="text" name="slots[${idx}][name]" class="form-control" placeholder="Nama / Tujuan" required>
        </div>
        <div class="col-md-3">
          ${showLabel ? '<label class="form-label">Persentase (%)</label>' : ''}
          <input type="number" name="slots[${idx}][percentage]" class="form-control" min="0.01" max="100" step="0.01" placeholder="%" required>
        </div>
        <div class="col-md-3">
          ${showLabel ? '<label class="form-label">Menit</label>' : ''}
          <input type="number" name="slots[${idx}][minutes]" class="form-control" min="1" placeholder="Menit" required>
        </div>
        <div class="col-md-2">
          ${showLabel ? '<label class="form-label">Urutan</label>' : ''}
          <div class="d-flex align-items-center gap-2">
            <input type="number" name="slots[${idx}][order]" class="form-control" min="0" value="${idx}">
            <button type="button" class="btn btn-sm btn-outline-danger remove-slot">X</button>
          </div>
        </div>
      `;
      slotList.appendChild(row);
      attachSlotSync(row);
      updateSummary();
      idx++;
    };

    addBtn.addEventListener('click', addSlotRow);

    slotList.addEventListener('click', (e) => {
      if (e.target.classList.contains('remove-slot')) {
        e.preventDefault();
        const row = e.target.closest('.slot-row');
        if (row) row.remove();
        updateSummary();
      }
    });

    clearBtn.addEventListener('click', () => {
      slotList.innerHTML = '';
      idx = 0;
      addSlotRow();
    });

    updateSummary();
  })();
})();
