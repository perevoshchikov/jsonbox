<?php

namespace Anper\Jsonbox\Tests;

use Anper\Jsonbox\Client;
use Anper\Jsonbox\Collection;
use Anper\Jsonbox\Filter;
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

        $client = $this->createMock(Client::class);
        $client->expects($this->once())
            ->method('create')
            ->with($uri, $body)
            ->willReturn($data);

        $collection = new Collection($client, $uri);

        $this->assertEquals($data, $collection->create($body));
    }

    public function testRead(): void
    {
        $data = ['foo' => 'bar'];
        $uri = $this->createMock(UriInterface::class);

        $client = $this->createMock(Client::class);
        $client->expects($this->once())
            ->method('read')
            ->with($uri)
            ->willReturn($data);

        $collection = new Collection($client, $uri);

        $this->assertEquals($data, $collection->read());
    }

    public function testReadWithFilter(): void
    {
        $data = ['foo' => 'bar'];

        $filter = new Filter();
        $filter->equalTo('name', 'John');

        $uri = (new Uri('/'))->withQuery((string) $filter);

        $client = $this->createMock(Client::class);
        $client->expects($this->once())
            ->method('read')
            ->with($uri)
            ->willReturn($data);

        $collection = new Collection($client, $uri);

        $this->assertEquals($data, $collection->read($filter));
    }
}