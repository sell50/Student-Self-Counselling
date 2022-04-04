<?php

class Router
{
    private array $routes = [];

    public function add(string $route, array $controller): void
    {
        if (!isset($this->routes[$route])) {
            $this->routes[$route] = [];
        }
        $this->routes[$route][] = $controller;
    }

    public function handle(): Response
    {
        $path = parse_url($_SERVER["REQUEST_URI"], PHP_URL_PATH);

        foreach ($this->routes as $key => $values) {

            // check if the route is valid
            if (strtolower($key) !== strtolower($path)) {
                continue;
            }

            foreach ($values as $value) {

//                $method = $_POST['_method'] ?? $_SERVER['REQUEST_METHOD'];

                // check if the route method is valid
//                if (strtolower($value[1]) !== strtolower($method)) {
//                    continue;
//                }

                $path = $path == '/' ? 'first' : ltrim($path, '/');

                try {
                    return call_user_func([new $value[0], $path]);
                } catch (Exception) {
                    return new Response(404, file_get_contents(__DIR__ . '/../templates/errors/500.html'));
                }

            }

        }

        return new Response(404, file_get_contents(__DIR__ . '/../templates/errors/404.html'));
    }
}
