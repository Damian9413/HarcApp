<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>500 - Błąd serwera | HarcApp</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Hubballi&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="/css/style.css">
    <style>
        .error-page {
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            background: var(--bg);
            color: var(--text);
            text-align: center;
            padding: 2rem;
        }
        .error-code {
            font-size: 8rem;
            font-weight: bold;
            color: var(--primary);
            margin: 0;
            line-height: 1;
        }
        .error-title {
            font-size: 2rem;
            margin: 1rem 0;
        }
        .error-message {
            font-size: 1.1rem;
            color: var(--text-muted);
            max-width: 500px;
            margin-bottom: 2rem;
        }
        .error-link {
            display: inline-block;
            background: var(--primary);
            color: white;
            padding: 0.75rem 2rem;
            border-radius: 8px;
            text-decoration: none;
            transition: background 0.2s;
        }
        .error-link:hover {
            background: var(--primary-hover);
        }
    </style>
</head>
<body>
    <div class="error-page">
        <p class="error-code">500</p>
        <h1 class="error-title">Błąd serwera</h1>
        <p class="error-message">
            Wystąpił nieoczekiwany błąd po stronie serwera.
            Spróbuj ponownie za chwilę lub skontaktuj się z administratorem.
        </p>
        <a href="/" class="error-link">Wróć do strony głównej</a>
    </div>
</body>
</html>
