<?php

namespace Liip\RasterizeBundle\Helper;

use Liip\RasterizeBundle\Helper\Cache,
    Liip\RasterizeBundle\Helper\PhantomJs;

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
     * @var string
     */
    protected $filetype;

    /**
     * @throws \InvalidArgumentException
     * @param \Liip\RasterizeBundle\Helper\PhantomJs $phantomjs instance of the PhantomJS helper
     * @param \Liip\RasterizeBundle\Helper\Cache $cache
     * @param $rasterize_script The path of the rsaterize script to use
     * @param int $viewport_width The width of the viewport
     * @param int $viewport_height The height of the viewport
     * @param string $output_file_type The type of the output file (png|jpeg)
     * @return \Liip\RasterizeBundle\Helper\Rasterizer
     */
    public function __construct
    (
        PhantomJs $phantomjs,
        Cache $cache,
        $rasterize_script,
        $viewport_width = 1024,
        $viewport_height = 768,
        $output_file_type = 'png'
    )
    {
        $this->cache = $cache;
        $this->phantomjs = $phantomjs;
        $this->rasterize_script = $rasterize_script;
        $this->width = $viewport_width;
        $this->height = $viewport_height;

        if (!in_array($output_file_type, array('png', 'jpeg'))) {
            throw new \InvalidArgumentException("Invalid output file type. It must be either png or jpeg, got '$output_file_type'");
        }
        $this->filetype = $output_file_type;
    }

    /**
     * Render a screenshot of the given URL in PNG format and returns the content of the image
     * @param $url The URL to render
     * @param int $width Width of the output image
     * @param int $height Height of the output image
     * @param bool $force Set to true to force rendering even if a valid matching file is found in the cache
     * @param string|null $output_file_type Set to null to use the default, or use either png or jpeg.
     * @return string The file content of the rendered PNG
     */
    public function rasterize($url, $width = 1024, $height = 768, $force = false, $output_file_type = null)
    {
        // TODO: handle redirects in rasterize script

        if(!is_null($output_file_type) && in_array($output_file_type, array('png', 'jpeg'))) {
            $filetype = $output_file_type;
        } else {
            $filetype = $this->filetype;
        }

        $size = array('width' => $width, 'height' => $height);
        $original_filename = $this->cache->getPathFor($url, array(), $filetype);
        $resized_filename = $this->cache->getPathFor($url, $size, $filetype);

        // Create the screenshot of the URL in the original size
        if ($force || !$this->cache->hasValidFileFor($url, array(), $filetype)) {

            $out = $this->phantomjs->exec($this->rasterize_script, "$url $original_filename");

            // Check if the output file now exists, if it doesn't, then something went wrong with the rasterize script
            // Maybe the URL does not exist or it is empty
            if (!file_exists($original_filename)) {
                throw new \Exception("PhantomJs could not create a screenshot of '$url': " . print_r($out, true));
            }
        }

        // Create a resized version of the screenshot
        if ($force || !$this->cache->hasValidFileFor($url, $size, $filetype)) {
            $this->resize($original_filename, $resized_filename, $width, $height);
        }
        
        return file_get_contents($resized_filename);
    }

    protected function resize($in, $out, $width, $height)
    {
        var_dump(dirname($out));
        assert(file_exists($in));
        assert(is_writable(dirname($out)));
        $image = imagecreatefrompng($in);
        $new_image = imagecreatetruecolor($width, $height);
        imagecopyresampled($new_image, $image, 0, 0, 0, 0, $width, $height, imagesx($image), imagesy($image));
        imagepng($new_image, $out);
        imagedestroy($image);
        imagedestroy($new_image);
    }
}
