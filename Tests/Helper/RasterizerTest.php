<?php

namespace Liip\RasterizeBundle\Tests\Helper;

use Liip\FunctionalTestBundle\Test\WebTestCase;

class RasterizerTest extends WebTestCase
{
    protected $cache;

    protected $raster;

    public function setUp()
    {
        $this->raster = $this->getContainer()->get('liip_rasterize.rasterizer');
        $this->cache = $this->getContainer()->get('liip_rasterize.cache');
    }

    public function testRasterize()
    {
        $url = 'http://google.ch';
        $output = $this->raster->rasterize($url);
        $origfile = $this->cache->getPathFor($url);
        $resizedfile = $this->cache->getPathFor($url, array('width' => 1024, 'height' => 768));

        $this->assertNotEmpty($output);
        $this->assertFileExists($origfile);
        $this->assertFileExists($resizedfile);
        $this->assertEquals(file_get_contents($resizedfile), $output);
    }
}
