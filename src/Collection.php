<?php

namespace Anper\Jsonbox;

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
     * @var Uri
     */
    protected $uri;

    /**
     * @param Client $client
     * @param Uri $uri
     */
    public function __construct(Client $client, Uri $uri)
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
        return $this->client->create($this->uri, $values);
    }

    /**
     * @param Filter|null $filter
     *
     * @return array
     * @throws Exception
     */
    public function read(Filter $filter = null): array
    {
        return $this->client->read($this->resolveUri($filter));
    }

    /**
     * @param Filter $filter
     *
     * @return array
     * @throws Exception
     */
    public function delete(Filter $filter): array
    {
        return $this->client->delete($this->resolveUri($filter));
    }

    /**
     * @param Filter|null $filter
     *
     * @return Uri
     */
    protected function resolveUri(Filter $filter = null): Uri
    {
        return $filter
            ? $this->uri->withQuery($filter->toArray())
            : $this->uri;
    }
}
