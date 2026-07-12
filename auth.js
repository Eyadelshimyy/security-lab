(function () {
    var MESSAGES = {
        invalid: { text: 'Invalid username or password.', type: 'error' },
        duplicate: { text: 'That username or email is already taken.', type: 'error' },
        weakpass: { text: 'Password must be at least 8 characters.', type: 'error' },
        server_error: { text: 'Something went wrong. Please try again.', type: 'error' },
        registered: { text: 'Account created! Log in below.', type: 'success' }
    };

    function showBanner() {
        var params = new URLSearchParams(window.location.search);
        var code = params.get('error') || (params.get('registered') === '1' ? 'registered' : null);
        if (!code || !MESSAGES[code]) return;

        var form = document.querySelector('.auth-form form');
        if (!form) return;

        var info = MESSAGES[code];
        var banner = document.createElement('div');
        banner.className = 'auth-banner ' + info.type;
        banner.textContent = info.text;
        form.parentNode.insertBefore(banner, form);
    }

    function wirePasswordHint() {
        var password = document.getElementById('password');
        var hint = document.querySelector('.form-hint');
        if (!password || !hint) return;

        password.addEventListener('input', function () {
            if (password.value.length >= 8) {
                hint.classList.add('hint-ok');
            } else {
                hint.classList.remove('hint-ok');
            }
        });
    }

    document.addEventListener('DOMContentLoaded', function () {
        showBanner();
        wirePasswordHint();
    });
})();
