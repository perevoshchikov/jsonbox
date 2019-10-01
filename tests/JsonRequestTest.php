<?php

namespace Anper\Jsonbox\Tests;

use Anper\Jsonbox\Exception;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\UriInterface;
use function Anper\Jsonbox\json_request;

/**
 * Class JsonRequestTest
 * @package Anper\Jsonbox\Tests
 */
class JsonRequestTest extends TestCase
{
    use AssertJsonTrait;

    public function testWithoutBody(): void
    {
        $method = 'GET';
        $uri    = $this->createMock(UriInterface::class);

        $request = json_request($method, $uri);

        $this->assertEquals($request->getMethod(), $method);
        $this->assertEquals($request->getUri(), $uri);
        $this->assertJsonRequestEqualsBody($request, null);
    }

    public function testWithBody(): void
    {
        $method = 'GET';
        $body   = ['name' => 'John'];
        $uri    = $this->createMock(UriInterface::class);

        $request = json_request($method, $uri, $body);

        $this->assertEquals($request->getMethod(), $method);
        $this->assertEquals($request->getUri(), $uri);
        $this->assertJsonRequestEqualsBody($request, $body);
    }

    public function testInvalidJson(): void
    {
        $this->expectException(Exception::class);

        $method = 'GET';
        $uri    = $this->createMock(UriInterface::class);
        $stream = \fopen('php://memory', 'rb+');

        try {
            json_request($method, $uri, [$stream]);
        } catch (\Exception $exception) {
            throw $exception;
        } finally {
            \fclose($stream);
        }
    }

    public function testHeaders(): void
    {
        $uri = $this->createMock(UriInterface::class);

        $request = json_request('GET', $uri);

        $this->assertEquals($request->getHeader('Content-Type'), ['application/json']);
        $this->assertEquals($request->getHeader('Accept'), ['application/json']);
    }
}
