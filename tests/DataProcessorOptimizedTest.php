<?php

use PHPUnit\Framework\TestCase;
use Amie\PackageX\DataProcessorOptimized;

class DataProcessorOptimizedTest extends TestCase
{
    private DataProcessorOptimized $processor;

    protected function setUp(): void
    {
        $this->processor = new DataProcessorOptimized();
    }

    public function testProcessItems()
    {
        $items = [1, 2, 3, 4, 5];
        $result = $this->processor->processItems($items);
        $this->assertEquals([2, 4, 6, 8, 10], $result);
    }

    public function testProcessItemsLarge()
    {
        // Test with larger dataset
        $items = range(1, 1000);
        $result = $this->processor->processItems($items);
        $this->assertCount(1000, $result);
        $this->assertEquals(2, $result[0]);
        $this->assertEquals(2000, $result[999]);
    }

    public function testBuildLargeString()
    {
        $parts = ['apple', 'banana', 'cherry'];
        $result = $this->processor->buildLargeString($parts);
        $this->assertEquals('apple, banana, cherry', $result);
    }

    public function testBuildLargeStringEmpty()
    {
        $result = $this->processor->buildLargeString([]);
        $this->assertEquals('', $result);
    }

    public function testBuildLargeStringMany()
    {
        // Test with larger dataset
        $parts = array_fill(0, 1000, 'item');
        $result = $this->processor->buildLargeString($parts);
        $this->assertStringContainsString('item, item', $result);
    }

    public function testFilterAndTransform()
    {
        $data = [5, 15, 25, 35, 45, 55];
        $result = $this->processor->filterAndTransform($data);
        // Items > 10: [15, 25, 35, 45, 55]
        // Transformed (*2): [30, 50, 70, 90, 110]
        // Items < 100: [30, 50, 70, 90]
        $this->assertEquals([30, 50, 70, 90], $result);
    }

    public function testFilterAndTransformEdgeCases()
    {
        // All filtered out
        $result = $this->processor->filterAndTransform([1, 2, 3]);
        $this->assertEquals([], $result);
        
        // All pass through
        $data = [11, 12, 13];
        $result = $this->processor->filterAndTransform($data);
        $this->assertEquals([22, 24, 26], $result);
    }

    public function testGetUsersWithDetails()
    {
        $userIds = [1, 2, 3];
        $result = $this->processor->getUsersWithDetails($userIds);
        
        $this->assertCount(3, $result);
        $this->assertEquals(1, $result[0]['id']);
        $this->assertEquals('User 1', $result[0]['name']);
        $this->assertEquals('user1@example.com', $result[0]['email']);
    }

    public function testSortByComplexCriteria()
    {
        $items = [
            ['name' => 'Charlie', 'age' => 30],
            ['name' => 'Alice', 'age' => 25],
            ['name' => 'Bob', 'age' => 35]
        ];
        
        $result = $this->processor->sortByComplexCriteria($items);
        
        // Should be sorted (by hash, deterministic)
        $this->assertCount(3, $result);
        // Verify it's an array of items
        $this->assertArrayHasKey('name', $result[0]);
    }

    public function testFindDuplicates()
    {
        $items = [1, 2, 3, 2, 4, 3, 5];
        $result = $this->processor->findDuplicates($items);
        
        $this->assertCount(2, $result);
        $this->assertContains(2, $result);
        $this->assertContains(3, $result);
    }

    public function testFindDuplicatesNoDuplicates()
    {
        $items = [1, 2, 3, 4, 5];
        $result = $this->processor->findDuplicates($items);
        $this->assertEquals([], $result);
    }

    public function testMatchItems()
    {
        $source = [
            ['id' => 1, 'name' => 'Alice'],
            ['id' => 2, 'name' => 'Bob']
        ];
        
        $target = [
            ['id' => 1, 'age' => 25],
            ['id' => 3, 'age' => 30]
        ];
        
        $result = $this->processor->matchItems($source, $target);
        
        $this->assertCount(1, $result);
        $this->assertEquals(1, $result[0]['id']);
        $this->assertEquals('Alice', $result[0]['name']);
        $this->assertEquals(25, $result[0]['age']);
    }

    public function testCalculateStats()
    {
        $numbers = [10, 20, 30, 40, 50];
        $result = $this->processor->calculateStats($numbers);
        
        $this->assertEquals(150, $result['sum']);
        $this->assertEquals(30, $result['mean']);
        $this->assertEquals(50, $result['max']);
    }

    public function testCalculateStatsEmpty()
    {
        $result = $this->processor->calculateStats([]);
        
        $this->assertEquals(0, $result['sum']);
        $this->assertEquals(0, $result['mean']);
        $this->assertNull($result['max']);
    }

    public function testCalculateStatsSingle()
    {
        $result = $this->processor->calculateStats([42]);
        
        $this->assertEquals(42, $result['sum']);
        $this->assertEquals(42, $result['mean']);
        $this->assertEquals(42, $result['max']);
    }
}
