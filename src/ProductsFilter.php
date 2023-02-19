<?php

namespace Equip;

use DateTimeImmutable;

class ProductsFilter
{
    private int $catalogTypeId = Client::CATALOG_TYPE_ANY;
    private ?DateTimeImmutable $changeFrom = null;
    private ?DateTimeImmutable $changeTo = null;
    private int $perPage = 100;
    private int $page = 1;
    private string $sortBy = 'id';
    private string $sortDir = 'asc';
    private bool $active = true;
    private string $brand = '';
    private bool $withRRC = true;
    private bool $withCategory = true;
    private string $categoryId = '';
    private string $inn = '';
    private string $lang = '';

    public function __construct(string $lang = Client::LANG_RU)
    {
        $this->lang = $lang;
    }

    public function getLang(): string
    {
        return $this->lang;
    }

    public function setCatalogTypeId(int $catalogTypeId): ProductsFilter
    {
        $this->catalogTypeId = $catalogTypeId;
        return $this;
    }

    public function getCatalogTypeId(): int
    {
        return $this->catalogTypeId;
    }

    public function setChangeFrom(?DateTimeImmutable $changeFrom): ProductsFilter
    {
        $this->changeFrom = $changeFrom;
        return $this;
    }

    public function getChangeFrom(): ?DateTimeImmutable
    {
        return $this->changeFrom;
    }

    public function setChangeTo(?DateTimeImmutable $changeTo): ProductsFilter
    {
        $this->changeTo = $changeTo;
        return $this;
    }

    public function getChangeTo(): ?DateTimeImmutable
    {
        return $this->changeTo;
    }

    public function setPerPage(int $perPage): ProductsFilter
    {
        $this->perPage = $perPage;
        return $this;
    }

    public function getPerPage(): int
    {
        return $this->perPage;
    }

    public function setPage(int $page): ProductsFilter
    {
        $this->page = $page;
        return $this;
    }

    public function getPage(): int
    {
        return $this->page;
    }

    public function setActive(bool $active): ProductsFilter
    {
        $this->active = $active;
        return $this;
    }

    public function isActive(): bool
    {
        return $this->active;
    }

    public function setBrand(string $brand): ProductsFilter
    {
        $this->brand = $brand;
        return $this;
    }

    public function getBrand(): string
    {
        return $this->brand;
    }

    public function setWithCategory(bool $withCategory): ProductsFilter
    {
        $this->withCategory = $withCategory;
        return $this;
    }

    public function isWithCategory(): bool
    {
        return $this->withCategory;
    }

    public function setWithRRC(bool $withRRC): ProductsFilter
    {
        $this->withRRC = $withRRC;
        return $this;
    }

    public function isWithRRC(): bool
    {
        return $this->withRRC;
    }

    public function setCategoryId(string $categoryId): ProductsFilter
    {
        $this->categoryId = $categoryId;
        return $this;
    }

    public function getCategoryId(): string
    {
        return $this->categoryId;
    }

    public function setInn(string $inn): ProductsFilter
    {
        $this->inn = $inn;
        return $this;
    }

    function getInn(): string
    {
        return $this->inn;
    }

    public function setSortId(): ProductsFilter
    {
        $this->sortBy = 'id';
        return $this;
    }

    public function setSortDateUpdate(): ProductsFilter
    {
        $this->sortBy = 'date_update';
        return $this;
    }

    public function getSortBy(): string
    {
        return $this->sortBy;
    }

    public function setSortASC(): ProductsFilter
    {
        $this->sortBy = 'asc';
        return $this;
    }

    public function setSortDESC(): ProductsFilter
    {
        $this->sortBy = 'desc';
        return $this;
    }

    public function getSortDir(): string
    {
        return $this->sortDir;
    }

    public function toParams(): array
    {
        return [
            'catalog_type_id' => $this->getCatalogTypeId() >= 0 ? $this->getCatalogTypeId() : '',
            'change_from' => $this->changeFrom ? $this->changeFrom->format('Y-m-dTH:i:s') : '',
            'change_to' => $this->changeTo ? $this->changeTo->format('Y-m-dTH:i:s') : '',
            'per_page' => $this->getPerPage(),
            'page' => $this->getPage(),
            'sort' => $this->getSortBy(),
            'direction' => $this->getSortDir(),
            'active' => (int)$this->isActive(),
            'brand' => $this->getBrand(),
            'with_rrc' => $this->isWithRRC() ? 'true' : 'false',
            'with_category' => $this->isWithCategory() ? 'true' : 'false',
            'category_id' => $this->getCategoryId(),
            'inn' => $this->getInn(),
            'lang' => $this->getLang(),
        ];
    }
}