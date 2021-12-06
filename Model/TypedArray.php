<?php

namespace Arkounay\Bundle\QuickAdminGeneratorBundle\Model;


/**
 * @internal
 * @psalm-template TKey of array-key
 * @psalm-template T
 * @template-extends \IteratorAggregate<TKey, T>
 * @template-extends \ArrayAccess<TKey|null, T>
 */
abstract class TypedArray implements \IteratorAggregate, \ArrayAccess, \Countable
{

    /**
     * @var Listable[]
     */
    private $items = [];

    abstract protected function createFromIndexName(string $index): Listable;
    abstract protected function getType(): string;

    public function get(string $field): Listable
    {
        return $this->items[$field];
    }

    public function add($field): self
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
            throw new \UnexpectedValueException("Added Listable can only be an instance $type or a String. Found : " . get_class($field));
        }

        return $this;
    }

    public function remove(string $fieldIndex): self
    {
        unset($this->items[$fieldIndex]);

        return $this;
    }

    public function getIterator()
    {
        return new \ArrayIterator($this->items);
    }

    /**
     * @param mixed $offset
     * @param Listable $value
     */
    public function offsetSet($offset, $value)
    {
        if (is_null($offset)) {
            $this->add($value);
        } elseif ($offset === $value->getIndex()) {
            $this->items[$offset] = $value;
        } else {
            throw new \RuntimeException("Key doesn't match with Listable's index");
        }
    }

    public function offsetExists($offset)
    {
        return isset($this->items[$offset]);
    }

    public function offsetUnset($offset)
    {
        unset($this->items[$offset]);
    }

    public function offsetGet($offset)
    {
        return $this->items[$offset];
    }

    public function isEmpty(): bool
    {
        return empty($this->items);
    }

    public function clear(): self
    {
        $this->items = [];

        return $this;
    }

    public function set(iterable $fields): void
    {
        $this->clear();
        foreach ($fields as $field) {
            $this->add($field);
        }
    }

    public function count()
    {
        return \count($this->items);
    }

    public function moveToLastPosition(string $index): self
    {
        $tmp = $this->items[$index];
        unset($this->items[$index]);
        $this->items[$index] = $tmp;

        return $this;
    }

    public function moveToFirstPosition(string $index): self
    {
        $this->items = array_merge([$index => $this->items[$index]], $this->items);

        return $this;
    }

    public function contains(string $index): bool
    {
        return isset($this->items);
    }

    public function filter(callable $callback): self
    {
        $this->items = array_filter($this->items, $callback);

        return $this;
    }

}