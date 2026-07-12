(function () {
    var MESSAGES = {
        habit_added: { text: 'Habit added.', type: 'success' },
        habit_deleted: { text: 'Habit removed.', type: 'success' },
        habit_error: { text: 'Habit name is required.', type: 'error' },
        transaction_added: { text: 'Transaction added.', type: 'success' },
        transaction_deleted: { text: 'Transaction removed.', type: 'success' },
        transaction_error: { text: 'Please enter a valid description and amount.', type: 'error' },
        goal_added: { text: 'Learning goal added.', type: 'success' },
        goal_updated: { text: 'Progress updated.', type: 'success' },
        goal_deleted: { text: 'Learning goal removed.', type: 'success' },
        goal_error: { text: 'Please enter a goal title.', type: 'error' }
    };

    function showFlash() {
        var params = new URLSearchParams(window.location.search);
        var code = params.get('msg');
        var toast = document.getElementById('flashToast');
        if (!code || !toast || !MESSAGES[code]) return;

        var info = MESSAGES[code];
        toast.textContent = info.text;
        toast.className = 'flash-toast show ' + info.type;
        toast.hidden = false;

        window.setTimeout(function () {
            toast.className = 'flash-toast';
            window.setTimeout(function () { toast.hidden = true; }, 300);
        }, 3200);

        params.delete('msg');
        var newSearch = params.toString();
        var newUrl = window.location.pathname + (newSearch ? '?' + newSearch : '') + window.location.hash;
        window.history.replaceState({}, '', newUrl);
    }

    function wireSearch() {
        var input = document.getElementById('pageSearch');
        var list = document.querySelector('[data-searchable-list]');
        if (!input || !list) return;

        input.addEventListener('input', function () {
            var term = input.value.trim().toLowerCase();
            var items = list.querySelectorAll('[data-search-text]');
            var visibleCount = 0;
            items.forEach(function (item) {
                var haystack = item.getAttribute('data-search-text').toLowerCase();
                var match = haystack.indexOf(term) !== -1;
                item.style.display = match ? '' : 'none';
                if (match) visibleCount++;
            });
            var emptyState = list.querySelector('[data-search-empty]');
            if (emptyState) {
                emptyState.hidden = visibleCount !== 0 || term === '';
            }
        });
    }

    function wireDeleteConfirm() {
        document.querySelectorAll('[data-confirm]').forEach(function (form) {
            form.addEventListener('submit', function (e) {
                if (!window.confirm(form.getAttribute('data-confirm'))) {
                    e.preventDefault();
                }
            });
        });
    }

    function animateGauges() {
        var gauges = document.querySelectorAll('[data-gauge]');
        requestAnimationFrame(function () {
            requestAnimationFrame(function () {
                gauges.forEach(function (gauge) {
                    var percent = Math.max(0, Math.min(100, parseFloat(gauge.getAttribute('data-gauge')) || 0));
                    gauge.style.setProperty('--gauge-deg', (percent * 3.6) + 'deg');
                });
            });
        });
    }

    function animateBars() {
        var bars = document.querySelectorAll('.bar-fill');
        bars.forEach(function (bar, i) {
            var target = bar.getAttribute('data-bar-height') + '%';
            window.setTimeout(function () {
                bar.style.height = target;
            }, 80 + i * 60);
        });

        var hBars = document.querySelectorAll('.bar-fill-h');
        hBars.forEach(function (bar, i) {
            var target = bar.getAttribute('data-bar-width') + '%';
            window.setTimeout(function () {
                bar.style.width = target;
            }, 80 + i * 60);
        });
    }

    function animateCounters() {
        var counters = document.querySelectorAll('[data-count-to]');
        var duration = 900;
        counters.forEach(function (el) {
            var target = parseFloat(el.getAttribute('data-count-to')) || 0;
            var start = performance.now();

            function tick(now) {
                var progress = Math.min(1, (now - start) / duration);
                var eased = 1 - Math.pow(1 - progress, 3);
                el.textContent = Math.round(target * eased);
                if (progress < 1) {
                    requestAnimationFrame(tick);
                } else {
                    el.textContent = target;
                }
            }
            requestAnimationFrame(tick);
        });
    }

    document.addEventListener('DOMContentLoaded', function () {
        showFlash();
        wireSearch();
        wireDeleteConfirm();
        animateGauges();
        animateBars();
        animateCounters();
    });
})();
