<?php

namespace Liip\RasterizeBundle\Imagine;

use Liip\ImagineBundle\Imagine\Data\Loader\LoaderInterface,
    Imagine\Image\ImagineInterface,
    Liip\RasterizeBundle\Imagine\CachePathResolver,
    Liip\RasterizeBundle\Helper\Rasterizer;

class RasterizeDataLoader implements LoaderInterface
{
    protected $imagine;

    protected $cachePathResolver;

    protected $rasterizer;

    /**
     * @var string
     */
    protected $rootPath;

    protected $cachePrefix;

    public function __construct(ImagineInterface $imagine, CachePathResolver $cachePathResolver, Rasterizer $rasterizer, $rootPath, $cachePrefix)
    {
        $this->imagine = $imagine;
        $this->cachePathResolver = $cachePathResolver;
        $this->rasterizer = $rasterizer;
        $this->rootPath = realpath($rootPath);
        $this->cachePrefix = $cachePrefix;
    }

    public function find($url)
    {
        $path = $this->cachePathResolver->getPathFor($url);
        $path = $this->rootPath.$this->cachePrefix.'/liip_rasterize/'.ltrim($path, '/');

        $this->rasterizer->rasterize($url, $path);
        return $this->imagine->open($path);
    }
}