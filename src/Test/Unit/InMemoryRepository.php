<?php

namespace ConferenceTools\Attendance\Test\Unit;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\Criteria;
use Phactor\ReadModel\Repository;

class InMemoryRepository implements Repository
{
    private $collection;

    public function __construct()
    {
        $this->collection = new ArrayCollection();
    }

    public function add($element): void
    {
        if (method_exists($element, 'getId')) {
            $this->collection->set($element->getId(), $element);
            return;
        }

        $this->collection->add($element);
    }

    public function remove($element): void
    {
        $this->collection->remove($element);
    }

    public function get($key)
    {
        return $this->collection->get($key);
    }

    public function matching(Criteria $criteria): Collection
    {
        return $this->collection->matching($criteria);
    }

    public function commit(): void
    {
        return;
    }
}
