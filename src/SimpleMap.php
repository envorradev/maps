<?php

namespace Envorra\Maps;

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
    public function __construct(
        protected array $map = [],
    ) {
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
}
