<?php

namespace Liip\RasterizeBundle\Helper;

/**
 * PhantomJS wrapper class
 */
class PhantomJs
{
    /**
     * The path of the PhantomJS binary
     * @var string
     */
    protected $phantomjs_bin;

    /**
     * The display number of XVFB
     * @var int
     */
    protected $xvfb_display;

    public function __construct($phantomjs_binary, $xvfb_display = 0)
    {
        if (!file_exists($phantomjs_binary)) {
            throw new \InvalidArgumentException("PhantomJS binary not found in '$phantomjs_binary'.");
        }

        $this->phantomjs_bin = $phantomjs_binary;
        $this->xvfb_display = $xvfb_display;
    }

    /**
     * Execute a PhantomJS script
     * @param $script The path to the script
     * @param $args The arguments to pass to the script
     * @return string The output of the execution
     */
    public function exec($script, $args)
    {
        if (!file_exists($script)) {
            throw new \InvalidArgumentException("PhantomJS script '$script' not found.");
        }

        $command = "DISPLAY=:{$this->xvfb_display} {$this->phantomjs_bin} $script $args 2>&1";
        $output = shell_exec($command);

        if (!is_null($output)) {
            // Ignore warnings
            if (!preg_match('/[WARNING]/', $output)) {
                throw new \Exception("An error occurred while running the PhantomJS script '$script': $output");
            }
        }

        return $output;
    }
}
