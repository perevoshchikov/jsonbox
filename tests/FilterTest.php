<?php

namespace Anper\Jsonbox\Tests;

use Anper\Jsonbox\Filter;
use PHPUnit\Framework\ExpectationFailedException;
use PHPUnit\Framework\TestCase;

/**
 * Class FilterTest
 * @package Anper\Jsonbox\Tests
 */
class FilterTest extends TestCase
{
    public function testLimit(): void
    {
        $filter = new Filter();
        $filter->limit(10);

        $this->assertArrayKeyAndValue($filter->toArray(), 'limit', 10);
    }

    public function testSkip(): void
    {
        $filter = new Filter();
        $filter->skip(10);

        $this->assertArrayKeyAndValue($filter->toArray(), 'skip', 10);
    }

    public function testSort(): void
    {
        $filter = new Filter();
        $filter->sort('name');

        $this->assertArrayKeyAndValue($filter->toArray(), 'sort', 'name');
    }

    /**
     * @param array $array
     * @param string $key
     * @param $value
     */
    protected function assertArrayKeyAndValue(array $array, string $key, $value): void
    {
        if (isset($array[$key])) {
            $this->assertEquals($array[$key], $value, "Item with key `$key` have mismatch value");
        } else {
            throw new ExpectationFailedException("Array don't have key `$key`");
        }
    }

    public function testLimitNull(): void
    {
        $filter = new Filter();
        $filter->limit(null);

        $this->assertArrayNotHasKey('limit', $filter->toArray());
    }

    public function testLimitMin(): void
    {
        $filter = new Filter();
        $filter->limit(-1);

        $this->assertArrayKeyAndValue($filter->toArray(), 'limit', 1);
    }

    public function testSkipNull(): void
    {
        $filter = new Filter();
        $filter->skip(null);

        $this->assertArrayNotHasKey('limit', $filter->toArray());
    }

    public function testSkipMin(): void
    {
        $filter = new Filter();
        $filter->limit(-1);

        $this->assertArrayNotHasKey('sort', $filter->toArray());
    }

    public function testSortNull(): void
    {
        $filter = new Filter();
        $filter->skip(null);

        $this->assertArrayNotHasKey('sort', $filter->toArray());
    }

    public function testWhere(): void
    {
        $filter = new Filter();
        $filter->where('name', 'John', '=');
        $filter->where('age', '18', '=');

        $this->assertArrayKeyAndValue($filter->toArray(), 'q', 'name=John,age=18');
    }

    public function testClear(): void
    {
        $filter = new Filter();
        $filter->where('name', 'John', '=');

        $this->assertArrayKeyAndValue($filter->toArray(), 'q', 'name=John');

        $filter->clear();

        $this->assertArrayNotHasKey('q', $filter->toArray());
    }

    /**
     * @return array
     */
    public function sugarMethodsProvider(): array
    {
        return [
            ['equalTo', 'age', '18', 'age:18'],
            ['greaterThan', 'age', '18', 'age:>18'],
            ['greaterThanOrEqual', 'age', '18', 'age:>=18'],
            ['lessThan', 'age', '18', 'age:<18'],
            ['lessThanOrEqual', 'age', '18', 'age:<=18'],
        ];
    }

    /**
     * @dataProvider sugarMethodsProvider
     * @param string $method
     * @param string $field
     * @param string $value
     * @param string $expected
     */
    public function testSugarMethods(
        string $method,
        string $field,
        string $value,
        string $expected
    ): void {
        $filter = new Filter();

        $filter->$method($field, $value);

        $this->assertArrayKeyAndValue($filter->toArray(), 'q', $expected);
    }
}
