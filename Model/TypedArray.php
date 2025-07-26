<?php

namespace Arkounay\Bundle\QuickAdminGeneratorBundle\Model;


/**
 * @internal
 * @template TKey of array-key
 * @template T of Listable
 * @template-implements \IteratorAggregate<TKey, T>
 * @template-implements \ArrayAccess<TKey|null, T>
 */
abstract class TypedArray implements \IteratorAggregate, \ArrayAccess, \Countable
{

    /**
     * @var array<TKey, T>
     */
    protected array $items = [];

    /**
     * @return T
     */
    abstract public function createFromIndexName(string $index): Listable;

    abstract protected function getType(): string;

    /**
     * @phpstan-return T
     */
    public function get(string $field): Listable
    {
        return $this->items[$field];
    }

    /**
     * @phpstan-param T|string $field
     */
    public function add(Listable|string $field): static
    {
        $type = $this->getType();
        if ($field instanceof $type) {
            $this->items[$field->getIndex()] = $field;
        } elseif (is_string($field)) {
            if (isset($this->items[$field])) {
                // move element to the end
                $this->moveToLastPosition($field);
            } else {
                $this->items[$field] = $this->createFromIndexName($field);
            }
        } else {
            throw new \UnexpectedValueException("Added Listable can only be an instance $type or a String. Found: " . $field::class);
        }

        return $this;
    }

    public function remove(string $fieldIndex): static
    {
        unset($this->items[$fieldIndex]);

        return $this;
    }

    /**
     * @return \ArrayIterator<TKey, T>
     */
    public function getIterator(): \Traversable
    {
        return new \ArrayIterator($this->items);
    }

    /**
     * @param T $value
     */
    public function offsetSet(mixed $offset, $value): void
    {
        if (is_null($offset)) {
            $this->add($value);
        } elseif ($offset === $value->getIndex()) {
            $this->items[$offset] = $value;
        } else {
            throw new \RuntimeException("Key doesn't match with Listable's index");
        }
    }

    /**
     * @param TKey $offset
     */
    public function offsetExists($offset): bool
    {
        return isset($this->items[$offset]);
    }

    /**
     * @param TKey $offset
     */
    public function offsetUnset($offset): void
    {
        unset($this->items[$offset]);
    }

    /**
     * @param TKey $offset
     * @return T
     */
    public function offsetGet($offset): Listable
    {
        return $this->items[$offset];
    }

    public function isEmpty(): bool
    {
        return empty($this->items);
    }

    public function clear(): static
    {
        $this->items = [];

        return $this;
    }

    /**
     * @param iterable<TKey, T>|iterable<T> $fields
     */
    public function set(iterable $fields): void
    {
        $this->clear();
        foreach ($fields as $field) {
            $this->add($field);
        }
    }

    public function count(): int
    {
        return \count($this->items);
    }

    public function moveToLastPosition(string $index): static
    {
        $tmp = $this->items[$index];
        unset($this->items[$index]);
        $this->items[$index] = $tmp;

        return $this;
    }

    public function moveToFirstPosition(string $index): static
    {
        $this->items = array_merge([$index => $this->items[$index]], $this->items);

        return $this;
    }

    public function contains(string $index): bool
    {
        return isset($this->items[$index]);
    }

    /**
     * @param callable(T, TKey): bool $callback
     * @return static
     */
    public function filter(callable $callback): static
    {
        $this->items = array_filter($this->items, $callback, ARRAY_FILTER_USE_BOTH);

        return $this;
    }

}