<?php

declare(strict_types = 1);

namespace Vlsoprun\VkMarket;

use Vlsoprun\VkMarket\Exceptions\BuilderException;

class Product
{
    protected $name;
    protected $description;
    protected $category_id;
    protected $price;
    protected $main_photo_id;
    protected $photo_ids;

    public function __construct(Market $market)
    {
        $this->market = $market;
    }

    public function setName(string $name)
    {
        $this->name = $name;

        return $this;
    }

    public function setDescription(string $description)
    {
        $this->description = $description;

        return $this;
    }

    public function setCategoryId(int $category_id)
    {
        $this->category_id = $category_id;

        return $this;
    }

    public function setPrice(string $price)
    {
        $this->price = $price;

        return $this;
    }

    public function setPhotoMain(string $file)
    {
        $this->main_photo_id = $this->market->uploadFile($file, true)['pid'];
    }

    public function setPhotos(array $files)
    {
        $files = array_values($files);

        if (count($files) > 4) {
            throw new BuilderException('The maximum you can load only 4 images');
        }

        $photo_ids = [];

        foreach ($files as $key => $file) {
            $pid = $this->market->uploadFile($file, $this->main_photo_id === null)['pid'];

            if ($this->main_photo_id === null) {
                $this->main_photo_id = $pid;
            }

            $photo_ids[] = $pid;
        }

        $this->photo_ids = implode(',', $photo_ids);
    }

    public function getParams()
    {
        return [
            'name'          => $this->name,
            'description'   => $this->description,
            'category_id'   => $this->category_id,
            'price'         => $this->price,
            'main_photo_id' => $this->main_photo_id,
            'photo_ids'     => $this->photo_ids,
        ];
    }
}
