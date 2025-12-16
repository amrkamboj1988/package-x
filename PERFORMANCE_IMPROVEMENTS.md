# Performance Improvements

This document outlines the performance issues identified in the codebase and the optimizations implemented to address them.

## Overview

The `DataProcessor` class demonstrates common PHP performance anti-patterns, while `DataProcessorOptimized` shows the optimized versions of the same methods. Both classes are functionally equivalent but have drastically different performance characteristics.

## Performance Issues Identified and Fixed

### 1. Inefficient Array Operations (O(n²) → O(n))

**Problem:** Using `array_merge()` in a loop
```php
// INEFFICIENT - O(n²)
$result = [];
foreach ($items as $item) {
    $result = array_merge($result, [$item * 2]);
}
```

**Issue:** `array_merge()` creates a new array each iteration by copying all existing elements plus the new one. For n items, this results in O(n²) time complexity.

**Solution:** Direct array assignment
```php
// OPTIMIZED - O(n)
$result = [];
foreach ($items as $item) {
    $result[] = $item * 2;
}
```

**Impact:** For 1,000 items: O(n²) = ~1,000,000 operations vs O(n) = ~1,000 operations (1000x faster)

---

### 2. Inefficient String Concatenation

**Problem:** String concatenation in loops
```php
// INEFFICIENT
$result = '';
foreach ($parts as $part) {
    $result .= $part . ', ';
}
return rtrim($result, ', ');
```

**Issue:** Each concatenation creates a new string object, copying all existing characters plus the new ones. This results in O(n²) string copies.

**Solution:** Use `implode()` for single-pass operation
```php
// OPTIMIZED
return implode(', ', $parts);
```

**Impact:** For 1,000 strings: Multiple string reallocations vs single join operation (significantly faster and less memory)

---

### 3. Multiple Array Passes (3 passes → 1 pass)

**Problem:** Iterating over arrays multiple times
```php
// INEFFICIENT - 3 separate loops
$filtered = [];
foreach ($data as $item) {
    if ($item > 10) $filtered[] = $item;
}

$transformed = [];
foreach ($filtered as $item) {
    $transformed[] = $item * 2;
}

$result = [];
foreach ($transformed as $item) {
    if ($item < 100) $result[] = $item;
}
```

**Issue:** Each iteration has overhead. Processing the same data three times is wasteful.

**Solution:** Combine operations in a single pass
```php
// OPTIMIZED - 1 loop
$result = [];
foreach ($data as $item) {
    if ($item > 10) {
        $transformed = $item * 2;
        if ($transformed < 100) {
            $result[] = $transformed;
        }
    }
}
```

**Impact:** 3x reduction in loop overhead, better CPU cache utilization

---

### 4. N+1 Query Pattern (N calls → 1 batch call)

**Problem:** Individual calls in a loop
```php
// INEFFICIENT - N+1 pattern
$users = [];
foreach ($userIds as $id) {
    $users[] = $this->fetchUser($id);  // Separate call each time
}
```

**Issue:** Making N separate database/API calls has significant overhead (network latency, connection overhead, etc.)

**Solution:** Batch processing
```php
// OPTIMIZED - Single batch call
return $this->fetchUsersBatch($userIds);  // One call for all IDs
```

**Impact:** For 100 users with 10ms latency: 1,000ms vs 10ms (100x faster)

---

### 5. Redundant Computations in Sorting

**Problem:** Computing expensive operations repeatedly in comparison function
```php
// INEFFICIENT
usort($items, function($a, $b) {
    $hashA = md5(json_encode($a));  // Computed repeatedly for same items
    $hashB = md5(json_encode($b));
    return strcmp($hashA, $hashB);
});
```

**Issue:** For n items, each comparison computes hashes. With O(n log n) comparisons, hashes are computed O(n² log n) times.

**Solution:** Pre-compute values before sorting
```php
// OPTIMIZED
$itemsWithHash = array_map(function($item) {
    return ['data' => $item, 'hash' => md5(json_encode($item))];
}, $items);

usort($itemsWithHash, function($a, $b) {
    return strcmp($a['hash'], $b['hash']);  // Use pre-computed hash
});
```

**Impact:** For 1,000 items: ~10,000 hash computations vs 1,000 (10x fewer)

---

### 6. Inefficient Duplicate Detection (O(n²) → O(n))

**Problem:** Using nested loops with `in_array()`
```php
// INEFFICIENT - O(n²)
$duplicates = [];
foreach ($items as $i => $item) {
    for ($j = $i + 1; $j < count($items); $j++) {
        if ($items[$j] === $item && !in_array($item, $duplicates)) {
            $duplicates[] = $item;
        }
    }
}
```

**Issue:** Nested loops create O(n²) comparisons. `in_array()` adds another O(n) check.

**Solution:** Use hash map (array_count_values)
```php
// OPTIMIZED - O(n)
$counts = array_count_values($items);
$duplicates = [];

foreach ($counts as $value => $count) {
    if ($count > 1) {
        $duplicates[] = $value;
    }
}
```

**Impact:** For 1,000 items: ~1,000,000 comparisons vs ~2,000 operations (500x faster)

---

### 7. Inefficient Array Matching (O(n×m) → O(n+m))

**Problem:** Nested loops for matching
```php
// INEFFICIENT - O(n×m)
$matches = [];
foreach ($source as $sourceItem) {
    foreach ($target as $targetItem) {
        if ($sourceItem['id'] === $targetItem['id']) {
            $matches[] = array_merge($sourceItem, $targetItem);
        }
    }
}
```

**Issue:** Every source item is compared with every target item.

**Solution:** Build a hash map for O(1) lookups
```php
// OPTIMIZED - O(n+m)
$targetMap = [];
foreach ($target as $targetItem) {
    $targetMap[$targetItem['id']] = $targetItem;
}

$matches = [];
foreach ($source as $sourceItem) {
    if (isset($targetMap[$sourceItem['id']])) {
        $matches[] = array_merge($sourceItem, $targetMap[$sourceItem['id']]);
    }
}
```

**Impact:** For 100 source and 100 target items: 10,000 comparisons vs 200 operations (50x faster)

---

### 8. Redundant Statistical Calculations (3 passes → 1 pass)

**Problem:** Multiple iterations to calculate statistics
```php
// INEFFICIENT - 3 separate loops
$sum = 0;
foreach ($numbers as $num) { $sum += $num; }

$total = 0; $count = 0;
foreach ($numbers as $num) { $total += $num; $count++; }
$mean = $count > 0 ? $total / $count : 0;

$max = null;
foreach ($numbers as $num) {
    if ($max === null || $num > $max) $max = $num;
}
```

**Issue:** Iterating over the same data multiple times is wasteful.

**Solution:** Calculate all statistics in one pass
```php
// OPTIMIZED - 1 loop
$sum = 0; $max = null; $count = 0;

foreach ($numbers as $num) {
    $sum += $num;
    $count++;
    if ($max === null || $num > $max) $max = $num;
}

$mean = $count > 0 ? $sum / $count : 0;
```

**Impact:** 3x reduction in iterations and better cache utilization

---

## Performance Testing Results

Both `DataProcessor` and `DataProcessorOptimized` pass identical test suites, proving functional equivalence. However, the optimized version demonstrates:

- **Array operations:** 100-1000x faster for large datasets
- **String building:** Significantly faster and less memory usage
- **Multi-pass operations:** 3x reduction in loop overhead
- **N+1 patterns:** 10-100x faster depending on latency
- **Sorting:** 10x reduction in expensive computations
- **Duplicate detection:** 500x faster for large arrays
- **Array matching:** 50x faster for large datasets
- **Statistics:** 3x reduction in iterations

## Best Practices Applied

1. **Minimize array copying** - Use direct assignment instead of merge
2. **Use built-in functions** - `implode()`, `array_count_values()`, etc. are optimized
3. **Single-pass algorithms** - Combine operations when possible
4. **Batch operations** - Reduce I/O overhead
5. **Pre-compute expensive operations** - Especially in sorting/comparison functions
6. **Use hash maps** - O(1) lookups instead of O(n) searches
7. **Avoid nested loops** - Use hash maps or algorithms with better complexity

## Conclusion

These optimizations demonstrate that understanding algorithm complexity and PHP internals can lead to dramatic performance improvements without changing functionality. The key is to:

1. **Identify the bottleneck** - Profile to find slow operations
2. **Analyze complexity** - Understand O(n²) vs O(n) impact
3. **Apply appropriate data structures** - Hash maps for lookups, pre-computed values for sorting
4. **Batch operations** - Reduce I/O and overhead
5. **Test thoroughly** - Ensure optimizations don't change behavior

These patterns are applicable to many real-world scenarios including database queries, API calls, data processing pipelines, and more.
