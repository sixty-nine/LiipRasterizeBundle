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
        if (false === ($host = parse_url($url, PHP_URL_HOST))) {
            throw new \InvalidArgumentException("Invalid URL '$url'");
        }

        // Group the files by host so that it's possible to clear the files of a given host
        $filename = sha1($host) . '_' . sha1($url);

        if (!array_key_exists('width', $size) || !array_key_exists('height', $size)) {

            $path =
                $this->getCacheDirForOriginals() .
                '/' . $filename .
                '.' . $file_extension;

        } else {

            $path =
                $this->getCacheDirForResized() .
                '/' . $filename .
                '_' . $size['width'] .
                '_' . $size['height'] .
                '.' . $file_extension;

        }

        return $path;
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