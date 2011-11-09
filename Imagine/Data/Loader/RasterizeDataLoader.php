<?php

namespace Liip\RasterizeBundle\Imagine\Data\Loader;

use Imagine\Image\ImagineInterface,
    Liip\ImagineBundle\Imagine\Data\Loader\LoaderInterface,
    Liip\RasterizeBundle\Helper\Rasterizer;

class RasterizeDataLoader implements LoaderInterface
{
    protected $imagine;

    protected $cachePath;

    protected $rasterizer;

    public function __construct(ImagineInterface $imagine, Rasterizer $rasterizer, $cachePath)
    {
        $this->imagine = $imagine;
        $this->rasterizer = $rasterizer;
        $this->cachePath = $cachePath;
    }

    public function find($url)
    {
        $output = $this->cachePath . '/' . uniqid('liip_rasterize_') . '.png';
        $this->rasterizer->rasterize($url, $output);
        $image = $this->imagine->open($output);
        unlink($output);
        return $image;
    }
}