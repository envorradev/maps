<?php

namespace Envorra\Maps\Factories;

use Ramsey\Uuid\Uuid;
use Envorra\Maps\UuidMap;
use Envorra\Maps\Helpers\ArrayHelper;
use Envorra\Maps\Exceptions\UuidMapFactoryException;

/**
 * UuidMapFactory
 *
 * @package Envorra\Maps\Factories
 */
class UuidMapFactory
{
    /**
     * @param  array  $dottedArray
     * @param  array  $columns
     * @return UuidMap
     * @throws UuidMapFactoryException
     */
    public static function createFromDottedArray(array $dottedArray, array $columns = []): UuidMap
    {
        $columnCount = $columns ? count($columns) : count(explode('.', array_key_first($dottedArray))) + 1;
        $map = static::emptyMap($columns, $columnCount);

        foreach ($dottedArray as $dotted => $value) {
            $columns = explode('.', $dotted);
            $columns[] = $value;

            if (count($columns) !== $columnCount) {
                throw new UuidMapFactoryException('Columns are not consistent');
            }

            $uuid = Uuid::uuid4()->toString();
            $columns = array_combine(array_keys($map), $columns);

            foreach ($columns as $columnName => $columnValue) {
                $map[$columnName][$uuid] = $columnValue;
            }
        }

        return new UuidMap($map);
    }

    /**
     * @param  array  $nestedArray
     * @param  array  $columnNames
     * @return UuidMap
     * @throws UuidMapFactoryException
     */
    public static function createFromNestedArray(array $nestedArray, array $columnNames = []): UuidMap
    {
        return static::createFromDottedArray(ArrayHelper::toDotted($nestedArray), $columnNames);
    }

    /**
     * @param  array     $items
     * @param  array     $columns
     * @param  callable  $callable
     * @return UuidMap
     */
    public static function createUsing(array $items, array $columns, callable $callable): UuidMap
    {
        $map = static::emptyMap($columns);

        foreach ($items as $key => $item) {
            $uuid = Uuid::uuid4()->toString();
            foreach ($columns as $column) {
                $map[$column][$uuid] = $callable($item, $key, $column, $uuid);
            }
        }

        return new UuidMap($map);
    }

    /**
     * @param  array     $names
     * @param  int|null  $count
     * @return array
     */
    protected static function emptyMap(array $names, ?int $count = null): array
    {
        $count ??= count($names);

        $map = array_fill(0, $count, []);

        if (count($names) === $count) {
            return array_combine($names, $map);
        }

        return $map;
    }
}
