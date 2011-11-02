<?php

namespace Liip\RasterizeBundle\Helper;

class Rasterizer
{
    /**
     * @var Dreamcraft\WebScreenshotBundle\Helper\PhantomJs
     */
    protected $phantomjs;

    /**
     * Path to the rasterize.js script
     * @var string
     */
    protected $rasterize_script;

    /**
     * Path to the directory to store temporary files
     * @var Dreamcraft\WebScreenshotBundle\Helper\Cache
     */
    protected $cache;

    /**
     * Prefix to prepend to temporary file names
     * @var string
     */
    protected $file_prefix;

    /**
     * Time to live in seconds for temporary files
     * @var int
     */
    protected $ttl;

    /**
     * Viewport width
     * @var int
     */
    protected $width;

    /**
     * Viewport height
     * @var int
     */
    protected $height;

    /**
     * @throws \InvalidArgumentException
     * @param $phantomjs An instance of the PhantomJS helper
     * @param $rasterize_script The path of the rsaterize script to use
     * @param $cache
     * @param string $file_prefix The prefix to prepend to temporary files
     * @param int $ttl The time to live in seconds for temporary files
     * @param int $width The width of the viewport
     * @param int $height The height of the viewport
     */
    public function __construct($phantomjs, $rasterize_script, $cache, $file_prefix = 'raster_', $ttl = 300, $width = 1024, $height = 768)
    {
        $this->cache = $cache;
        $this->phantomjs = $phantomjs;
        $this->rasterize_script = $rasterize_script;
        $this->file_prefix = $file_prefix;
        $this->ttl = $ttl;
        $this->width = $width;
        $this->height = $height;
    }

    /**
     * Render a screenshot of the given URL in PNG format and returns the content of the image
     * @param $url The URL to render
     * @param int $width Width of the output image
     * @param int $height Height of the output image
     * @param bool $force Set to true to force rendering even if a valid matching file is found in the cache
     * @return string The file content of the rendered PNG
     */
    public function rasterize($url, $width = 1024, $height = 768, $force = false)
    {
        // TODO: handle redirects in rasterize script

        $size = array('width' => $width, 'height' => $height);
        $original_filename = $this->cache->getPathFor($url);
        $resized_filename = $this->cache->getPathFor($url, $size);

        // Create the screenshot of the URL in the original size
        if ($force || !$this->cache->hasValidFileFor($url)) {

            $this->phantomjs->exec($this->rasterize_script, "$url $original_filename");

            // Check if the output file now exists, if it doesn't, then something went wrong with the rasterize script
            // Maybe the URL does not exist or it is empty
            if (!file_exists($original_filename)) {
                throw new \Exception("PhantomJs could not create a screenshot of '$url'");
            }
        }

        // Create a resized version of the screenshot
        if ($force || !$this->cache->hasValidFileFor($url, $size)) {
            $this->resize($original_filename, $resized_filename, $width, $height);
        }
        
        return file_get_contents($resized_filename);
    }

    protected function resize($in, $out, $width, $height)
    {
        $image = imagecreatefrompng($in);
        $new_image = imagecreatetruecolor($width, $height);
        imagecopyresampled($new_image, $image, 0, 0, 0, 0, $width, $height, imagesx($image), imagesy($image));
        imagepng($new_image, $out);
        imagedestroy($image);
        imagedestroy($new_image);
    }
}
