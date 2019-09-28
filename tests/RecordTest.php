<?php

namespace Anper\Jsonbox\Tests;

use Anper\Jsonbox\Client;
use Anper\Jsonbox\Record;
use Anper\Jsonbox\Uri;
use PHPUnit\Framework\TestCase;

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

        $uri = $this->createMock(Uri::class);

        $with = $arguments;
        \array_unshift($with, $uri);

        $client = $this->createMock(Client::class);
        $client->expects($this->once())
            ->method($method)
            ->with(...$with)
            ->willReturn($expected);

        $record = new Record($client, $uri);

        $result = $arguments
            ? $record->$method(...$arguments)
            : $record->$method();

        $this->assertEquals($expected, $result);
    }
}
