function initColToggle(storageKey, alwaysColsArray) {
    var alwaysCols = new Set(alwaysColsArray || []);

    function applyColVisibility(hidden) {
        document.querySelectorAll('.js-col-toggle').forEach(function (cb) {
            var col = cb.dataset.col;
            cb.checked = !hidden.has(col);
            document.querySelectorAll('.pc-' + col).forEach(function (el) {
                el.style.display = hidden.has(col) ? 'none' : '';
            });
        });
    }

    function loadHidden() {
        try {
            var stored = JSON.parse(localStorage.getItem(storageKey) || '[]');
            return new Set(stored.filter(function (c) { return !alwaysCols.has(c); }));
        } catch (e) { return new Set(); }
    }

    function saveHidden(hidden) {
        localStorage.setItem(storageKey, JSON.stringify(Array.from(hidden)));
    }

    var btn = document.getElementById('colToggleBtn');
    var panel = document.getElementById('colTogglePanel');
    if (!btn || !panel) return;

    btn.addEventListener('click', function (e) {
        e.stopPropagation();
        panel.style.display = panel.style.display === 'none' ? 'block' : 'none';
    });
    panel.addEventListener('click', function (e) { e.stopPropagation(); });
    document.addEventListener('click', function () {
        if (panel) panel.style.display = 'none';
    });

    document.querySelectorAll('.js-col-toggle').forEach(function (cb) {
        cb.addEventListener('change', function () {
            var hidden = loadHidden();
            if (this.checked) { hidden.delete(this.dataset.col); }
            else              { hidden.add(this.dataset.col); }
            saveHidden(hidden);
            applyColVisibility(hidden);
        });
    });

    var resetBtn = document.getElementById('colResetBtn');
    if (resetBtn) {
        resetBtn.addEventListener('click', function () {
            saveHidden(new Set());
            applyColVisibility(new Set());
        });
    }

    applyColVisibility(loadHidden());
}
