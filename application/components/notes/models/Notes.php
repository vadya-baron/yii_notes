<?php

declare(strict_types=1);


namespace components\notes\models;

use commonComponents\traits\ArrayFilter;
use commonComponents\traits\Makeable;

class Notes
{
    use Makeable;
    use ArrayFilter;

    /**
     * @var array<Note>
     */
    protected array $items = [];

    /**
     * @param array<Note>|null $items
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
    public function addItem(Note $note): self
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

    public function remove(Note $item): self
    {
        $this->items = $this->filter(fn(Note $current) => $item !== $current);

        return $this;
    }

    public function removeById(int $id): self
    {
        $this->items = $this->filter(fn(Note $current) => $id !== $current->getId());

        return $this;
    }

    public function getItem(int $id): ?Note
    {
        return $this->items[$id] ?? null;
    }

    /**
     * @return Note[]
     */
    public function getItems(): array
    {
        return $this->items;
    }

    public function toArray(): array
    {
        $items = [];
        if ($this->items) {
            foreach ($this->items as $note) {
                $items[$note->getId()] = $note->toArray();
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