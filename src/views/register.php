<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Rejestracja – HarcApp</title>
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
                <?php if (isset($success) && $success): ?>
                    <h2 class="login-title">Konto utworzone</h2>
                    <p class="login-register" style="margin-top: 1rem;">Konto zostało utworzone. Czekaj na zatwierdzenie administratora.</p>
                    <p class="login-register" style="margin-top: 1.5rem;"><a href="/security/login" class="login-link-accent">Zaloguj się</a></p>
                <?php else: ?>
                <h2 class="login-title">Rejestracja</h2>
                <?php if (isset($error) && $error !== ''): ?>
                    <p class="login-error"><?= htmlspecialchars($error) ?></p>
                <?php endif; ?>
                <form class="login-form" method="post" action="/security/register">
                    <label class="login-label">Email</label>
                    <div class="login-input-wrap">
                        <span class="login-input-icon icon-left" aria-hidden="true">
                            <span class="iconify" data-icon="mdi:email-outline" data-width="28" data-height="28"></span>
                        </span>
                        <input type="email" name="email" class="login-input" placeholder="Wpisz email" value="<?= htmlspecialchars($email ?? '') ?>" required>
                    </div>
                    <label class="login-label">Hasło</label>
                    <div class="login-input-wrap">
                        <span class="login-input-icon icon-left" aria-hidden="true">
                            <span class="iconify" data-icon="mdi:lock-outline" data-width="28" data-height="28"></span>
                        </span>
                        <input type="password" name="password" class="login-input" placeholder="Wpisz hasło" required>
                    </div>
                    <label class="login-label">Powtórz hasło</label>
                    <div class="login-input-wrap">
                        <span class="login-input-icon icon-left" aria-hidden="true">
                            <span class="iconify" data-icon="mdi:lock-outline" data-width="28" data-height="28"></span>
                        </span>
                        <input type="password" name="password_repeat" class="login-input" placeholder="Powtórz hasło" required>
                    </div>
                    <label class="login-label">Imię</label>
                    <div class="login-input-wrap">
                        <span class="login-input-icon icon-left" aria-hidden="true">
                            <span class="iconify" data-icon="mdi:account-outline" data-width="28" data-height="28"></span>
                        </span>
                        <input type="text" name="name" class="login-input" placeholder="Wpisz imię" value="<?= htmlspecialchars($name ?? '') ?>" required>
                    </div>
                    <button type="submit" class="login-button">Zarejestruj się</button>
                    <p class="login-register">Masz konto? <a href="/security/login" class="login-link-accent">Zaloguj się!</a></p>
                </form>
                <?php endif; ?>
            </div>
        </div>
    </div>
</body>
</html>
