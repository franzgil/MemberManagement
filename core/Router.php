<?php

class Router
{
    private $routes = [];

    // Route registrieren
    public function add($method, $path, $handler)
    {
        $this->routes[] = [
            'method' => strtoupper($method),
            'path'   => $this->convertPathToRegex($path),
            'handler'=> $handler
        ];
    }

    // Router starten
    public function dispatch($url, $method)
    {
        $url = trim($url, '/');

        foreach ($this->routes as $route) {
            if ($route['method'] === strtoupper($method) && preg_match($route['path'], $url, $matches)) {

                list($controllerName, $action) = explode('@', $route['handler']);
                $controllerFile = 'controllers/' . $controllerName . '.php';

                if (!file_exists($controllerFile)) {
                    http_response_code(500);
                    die("Controller $controllerName nicht gefunden");
                }

                require_once $controllerFile;
                $controller = new $controllerName();

                if (!method_exists($controller, $action)) {
                    http_response_code(500);
                    die("Methode $action in $controllerName nicht gefunden");
                }

                // Parameter entfernen (erstes Element ist der gesamte Match)
                array_shift($matches);
                return call_user_func_array([$controller, $action], $matches);
            }
        }

        // keine Route gefunden
        http_response_code(404);
        echo "<h1>404 - Seite nicht gefunden</h1>";
    }

    // Umwandeln z. B. '/user/{id}' → regulärer Ausdruck
    private function convertPathToRegex($path)
    {
        $path = trim($path, '/');
        $path = preg_replace('/\{([a-zA-Z0-9_]+)\}/', '([a-zA-Z0-9_-]+)', $path);
        return '/^' . str_replace('/', '\/', $path) . '$/';
    }
}