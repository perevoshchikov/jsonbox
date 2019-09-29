<?php

namespace Anper\Jsonbox\Tests;

use Anper\Jsonbox\Client;
use Anper\Jsonbox\Exception;
use GuzzleHttp\ClientInterface;
use GuzzleHttp\Psr7\Uri;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ResponseInterface;

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

        $client = new Client($guzzle);
    }

    public function testSend(): void
    {
        $uri    = new Uri('/');
        $body   = ['foo' => 'bar'];
        $data   = ['name' => 123];
        $method = 'GET';

        $response = $this->createMock(ResponseInterface::class);
        $response->expects($this->once())
            ->method('getBody')
            ->willReturn(\json_encode($data));

        $guzzle = $this->getGuzzleClient();
        $guzzle->expects($this->once())
            ->method('request')
            ->with($method, $uri, $this->contains($body))
            ->willReturn($response);

        $client = new Client($guzzle);

        $this->assertEquals($data, $client->send($method, $uri, $body));
    }

    public function testInvalidRequest(): void
    {
        $message = 'exception message';

        $this->expectException(Exception::class);
        $this->expectExceptionMessage($message);

        $guzzle = $this->getGuzzleClient();
        $guzzle->expects($this->once())
            ->method('request')
            ->willThrowException(new \Exception($message));

        $client = new Client($guzzle);

        $client->send('GET', new Uri('/'));
    }

    public function testInvalidJson(): void
    {
        $this->expectException(Exception::class);

        $response = $this->createMock(ResponseInterface::class);;
        $response->expects($this->once())
            ->method('getBody')
            ->willReturn('');

        $guzzle = $this->getGuzzleClient();
        $guzzle->expects($this->once())
            ->method('request')
            ->willReturn($response);

        $client = new Client($guzzle);

        $client->send('GET', new Uri('/'));
    }

    /**
     * @return ClientInterface|\PHPUnit\Framework\MockObject\MockObject
     */
    protected function getGuzzleClient()
    {
        $guzzle = $this->createMock(ClientInterface::class);
        $guzzle->expects($this->once())
            ->method('getConfig')
            ->willReturn(true);

        return $guzzle;
    }
}
