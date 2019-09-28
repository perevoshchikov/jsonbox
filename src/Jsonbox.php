<?php

namespace Anper\Jsonbox;

use GuzzleHttp\Client;
use GuzzleHttp\ClientInterface;
use Anper\Jsonbox\Client as CrudClient;

/**
 * Class Jsonbox
 * @package Anper\Jsonbox
 */
class Jsonbox extends Collection
{
    /**
     * @param string $boxId
     * @param ClientInterface|null $client
     */
    public function __construct(string $boxId, ClientInterface $client = null)
    {
        $client = $client ?? new Client([
            'base_uri' => 'https://jsonbox.io'
        ]);

        $crudClient = new CrudClient($client);
        $uri = new Uri($boxId);

        parent::__construct($crudClient, $uri);
    }

    /**
     * @param string $recordId
     *
     * @return Record
     */
    public function record(string $recordId): Record
    {
        $uri = $this->uri->withPath($recordId);

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
        $uri = $this->uri->withPath($collection);

        return new Collection(
            $this->client,
            $uri
        );
    }
}
