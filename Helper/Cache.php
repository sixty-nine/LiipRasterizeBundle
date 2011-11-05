<?php

namespace Liip\RasterizeBundle\Helper;

class Cache
{
    /**
     * Path to the directory to store temporary files
     * @var string
     */
    protected $cache_path;

    /**
     * Time to live in seconds for temporary files (0 = Forever)
     * @var int
     */
    protected $ttl;

    public function __construct($cache_path, $ttl = 300)
    {
        $this->cache_path = $cache_path;
        $this->ttl = $ttl;

        $this->checkAndCreateCacheDir($cache_path);
    }

    public function getPathFor($url, $size = array(), $file_extension = 'png')
    {
        if (!array_key_exists('width', $size) || !array_key_exists('height', $size)) {

            return
                $this->cache_path .
                $this->getCacheDirForOriginals() .
                sha1($url) .
                '.' . $file_extension;

        } else {

            return
                $this->cache_path .
                $this->getCacheDirForResized() .
                sha1($url) .
                '.' . $size['width'] .
                '.' . $size['height'] .
                '.' . $file_extension;

        }
    }

    public function hasValidFileFor($url, $size = array(), $file_extension = 'png')
    {
        $filename = $this->getPathFor($url, $size, $file_extension);
        return
            file_exists($filename)
            && (
                $this->ttl === 0 || time() - filectime($filename) <= $this->ttl
            );
    }

    public function getCacheDir()
    {
        return $this->cache_path . '/liip_rasterize';
    }

    public function getCacheDirForOriginals()
    {
        return $this->getCacheDir() . '/original';
    }

    public function getCacheDirForResized()
    {
        return $this->getCacheDir() . '/resized';
    }

    protected function checkAndCreateCacheDir()
    {
        if (!is_dir($this->cache_path) || !is_writable($this->cache_path)) {
            throw new \InvalidArgumentException("Invalid cache path '{$this->cache_path}'");
        }

        $paths = array(
            $this->getCacheDir(),
            $this->getCacheDirForOriginals(),
            $this->getCacheDirForResized(),
        );

        foreach($paths as $path) {
            if (!is_dir($path)) {
                mkdir($path);
            }
        }
    }

}