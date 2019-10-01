<?php

namespace Anper\Jsonbox\Tests;

use GuzzleHttp\Psr7\Uri;
use PHPUnit\Framework\TestCase;
use function Anper\Jsonbox\path_push;

/**
 * Class PathPushTest
 * @package Anper\Jsonbox\Tests
 */
class PathPushTest extends TestCase
{
    /**
     * @return array
     */
    public function pathProvider(): array
    {
        return [
            ['', '', ''],
            ['/', '', '/'],
            ['foo', '', 'foo'],
            ['/foo', '', '/foo'],
            ['/foo/', '', '/foo'],

            ['', '/', ''],
            ['/', '/', '/'],
            ['foo', '/', 'foo'],
            ['/foo', '/', '/foo'],
            ['/foo/', '/', '/foo'],

            ['', 'bar', 'bar'],
            ['/', 'bar', '/bar'],
            ['foo', 'bar', 'foo/bar'],
            ['/foo', 'bar', '/foo/bar'],
            ['/foo/', 'bar', '/foo/bar'],


            ['', '/bar', 'bar'],
            ['/', '/bar', '/bar'],
            ['foo', '/bar', 'foo/bar'],
            ['/foo', '/bar', '/foo/bar'],
            ['/foo/', '/bar', '/foo/bar'],

            ['', '/bar/', 'bar'],
            ['/', '/bar/', '/bar'],
            ['foo', '/bar/', 'foo/bar'],
            ['/foo', '/bar/', '/foo/bar'],
            ['/foo/', '/bar/', '/foo/bar'],
        ];
    }

    /**
     * @dataProvider pathProvider
     * @param string $path
     * @param string $push
     * @param string $expected
     */
    public function testPush(string $path, string $push, string $expected)
    {
        $uri = new Uri($path);

        $res = path_push($uri, $push);

        $this->assertEquals($expected, $res->getPath());
        $this->assertNotSame($uri, $res);
    }
}
