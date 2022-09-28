<?php declare(strict_types=1);

namespace Envorra\Maps\Helpers;

/**
 * ArrayHelper
 *
 * @package Envorra\Maps\Helpers
 */
class ArrayHelper
{
    /**
     * Flatten a multidimensional associative array with dots.
     *
     * Adapted from Laravel Arr::dot helper:
     * @see https://github.com/laravel/framework/blob/9.x/src/Illuminate/Collections/Arr.php#L110
     *
     * @param  array  $nestedArray
     * @param  string  $prepend
     * @return array
     */
    public static function toDotted(array $nestedArray, string $prepend = ''): array
    {
        $results = [];

        foreach ($nestedArray as $key => $value) {
            if (is_array($value) && ! empty($value)) {
                $results = array_merge($results, static::toDotted($value, $prepend.$key.'.'));
            } else {
                $results[$prepend.$key] = $value;
            }
        }

        return $results;
    }

    /**
     * Convert a flattened "dot" notation array into an expanded array.
     *
     * Adapted from Laravel Arr::undot helper:
     * @see https://github.com/laravel/framework/blob/9.x/src/Illuminate/Collections/Arr.php#L131
     *
     * @param  array  $dottedArray
     * @return array
     */
    public static function toNested(array $dottedArray): array
    {
        $results = [];

        foreach ($dottedArray as $key => $value) {
            static::set($results, $key, $value);
        }

        return $results;
    }

    /**
     * Set an array item to a given value using "dot" notation.
     *
     * If no key is given to the method, the entire array will be replaced.
     *
     * Adapted from Laravel Arr::set helper:
     * @see https://github.com/laravel/framework/blob/9.x/src/Illuminate/Collections/Arr.php#L672
     *
     * @param  array  $array
     * @param  string|int|null  $key
     * @param  mixed  $value
     * @return array
     */
    public static function set(array &$array, string|int|null $key, mixed $value): array
    {
        if (is_null($key)) {
            return $array = $value;
        }

        $keys = explode('.', $key);

        foreach ($keys as $i => $key) {
            if (count($keys) === 1) {
                break;
            }

            unset($keys[$i]);

            // If the key doesn't exist at this depth, we will just create an empty array
            // to hold the next value, allowing us to create the arrays to hold final
            // values at the correct depth. Then we'll keep digging into the array.
            if (! isset($array[$key]) || ! is_array($array[$key])) {
                $array[$key] = [];
            }

            $array = &$array[$key];
        }

        $array[array_shift($keys)] = $value;

        return $array;
    }
}
