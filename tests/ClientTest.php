<?php

namespace Anper\Jsonbox\Tests;

use Anper\Jsonbox\Client;
use Anper\Jsonbox\Exception;
use Anper\Jsonbox\Uri;
use GuzzleHttp\ClientInterface;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ResponseInterface;

/**
 * Class ClientTest
 * @package Anper\Jsonbox\Tests
 */
class ClientTest extends TestCase
{
    public function testSetAndGetGuzzleClient(): void
    {
        $guzzle = $this->createMock(ClientInterface::class);
        $client = new Client($guzzle);

        $this->assertEquals($client->getClient(), $guzzle);
    }

    public function testSend(): void
    {
        $query  = ['q' => '1'];
        $uri    = new Uri('/', $query);
        $body   = ['foo' => 'bar'];
        $data   = ['name' => 123];
        $method = 'GET';

        $response = $this->createMock(ResponseInterface::class);;
        $response->expects($this->once())
            ->method('getBody')
            ->willReturn(\json_encode($data));

        $guzzle = $this->createMock(ClientInterface::class);
        $guzzle->expects($this->once())
            ->method('request')
            ->with($method, $uri->getPath(), $this->logicalAnd(
                $this->contains($body),
                $this->contains($query)
            ))
            ->willReturn($response);

        $client = new Client($guzzle);

        $this->assertEquals($data, $client->send($method, $uri, $body));
    }

    public function testInvalidRequest(): void
    {
        $message = 'exception message';

        $this->expectException(Exception::class);
        $this->expectExceptionMessage($message);

        $guzzle = $this->createMock(ClientInterface::class);
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

        $guzzle = $this->createMock(ClientInterface::class);
        $guzzle->expects($this->once())
            ->method('request')
            ->willReturn($response);

        $client = new Client($guzzle);

        $client->send('GET', new Uri('/'));
    }
}