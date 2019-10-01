<?php

namespace Anper\Jsonbox\Tests;

use Anper\Jsonbox\Client;
use Anper\Jsonbox\Exception;
use GuzzleHttp\ClientInterface;
use GuzzleHttp\Promise\PromiseInterface;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Uri;
use PHPUnit\Framework\TestCase;
use function Anper\Jsonbox\json_request;

/**
 * Class ClientTest
 * @package Anper\Jsonbox\Tests
 */
class ClientTest extends TestCase
{
    public function testInvalidGuzzleClient(): void
    {
        $this->expectException(Exception::class);

        $guzzle = $this->createMock(ClientInterface::class);

        new Client($guzzle);
    }

    public function testSend(): void
    {
        $client = $this->getClient();

        $request = new Request('GET', new Uri('/json'));

        $this->assertIsArray($client->send($request)->wait());
    }

    public function testInvalidRequest(): void
    {
        $this->expectException(Exception::class);

        $client = $this->getClient();

        $request = new Request('GET', new Uri('/status/400'));

        $client->send($request)->wait();
    }

    public function testInvalidJson(): void
    {
        $this->expectException(Exception::class);

        $client = $this->getClient();

        $request = new Request('GET', new Uri('/xml'));

        $client->send($request)->wait();
    }

    public function testCreate(): void
    {
        $client = $this->getClient();
        $body = ['name' => 'John'];

        $result = $client->create(new Uri('/post'), $body)->wait();

        $this->assertEquals($result['json'] ?? [], $body);
    }

    public function testRead(): void
    {
        $client = $this->getClient();

        $this->assertIsArray($client->read(new Uri('/get'))->wait());
    }

    public function testDelete(): void
    {
        $client = $this->getClient();

        $this->assertIsArray($client->delete(new Uri('/delete'))->wait());
    }

    public function testUpdate(): void
    {
        $client = $this->getClient();

        $body = ['name' => 'John'];

        $result = $client->update(new Uri('/put'), $body)->wait();

        $this->assertEquals($result['json'] ?? [], $body);
    }

    /**
     * @return ClientInterface|\PHPUnit\Framework\MockObject\MockObject
     */
    protected function getClient()
    {
        $guzzle = new \GuzzleHttp\Client([
            'base_uri' => 'https://httpbin.org',
        ]);

        return new Client($guzzle);
    }

    public function testBatch()
    {
        $client = $this->getClient();

        $data = [
            ['name' => 'Foo'],
            ['name' => 'Bar'],
        ];

        $request1 = json_request('POST', new Uri('/post'), $data[0]);
        $request2 = json_request('POST', new Uri('/post'), $data[1]);
        $request3 = json_request('GET', new Uri('/status/400'));

        $batch = [$request1, $request2, $request3];

        $result = $client->batch($batch)->wait();

        $this->assertEquals(
            $result[0]['state'] ?? '',
            PromiseInterface::FULFILLED,
            'First async request failed'
        );

        $this->assertEquals(
            $result[1]['state'] ?? '',
            PromiseInterface::FULFILLED,
            'Second async request failed'
        );

        $this->assertEquals(
            $result[2]['state'] ?? '',
            PromiseInterface::REJECTED,
            'Invalid async request is not failed'
        );

        $this->assertEquals(
            $result[0]['value']['json'] ?? [],
            $data[0],
            'First async request return invalid data'
        );

        $this->assertEquals(
            $result[1]['value']['json'] ?? [],
            $data[1],
            'Second async request return invalid data'
        );

        $this->assertInstanceOf(
            Exception::class,
            $result[2]['value'],
            'Expected invalid async request return exception'
        );
    }
}
