<?php

namespace Anper\Jsonbox;

/**
 * Class Record
 * @package Anper\Jsonbox
 */
class Record
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
     * @return array
     * @throws Exception
     */
    public function read(): array
    {
        return $this->client->read($this->uri);
    }

    /**
     * @param array $values
     *
     * @return array
     * @throws Exception
     */
    public function update(array $values): array
    {
        return $this->client->update($this->uri, $values);
    }

    /**
     * @return array
     * @throws Exception
     */
    public function delete(): array
    {
        return $this->client->delete($this->uri);
    }
}
