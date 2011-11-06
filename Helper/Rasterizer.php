<?php

namespace Liip\RasterizeBundle\Helper;

use Liip\RasterizeBundle\Helper\PhantomJs;

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
    protected $format;

    /**
     * @throws \InvalidArgumentException
     * @param \Liip\RasterizeBundle\Helper\PhantomJs $phantomjs instance of the PhantomJS helper
     * @param \Liip\RasterizeBundle\Helper\Cache $cache
     * @param $rasterize_script The path of the rsaterize script to use
     * @param int $viewport_width The width of the viewport
     * @param int $viewport_height The height of the viewport
     * @param string $format The type of the output file (png|jpeg)
     * @return \Liip\RasterizeBundle\Helper\Rasterizer
     */
    public function __construct
    (
        PhantomJs $phantomjs,
        $rasterize_script,
        $viewport_width = 1024,
        $viewport_height = 768,
        $format = 'png'
    )
    {
        $this->phantomjs = $phantomjs;
        $this->rasterize_script = $rasterize_script;
        $this->width = $viewport_width;
        $this->height = $viewport_height;

        // TODO: the format should be read from the imagine filter_set config
        if (!in_array($format, array('png', 'jpeg'))) {
            throw new \InvalidArgumentException("Invalid format. It must be either png or jpeg, got '$format'");
        }
        $this->format = $format;
    }

    /**
     * Render a screenshot of the given URL in PNG format and returns the content of the image
     * @param $url The URL to render
     * @return string The file content of the rendered image
     */
    public function rasterize($url, $outputFile)
    {
        // TODO: handle redirects in rasterize script

        $out = $this->phantomjs->exec($this->rasterize_script, "$url $outputFile");

        // Check if the output file now exists, if it doesn't, then something went wrong with the rasterize script
        // Maybe the URL does not exist or it is empty
        if (!file_exists($outputFile)) {
            throw new \Exception("PhantomJs could not create a screenshot of '$url': " . print_r($out, true));
        }
    }
}
