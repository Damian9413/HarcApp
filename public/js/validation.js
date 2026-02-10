// walidacja formularza rejestracji
(function() {
    var form = document.querySelector('.login-form');
    if (!form) return;

    var emailInput = form.querySelector('input[name="email"]');
    var passwordInput = form.querySelector('input[name="password"]');
    var confirmedPasswordInput = form.querySelector('input[name="password_repeat"]');
    var nameInput = form.querySelector('input[name="name"]');

    if (!emailInput || !passwordInput || !confirmedPasswordInput || !nameInput) return;

    var validationTimeouts = {};

    function isEmail(email) {
        return /\S+@\S+\.\S+/.test(email);
    }

    function arePasswordsSame(password, confirmedPassword) {
        return password === confirmedPassword;
    }

    function isPasswordStrong(password) {
        return password.length >= 6;
    }

    function isNameValid(name) {
        if (name.length < 2) return false;
        return /^[a-zA-ZąćęłńóśźżĄĆĘŁŃÓŚŹŻ\s\-]+$/.test(name);
    }

    function markValidation(element, condition) {
        // puste - tylko tekst pod spodem
    }

    // debounce zeby nie spamowac
    function debounce(key, delay, fn) {
        if (validationTimeouts[key]) clearTimeout(validationTimeouts[key]);
        validationTimeouts[key] = setTimeout(function() {
            fn();
            validationTimeouts[key] = null;
        }, delay);
    }

    var DEBOUNCE_MS = 500;

    function validateEmail() {
        debounce('email', DEBOUNCE_MS, function() {
            var email = emailInput.value.trim();
            if (!isEmail(email)) {
                markValidation(emailInput, false);
                showEmailStatus('Nieprawidłowy format email', false);
                return;
            }
            checkEmailAvailability(email);
        });
    }

    // fetch do sprawdzenia maila
    function checkEmailAvailability(email) {
        showEmailStatus('Sprawdzanie...', null);

        fetch('/security/checkEmail', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ email: email })
        })
        .then(function(response) {
            return response.json();
        })
        .then(function(data) {
            if (data.available) {
                markValidation(emailInput, true);
                showEmailStatus(data.message, true);
            } else {
                markValidation(emailInput, false);
                showEmailStatus(data.message, false);
            }
        })
        .catch(function(error) {
            console.error('blad fetch:', error);
            markValidation(emailInput, isEmail(email));
            showEmailStatus('', null);
        });
    }

    function showEmailStatus(message, isSuccess) {
        var statusEl = document.getElementById('email-status');
        var wrapper = emailInput.closest('.login-input-wrapper') || emailInput.parentNode;
        
        if (!statusEl) {
            statusEl = document.createElement('div');
            statusEl.id = 'email-status';
            statusEl.className = 'validation-message';
            wrapper.parentNode.insertBefore(statusEl, wrapper.nextSibling);
        }
        
        statusEl.textContent = message;
        statusEl.classList.remove('validation-message--success', 'validation-message--error', 'validation-message--loading');
        
        if (isSuccess === true) {
            statusEl.classList.add('validation-message--success');
        } else if (isSuccess === false) {
            statusEl.classList.add('validation-message--error');
        } else if (message) {
            statusEl.classList.add('validation-message--loading');
        }
    }

    function validatePassword() {
        debounce('password', DEBOUNCE_MS, function() {
            markValidation(passwordInput, isPasswordStrong(passwordInput.value));
        });
    }

    function validatePasswordRepeat() {
        debounce('password_repeat', DEBOUNCE_MS, function() {
            var condition = arePasswordsSame(passwordInput.value, confirmedPasswordInput.value);
            markValidation(confirmedPasswordInput, condition);
        });
    }

    function validateName() {
        debounce('name', DEBOUNCE_MS, function() {
            markValidation(nameInput, isNameValid(nameInput.value.trim()));
        });
    }

    emailInput.addEventListener('keyup', validateEmail);
    emailInput.addEventListener('blur', function() {
        markValidation(emailInput, isEmail(emailInput.value.trim()));
    });

    passwordInput.addEventListener('keyup', validatePassword);
    passwordInput.addEventListener('blur', function() {
        markValidation(passwordInput, isPasswordStrong(passwordInput.value));
    });

    confirmedPasswordInput.addEventListener('keyup', validatePasswordRepeat);
    confirmedPasswordInput.addEventListener('blur', function() {
        markValidation(confirmedPasswordInput, arePasswordsSame(passwordInput.value, confirmedPasswordInput.value));
    });

    nameInput.addEventListener('keyup', validateName);
    nameInput.addEventListener('blur', function() {
        markValidation(nameInput, isNameValid(nameInput.value.trim()));
    });

    form.addEventListener('submit', function(ev) {
        var emailOk = isEmail(emailInput.value.trim());
        var passwordOk = isPasswordStrong(passwordInput.value);
        var sameOk = arePasswordsSame(passwordInput.value, confirmedPasswordInput.value);
        var nameOk = isNameValid(nameInput.value.trim());

        markValidation(emailInput, emailOk);
        markValidation(passwordInput, passwordOk);
        markValidation(confirmedPasswordInput, sameOk);
        markValidation(nameInput, nameOk);

        if (!emailOk || !passwordOk || !sameOk || !nameOk) {
            ev.preventDefault();
        }
    });
})();
