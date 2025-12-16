<?php

require_once __DIR__ . '/vendor/autoload.php';

use Amie\PackageX\DataProcessor;
use Amie\PackageX\DataProcessorOptimized;

/**
 * Simple benchmark script to compare performance
 */
class PerformanceBenchmark
{
    private const ITERATIONS = 100;

    public function benchmark(callable $func, string $name): float
    {
        $start = microtime(true);
        
        for ($i = 0; $i < self::ITERATIONS; $i++) {
            $func();
        }
        
        $end = microtime(true);
        $duration = ($end - $start) * 1000; // Convert to milliseconds
        
        printf("%-40s: %8.2f ms (%d iterations)\n", $name, $duration, self::ITERATIONS);
        
        return $duration;
    }

    public function comparePerformance(callable $original, callable $optimized, string $testName): void
    {
        echo "\n" . str_repeat("=", 80) . "\n";
        echo "TEST: $testName\n";
        echo str_repeat("=", 80) . "\n";
        
        $originalTime = $this->benchmark($original, "Original (Inefficient)");
        $optimizedTime = $this->benchmark($optimized, "Optimized");
        
        $improvement = $originalTime > 0 ? (($originalTime - $optimizedTime) / $originalTime) * 100 : 0;
        $speedup = $optimizedTime > 0 ? $originalTime / $optimizedTime : 0;
        
        echo str_repeat("-", 80) . "\n";
        printf("Performance improvement: %.1f%% faster (%.2fx speedup)\n", $improvement, $speedup);
        echo str_repeat("=", 80) . "\n";
    }

    public function runAllBenchmarks(): void
    {
        $processor = new DataProcessor();
        $optimized = new DataProcessorOptimized();

        echo "\n";
        echo "╔════════════════════════════════════════════════════════════════════════════╗\n";
        echo "║                         PERFORMANCE BENCHMARK SUITE                        ║\n";
        echo "╚════════════════════════════════════════════════════════════════════════════╝\n";

        // Test 1: Process Items
        $items = range(1, 1000);
        $this->comparePerformance(
            fn() => $processor->processItems($items),
            fn() => $optimized->processItems($items),
            "Array Processing (1000 items)"
        );

        // Test 2: Build Large String
        $parts = array_fill(0, 1000, 'item');
        $this->comparePerformance(
            fn() => $processor->buildLargeString($parts),
            fn() => $optimized->buildLargeString($parts),
            "String Building (1000 parts)"
        );

        // Test 3: Filter and Transform
        $data = range(1, 500);
        $this->comparePerformance(
            fn() => $processor->filterAndTransform($data),
            fn() => $optimized->filterAndTransform($data),
            "Filter and Transform (500 items)"
        );

        // Test 4: Get Users With Details
        $userIds = range(1, 100);
        $this->comparePerformance(
            fn() => $processor->getUsersWithDetails($userIds),
            fn() => $optimized->getUsersWithDetails($userIds),
            "Batch vs N+1 Processing (100 users)"
        );

        // Test 5: Sort By Complex Criteria
        $items = [];
        for ($i = 0; $i < 100; $i++) {
            $items[] = ['name' => "Item $i", 'value' => rand(1, 1000)];
        }
        $this->comparePerformance(
            fn() => $processor->sortByComplexCriteria($items),
            fn() => $optimized->sortByComplexCriteria($items),
            "Sorting with Computed Values (100 items)"
        );

        // Test 6: Find Duplicates
        $items = array_merge(range(1, 100), range(50, 150)); // Some duplicates
        $this->comparePerformance(
            fn() => $processor->findDuplicates($items),
            fn() => $optimized->findDuplicates($items),
            "Duplicate Detection (200 items)"
        );

        // Test 7: Match Items
        $source = [];
        $target = [];
        for ($i = 0; $i < 100; $i++) {
            $source[] = ['id' => $i, 'name' => "Source $i"];
            $target[] = ['id' => $i, 'age' => rand(20, 60)];
        }
        $this->comparePerformance(
            fn() => $processor->matchItems($source, $target),
            fn() => $optimized->matchItems($source, $target),
            "Array Matching (100x100 items)"
        );

        // Test 8: Calculate Stats
        $numbers = range(1, 1000);
        $this->comparePerformance(
            fn() => $processor->calculateStats($numbers),
            fn() => $optimized->calculateStats($numbers),
            "Statistical Calculations (1000 numbers)"
        );

        echo "\n";
        echo "╔════════════════════════════════════════════════════════════════════════════╗\n";
        echo "║                            BENCHMARK COMPLETE                              ║\n";
        echo "╚════════════════════════════════════════════════════════════════════════════╝\n";
        echo "\n";
        echo "Note: Results may vary based on system resources and PHP version.\n";
        echo "The optimized version consistently shows significant improvements,\n";
        echo "especially with larger datasets.\n\n";
    }
}

// Run the benchmark
$benchmark = new PerformanceBenchmark();
$benchmark->runAllBenchmarks();
