<?php declare(strict_types=1);

namespace Envorra\Maps\Builders;

use Ramsey\Uuid\Uuid;

/**
 * UuidMapItem
 *
 * @package Envorra\Maps\Builders
 */
class UuidMapItem
{
    /**
     * @var bool
     */
    public readonly bool $isParent;
    /**
     * @var mixed
     */
    public readonly mixed $item;
    /**
     * @var string
     */
    public readonly string $uuid;
    /**
     * @var self[]
     */
    protected array $children = [];

    /**
     * @var mixed|null
     */
    protected mixed $lastChild = null;

    /**
     * @param  mixed        $item
     * @param  string|null  $uuid
     */
    public function __construct(mixed $item, ?string $uuid = null)
    {
        $this->isParent = is_null($uuid);
        $this->uuid = $uuid ?? Uuid::uuid4()->toString();
        $this->item = $item instanceof self ? $item->item : $item;
    }

    /**
     * @param  mixed  $item
     * @return $this
     */
    public function child(mixed $item): self
    {
        $child = new self($item, $this->uuid);
        $this->children[] = $child;
        $this->lastChild = $child;
        return $this;
    }

    /**
     * @param  array  $items
     * @return $this
     */
    public function children(array $items): self
    {
        foreach($items as $item) {
            $this->child($item);
        }
        return $this;
    }

    /**
     * @return UuidMapItem[]
     */
    public function getChildren(): array
    {
        return $this->children;
    }

    /**
     * @return mixed
     */
    public function getLastChild(): mixed
    {
        return $this->lastChild;
    }
}
