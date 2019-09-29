<?php

namespace Anper\Jsonbox;

use GuzzleHttp\ClientInterface;
use GuzzleHttp\Promise\PromiseInterface;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\RequestOptions;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\UriInterface;

/**
 * Class Client
 * @package Anper\Jsonbox
 */
class Client
{
    public const METHOD_GET    = 'GET';
    public const METHOD_POST   = 'POST';
    public const METHOD_PUT    = 'PUT';
    public const METHOD_DELETE = 'DELETE';

    /**
     * @var ClientInterface
     */
    protected $client;

    /**
     * @param ClientInterface $client
     *
     * @throws Exception
     */
    public function __construct(ClientInterface $client)
    {
        $baseUri = $client->getConfig('base_uri');

        if (empty($baseUri)) {
            throw new Exception('Config `base_uri` not found');
        }

        $this->client = $client;
    }

    /**
     * @param UriInterface $uri
     * @param array $values
     *
     * @return PromiseInterface
     * @throws Exception
     */
    public function create(UriInterface $uri, array $values): PromiseInterface
    {
        return $this->send(
            $this->createRequest(static::METHOD_POST, $uri, $values)
        );
    }

    /**
     * @param UriInterface $uri
     *
     * @return PromiseInterface
     * @throws Exception
     */
    public function read(UriInterface $uri): PromiseInterface
    {
        return $this->send(
            $this->createRequest(static::METHOD_GET, $uri)
        );
    }

    /**
     * @param UriInterface $uri
     * @param array $values
     *
     * @return PromiseInterface
     * @throws Exception
     */
    public function update(UriInterface $uri, array $values): PromiseInterface
    {
        return $this->send(
            $this->createRequest(static::METHOD_PUT, $uri, $values)
        );
    }

    /**
     * @param UriInterface $uri
     *
     * @return PromiseInterface
     * @throws Exception
     */
    public function delete(UriInterface $uri): PromiseInterface
    {
        return $this->send(
            $this->createRequest(static::METHOD_DELETE, $uri)
        );
    }

    /**
     * @param RequestInterface $request
     *
     * @return PromiseInterface
     */
    public function send(RequestInterface $request): PromiseInterface
    {
        return $this->client->sendAsync($request)
            ->then(static function (ResponseInterface $response) {
                return $response->getBody();
            })
            ->then(static function (string $body) {
                return (array) \GuzzleHttp\json_decode($body, true);
            })
            ->otherwise(function ($value) {
                if ($value instanceof \Throwable) {
                    throw new Exception($value->getMessage());
                }

                throw new Exception((string) $value);
            });
    }

    /**
     * @param string $method
     * @param UriInterface $uri
     * @param array $body
     *
     * @return RequestInterface
     * @throws Exception
     */
    public function createRequest(string $method, UriInterface $uri, array $body = []): RequestInterface
    {
        try {
            $data = $body ? \GuzzleHttp\json_encode($body): null;
        } catch (\Exception $exception) {
            throw new Exception($exception->getMessage());
        }

        return new Request($method, $uri, [
            'Content-Type' => 'application/json',
            'Accept' => 'application/json',
        ], $data);
    }
}
