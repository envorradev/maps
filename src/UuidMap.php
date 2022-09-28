<?php

namespace Envorra\Maps;

use Traversable;
use ArrayIterator;
use Envorra\Maps\Helpers\ArrayHelper;
use Envorra\Maps\Exceptions\MapItemNotFound;
use Envorra\Maps\Exceptions\CannotDirectlyModifyMapData;

/**
 * UuidMap
 *
 * @package  Envorra\TypeHandler\Maps
 *
 * @template T
 *
 * @extends SimpleMap<T>
 */
class UuidMap extends SimpleMap
{
    protected array $keys = [];

    protected array $uuids = [];

    /**
     * @param  array  $map
     */
    public function __construct(array $map = [])
    {
        parent::__construct($map);
        $this->map = array_change_key_case($this->map);
        $this->keys = array_keys($this->map);
        $this->uuids = array_keys($this->map[$this->keys[0]]);
    }

    /**
     * @param  string  $uuid
     * @return array
     */
    public function allOfUuid(string $uuid): array
    {
        return array_combine($this->keys, array_column($this->map, $uuid));
    }

    /**
     * @param  mixed    $item
     * @param  ?string  $keyType
     * @return string|T|null
     */
    public function find(mixed $item, ?string $keyType = null): mixed
    {
        return $this->arrayByKeyOrLast(
            array: $this->findAll($item),
            key: $keyType
        );
    }

    /**
     * @param  mixed  $item
     * @return array
     */
    public function findAll(mixed $item): array
    {
        if ($uuid = $this->findUuid($item)) {
            return $this->allOfUuid($uuid);
        }
        return [];
    }

    /**
     * @param  string  $item
     * @return array
     */
    public function findAllIgnoreCase(string $item): array
    {
        if ($uuid = $this->findUuidIgnoreCase($item)) {
            return $this->allOfUuid($uuid);
        }
        return [];
    }

    /**
     * @param  string       $item
     * @param  string|null  $keyType
     * @return mixed
     */
    public function findIgnoreCase(string $item, ?string $keyType = null): mixed
    {
        return $this->arrayByKeyOrLast(
            array: $this->findAllIgnoreCase($item),
            key: $keyType
        );
    }

    /**
     * @inheritDoc
     */
    public function findOrFail(mixed $item, ?string $keyType = null): mixed
    {
        if ($found = $this->find($item, $keyType)) {
            return $found;
        }

        throw new MapItemNotFound();
    }

    /**
     * @param  mixed  $item
     * @return string|null
     */
    public function findUuid(mixed $item): ?string
    {
        foreach ($this->keys as $key) {
            if (in_array($item, $this->map[$key])) {
                return array_search($item, $this->map[$key]);
            }
        }

        return null;
    }

    /**
     * @param  string  $item
     * @return string|null
     * @noinspection SpellCheckingInspection
     */
    public function findUuidIgnoreCase(string $item): ?string
    {
        $item = strtolower($item);
        foreach ($this->keys as $key) {
            $lowerMap = array_map('strtolower', $this->map[$key]);
            if (in_array($item, $lowerMap)) {
                return array_search($item, $lowerMap);
            }
        }

        return null;
    }

    /**
     * @return string[]
     */
    public function getKeys(): array
    {
        return $this->keys;
    }

    /**
     * @return string[]
     */
    public function getUuids(): array
    {
        return $this->uuids;
    }

    /**
     * @return array
     */
    public function getMap(): array
    {
        return $this->map;
    }

    /**
     * @inheritDoc
     */
    public function toArray(): array
    {
        return $this->map;
    }

    /**
     * @return array
     */
    public function toDottedArray(): array
    {
        $array = [];

        foreach($this->uuids as $uuid) {
            $current = null;
            foreach($this->allOfUuid($uuid) as $key => $value) {
                if($key === $this->lastKey()) {
                    $array[$current] = $value;
                } else {
                    $current = is_null($current) ? $value : $current.'.'.$value;
                }
            }
        }

        return $array;
    }

    /**
     * @return array
     */
    public function toNestedArray(): array
    {
        return ArrayHelper::toNested($this->toDottedArray());
    }

    /**
     * @param  array        $array
     * @param  string|null  $key
     * @return mixed
     */
    protected function arrayByKeyOrLast(array $array, ?string $key = null): mixed
    {
        if ($key) {
            $key = strtolower($key);
        }

        return array_key_exists($key, $array) ? $array[$key] : end($array);
    }

    /**
     * @return string|int
     */
    protected function lastKey(): string|int
    {
        $keys = $this->keys;
        return end($keys);
    }

    /**
     * @inheritDoc
     */
    public function getIterator(): Traversable
    {
        return new ArrayIterator($this->all());
    }

    /**
     * @inheritDoc
     */
    public function offsetExists(mixed $offset): bool
    {
        return $this->offsetGet($offset) !== null;
    }

    /**
     * @inheritDoc
     */
    public function offsetGet(mixed $offset): mixed
    {
        if(is_int($offset) && $offset < $this->count()) {
            return $this->map[$this->lastKey()][$this->uuids[$offset]];
        }

        if(in_array($offset, $this->keys)) {
            return array_values($this->map[$offset]);
        }

        if(in_array($offset, $this->uuids)) {
            return $this->allOfUuid($offset);
        }

        return $this->find($offset);
    }

    /**
     * @inheritDoc
     * @throws CannotDirectlyModifyMapData
     */
    public function offsetSet(mixed $offset, mixed $value): void
    {
        throw new CannotDirectlyModifyMapData();
    }

    /**
     * @inheritDoc
     * @throws CannotDirectlyModifyMapData
     */
    public function offsetUnset(mixed $offset): void
    {
        throw new CannotDirectlyModifyMapData();
    }

    /**
     * @inheritDoc
     */
    public function count(): int
    {
        return count($this->uuids);
    }

    /**
     * @inheritDoc
     */
    public function all(): array
    {
        return array_values($this->map[$this->lastKey()]);
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
    public function last(): mixed
    {
        return $this->nth($this->count());
    }

    /**
     * @inheritDoc
     */
    public function nth(int $nth): mixed
    {
        $nth--;
        if($nth < $this->count()) {
            return $this->map[$this->lastKey()][$this->uuids[$nth]];
        }
        return null;
    }
}
