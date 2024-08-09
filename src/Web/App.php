<?php

namespace App\Web;

use App\Web\Request;
use App\Web\Response;
use App\Web\Router;

class App
{
    protected Request $request;
    protected Response $response;
    protected array $routes;
    function __construct($env_path = null)
    {
        ob_start();
        session_start();
        $this->_loadDOTENV($env_path);
        $this->request = new Request();
        $this->response = new Response();
        $this->routes = [];
    }

    private function _loadDOTENV($env_path): void
    {
        $dotenvFile = $env_path;
        if (is_null($dotenvFile))
            $dotenvFile = __DIR__ . "/../../.env";

        if (file_exists($dotenvFile)) {
            $lines = file($dotenvFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
            foreach ($lines as $line) {
                if (strpos($line, '#') === 0) {
                    continue; // Skip comments
                }
                list($key, $value) = explode('=', $line, 2) + [null, null];
                $key = trim($key);
                $value = trim($value);
                if (!empty($key)) {
                    putenv("{$key}={$value}");
                    $_ENV[$key] = $value;
                }
            }
        }
    }

    protected function addRoute(string $method, string $route, $action, array $middleware = []): void
    {
        $this->routes[] = new Router($this->request, $this->response, $method, $route, $action, $middleware);
    }

    public function get(string $route, $action, array $middleware = []): void
    {
        $this->addRoute('GET', $route, $action, $middleware);
    }

    public function post(string $route, $action, array $middleware = []): void
    {
        $this->addRoute('POST', $route, $action, $middleware);
    }

    public function run(): void
    {
        foreach ($this->routes as $router) {
            if ($router->match()) {
                foreach ($router->middleware as $middleware) {
                    $middleware($this->request, $this->response);
                }
                if (is_callable($router->action)) {
                    call_user_func($router->action, $this->request, $this->response);
                } elseif (is_string($router->action)) {
                    if (strpos($router->action, 'view::') === 0) {
                        $viewPath = str_replace('view::', '', $router->action);
                        $this->renderView($viewPath);
                    } else {
                        list($controller, $method) = explode('::', $router->action);
                        $controller = 'App\Controllers\\' . $controller;
                        $controller = new $controller();
                        call_user_func([$controller, $method], $this->request, $this->response);
                    }
                } else {
                    throw new \Exception("Route action not callable.");
                }
                return;
            }
        }
        $this->response->setStatusCode(404)->send(['error' => 'Not Found']);
    }

    protected function renderView(string $viewPath): void
    {
        $viewFile = realpath(str_replace(['/', '\\'], DIRECTORY_SEPARATOR, __DIR__ . '/../Views/' . $viewPath . '.php'));
        if (file_exists($viewFile)) {
            require $viewFile;
        } else {
            $this->response->setStatusCode(404)->send(['error' => 'Not Found']);
        }
        exit();
    }
}
