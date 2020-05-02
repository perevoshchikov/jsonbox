<?php

namespace Anper\Jsonbox;

use GuzzleHttp\ClientInterface;
use GuzzleHttp\Promise\PromiseInterface;
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
     * @var array
     */
    protected $options = [];

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
     * @return ClientInterface
     */
    public function getClient(): ClientInterface
    {
        return $this->client;
    }

    /**
     * @return array
     */
    public function getOptions(): array
    {
        return $this->options;
    }

    /**
     * @param array $options
     *
     * @return Client
     */
    public function setOptions(array $options): self
    {
        $this->options = $options;

        return $this;
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
        return $this->raw(static::METHOD_POST, $uri, $values);
    }

    /**
     * @param UriInterface $uri
     *
     * @return PromiseInterface
     * @throws Exception
     */
    public function read(UriInterface $uri): PromiseInterface
    {
        return $this->raw(static::METHOD_GET, $uri);
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
        return $this->raw(static::METHOD_PUT, $uri, $values);
    }

    /**
     * @param UriInterface $uri
     *
     * @return PromiseInterface
     * @throws Exception
     */
    public function delete(UriInterface $uri): PromiseInterface
    {
        return $this->raw(static::METHOD_DELETE, $uri);
    }

    /**
     * @param RequestInterface $request
     * @param bool $async
     *
     * @return PromiseInterface
     */
    public function send(RequestInterface $request, bool $async = false): PromiseInterface
    {
        return $this->client->sendAsync($request, \array_merge($this->options, [
                RequestOptions::SYNCHRONOUS => $async === false
            ]))
            ->then(static function (ResponseInterface $response) {
                return (array) \GuzzleHttp\json_decode($response->getBody(), true);
            })
            ->otherwise(static function ($value) {
                $message = $value instanceof \Throwable
                    ? $value->getMessage()
                    : (string) $value;

                throw new Exception($message);
            });
    }

    /**
     * @param iterable|RequestInterface[] $requests
     * @param int $concurrency
     *
     * @return PromiseInterface
     */
    public function batch(iterable $requests, int $concurrency = 10): PromiseInterface
    {
        $generator = function (&$requests) {
            foreach ($requests as $id => $request) {
                yield $id => $this->send($request, true);
            }
        };

        $results = [];

        $promise = \GuzzleHttp\Promise\each_limit(
            $generator($requests),
            $concurrency,
            function ($value, $idx) use (&$results) {
                $results[$idx] = [
                    'state' => PromiseInterface::FULFILLED,
                    'value' => $value,
                ];
            },
            function ($reason, $idx) use (&$results) {
                $results[$idx] = [
                    'state' => PromiseInterface::REJECTED,
                    'value' => $reason,
                ];
            }
        );

        return $promise->then(static function () use (&$results) {
            return $results;
        });
    }

    /**
     * @param string $method
     * @param UriInterface $uri
     * @param array $body
     *
     * @return PromiseInterface
     * @throws Exception
     */
    protected function raw(string $method, UriInterface $uri, array $body = []): PromiseInterface
    {
        return $this->send(
            json_request($method, $uri, $body)
        );
    }
}
