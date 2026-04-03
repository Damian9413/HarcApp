<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Logowanie – HarcApp</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Hubballi&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="/css/style.css">
    <script src="https://code.iconify.design/3/3.1.0/iconify.min.js"></script>
</head>
<body>
    <div class="login-page">
        <div class="login-background"></div>
        <div class="login-right">
        <div class="login-container">
            <div class="login-logo-icon">
                <span class="iconify" data-icon="ph:compass-fill" data-width="150" data-height="150" style="color: #FF383C;"></span>
            </div>
            <h2 class="login-title">Logowanie do aplikacji</h2>
            <?php if ($error !== ''): ?>
                <p class="login-error"><?= htmlspecialchars($error) ?></p>
            <?php endif; ?>
            <form class="login-form" method="post" action="/security/login">
                <label class="login-label">Nazwa użytkownika</label>
                <div class="login-input-wrap">
                    <span class="login-input-icon icon-left" aria-hidden="true">
                        <span class="iconify" data-icon="mdi:account-outline" data-width="28" data-height="28"></span>
                    </span>
                    <input type="email" name="email" class="login-input" placeholder="Wpisz nazwę użytkownika" value="<?= htmlspecialchars($email ?? '') ?>" required>
                </div>
                <label class="login-label">Hasło</label>
                <div class="login-input-wrap">
                    <span class="login-input-icon icon-left" aria-hidden="true">
                        <span class="iconify" data-icon="mdi:lock-outline" data-width="28" data-height="28"></span>
                    </span>
                    <input type="password" name="password" id="login-password" class="login-input" placeholder="Wpisz hasło" required>
                    <button type="button" class="login-input-icon icon-right login-toggle-password" aria-label="Pokaż/ukryj hasło" title="Pokaż hasło">
                        <span class="iconify login-icon-eye" data-icon="mdi:eye-off-outline" data-width="28" data-height="28"></span>
                    </button>
                </div>
                <button type="submit" class="login-button">Zaloguj się</button>
                <p class="login-register">Nie masz konta? <a href="/security/register" class="login-link-accent">Zarejestruj się!</a></p>
            </form>
        </div>
        </div>
    </div>
    <script>
        (function() {
            var btn = document.querySelector('.login-toggle-password');
            var input = document.getElementById('login-password');
            var icon = document.querySelector('.login-icon-eye');
            if (btn && input && icon) {
                btn.addEventListener('click', function() {
                    var isPassword = input.type === 'password';
                    input.type = isPassword ? 'text' : 'password';
                    icon.setAttribute('data-icon', isPassword ? 'mdi:eye-outline' : 'mdi:eye-off-outline');
                    btn.setAttribute('title', isPassword ? 'Ukryj hasło' : 'Pokaż hasło');
                });
            }
        })();
    </script>
</body>
</html>