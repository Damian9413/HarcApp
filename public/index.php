<?php
// ustawienia sesji - httponly i secure dla bezp.
session_set_cookie_params([
    'secure' => isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on',
    'httponly' => true,
    'samesite' => 'Lax'
]);
session_start();

require_once __DIR__ . '/../src/Database.php';
require_once __DIR__ . '/../src/Routing.php';
require_once __DIR__ . '/../src/repository/UserRepository.php';

// start
Routing::run();
