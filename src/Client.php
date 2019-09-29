<?php

namespace Anper\Jsonbox;

use GuzzleHttp\ClientInterface;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\RequestOptions;
use Psr\Http\Message\UriInterface;

/**
 * Class Client
 * @package Anper\Jsonbox
 */
class Client
{
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
     * @return array
     * @throws Exception
     */
    public function create(UriInterface $uri, array $values): array
    {
        return $this->send('POST', $uri, $values);
    }

    /**
     * @param UriInterface $uri
     *
     * @return array
     * @throws Exception
     */
    public function read(UriInterface $uri): array
    {
        return $this->send('GET', $uri);
    }

    /**
     * @param UriInterface $uri
     * @param array $values
     *
     * @return array
     * @throws Exception
     */
    public function update(UriInterface $uri, array $values): array
    {
        return $this->send('PUT', $uri, $values);
    }

    /**
     * @param UriInterface $uri
     *
     * @return array
     * @throws Exception
     */
    public function delete(UriInterface $uri): array
    {
        return $this->send('DELETE', $uri);
    }

    /**
     * @param string $method
     * @param UriInterface $uri
     * @param array $body
     *
     * @return array
     * @throws Exception
     */
    public function send(string $method, UriInterface $uri, array $body = []): array
    {
        $options = [
            RequestOptions::JSON    => $body,
            RequestOptions::HEADERS => [
                'Accept' => 'application/json',
            ],
        ];

        $options = \array_filter($options);

        try {
            $response = $this->client->request($method, $uri, $options);
        } catch (\Exception|GuzzleException $exception) {
            throw new Exception($exception->getMessage(), 0, $exception);
        }

        try {
            return (array) \GuzzleHttp\json_decode($response->getBody(), true);
        } catch (\Exception $exception) {
            throw new Exception($exception->getMessage(), 0, $exception);
        }
    }
}
