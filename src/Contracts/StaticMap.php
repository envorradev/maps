<?php

namespace Envorra\Maps\Contracts;

/**
 * StaticMap
 *
 * @package  Envorra\Maps\Contracts
 *
 * @template T
 *
 * @extends Map<T>
 */
interface StaticMap extends Map
{
    public function __construct();
}
