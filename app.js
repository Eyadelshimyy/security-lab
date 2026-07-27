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
        goal_error: { text: 'Please enter a goal title.', type: 'error' },
        entry_added: { text: 'Journal entry saved.', type: 'success' },
        entry_deleted: { text: 'Journal entry removed.', type: 'success' },
        entry_error: { text: 'Please fill in a title and some text.', type: 'error' },
        profile_updated: { text: 'Profile updated.', type: 'success' },
        password_updated: { text: 'Password changed.', type: 'success' },
        profile_error: { text: 'That update could not be completed.', type: 'error' },
        ctf_added: { text: 'CTF room added.', type: 'success' },
        ctf_updated: { text: 'Notes saved.', type: 'success' },
        ctf_deleted: { text: 'CTF room removed.', type: 'success' },
        ctf_error: { text: 'Please enter a room name.', type: 'error' }
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

    function wireThemeToggle() {
        var toggle = document.getElementById('themeToggle');
        if (!toggle) return;

        toggle.addEventListener('click', function () {
            var isRed = document.documentElement.getAttribute('data-theme') === 'red';
            if (isRed) {
                document.documentElement.removeAttribute('data-theme');
                try { localStorage.setItem('theme', 'green'); } catch (e) {}
            } else {
                document.documentElement.setAttribute('data-theme', 'red');
                try { localStorage.setItem('theme', 'red'); } catch (e) {}
            }
        });
    }

    function wireProfileMenu() {
        var trigger = document.getElementById('profileTrigger');
        var dropdown = document.getElementById('profileDropdown');
        if (!trigger || !dropdown) return;

        function close() {
            dropdown.hidden = true;
            trigger.setAttribute('aria-expanded', 'false');
        }

        function open() {
            dropdown.hidden = false;
            trigger.setAttribute('aria-expanded', 'true');
        }

        trigger.addEventListener('click', function (e) {
            e.stopPropagation();
            if (dropdown.hidden) {
                open();
            } else {
                close();
            }
        });

        document.addEventListener('click', function (e) {
            if (!dropdown.hidden && !dropdown.contains(e.target) && e.target !== trigger) {
                close();
            }
        });

        document.addEventListener('keydown', function (e) {
            if (e.key === 'Escape') close();
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
        wireProfileMenu();
        wireThemeToggle();
        animateGauges();
        animateBars();
        animateCounters();
    });
})();
