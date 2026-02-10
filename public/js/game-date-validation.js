// walidacja dat w formularzu tworzenia gry
(function() {
    var form = document.querySelector('.game-create-form');
    if (!form) return;

    var startInput = form.querySelector('#game-start');
    var endInput = form.querySelector('#game-end');
    if (!startInput || !endInput) return;

    // tworzymy elementy na błędy
    var startError = document.createElement('div');
    startError.className = 'game-create-error';
    startError.style.display = 'none';
    startInput.parentNode.insertBefore(startError, startInput.nextSibling);

    var endError = document.createElement('div');
    endError.className = 'game-create-error';
    endError.style.display = 'none';
    endInput.parentNode.insertBefore(endError, endInput.nextSibling);

    // zwraca obiekt Date z wartości datetime-local (YYYY-MM-DDTHH:mm)
    function parseDateTime(value) {
        if (!value) return null;
        return new Date(value);
    }

    // sprawdza czy data jest w przyszłości (nie wcześniej niż dzisiaj)
    function isFutureOrToday(dateValue) {
        if (!dateValue) return true; // puste pole = ok (opcjonalne)
        var date = parseDateTime(dateValue);
        if (!date || isNaN(date.getTime())) return false;
        var now = new Date();
        now.setHours(0, 0, 0, 0); // dzisiaj 00:00
        return date >= now;
    }

    // sprawdza czy data zakończenia jest po dacie rozpoczęcia
    function isEndAfterStart(startValue, endValue) {
        if (!startValue || !endValue) return true; // puste = ok
        var start = parseDateTime(startValue);
        var end = parseDateTime(endValue);
        if (!start || !end || isNaN(start.getTime()) || isNaN(end.getTime())) return true;
        return end > start;
    }

    // pokazuje lub ukrywa błąd
    function showError(errorElement, message) {
        errorElement.textContent = message;
        errorElement.style.display = 'block';
    }

    function hideError(errorElement) {
        errorElement.style.display = 'none';
    }

    function validateStartDate() {
        var ok = isFutureOrToday(startInput.value);
        if (ok) {
            hideError(startError);
        } else {
            showError(startError, 'Data rozpoczęcia nie może być wcześniejsza niż dzisiaj');
        }
        // po zmianie daty rozpoczęcia sprawdź też zakończenie
        if (endInput.value) {
            validateEndDate();
        }
        return ok;
    }

    function validateEndDate() {
        var futureOk = isFutureOrToday(endInput.value);
        var afterStartOk = isEndAfterStart(startInput.value, endInput.value);
        if (futureOk && afterStartOk) {
            hideError(endError);
        } else if (!futureOk) {
            showError(endError, 'Data zakończenia nie może być wcześniejsza niż dzisiaj');
        } else if (!afterStartOk) {
            showError(endError, 'Data zakończenia musi być późniejsza niż data rozpoczęcia');
        }
        return futureOk && afterStartOk;
    }

    // walidacja przy zmianie pola
    startInput.addEventListener('change', validateStartDate);
    endInput.addEventListener('change', validateEndDate);

    // walidacja przed wysłaniem formularza
    form.addEventListener('submit', function(ev) {
        var startOk = validateStartDate();
        var endOk = validateEndDate();
        if (!startOk || !endOk) {
            ev.preventDefault();
        }
    });
})();
