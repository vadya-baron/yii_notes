<?php

declare(strict_types=1);


namespace components\notes\models;

use commonComponents\traits\ArrayFilter;
use commonComponents\traits\Makeable;

class Tags
{
    use Makeable;
    use ArrayFilter;

    /**
     * @var array<Tag>
     */
    public array $items = [];

    /**
     * @param array<Tag>|null $items
     */
    public function __construct(
        protected ?string $title = null,
        protected ?string $description = null,
        ?array $items = null,
    )
    {
        if ($items) {
            foreach ($items as $item) {
                $this->addItem($item);
            }
        }
    }

    public function addItem(Tag $note): self
    {
        $this->items[$note->getId()] = $note;

        return $this;
    }
    public function addTitle(string $title): self
    {
        $this->title = $title;

        return $this;
    }
    public function addDescription(string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function getCount(): int
    {
        return count($this->items);
    }

    public function remove(Tag $item): self
    {
        $this->items = $this->filter(fn(Tag $current) => $item !== $current);

        return $this;
    }

    public function removeById(int $id): self
    {
        $this->items = $this->filter(fn(Tag $current) => $id !== $current->getId());

        return $this;
    }

    public function getItem(int $id): ?Tag
    {
        return $this->items[$id] ?? null;
    }

    /**
     * @return Tag[]
     */
    public function getItems(): array
    {
        return $this->items;
    }

    public function toArray(): array
    {
        $items = [];
        if ($this->items) {
            foreach ($this->items as $tag) {
                $items[$tag->getId()] = $tag->toArray();
            }
        }

        return [
            'title' => $this->getTitle(),
            'description' => $this->getDescription(),
            'items' => $items,
            'count' => $this->getCount(),
        ];
    }
}