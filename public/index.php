<?php

// Ładujemy połączenie z bazą (będzie potrzebne w kontrolerach).
require_once __DIR__ . '/../src/Database.php';
// Ładujemy router – on zdecyduje, który kontroler i metoda się wykonają.
require_once __DIR__ . '/../src/Routing.php';

// Jedna linijka na start: wszystko idzie przez router.
Routing::run();