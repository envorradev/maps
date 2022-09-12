<?php

namespace Envorra\Maps;

use Envorra\Maps\Exceptions\MapItemNotFound;

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

    /**
     * @param  array  $map
     */
    public function __construct(array $map = [])
    {
        parent::__construct($map);
        $this->map = array_change_key_case($this->map);
        $this->keys = array_keys($this->map);
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
}
