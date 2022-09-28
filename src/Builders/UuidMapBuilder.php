<?php declare(strict_types=1);

namespace Envorra\Maps\Builders;

use Envorra\Maps\UuidMap;
use Envorra\Maps\Contracts\MapBuilder;

/**
 * UuidMapBuilder
 *
 * @package  Envorra\Maps\Builders
 *
 * @template TUuidMap of UuidMap
 *
 * @implements MapBuilder<TUuidMap>
 */
class UuidMapBuilder implements MapBuilder
{
    /**
     * @var array
     */
    protected array $columns = [];

    /** @var UuidMapItem[] */
    protected array $items = [];

    /**
     * @return array
     */
    public function buildMap(): array
    {
        $map = [];

        foreach ($this->items as $item) {
            $counter = 0;

            if (!isset($map[$this->getOrAddColumnFromOffset(0)])) {
                $map[$this->getOrAddColumnFromOffset(0)] = [];
            }

            $map[$this->getOrAddColumnFromOffset(0)][$item->uuid] = $item->item;

            foreach ($item->getChildren() as $child) {
                $counter++;

                if (!isset($map[$this->getOrAddColumnFromOffset($counter)])) {
                    $map[$this->getOrAddColumnFromOffset($counter)] = [];
                }

                $map[$this->getOrAddColumnFromOffset($counter)][$item->uuid] = $child->item;
            }
        }

        foreach ($this->columns as $column) {
            if (!isset($map[$column])) {
                $map[$column] = [];
            }

            if (count($map[$column]) < count($this->items)) {
                foreach ($this->items as $item) {
                    if (!array_key_exists($item->uuid, $map[$column])) {
                        $map[$column][$item->uuid] = $item->getLastChild()?->item ?? $item->item;
                    }
                }
            }
        }

        return $map;
    }

    /**
     * @param  string  $column
     * @return $this
     */
    public function column(string $column): static
    {
        $this->columns[] = $column;
        return $this;
    }

    /**
     * @param  array  $columns
     * @return $this
     */
    public function columns(array $columns): static
    {
        $this->columns = $columns;
        return $this;
    }

    /**
     * @return array
     */
    public function getColumns(): array
    {
        return $this->columns;
    }

    /**
     * @return array
     */
    public function getItems(): array
    {
        return $this->items;
    }

    /**
     * @return UuidMap
     */
    public function getMap(): UuidMap
    {
        return new UuidMap($this->buildMap());
    }

    /**
     * @param  mixed  $item
     * @return UuidMapItem
     */
    public function item(mixed $item): UuidMapItem
    {
        $item = new UuidMapItem($item);
        $this->items[] = $item;
        return $item;
    }

    /**
     * @param  int  $offset
     * @return string|int
     */
    protected function getOrAddColumnFromOffset(int $offset): string|int
    {
        if (isset($this->columns[$offset])) {
            return $this->columns[$offset];
        }

        $this->columns[$offset] = $offset;

        return $offset;
    }
}
