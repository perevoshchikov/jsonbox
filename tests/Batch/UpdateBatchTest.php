<?php

namespace Anper\Jsonbox\Tests\Batch;

use Anper\Jsonbox\Batch\UpdateBatch;
use Anper\Jsonbox\Exception;
use Anper\Jsonbox\Tests\AssertJsonTrait;
use GuzzleHttp\Psr7\Uri;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\RequestInterface;

/**
 * Class DeleteBatchTest
 * @package Anper\Jsonbox\Tests\Batch
 */
class UpdateBatchTest extends TestCase
{
    use AssertJsonTrait;

    public function testInvalidDataKey(): void
    {
        $this->expectException(Exception::class);

        $notString = 1;

        new UpdateBatch(new Uri(), [
            $notString => [],
        ]);
    }

    public function testInvalidDataValue(): void
    {
        $this->expectException(Exception::class);

        $notArray = 1;

        new UpdateBatch(new Uri(), [
            'a' => $notArray,
        ]);
    }

    public function testCreateRequests(): void
    {
        $batch = new UpdateBatch(new Uri(), [
            'a' => ['name' => 'Foo'],
            'b' => ['name' => 'Bar'],
        ]);

        $requests = \iterator_to_array($batch);

        $this->assertContainsOnlyInstancesOf(RequestInterface::class, $requests);
        $this->assertArrayHasKey('a', $requests);
        $this->assertArrayHasKey('b', $requests);

        $this->assertJsonRequestEqualsBody($requests['a'], ['name' => 'Foo']);
        $this->assertJsonRequestEqualsBody($requests['b'], ['name' => 'Bar']);
    }
}
