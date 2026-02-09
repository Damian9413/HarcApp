<?php

class HomeController
{
    public function index(): void
    {
        echo '<h1>HarcApp</h1>';
        echo '<p>Strona główna.</p>';

        // Sprawdzenie, czy użytkownik jest zalogowany (sesja ma user_id)
        if (isset($_SESSION['user_id'])) {
            echo '<p>Zalogowany jako: ' . htmlspecialchars($_SESSION['user_email'] ?? '') . '</p>';
            echo '<p><a href="/security/logout">Wyloguj</a></p>';
        } else {
            echo '<p><a href="/security/login">Zaloguj się</a></p>';
        }
    }
}