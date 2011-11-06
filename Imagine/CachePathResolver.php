<?php

namespace Liip\RasterizeBundle\Imagine;

use Liip\ImagineBundle\Imagine\CachePathResolver as BaseCachePathResolver,
    Symfony\Component\HttpFoundation\Request;

class CachePathResolver extends BaseCachePathResolver
{
    protected $filePrefix = 'liip_rasterize';

    public function getPathFor($url, $format = 'png')
    {
        if (false === ($host = parse_url($url, PHP_URL_HOST))) {
            throw new \InvalidArgumentException("Invalid URL '$url'");
        }

        // Group the files by host so that it's possible to clear the files of a given host
        return $this->filePrefix . '.' . sha1($host) . '.' . sha1($url) . '.' . $format;
    }

    public function resolve(Request $request, $targetPath, $filter)
    {
        if ($filter === 'liip_rasterize') {
            $targetPath = $this->getPathFor($targetPath);
        }
        return parent::resolve($request, $targetPath, $filter);
    }

}
