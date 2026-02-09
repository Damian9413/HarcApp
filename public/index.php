<?php


//klasa do polaczenia z baza
require_once __DIR__ . '/../src/Database.php';

//CZy dziala
$pdo = Database::connect();
// Test: czy PHP i serwer działają
echo '<h1>HarcApp</h1>';
echo '<p>PHP: ' . phpversion() . '</p>';

echo '<p>Połączenie z bazą: OK</p>';

//stmt - kurier 
$stmt = $pdo->query('SELECT 1');
echo '<p>Zapytanie testowe: ' . ($stmt ? 'OK' : 'błąd') . '</p>';


