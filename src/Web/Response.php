<?php

namespace App\Web;

use App\Views\ViewEngine;

class Response
{
    private $headers = [];
    private $statusCode = 200;
    private $body;

    public function setHeader($name, $value)
    {
        $this->headers[$name] = $value;
        return $this;
    }

    public function setHeaders(array $headers)
    {
        foreach ($headers as $name => $value) {
            $this->setHeader($name, $value);
        }
        return $this;
    }

    public function setStatusCode($code)
    {
        $this->statusCode = $code;
        return $this;
    }

    public function setBody($body)
    {
        $this->body = $body;
        return $this;
    }

    public function redirect($url, $statusCode = 302)
    {
        $this->setStatusCode($statusCode);
        $this->setHeader('Location', $url);
        $this->send();
    }

    public function send($body = "")
    {
        ob_clean();

        $this->body = $body;
        http_response_code($this->statusCode);

        if (is_array($this->body) || is_object($this->body)) {
            $this->setHeader('Content-Type', 'application/json');
            $this->body = json_encode($this->body);
        }

        foreach ($this->headers as $name => $value) {
            header("$name: $value");
        }

        echo $this->body;
        exit();
    }

    public function render(string $view, array $locals = [])
    {
        $engine = new ViewEngine();
        foreach ($locals as $key => $value) {
            $engine->assign($key, $value);
        }
        $this->body = $engine->render($view);
        foreach ($this->headers as $name => $value) {
            header("$name: $value");
        }
        echo $this->body;
        exit();
    }
}
