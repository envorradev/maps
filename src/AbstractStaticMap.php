<?php

namespace Envorra\Maps;

use Envorra\Maps\Contracts\StaticMap;
use Envorra\Maps\Exceptions\MapItemNotFound;

/**
 * StaticMap
 *
 * @package  Envorra\TypeHandler\Maps
 *
 * @template T
 *
 * @extends SimpleMap<T>
 * @implements StaticMap<T>
 */
abstract class AbstractStaticMap extends SimpleMap implements StaticMap
{
    public function __construct()
    {
        parent::__construct(static::defineMap());
    }

    /**
     * @param  mixed  $item
     * @return T|null
     */
    public static function get(mixed $item): mixed
    {
        return (new static)->find($item);
    }

    /**
     * @param  mixed  $item
     * @return mixed
     * @throws MapItemNotFound
     */
    public static function getOrFail(mixed $item): mixed
    {
        return (new static)->findOrFail($item);
    }

    /**
     * @return array
     */
    abstract protected static function defineMap(): array;
}
