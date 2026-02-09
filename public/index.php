<?php
// Sesja: parametry ciasteczka (HttpOnly, SameSite) - bezpieczne ciasteczko sesji.
session_set_cookie_params([
    'httponly' => true,
    'samesite' => 'Lax'
]);
session_start();


// Ładujemy połączenie z bazą (będzie potrzebne w kontrolerach).
require_once __DIR__ . '/../src/Database.php';
// Ładujemy router – on zdecyduje, który kontroler i metoda się wykonają.
require_once __DIR__ . '/../src/Routing.php';

require_once __DIR__ . '/../src/repository/UserRepository.php';




// Jedna linijka na start: wszystko idzie przez router.
Routing::run();