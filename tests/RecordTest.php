<?php

namespace Anper\Jsonbox\Tests;

use Anper\Jsonbox\Client;
use Anper\Jsonbox\Record;
use GuzzleHttp\Promise\PromiseInterface;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\UriInterface;

/**
 * Class RecordTest
 * @package Anper\Jsonbox\Tests
 */
class RecordTest extends TestCase
{
    public function methodsProvider(): array
    {
        return [
            ['read'],
            ['update', [['name' => 'John']]],
            ['delete'],
        ];
    }

    /**
     * @dataProvider methodsProvider
     * @param string $method
     * @param array|null $arguments
     */
    public function testMethods(string $method, array $arguments = []): void
    {
        $expected = ['foo' => 'bar'];

        $uri = $this->createMock(UriInterface::class);

        $with = $arguments;
        \array_unshift($with, $uri);

        $promise = $this->createMock(PromiseInterface::class);
        $promise->expects($this->once())
            ->method('wait')
            ->willReturn($expected);

        $client = $this->createMock(Client::class);
        $client->expects($this->once())
            ->method($method)
            ->with(...$with)
            ->willReturn($promise);

        $record = new Record($client, $uri);

        $result = $arguments
            ? $record->$method(...$arguments)
            : $record->$method();

        $this->assertEquals($expected, $result);
    }
}
