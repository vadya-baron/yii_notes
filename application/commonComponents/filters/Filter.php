<?php

namespace commonComponents\filters;

use commonComponents\traits\Makeable;

abstract class Filter
{
    use Makeable;
    protected ?int $id = null;
    protected ?array $ids = null;
    protected ?string $sort = null;
    protected ?int $limit = null;
    protected ?int $offset = null;

    public function addId(int $id): ?self
    {
        $this->id = $id;
        return $this;
    }

    public function addIds(array $ids): ?self
    {
        $this->ids = $ids;
        return $this;
    }

    public function addSort(string $sort): ?self
    {
        $this->sort = $sort;
        return $this;
    }

    public function addLimit(int $limit): ?self
    {
        $this->limit = $limit;
        return $this;
    }

    public function addOffset(int $offset): ?self
    {
        $this->offset = $offset;
        return $this;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getIds(): ?array
    {
        return $this->ids;
    }

    public function getSort(): ?string
    {
        return $this->sort;
    }

    public function getLimit(): ?int
    {
        return $this->limit;
    }

    public function getOffset(): ?int
    {
        return $this->offset;
    }
}