<?php

namespace Anper\Jsonbox\Tests;

use Anper\Jsonbox\Client;
use Anper\Jsonbox\Collection;
use Anper\Jsonbox\Filter;
use GuzzleHttp\Promise\PromiseInterface;
use GuzzleHttp\Psr7\Uri;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\UriInterface;

/**
 * Class CollectionTest
 * @package Anper\Jsonbox\Tests
 */
class CollectionTest extends TestCase
{
    public function testCreate(): void
    {
        $body = ['name' => 'John'];
        $data = ['foo' => 'bar'];
        $uri = $this->createMock(UriInterface::class);

        $client = $this->createClient('create', [$uri, $body], $data);

        $collection = new Collection($client, $uri);

        $this->assertEquals($data, $collection->create($body));
    }

    public function testRead(): void
    {
        $data = ['foo' => 'bar'];
        $uri = $this->createMock(UriInterface::class);

        $client = $this->createClient('read', [$uri], $data);

        $collection = new Collection($client, $uri);

        $this->assertEquals($data, $collection->read());
    }

    public function testReadWithFilter(): void
    {
        $data = ['foo' => 'bar'];

        $filter = new Filter();
        $filter->equalTo('name', 'John');

        $uri = (new Uri('/'))->withQuery((string) $filter);

        $client = $this->createClient('read', [$uri], $data);

        $collection = new Collection($client, $uri);

        $this->assertEquals($data, $collection->read($filter));
    }

    /**
     * @param string $method
     * @param array $arguments
     * @param array $return
     *
     * @return Client|\PHPUnit\Framework\MockObject\MockObject
     */
    protected function createClient(string $method, array $arguments, array $return)
    {
        $promise = $this->createMock(PromiseInterface::class);
        $promise->expects($this->once())
            ->method('wait')
            ->willReturn($return);

        $client = $this->createMock(Client::class);
        $client->expects($this->once())
            ->method($method)
            ->with(...$arguments)
            ->willReturn($promise);

        return $client;
    }
}
