# Package-X

A PHP library demonstrating performance optimization techniques and best practices.

## Overview

This package contains example code demonstrating common performance issues in PHP and their optimized solutions. It serves as both a learning resource and a reference for writing efficient PHP code.

## Contents

- **DataProcessor** - Original implementation with common performance anti-patterns (preserved for educational purposes)
- **DataProcessorOptimized** - Optimized implementation with best practices applied
- **Performance Documentation** - Detailed explanations of each optimization
- **Benchmark Suite** - Performance comparison tool

## Performance Improvements

The optimized implementations demonstrate significant performance gains:

- **33x faster** array processing (97% improvement)
- **5x faster** string building (80% improvement)  
- **391x faster** duplicate detection (99.7% improvement)
- **11x faster** array matching (91% improvement)
- **2x faster** batch operations vs N+1 pattern (53% improvement)

See [PERFORMANCE_IMPROVEMENTS.md](PERFORMANCE_IMPROVEMENTS.md) for detailed analysis of each optimization.

## Running Benchmarks

```bash
composer install
php benchmark.php
```

## Running Tests

```bash
composer install
./vendor/bin/phpunit tests/
```

## Key Performance Patterns

### 1. Array Operations
❌ Avoid: `array_merge()` in loops (O(n²))  
✅ Use: Direct assignment with `[]` (O(n))

### 2. String Building
❌ Avoid: String concatenation in loops  
✅ Use: `implode()` for joining (single pass)

### 3. Data Processing
❌ Avoid: Multiple iterations over same data  
✅ Use: Single-pass algorithms

### 4. Database/API Calls
❌ Avoid: N+1 query pattern  
✅ Use: Batch operations

### 5. Lookups
❌ Avoid: Nested loops with `in_array()` (O(n²))  
✅ Use: Hash maps for O(1) lookups

### 6. Sorting
❌ Avoid: Expensive computations in comparison functions  
✅ Use: Pre-compute values before sorting

### 7. Statistics
❌ Avoid: Multiple passes for different metrics  
✅ Use: Single pass to calculate all metrics

## Best Practices

1. **Profile before optimizing** - Measure to find real bottlenecks
2. **Understand complexity** - Know the difference between O(n) and O(n²)
3. **Use appropriate data structures** - Hash maps for lookups, arrays for iteration
4. **Minimize I/O** - Batch operations when possible
5. **Leverage built-in functions** - PHP's native functions are optimized
6. **Test thoroughly** - Ensure optimizations don't change behavior

## Requirements

- PHP 8.0 or higher
- Composer

## Installation

```bash
composer require amie/package-x
```

## License

This is an educational project demonstrating PHP performance optimization techniques.

## Contributing

This package is designed to demonstrate performance optimization patterns. Contributions that add new examples or improve existing ones are welcome.
