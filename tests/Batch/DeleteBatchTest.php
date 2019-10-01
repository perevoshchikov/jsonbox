<?php

namespace Anper\Jsonbox\Tests\Batch;

use Anper\Jsonbox\Batch\DeleteBatch;
use Anper\Jsonbox\Exception;
use GuzzleHttp\Psr7\Uri;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\RequestInterface;

/**
 * Class DeleteBatchTest
 * @package Anper\Jsonbox\Tests\Batch
 */
class DeleteBatchTest extends TestCase
{
    public function testInvalidData(): void
    {
        $this->expectException(Exception::class);

        $notString = [];

        new DeleteBatch(new Uri(), [$notString]);
    }

    public function testCreateRequests(): void
    {
        $batch = new DeleteBatch(new Uri(), [
            'a',
            'b'
        ]);

        $requests = \iterator_to_array($batch);

        $this->assertContainsOnlyInstancesOf(RequestInterface::class, $requests);
        $this->assertArrayHasKey('a', $requests);
        $this->assertArrayHasKey('b', $requests);
    }
}
