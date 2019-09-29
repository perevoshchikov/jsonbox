<?php

namespace Anper\Jsonbox;

use Psr\Http\Message\UriInterface;

/**
 * Class Collection
 * @package Anper\Jsonbox
 */
class Collection
{
    /**
     * @var Client
     */
    protected $client;

    /**
     * @var UriInterface
     */
    protected $uri;

    /**
     * @param Client $client
     * @param UriInterface $uri
     */
    public function __construct(Client $client, UriInterface $uri)
    {
        $this->client = $client;
        $this->uri = $uri;
    }

    /**
     * @param array $values
     *
     * @return array
     * @throws Exception
     */
    public function create(array $values): array
    {
        return $this->client
            ->create($this->uri, $values)
            ->wait();
    }

    /**
     * @param Filter|null $filter
     *
     * @return array
     * @throws Exception
     */
    public function read(Filter $filter = null): array
    {
        return $this->client
            ->read($this->resolveUri($filter))
            ->wait();
    }

    /**
     * @param Filter|null $filter
     *
     * @return UriInterface
     */
    protected function resolveUri(Filter $filter = null): UriInterface
    {
        return $filter
            ? $this->uri->withQuery((string) $filter)
            : $this->uri;
    }
}
