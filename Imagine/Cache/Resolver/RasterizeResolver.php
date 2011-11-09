<?php

namespace Liip\RasterizeBundle\Imagine\Cache\Resolver;

use Liip\ImagineBundle\Imagine\Cache\Resolver\WebPathResolver as BaseResolver;
use Symfony\Component\HttpFoundation\Request;

class RasterizeResolver extends BaseResolver
{
    protected $filePrefix = 'liip_rasterize';

    public function getFilenameFor($url, $format = 'png')
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
            $targetPath = $this->getFilenameFor($targetPath);
        }
        return parent::resolve($request, $targetPath, $filter);
    }
}
