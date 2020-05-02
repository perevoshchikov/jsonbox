<?php

namespace Anper\Jsonbox\Tests;

use Anper\Jsonbox\Client;
use Anper\Jsonbox\Exception;
use Anper\Jsonbox\Filter;
use Anper\Jsonbox\Jsonbox;
use GuzzleHttp\Promise\PromiseInterface;
use GuzzleHttp\Psr7\Uri;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\UriInterface;

/**
 * Class JsonboxTest
 * @package Anper\Jsonbox\Tests
 */
class JsonboxTest extends TestCase
{
    public function testUpdate(): void
    {
        $expected = ['a' => true];

        $promise = $this->promise($expected);

        $client = $this->createMock(Client::class);
        $client->expects($this->once())
            ->method('batch')
            ->with(
                $this->isType('iterable'),
                $this->isType('integer')
            )
            ->willReturn($promise);

        $uri = $this->createMock(UriInterface::class);

        $jsonbox = new Jsonbox($client, $uri);
        $actual = $jsonbox->update([]);

        $this->assertEquals($expected, $actual);
    }

    public function testDeleteAllRecord(): void
    {
        $expected = ['b' => true];

        $promise = $this->promise($expected);

        $client = $this->createMock(Client::class);
        $client->expects($this->once())
            ->method('batch')
            ->with(
                $this->isType('iterable'),
                $this->isType('integer')
            )
            ->willReturn($promise);

        $uri = $this->createMock(UriInterface::class);

        $jsonbox = new Jsonbox($client, $uri);
        $actual = $jsonbox->delete([]);

        $this->assertEquals($expected, $actual);
    }

    public function testDeleteByFilter(): void
    {
        $expected = ['c' => true];
        $filter = new Filter();
        $filter->equalTo('name', 'John');

        $uri = (new Uri('/'))
            ->withQuery((string) $filter);

        $promise = $this->promise($expected);

        $client = $this->createMock(Client::class);
        $client->expects($this->once())
            ->method('delete')
            ->with($uri)
            ->willReturn($promise);

        $jsonbox = new Jsonbox($client, $uri);
        $actual = $jsonbox->delete($filter);

        $this->assertEquals($expected, $actual);
    }

    public function testInvalidDelete(): void
    {
        $this->expectException(Exception::class);

        $client = $this->createMock(Client::class);
        $uri = $this->createMock(UriInterface::class);

        $jsonbox = new Jsonbox($client, $uri);

        $jsonbox->delete(null);
    }

    public function testSetApiKey(): void
    {
        $secret = 'secret';

        $client = $this->createMock(Client::class);
        $client->expects($this->once())
            ->method('setOptions')
            ->with([
                'headers' => [
                    'X-API-KEY' => $secret,
                ]
            ]);

        $uri = $this->createMock(UriInterface::class);

        $jsonbox1 = new Jsonbox($client, $uri);
        $jsonbox2 = $jsonbox1->withApiKey($secret);

        $this->assertNotEquals(\spl_object_hash($jsonbox1), \spl_object_hash($jsonbox2));
    }

    /**
     * @param $return
     *
     * @return PromiseInterface
     */
    protected function promise($return)
    {
        $promise = $this->createMock(PromiseInterface::class);

        $promise->expects($this->once())
            ->method('wait')
            ->willReturn($return);

        return $promise;
    }
}
