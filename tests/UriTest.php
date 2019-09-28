<?php

namespace Anper\Jsonbox\Tests;

use Anper\Jsonbox\Uri;
use PHPUnit\Framework\TestCase;

/**
 * Class UriTest
 * @package Anper\Jsonbox\Tests
 */
class UriTest extends TestCase
{
    public function testSetAndGetPath(): void
    {
        $uri = new Uri('/foo');

        $this->assertEquals('/foo', $uri->getPath());
    }

    public function testSetAndGetQuery(): void
    {
        $query = ['q' => 123];

        $uri = new Uri('/', $query);

        $this->assertEquals($query, $uri->getQuery());
    }

    public function testWithPath(): void
    {
        $uri1 = new Uri('/foo');
        $uri2 = $uri1->withPath('bar');

        $this->assertEquals('/foo/bar', $uri2->getPath());
        $this->assertNotSame($uri1, $uri2);
    }

    public function testWithQuery(): void
    {
        $query1 = ['q1' => 123];
        $query2 = ['q2' => 123];

        $uri1 = new Uri('/', $query1);
        $uri2 = $uri1->withQuery($query2);

        $this->assertEquals($query2, $uri2->getQuery());
        $this->assertNotSame($uri1, $uri2);
    }
}
