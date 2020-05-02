<?php

namespace Anper\Jsonbox;

use Anper\Jsonbox\Batch\DeleteBatch;
use Anper\Jsonbox\Batch\UpdateBatch;
use GuzzleHttp\Client;
use GuzzleHttp\ClientInterface;
use Anper\Jsonbox\Client as CrudClient;
use GuzzleHttp\Psr7\Uri;

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
     * @param string $key
     *
     * @return $this
     */
    public function withApiKey(string $key): self
    {
        $copy = clone $this;
        $copy->client->setOptions([
            'headers' => [
                'X-API-KEY' => $key,
            ]
        ]);

        return $copy;
    }

    /**
     * @param string $recordId
     *
     * @return Record
     */
    public function record(string $recordId): Record
    {
        $uri = \Anper\Jsonbox\path_push($this->uri, $recordId);

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
        $uri = \Anper\Jsonbox\path_push($this->uri, $collection);

        return new Collection(
            $this->client,
            $uri
        );
    }

    /**
     * @param Filter|string[] $recordsOrFilter
     * @param int $concurrency
     *
     * @return array
     * @throws Exception
     */
    public function delete($recordsOrFilter, int $concurrency = 10): array
    {
        if (\is_array($recordsOrFilter)) {
            $batch = new DeleteBatch($this->uri, $recordsOrFilter);

            return $this->client
                ->batch($batch, $concurrency)
                ->wait();
        }

        if ($recordsOrFilter instanceof Filter) {
            return $this->client
                ->delete($this->withFilter($recordsOrFilter))
                ->wait();
        }

        throw new Exception(\sprintf(
            'Expected array or instance of \Anper\Jsonbox\Filter, given `%s`',
            \is_object($recordsOrFilter)
                ? \get_class($recordsOrFilter)
                : \gettype($recordsOrFilter)
        ));
    }

    /**
     * @param array $values
     * @param int $concurrency
     *
     * Example: $values = [
     *   '5d8fbea4586bc10117c85fbb' => ['name' => 'John'],
     *    ...
     * ]
     *
     * @return array
     * @throws Exception
     *
     */
    public function update(array $values, int $concurrency = 10): array
    {
        $batch = new UpdateBatch($this->uri, $values);

        return $this->client
            ->batch($batch, $concurrency)
            ->wait();
    }

    public function __clone()
    {
        $this->client = clone $this->client;
    }
}
