<?php

declare(strict_types=1);

namespace Amie\PackageX;

/**
 * DataProcessor class with performance issues to be optimized
 * 
 * This class demonstrates several common performance anti-patterns:
 * 1. Inefficient array operations (repeated array_merge in loops)
 * 2. Inefficient string concatenation in loops
 * 3. Redundant computations
 * 4. N+1 query pattern
 * 5. Inefficient sorting and filtering
 */
class DataProcessor
{
    /**
     * Process array of items with inefficient array merging
     * ISSUE: Using array_merge in a loop is O(n²) due to array copying
     */
    public function processItems(array $items): array
    {
        $result = [];
        foreach ($items as $item) {
            // Inefficient: array_merge creates a new array each time
            $result = array_merge($result, [$item * 2]);
        }
        return $result;
    }

    /**
     * Build a large string inefficiently
     * ISSUE: String concatenation in loops causes repeated memory allocation
     */
    public function buildLargeString(array $parts): string
    {
        $result = '';
        foreach ($parts as $part) {
            // Inefficient: creates new string object each iteration
            $result .= $part . ', ';
        }
        return rtrim($result, ', ');
    }

    /**
     * Filter and transform data with redundant iterations
     * ISSUE: Multiple passes over the same array
     */
    public function filterAndTransform(array $data): array
    {
        // First pass: filter
        $filtered = [];
        foreach ($data as $item) {
            if ($item > 10) {
                $filtered[] = $item;
            }
        }

        // Second pass: transform
        $transformed = [];
        foreach ($filtered as $item) {
            $transformed[] = $item * 2;
        }

        // Third pass: filter again
        $result = [];
        foreach ($transformed as $item) {
            if ($item < 100) {
                $result[] = $item;
            }
        }

        return $result;
    }

    /**
     * Simulate N+1 query pattern
     * ISSUE: Making individual calls instead of batch processing
     */
    public function getUsersWithDetails(array $userIds): array
    {
        $users = [];
        foreach ($userIds as $id) {
            // Simulating individual database calls
            $users[] = $this->fetchUser($id);
        }
        return $users;
    }

    /**
     * Sort with redundant operations
     * ISSUE: Performing expensive operations inside comparison function
     */
    public function sortByComplexCriteria(array $items): array
    {
        usort($items, function($a, $b) {
            // Inefficient: computing hash repeatedly for same items
            $hashA = md5(json_encode($a));
            $hashB = md5(json_encode($b));
            return strcmp($hashA, $hashB);
        });
        return $items;
    }

    /**
     * Check if array contains value (inefficient implementation)
     * ISSUE: Using in_array on large arrays repeatedly
     */
    public function findDuplicates(array $items): array
    {
        $duplicates = [];
        foreach ($items as $i => $item) {
            // Inefficient: in_array is O(n) making this O(n²)
            for ($j = $i + 1; $j < count($items); $j++) {
                if ($items[$j] === $item && !in_array($item, $duplicates)) {
                    $duplicates[] = $item;
                }
            }
        }
        return $duplicates;
    }

    /**
     * Process nested data with inefficient lookups
     * ISSUE: Nested loops with array_search
     */
    public function matchItems(array $source, array $target): array
    {
        $matches = [];
        foreach ($source as $sourceItem) {
            foreach ($target as $targetItem) {
                // Inefficient: comparing every source with every target
                if ($sourceItem['id'] === $targetItem['id']) {
                    $matches[] = array_merge($sourceItem, $targetItem);
                }
            }
        }
        return $matches;
    }

    /**
     * Calculate statistics with redundant calculations
     * ISSUE: Repeatedly calculating the same values
     */
    public function calculateStats(array $numbers): array
    {
        $stats = [];
        
        // Calculate sum (will be used for mean)
        $sum = 0;
        foreach ($numbers as $num) {
            $sum += $num;
        }
        $stats['sum'] = $sum;
        
        // Calculate mean (iterating again unnecessarily)
        $total = 0;
        $count = 0;
        foreach ($numbers as $num) {
            $total += $num;
            $count++;
        }
        $stats['mean'] = $count > 0 ? $total / $count : 0;
        
        // Calculate max (iterating again)
        $max = null;
        foreach ($numbers as $num) {
            if ($max === null || $num > $max) {
                $max = $num;
            }
        }
        $stats['max'] = $max;
        
        return $stats;
    }

    /**
     * Simulate fetching a single user (represents database call)
     */
    private function fetchUser(int $id): array
    {
        // Simulate some processing
        return [
            'id' => $id,
            'name' => "User $id",
            'email' => "user$id@example.com"
        ];
    }
}