<?php

namespace App\Web;

class Request
{
    private $headers;
    private $method;
    private $uri;
    private $queryParams;
    private $body;
    private $cookies;
    private $host;
    private $hostname;
    private $ip;
    private $originalUrl;
    private $path;
    private $protocol;
    private $accepts;
    private $baseUrl;
    public $params = [];

    public function __construct()
    {
        $this->headers = getallheaders();
        $this->method = $_SERVER['REQUEST_METHOD'];
        $this->uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
        $this->queryParams = $_GET;
        $this->body = $this->_parseBody();
        $this->cookies = $_COOKIE;
        $this->host = $_SERVER['HTTP_HOST'];
        $this->hostname = $_SERVER['SERVER_NAME'];
        $this->ip = $_SERVER['REMOTE_ADDR'];
        $this->originalUrl = $_SERVER['REQUEST_URI'];
        $this->path = $this->uri;
        $this->protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http';
        $this->accepts = $this->_parseAccepts();
        $this->baseUrl = $this->_getBaseUrl();
    }

    public function set_custom_field(array $data)
    {
        foreach ($data as $key => $value) {
            $this->{$key} = $value;
        }
    }

    public function getHeader(string $name): ?string
    {
        return $this->headers[$name] ?? null;
    }

    public function getHeaders(): array
    {
        return $this->headers;
    }

    public function getMethod(): string
    {
        return $this->method;
    }

    public function getUri(): string
    {
        return $this->uri;
    }

    public function getQueryParams(): array
    {
        return $this->queryParams;
    }

    public function getBody()
    {
        return $this->body;
    }

    public function getCookies(): array
    {
        return $this->cookies;
    }

    public function getHost(): string
    {
        return $this->host;
    }

    public function getHostname(): string
    {
        return $this->hostname;
    }

    public function getIp(): string
    {
        return $this->ip;
    }

    public function getOriginalUrl(): string
    {
        return $this->originalUrl;
    }

    public function getPath(): string
    {
        return $this->path;
    }

    public function getProtocol(): string
    {
        return $this->protocol;
    }

    public function getAccepts(): array
    {
        return $this->accepts;
    }

    public function getBaseUrl(): string
    {
        return $this->baseUrl;
    }

    private function _parseBody()
    {
        $contentType = $this->getHeader('Content-Type');

        if (strpos($contentType, 'application/json') !== false) {
            return json_decode(file_get_contents('php://input'), true);
        }

        if (strpos($contentType, 'application/x-www-form-urlencoded') !== false) {
            parse_str(file_get_contents('php://input'), $parsedBody);
            return $parsedBody;
        }

        if (strpos($contentType, 'multipart/form-data') !== false) {
            $parsedBody = $_POST;
            foreach ($_FILES as $key => $file) {
                if (is_array($file['name'])) {
                    foreach ($file['name'] as $index => $name) {
                        $parsedBody[$key][] = [
                            'name' => $name,
                            'type' => $file['type'][$index],
                            'tmp_name' => $file['tmp_name'][$index],
                            'error' => $file['error'][$index],
                            'size' => $file['size'][$index],
                        ];
                    }
                } else {
                    $parsedBody[$key] = [
                        'name' => $file['name'],
                        'type' => $file['type'],
                        'tmp_name' => $file['tmp_name'],
                        'error' => $file['error'],
                        'size' => $file['size'],
                    ];
                }
            }
            return $parsedBody;
        }

        return [];
    }

    private function _parseAccepts(): array
    {
        $acceptHeader = $this->getHeader('Accept');
        return $acceptHeader ? array_map('trim', explode(',', $acceptHeader)) : [];
    }

    public function setParams(array $params): void
    {
        $this->params = $params;
    }

    private function _getBaseUrl(): string
    {
        return "{$this->protocol}://{$this->host}";
    }

    public function getBodyField(string $key)
    {
        return $this->body[$key] ?? null;
    }
}
