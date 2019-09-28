<?php

namespace Anper\Jsonbox;

use GuzzleHttp\ClientInterface;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\RequestOptions;
use Psr\Http\Message\ResponseInterface;

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
     */
    public function __construct(ClientInterface $client)
    {
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
     * @param Uri $uri
     * @param array $values
     *
     * @return array
     * @throws Exception
     */
    public function create(Uri $uri, array $values): array
    {
        return $this->send('POST', $uri, $values);
    }

    /**
     * @param Uri $uri
     *
     * @return array
     * @throws Exception
     */
    public function read(Uri $uri): array
    {
        return $this->send('GET', $uri);
    }

    /**
     * @param Uri $uri
     * @param array $values
     *
     * @return array
     * @throws Exception
     */
    public function update(Uri $uri, array $values): array
    {
        return $this->send('PUT', $uri, $values);
    }

    /**
     * @param Uri $uri
     *
     * @return array
     * @throws Exception
     */
    public function delete(Uri $uri): array
    {
        return $this->send('DELETE', $uri);
    }

    /**
     * @param string $method
     * @param Uri $uri
     * @param array $body
     *
     * @return array
     * @throws Exception
     */
    protected function send(string $method, Uri $uri, array $body = []): array
    {
        $options = [
            RequestOptions::HEADERS => [
                'Accept' => 'application/json',
            ],
        ];

        if ($data) {
            $options[RequestOptions::JSON] = $body;
        }

        if ($query = $uri->getQuery()) {
            $options[RequestOptions::QUERY] = $query;
        }

        try {
            $response = $this->client->request($method, $uri->getPath(), $options);
        } catch (GuzzleException $exception) {
            throw new Exception($exception->getMessage(), 0, $exception);
        }

        $this->assertJson($response);

        return \json_decode($response->getBody(), true);
    }

    /**
     * @param ResponseInterface $response
     *
     * @throws Exception
     */
    protected function assertJson(ResponseInterface $response): void
    {
        $header = $response->getHeaderLine('Content-Type');
        $parts = \explode(';', $header);
        $type = \mb_strtolower(\trim($parts[0] ?? ''));

        if ($type !== 'application/json') {
            throw new Exception("Expected json content type, given `$type`");
        }
    }
}
