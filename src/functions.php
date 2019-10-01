<?php

namespace Anper\Jsonbox;

use GuzzleHttp\Psr7\Request;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\UriInterface;

if (\function_exists('path_push') === false) {
    /**
     * @param UriInterface $uri
     * @param string $path
     *
     * @return UriInterface
     */
    function path_push(UriInterface $uri, string $path): UriInterface
    {
        $path = \trim($path, '/');

        if (($current = $uri->getPath()) && $current !== '/') {
            $current = \rtrim($current, '/') . ($path ? '/' : '');
        }

        return clone $uri->withPath($current . $path);
    }
}

if (\function_exists('json_request') === false) {
    /**
     * @param string $method
     * @param UriInterface $uri
     * @param array|null $body
     *
     * @return RequestInterface
     * @throws Exception
     */
    function json_request(string $method, UriInterface $uri, array $body = null): RequestInterface
    {
        try {
            $json = $body ? \GuzzleHttp\json_encode($body): null;
        } catch (\Exception $exception) {
            throw new Exception($exception->getMessage());
        }

        return new Request($method, $uri, [
            'Content-Type' => 'application/json',
            'Accept' => 'application/json',
        ], $json);
    }
}
