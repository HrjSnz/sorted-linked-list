# Sorted Linked List for PHP

[![PHP 8.4+](https://img.shields.io/badge/PHP-8.4%2B-blue)](https://php.net)

A type-safe sorted linked list implementation using the Comparator pattern.

## Table of Contents

- [Requirements](#requirements)
- [Installation](#installation)
  - [For development (contributing)](#for-development-contributing)
  - [For local usage (in your project)](#for-local-usage-in-your-project)
- [Quick Start](#quick-start)
- [Using Built-in Lists](#using-built-in-lists)
  - [Integer List](#integer-list)
  - [String List](#string-list)
  - [Locale-Aware String Sorting](#locale-aware-string-sorting)
- [Available Comparators](#available-comparators)
  - [IntComparator](#intcomparator)
  - [StringComparator](#stringcomparator)
  - [LocaleStringComparator](#localestringcomparator)
  - [ReverseComparator](#reversecomparator)
- [Creating a Custom Comparator](#creating-a-custom-comparator)
- [Range Queries](#range-queries)
  - [Greater Than](#greater-than)
  - [Less Than](#less-than)
  - [In Range](#in-range)
- [Advanced Features](#advanced-features)
  - [Handling Duplicates](#handling-duplicates)
  - [Finding Duplicates](#finding-duplicates)
  - [Changing Sort Order](#changing-sort-order)
  - [Pattern: Template Method](#pattern-template-method)
- [API Reference](#api-reference)
  - [IntSortedLinkedList / StringSortedLinkedList](#intsortedlinkedlist--stringsortedlinkedlist)
- [Development](#development)
  - [Running Tests](#running-tests)
  - [Static Analysis](#static-analysis)
  - [Code Style](#code-style)

## Requirements

- PHP 8.4 or higher
- ext-intl

## Installation

### For development (contributing)

```bash
git clone <repo-url>
cd sorted-linked-list
composer install
```

See [Development](#development) section for running tests, static analysis, and code style.

### For local usage (in your project)

Add to your project's `composer.json`:

```json
{
  "repositories": [
    {
      "type": "path",
      "url": "../sorted-linked-list"
    }
  ],
  "require": {
    "hrjsnz/sorted-linked-list": "*"
  }
}
```

Then run `composer update` in your project.

## Quick Start

```php
use HrjSnz\SortedLinkedList\SortedList\IntSortedLinkedList;
use HrjSnz\SortedLinkedList\Comparator\IntComparator;

$list = new IntSortedLinkedList(new IntComparator());
$list->addMultiple(5, 2, 8, 1, 3);

foreach ($list as $value) {
     // Output: 1 2 3 5 8
}
```

## Using Built-in Lists

### Integer List

```php
use HrjSnz\SortedLinkedList\SortedList\IntSortedLinkedList;
use HrjSnz\SortedLinkedList\Comparator\IntComparator;

$list = new IntSortedLinkedList(new IntComparator());

// Add values
$list->add(10);
$list->addMultiple(5, 15, 3);

// Check existence
$list->contains(10); // true

// Remove
$list->remove(10);
$list->removeAll(5); // removes all occurrences

// Access
$list->first(); // 3
$list->last();  // 15
$list->count(); // 2

// Iterate
foreach ($list as $value) { ... }
```

### String List

```php
use HrjSnz\SortedLinkedList\SortedList\StringSortedLinkedList;
use HrjSnz\SortedLinkedList\Comparator\StringComparator;

$list = new StringSortedLinkedList(new StringComparator());

// Case-insensitive option
$insensitive = new StringSortedLinkedList(
    new StringComparator(caseSensitive: false)
);

$list->addMultiple('zebra', 'apple', 'banana');
// Automatically sorted: apple, banana, zebra
```

### Locale-Aware String Sorting

```php
use HrjSnz\SortedLinkedList\Comparator\LocaleStringComparator;

$list = new StringSortedLinkedList(
    new LocaleStringComparator('de_DE') // German locale
);
```

## Available Comparators

### IntComparator

Compares integers in ascending order.

```php
use HrjSnz\SortedLinkedList\Comparator\IntComparator;

$comparator = new IntComparator();
$comparator->compare(1, 5); // -4 (negative means first < second)
```

### StringComparator

Compares strings with optional case sensitivity.

```php
use HrjSnz\SortedLinkedList\Comparator\StringComparator;

// Case-sensitive (default)
$default = new StringComparator();

// Case-insensitive
$insensitive = new StringComparator(caseSensitive: false);
```

### LocaleStringComparator

Uses ICU Collator for locale-aware sorting. Requires `ext-intl`.

```php
use HrjSnz\SortedLinkedList\Comparator\LocaleStringComparator;

// Basic usage
$comparator = new LocaleStringComparator('fr_FR');

// With strength (how strict comparison is)
$primary = new LocaleStringComparator('en_US', Collator::PRIMARY);
```

**Strength levels:**
- `Collator::PRIMARY` - ignores accents and case
- `Collator::SECONDARY` - ignores case only
- `Collator::TERTIARY` (default) - case and accent sensitive
- `Collator::QUATERNARY` - includes extra distinguishing features

### ReverseComparator

Wraps any comparator to reverse its order.

```php
use HrjSnz\SortedLinkedList\Comparator\ReverseComparator;

$descending = new ReverseComparator(new IntComparator());
$list = new IntSortedLinkedList($descending);
$list->addMultiple(1, 5, 3); // Sorted: 5, 3, 1
```

## Creating a Custom Comparator

To sort custom types, implement `ComparatorInterface`:

**Step 1: Create the Comparator**

```php
use HrjSnz\SortedLinkedList\Comparator\ComparatorInterface;

final readonly class YourComparator implements ComparatorInterface
{
    public function compare(mixed $a, mixed $b): int
    {
        // Validate types
        if (!$a instanceof YourType || !$b instanceof YourType) {
            throw new InvalidArgumentException('Expected YourType objects');
        }

        // Return negative if $a < $b, positive if $a > $b, 0 if equal
        return $a->property <=> $b->property;
    }
}
```

**Step 2: Create the Sorted List**

```php
use HrjSnz\SortedLinkedList\SortedLinkedListEngine;

/**
 * @extends SortedLinkedListEngine<YourType>
 */
final class YourSortedLinkedList extends SortedLinkedListEngine
{
    public function __construct(bool $allowDuplicates = true)
    {
        parent::__construct(new YourComparator(), $allowDuplicates);
    }

    public function addYourType(YourType $item): void
    {
        $this->insert($item);
    }
}
```

**The `compare()` method must:**
- Return a **negative integer** if `$a` should come before `$b`
- Return **zero** if `$a` and `$b` are equal
- Return a **positive integer** if `$a` should come after `$b`

## Range Queries

All lists support finding values within ranges using `BetweenBoundary` enum.

### Greater Than

```php
use HrjSnz\SortedLinkedList\BetweenBoundary;

// Inclusive (default): yields 5, 6, 7, ...
$result = $list->findGreaterThan(5, BetweenBoundary::INCLUSIVE);
foreach ($result as $value) {}

// Exclusive: yields 6, 7, 8, ...
$result = $list->findGreaterThan(5, BetweenBoundary::EXCLUSIVE);
foreach ($result as $value) {}
```

### Less Than

```php
// Inclusive: yields ..., 3, 4, 5
$result = $list->findLessThan(5, BetweenBoundary::INCLUSIVE);
foreach ($result as $value) {}

// Exclusive: yields ..., 3, 4
$result = $list->findLessThan(5, BetweenBoundary::EXCLUSIVE);
foreach ($result as $value) {}
```

### In Range

```php
// [5, 10] inclusive: yields 5, 6, 7, 8, 9, 10
$range = $list->findInRange(5, 10);
foreach ($range as $value) { }

// (5, 10) exclusive: yields 6, 7, 8, 9
$range = $list->findInRange(
    5,
    10,
    BetweenBoundary::EXCLUSIVE,
    BetweenBoundary::EXCLUSIVE
);
foreach ($range as $value) { }

// [5, 10) mixed: yields 5, 6, 7, 8, 9
$range = $list->findInRange(
    5,
    10,
    BetweenBoundary::INCLUSIVE,
    BetweenBoundary::EXCLUSIVE
);
foreach ($range as $value) { }
```

## Advanced Features

### Handling Duplicates

By default, lists allow duplicates:

```php
$list = new IntSortedLinkedList(new IntComparator());
$list->addMultiple(1, 2, 2, 3);
$list->count(); // 4
```

To disallow duplicates, pass `false` as the second argument:

```php
$list = new IntSortedLinkedList(new IntComparator(), allowDuplicates: false);
$list->addMultiple(1, 2, 2, 3);
$list->count(); // 3 - second '2' was ignored
```

### Finding Duplicates

```php
// Get duplicate values only
$duplicates = $list->getDuplicates();
// [2, 3] for [1, 2, 2, 3, 3, 3]

// Get duplicates with counts
$counts = $list->getDuplicatesWithCount();
// [['value' => 2, 'count' => 2], ['value' => 3, 'count' => 3]]
```

### Changing Sort Order

```php
$list = new IntSortedLinkedList(new IntComparator());
$list->addMultiple(3, 1, 2);

// Change comparator and resort
$list->setComparator(
    new ReverseComparator(new IntComparator())
);
// Now: 3, 2, 1
```

### Pattern: Template Method

Concrete lists (`IntSortedLinkedList`, `StringSortedLinkedList`) provide:
- Type-specific public methods
- Input validation
- Type safety via `@extends` template annotation

The engine handles:
- All sorting logic
- Insertion/deletion orchestration
- State management

## API Reference

### IntSortedLinkedList / StringSortedLinkedList

#### Adding Values
| Method | Description |
|--------|-------------|
| `add($value)` | Insert single value (maintains sort) |
| `addMultiple(...$values)` | Insert multiple values |

#### Removing Values
| Method | Returns | Description |
|--------|---------|-------------|
| `remove($value)` | `bool` | Remove first occurrence |
| `removeAll($value)` | `int` | Remove all occurrences, return count |

#### Querying
| Method | Returns | Description |
|--------|---------|-------------|
| `contains($value)` | `bool` | Check if value exists |
| `first()` | `T\|null` | Get first value |
| `last()` | `T\|null` | Get last value |
| `count()` | `int` | Get element count |
| `isEmpty()` | `bool` | Check if empty |
| `getDuplicates()` | `array<T>` | Get duplicate values |
| `getDuplicatesWithCount()` | `array` | Get duplicates with counts |

#### Iteration

The list implements `IteratorAggregate`, enabling native PHP iteration:

```php
// Full iteration
foreach ($list as $value) { }

// Or convert to array
$values = iterator_to_array($list);
```

| Method | Returns | Description |
|--------|---------|-------------|
| `findGreaterThan($min, $boundary)` | `Traversable<int, T>` | Values >= or > min |
| `findLessThan($max, $boundary)` | `Traversable<int, T>` | Values <= or < max |
| `findInRange($min, $max, ...)` | `Traversable<int, T>` | Values in range |

#### Comparator Management
| Method | Description |
|--------|-------------|
| `setComparator($comparator)` | Change comparator and automatically resort the list |
| `getComparator()` | Get current comparator |

## Development

### Running Tests

```bash
vendor/bin/phpunit
```

### Static Analysis

```bash
vendor/bin/phpstan analyse
```

### Code Style

```bash
vendor/bin/php-cs-fixer fix
```