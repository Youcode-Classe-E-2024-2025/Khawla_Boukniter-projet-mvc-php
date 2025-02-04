<?php

namespace App\Core;

class Router
{
    private array $routes = [];

    public function addRoute(string $method, string $path, array $handler)
    {
        $this->routes[] = [
            'method' => $method,
            'path' => $path,
            'handler' => $handler
        ];
    }

    private function extractParams(string $routePath, string $requestPath)
    {
        $pattern = preg_replace('/\{(\w+)\}/', '(?P<$1>[^/]+)', $routePath);
        $pattern = "#^{$pattern}$#";

        if (preg_match($pattern, $requestPath, $matches)) {
            return array_filter($matches, fn($key) => !is_numeric($key), ARRAY_FILTER_USE_KEY);
        }

        return null;
    }

    public function dispatch($method, $uri)
    {
        $uri = explode('?', $uri)[0];

        foreach ($this->routes as $route) {
            if ($route['method'] === $method) {
                $pattern = $this->convertPattern($route['path']);
                if (preg_match($pattern, $uri, $params)) {
                    $handler = $route['handler'];
                    $controller = new $handler[0]();
                    $action = $handler[1];
                    array_shift($params);
                    return $controller->$action(array_values($params));
                }
            }
        }
        throw new \Exception('Route not found');
    }

    private function convertPattern($path)
    {
        $pattern = str_replace('/', '\/', $path);
        return "#^" . preg_replace('/\{(\w+)\}/', '([^/]+)', $pattern) . "$#";
    }
}
