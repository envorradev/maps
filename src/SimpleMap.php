<?php

namespace Envorra\Maps;

use Traversable;
use ArrayIterator;
use Envorra\Maps\Exceptions\MapItemNotFound;
use Envorra\Maps\Contracts\Map as MapContract;
use Envorra\Castables\Traits\StringViaJsonViaArray;

/**
 * Map
 *
 * @package  Envorra\TypeHandler\Maps
 *
 * @template T
 *
 * @implements MapContract<T>
 */
class SimpleMap implements MapContract
{
    use StringViaJsonViaArray;

    /**
     * @param  array  $map
     */
    public function __construct(protected array $map = [])
    {

    }

    /**
     * @inheritDoc
     */
    public function all(): array
    {
        return $this->map;
    }

    /**
     * @inheritDoc
     */
    public function count(): int
    {
        return count($this->map);
    }

    /**
     * @inheritDoc
     */
    public function find(mixed $item): mixed
    {
        try {
            return $this->findOrFail($item);
        } catch (MapItemNotFound) {
            return null;
        }
    }

    /**
     * @inheritDoc
     */
    public function findOrFail(mixed $item): mixed
    {
        if (in_array($item, $this->map)) {
            return $this->map[array_search($item, $this->map)];
        }

        if (array_key_exists($item, $this->map)) {
            return $this->map[$item];
        }

        throw new MapItemNotFound();
    }

    /**
     * @inheritDoc
     */
    public function first(): mixed
    {
        return $this->nth(1);
    }

    /**
     * @inheritDoc
     */
    public function getIterator(): Traversable
    {
        return new ArrayIterator($this->map);
    }

    /**
     * @inheritDoc
     */
    public function getMap(): array
    {
        return $this->map;
    }

    /**
     * @inheritDoc
     */
    public function last(): mixed
    {
        $items = $this->map;
        return end($items);
    }

    /**
     * @inheritDoc
     */
    public function nth(int $nth): mixed
    {
        $nth--;
        if ($nth < $this->count()) {
            return $this->map[array_keys($this->map)[$nth]];
        }
        return null;
    }

    /**
     * @inheritDoc
     */
    public function offsetExists(mixed $offset): bool
    {
        return isset($this->map[$offset]);
    }

    /**
     * @inheritDoc
     */
    public function offsetGet(mixed $offset): mixed
    {
        return $this->offsetExists($offset) ? $this->map[$offset] : null;
    }

    /**
     * @inheritDoc
     */
    public function offsetSet(mixed $offset, mixed $value): void
    {
        if ($this->offsetExists($offset) || !is_int($offset)) {
            $this->map[$offset] = $value;
        } else {
            $this->map[] = $value;
        }
    }

    /**
     * @inheritDoc
     */
    public function offsetUnset(mixed $offset): void
    {
        unset($this->map[$offset]);
    }

    /**
     * @inheritDoc
     */
    public function toArray(): array
    {
        return $this->map;
    }
}
