<?php

declare(strict_types = 1);

namespace Vlsoprun\VkMarket;

class Market
{
    protected $group_id;
    protected $builder;

    public function __construct(Builder $builder)
    {
        $this->builder = $builder;
    }

    public function setGroup(int $group_id)
    {
        $this->group_id = $group_id;
    }

    public function get(int $count = 100, int $offset = 0, int $album_id = 0)
    {
        $response = $this->builder->getMethod('market.get', [
            'owner_id' => '-' . $this->group_id,
            'count'    => $count,
            'offset'   => $offset,
            'album_id' => $album_id,
        ]);

        unset($response['response'][0]);

        return $response['response'];
    }

    public function getCategories(int $count = 100, int $offset = 0)
    {
        $response = $this->builder->getMethod('market.getCategories', [
            'count'  => $count,
            'offset' => $offset,
        ]);

        unset($response['response'][0]);

        return $response['response'];
    }
}
