<?php

namespace Anper\Jsonbox\Tests;

use Anper\Jsonbox\Client;
use Anper\Jsonbox\Exception;
use GuzzleHttp\ClientInterface;
use GuzzleHttp\Psr7\Uri;
use PHPUnit\Framework\TestCase;

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
        $client = new Client(
            $this->getGuzzleClient()
        );

        $request = $client->createRequest('GET', new Uri('/json'));

        $this->assertIsArray($client->send($request)->wait());
    }

    public function testInvalidRequest(): void
    {
        $this->expectException(Exception::class);

        $client = new Client(
            $this->getGuzzleClient()
        );

        $request = $client->createRequest('GET', new Uri('/status/400'));

        $client->send($request)->wait();
    }

    public function testInvalidJson(): void
    {
        $this->expectException(Exception::class);

        $client = new Client(
            $this->getGuzzleClient()
        );

        $request = $client->createRequest('GET', new Uri('/xml'));

        $client->send($request)->wait();
    }

    public function testCreateRequest(): void
    {
        $client = new Client(
            $this->getGuzzleClient()
        );

        $uri = new Uri('/foo');
        $body = ['name' => 'John'];

        $request = $client->createRequest('GET', $uri, $body);

        $this->assertEquals('GET', $request->getMethod());
        $this->assertEquals($uri, $request->getUri());
        $this->assertEquals(\json_encode($body), (string) $request->getBody());
    }

    public function testCreate(): void
    {
        $client = new Client(
            $this->getGuzzleClient()
        );

        $body = ['name' => 'John'];

        $result = $client->create(new Uri('/post'), $body)->wait();

        $this->assertEquals($result['json'] ?? [], $body);
    }

    public function testRead(): void
    {
        $client = new Client(
            $this->getGuzzleClient()
        );

        $this->assertIsArray($client->read(new Uri('/get'))->wait());
    }

    public function testDelete(): void
    {
        $client = new Client(
            $this->getGuzzleClient()
        );

        $this->assertIsArray($client->delete(new Uri('/delete'))->wait());
    }

    public function testUpdate(): void
    {
        $client = new Client(
            $this->getGuzzleClient()
        );

        $body = ['name' => 'John'];

        $result = $client->update(new Uri('/put'), $body)->wait();

        $this->assertEquals($result['json'] ?? [], $body);
    }

    /**
     * @return ClientInterface|\PHPUnit\Framework\MockObject\MockObject
     */
    protected function getGuzzleClient()
    {
        $guzzle = new \GuzzleHttp\Client([
            'base_uri' => 'https://httpbin.org',
        ]);

        return $guzzle;
    }
}
