<?php

namespace Anper\Jsonbox;

use GuzzleHttp\Client;
use GuzzleHttp\ClientInterface;
use Anper\Jsonbox\Client as CrudClient;
use GuzzleHttp\Psr7\Uri;
use Psr\Http\Message\UriInterface;

/**
 * Class Jsonbox
 * @package Anper\Jsonbox
 */
class Jsonbox extends Collection
{
    /**
     * @param string $boxId
     * @param ClientInterface|null $client
     *
     * @return static
     * @throws Exception
     */
    public static function factory(string $boxId, ClientInterface $client = null): self
    {
        $client = $client ?? new Client([
            'base_uri' => 'https://jsonbox.io'
        ]);

        $crudClient = new CrudClient($client);
        $uri = new Uri($boxId);

        return new static($crudClient, $uri);
    }

    /**
     * @param string $recordId
     *
     * @return Record
     */
    public function record(string $recordId): Record
    {
        $uri = $this->withPath($recordId);

        return new Record(
            $this->client,
            $uri
        );
    }

    /**
     * @param string $collection
     *
     * @return Collection
     */
    public function collection(string $collection): Collection
    {
        $uri = $this->withPath($collection);

        return new Collection(
            $this->client,
            $uri
        );
    }

    /**
     * @param Filter $filter
     *
     * @return array
     * @throws Exception
     */
    public function delete(Filter $filter): array
    {
        return $this->client->delete(
            $this->resolveUri($filter)
        );
    }

    /**
     * @param string $path
     *
     * @return UriInterface
     */
    protected function withPath(string $path): UriInterface
    {
        $uri = $this->uri->getPath() . '/' . \trim($path. '/');

        return $this->uri->withPath($uri);
    }
}
