<?php

namespace Liip\RasterizeBundle\Imagine;

use Liip\ImagineBundle\Imagine\Data\Loader\LoaderInterface,
    Imagine\Image\ImagineInterface,
    Liip\RasterizeBundle\Helper\Cache;

class RasterizeDataLoader implements LoaderInterface
{
    protected $imagine;

    protected $cache;

    public function __construct(ImagineInterface $imagine, Cache $cache)
    {
        $this->imagine = $imagine;
        $this->cache = $cache;
    }

    public function find($url)
    {
        $path = $this->cache->getPathFor($url);
        return $this->imagine->open($path);
    }
}