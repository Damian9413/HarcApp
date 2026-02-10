<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Logowanie – HarcApp</title>
    <link rel="stylesheet" href="/css/style.css">
</head>
<body>
    <div class="login-page">
        <div class="login-background"></div>
        <div class="login-container">
            <h1 class="login-logo">HarcApp</h1>
            <h2 class="login-title">Logowanie do aplikacji</h2>
            <?php if ($error !== ''): ?>
                <p class="login-error"><?= htmlspecialchars($error) ?></p>
            <?php endif; ?>
            <form class="login-form" method="post" action="/security/login">
                <label class="login-label">Nazwa użytkownika</label>
                <input type="email" name="email" class="login-input" placeholder="Wpisz nazwę użytkownika" value="<?= htmlspecialchars($email ?? '') ?>" required>
                <label class="login-label">Hasło</label>
                <input type="password" name="password" class="login-input" placeholder="Wpisz hasło" required>
                <a href="#" class="login-link">Nie pamiętasz hasła?</a>
                <button type="submit" class="login-button">Zaloguj się</button>
                <p class="login-register">Nie masz konta? <a href="/security/register" class="login-link-accent">Zarejestruj się!</a></p>
            </form>
            <p><a href="/" class="login-link">Strona główna</a></p>
        </div>
    </div>
</body>
</html>