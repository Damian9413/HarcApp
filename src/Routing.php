<?php

class Routing{
    
    public static function run(): void{
        $uri = $_SERVER['REQUEST_URI'] ?? '/';
        $path = parse_url($uri, PHP_URL_PATH);
        $path = rtrim($path, '/') ?: '/';

        // sciezki bezposrednie
        if ($path === '/' || $path === '' || $path === '/home') {
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

        if ($path === '/game') {
            require __DIR__ . '/controllers/GameController.php';
            $controller = new GameController();
            $controller->index();
            return;
        }

        // /kontroler/akcja
        $path = ltrim($path, '/');
        $segments = explode('/', $path);

        $controllerName = $segments[0] ?? 'home';
        $action = $segments[1] ?? 'index';

        $controllerClass = ucfirst($controllerName) . 'Controller';
        $controllerFile = __DIR__ . '/controllers/' . $controllerClass . '.php';

        if (!file_exists($controllerFile)) {
            self::renderError(404);
            return;
        }

        require $controllerFile;

        if (!class_exists($controllerClass)) {
            self::renderError(500);
            return;
        }

        $controller = new $controllerClass();
        $method = $action;

        if (!method_exists($controller, $method)) {
            self::renderError(404);
            return;
        }

        $controller->$method();
    }

    // wyswietla strone bledu
    public static function renderError(int $code): void
    {
        http_response_code($code);
        $errorFile = __DIR__ . '/views/errors/' . $code . '.php';
        
        if (file_exists($errorFile)) {
            require $errorFile;
        } else {
            echo $code . ' Error';
        }
    }

    public static function forbidden(): void
    {
        self::renderError(403);
        exit;
    }

    public static function notFound(): void
    {
        self::renderError(404);
        exit;
    }
}
