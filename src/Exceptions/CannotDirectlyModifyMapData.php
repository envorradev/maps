<?php declare(strict_types=1);

namespace Envorra\Maps\Exceptions;

use Exception;

/**
 * CannotDirectlyModifyMapData
 *
 * @package Envorra\Maps\Exceptions
 */
class CannotDirectlyModifyMapData extends Exception
{
    public function __construct()
    {
        parent::__construct('Cannot directly modify map data using array accessors.');
    }
}
