<?php

class SecurityController{
    public function login(): void{

        if ($_SERVER['REQUEST_METHOD'] === 'GET'){
            echo '<h2>Logowanie</h2>';
            echo '<form method="post" action="/security/login">';
            echo '<label>Email: <input type="email" name="email" required></label><br>'; //requires -nie wysle jesli puste. name= -> potem bedzie dzieki temu $_POST['email']
            echo '<label>Hasło: <input type="password" name="password" required></label><br>';
            echo '<button type="submit">Zaloguj</button>';
            echo '</form>';
            echo '<p><a href="/">Strona główna</a></p>';
            return;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST'){
            $email = $_POST['email'] ?? '';
            $password = $_POST['password'] ?? '';
            echo '<h1>Odebrano dane (test)</h1>';
            echo '<p>Email: ' . htmlspecialchars($email) . '</p>';
            echo '<p>Hasło: (nie wyświetlamy – później password_verify)</p>';
            echo '<p><a href="/security/login">Wróć do formularza</a></p>';
            return;
            

        }

    }
}