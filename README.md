# ExternalSort - PHP External Sort

![Tests](https://github.com/fishingboy/ExternalSort/actions/workflows/ci.yml/badge.svg)
![License](https://img.shields.io/badge/license-MIT-blue.svg)
![Packagist Version](https://img.shields.io/packagist/v/fishingboy/external_sort.svg)

[繁體中文](README-zh.md) | **English**

A PHP library for external merge sort — designed for datasets too large to sort in memory.
You can set a `block_size` limit: data within the limit is sorted in memory, while larger datasets are split into sorted temporary files and merged via a k-way merge.

Available on Packagist for installation via Composer.

## Installation

```bash
composer require fishingboy/external_sort
```

Or add it manually to `composer.json`:

```json
{
    "require": {
        "fishingboy/external_sort": "dev-master"
    }
}
```

## Usage

```php
use fishingboy\external_sort\External_sort;

$sorter = new External_sort([
    'block_size'  => 1000,       // max rows buffered in memory before flushing to a temp file
    'result_file' => 'out.txt',  // output path for the final sorted result
    'data_type'   => 'number',   // 'number' (int cast) or 'text' (string trim)
]);

$sorter->add_data($value);  // accepts a single value or an array; call repeatedly
$sorter->create_result();   // performs k-way merge and writes to result_file
```
