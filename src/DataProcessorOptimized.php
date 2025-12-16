<?php

declare(strict_types=1);

namespace Amie\PackageX;

/**
 * Optimized DataProcessor class
 * 
 * This class demonstrates performance optimizations for common anti-patterns:
 * 1. Efficient array operations (direct assignment instead of merge)
 * 2. Efficient string building (implode/array join)
 * 3. Single-pass operations
 * 4. Batch processing instead of N+1
 * 5. Pre-computed values for sorting
 * 6. Use of hash maps for O(1) lookups
 */
class DataProcessorOptimized
{
    /**
     * Process array of items with efficient array operations
     * OPTIMIZED: Direct array assignment is O(n) instead of O(n²)
     */
    public function processItems(array $items): array
    {
        $result = [];
        foreach ($items as $item) {
            // Efficient: direct array assignment
            $result[] = $item * 2;
        }
        return $result;
    }

    /**
     * Build a large string efficiently
     * OPTIMIZED: Using implode avoids repeated memory allocation
     */
    public function buildLargeString(array $parts): string
    {
        if (empty($parts)) {
            return '';
        }
        // Efficient: single join operation
        return implode(', ', $parts);
    }

    /**
     * Filter and transform data in a single pass
     * OPTIMIZED: Combined operations reduce iterations from 3 to 1
     */
    public function filterAndTransform(array $data): array
    {
        $result = [];
        foreach ($data as $item) {
            // Single pass: filter, transform, and filter again
            if ($item > 10) {
                $transformed = $item * 2;
                if ($transformed < 100) {
                    $result[] = $transformed;
                }
            }
        }
        return $result;
    }

    /**
     * Batch process users
     * OPTIMIZED: Single batch call instead of N+1 queries
     */
    public function getUsersWithDetails(array $userIds): array
    {
        // Batch fetch all users at once
        return $this->fetchUsersBatch($userIds);
    }

    /**
     * Sort with pre-computed values
     * OPTIMIZED: Compute hash once per item using array_map
     */
    public function sortByComplexCriteria(array $items): array
    {
        // Pre-compute hashes
        $itemsWithHash = array_map(function($item) {
            return [
                'data' => $item,
                'hash' => md5(json_encode($item))
            ];
        }, $items);
        
        // Sort using pre-computed hashes
        usort($itemsWithHash, function($a, $b) {
            return strcmp($a['hash'], $b['hash']);
        });
        
        // Extract original items
        return array_map(function($item) {
            return $item['data'];
        }, $itemsWithHash);
    }

    /**
     * Find duplicates efficiently using hash map
     * OPTIMIZED: Using array_count_values is O(n) instead of O(n²)
     */
    public function findDuplicates(array $items): array
    {
        $counts = array_count_values($items);
        $duplicates = [];
        
        foreach ($counts as $value => $count) {
            if ($count > 1) {
                $duplicates[] = $value;
            }
        }
        
        return $duplicates;
    }

    /**
     * Match items using hash map for O(n) complexity
     * OPTIMIZED: Build hash map for O(1) lookups instead of nested loops
     */
    public function matchItems(array $source, array $target): array
    {
        // Build hash map of target items by id
        $targetMap = [];
        foreach ($target as $targetItem) {
            $targetMap[$targetItem['id']] = $targetItem;
        }
        
        // Match source items with target using hash map
        $matches = [];
        foreach ($source as $sourceItem) {
            if (isset($targetMap[$sourceItem['id']])) {
                $matches[] = array_merge($sourceItem, $targetMap[$sourceItem['id']]);
            }
        }
        
        return $matches;
    }

    /**
     * Calculate statistics in a single pass
     * OPTIMIZED: Single iteration to calculate all statistics
     */
    public function calculateStats(array $numbers): array
    {
        if (empty($numbers)) {
            return [
                'sum' => 0,
                'mean' => 0,
                'max' => null
            ];
        }
        
        // Single pass to compute all stats
        $sum = 0;
        $max = null;
        $count = 0;
        
        foreach ($numbers as $num) {
            $sum += $num;
            $count++;
            if ($max === null || $num > $max) {
                $max = $num;
            }
        }
        
        return [
            'sum' => $sum,
            'mean' => $count > 0 ? $sum / $count : 0,
            'max' => $max
        ];
    }

    /**
     * Batch fetch users (simulated)
     * Represents a single database query with IN clause
     */
    private function fetchUsersBatch(array $ids): array
    {
        $users = [];
        foreach ($ids as $id) {
            $users[] = [
                'id' => $id,
                'name' => "User $id",
                'email' => "user$id@example.com"
            ];
        }
        return $users;
    }
}