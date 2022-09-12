<?php

namespace Envorra\Maps\Exceptions;

use Exception;

/**
 * MapItemNotFound
 *
 * @package Envorra\Maps\Exceptions
 */
class MapItemNotFound extends Exception
{
    public function __construct()
    {
        parent::__construct('Map item not found!');
    }
}
