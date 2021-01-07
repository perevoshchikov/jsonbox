<?php

namespace Anper\Jsonbox\Batch;

use Anper\Jsonbox\Client;
use Anper\Jsonbox\Exception;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\UriInterface;

/**
 * Class UpdateRequest
 * @package Anper\Jsonbox\Generator
 *
 * @implements \IteratorAggregate<RequestInterface>
 */
class DeleteBatch implements \IteratorAggregate
{
    /**
     * @var UriInterface
     */
    protected $uri;

    /**
     * @var array
     */
    protected $values;

    /**
     * @param UriInterface $uri
     * @param array $values
     *
     * @throws Exception
     */
    public function __construct(UriInterface $uri, array $values)
    {
        $this->assertValidValues($values);

        $this->uri    = $uri;
        $this->values = $values;
    }

    /**
     * @inheritDoc
     */
    public function getIterator()
    {
        foreach ($this->values as $recordId) {
            yield $recordId => $this->request($recordId);
        }
    }

    /**
     * @param string $recordId
     *
     * @return RequestInterface
     * @throws Exception
     */
    protected function request(string $recordId): RequestInterface
    {
        return \Anper\Jsonbox\json_request(
            Client::METHOD_DELETE,
            \Anper\Jsonbox\path_push($this->uri, $recordId)
        );
    }

    /**
     * @param array $values
     *
     * @throws Exception
     */
    protected function assertValidValues(array &$values): void
    {
        foreach ($values as $recordId) {
            if (\is_string($recordId) === false) {
                throw new Exception(\sprintf(
                    'Record id must be string, given `%s`',
                    \gettype($recordId)
                ));
            }
        }
    }
}
