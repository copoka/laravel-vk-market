<?php

declare(strict_types = 1);

namespace Vlsoprun\VkMarket;

use Vlsoprun\VkMarket\Exceptions\BuilderException;

class Market
{
    protected $group_id;
    protected $builder;

    public function __construct(Builder $builder)
    {
        $this->builder = $builder;

        if (!$builder->isAuth()) {
            throw new BuilderException('You must obtain a access_token');
        }
    }

    public function setGroup(int $group_id)
    {
        $this->group_id = $group_id;

        return $this;
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

    public function add(Product $product)
    {
        $params = array_merge($product->getParams(), [
            'owner_id' => '-' . $this->group_id,
        ]);

        return $this->builder->getMethod('market.add', $params);
    }

    public function uploadFile(string $file, bool $main_photo = false)
    {
        if (!is_file($file)) {
            throw new BuilderException("File {$file} does not exist");
        }

        $uploadServer = $this->builder->getMethod('photos.getMarketUploadServer', [
            'group_id'   => $this->group_id,
            'main_photo' => $main_photo ? 1 : 0,
        ]);

        $request = $this->builder->client->request('POST', $uploadServer['response']['upload_url'], [
            'multipart' => [
                [
                    'name'     => 'file',
                    'contents' => fopen($file, 'rb'),
                ],
            ],
        ]);

        $response = json_decode($request->getBody()->getContents(), true);
        $response['group_id'] = $this->group_id;

        $response = $this->builder->getMethod('photos.saveMarketPhoto', $response);

        return $response['response']['0'];
    }
}
