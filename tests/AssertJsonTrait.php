<?php

namespace Anper\Jsonbox\Tests;

use Psr\Http\Message\RequestInterface;

/**
 * Trait AssertJsonTrait
 * @package Anper\Jsonbox\Tests
 */
trait AssertJsonTrait
{
    /**
     * @param RequestInterface $request
     * @param $body
     * @param string $message
     */
    public function assertJsonRequestEqualsBody(RequestInterface $request, $body, string $message = ''): void
    {
        if ($body === null) {
            static::assertEquals($request->getBody()->getSize(), 0, $message);
        } else {
            static::assertJsonStringEqualsJsonString(
                $request->getBody()->getContents(),
                \json_encode($body),
                $message
            );
        }
    }
}
