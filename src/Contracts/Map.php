<?php

namespace Envorra\Maps\Contracts;

use Envorra\Castables\Arrayable;
use Envorra\Castables\Jsonable;
use Envorra\Castables\Stringable;
use Envorra\Maps\Exceptions\MapItemNotFound;

/**
 * Map
 *
 * @package  Envorra\TypeHandler\Contracts
 *
 * @template T
 */
interface Map extends Arrayable, Jsonable, Stringable
{
    /**
     * @param  mixed  $item
     * @return string|T|null
     */
    public function find(mixed $item): mixed;

    /**
     * @param  mixed  $item
     * @return string|T|null
     * @throws MapItemNotFound
     */
    public function findOrFail(mixed $item): mixed;

    /**
     * @return array
     */
    public function getMap(): array;
}
