<?php

namespace App\Web;

class Router
{
    protected $request;
    protected $response;
    public $method;
    public $route;
    public $action;
    public $middleware;
    private $params = [];
    private $routePattern;
    private $paramKeys;

    public function __construct($request, $response, $method, $route, $action, $middleware = [])
    {
        $this->request = $request;
        $this->response = $response;
        $this->method = $method;
        $this->route = $route;
        $this->action = $action;
        $this->middleware = $middleware;

        $this->_parseRoute();
    }

    private function _parseRoute()
    {
        $routePattern = preg_replace('/\{([a-zA-Z0-9_]+)\}/', '([^/]+)', $this->route);
        $routePattern = "#^{$routePattern}$#";
        $this->routePattern = $routePattern;
        preg_match_all('/\{([a-zA-Z0-9_]+)\}/', $this->route, $paramKeys);
        $this->paramKeys = $paramKeys[1];
    }

    public function match()
    {
        if ($this->request->getMethod() !== $this->method) {
            return false;
        }
        $uri = $this->request->getUri();
        if (preg_match($this->routePattern, $uri, $matches)) {
            array_shift($matches);
            $this->params = array_combine($this->paramKeys, $matches);
            $this->request->setParams($this->params);
            return true;
        }

        return false;
    }

    public function getParams()
    {
        return $this->params;
    }
}
