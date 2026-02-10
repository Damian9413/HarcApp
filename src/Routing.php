<?php

class Routing{
    //Glowna metoda
    public static function run(): void{
        //Request URI
        $uri = $_SERVER['REQUEST_URI'] ?? '/';

        //wyciagamy path z URI
        $path = parse_url($uri, PHP_URL_PATH);
       
        $path = rtrim($path, '/') ?: '/';

        if ($path === '/' || $path === '') {
            require __DIR__ . '/controllers/HomeController.php';
            $controller = new HomeController();
            $controller->index();
            return;
        }

        if ($path === '/settings') {
            require __DIR__ . '/controllers/SettingsController.php';
            $controller = new SettingsController();
            $controller->index();
            return;
        }

        // Usuwamy slash z początku i dzielimy path na segmenty.
        // Np. "/security/login" → ["security", "login"].
        $path = ltrim($path, '/');
        $segments = explode('/', $path);

        // Pierwszy segment = nazwa kontrolera (np. security → SecurityController).
        // Drugi segment = nazwa akcji/metody (np. login → metoda login()).
        // Jeśli brak – używamy domyślnych: home, index.
        $controllerName = $segments[0] ?? 'home';
        $action = $segments[1] ?? 'index';

        //budujemy nazwe clasy security → SecurityController.
        $controllerClass = ucfirst($controllerName) . 'Controller';

        // Ścieżka do pliku kontrolera: src/controllers/SecurityController.php.
        $controllerFile = __DIR__ . '/controllers/' . $controllerClass . '.php';

        // Jeśli plik nie istnieje – 404.
        if (!file_exists($controllerFile)) {
            http_response_code(404);
            echo '404 Not Found';
            return;
        }

        //ladowanie pliku z definicja klasy kontrolera
        require $controllerFile;

        if (!class_exists($controllerClass)) {
            http_response_code(500);
            echo '500 Controller not found';
            return;
        }

        //wywolanie konstruktora, zeby nowy obiekt
        $controller = new $controllerClass();

        //nazwa metody do wywolania - jak login
        $method = $action;

        // Sprawdzenie, czy kontroler ma taką metodę.
        if (!method_exists($controller, $method)) {
            http_response_code(404);
            echo '404 Action not found';
            return;
        }

        // Wywołanie metody kontrolera – np. $controller->login().
        $controller->$method();

    }
}