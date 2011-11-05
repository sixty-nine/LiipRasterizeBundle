<?php

namespace Liip\RasterizeBundle\Tests\Helper;

use Liip\FunctionalTestBundle\Test\WebTestCase;

class CacheTest extends WebTestCase
{
    protected $cache;

    public function setUp()
    {
        $this->cache = $this->getContainer()->get('liip_rasterize.cache');
    }

    public function testCacheSetup()
    {
        $this->assertTrue(is_dir($this->cache->getCacheDir()));
        $this->assertTrue(is_dir($this->cache->getCacheDirForOriginals()));
        $this->assertTrue(is_dir($this->cache->getCacheDirForResized()));
    }

    public function testGetPathFor()
    {
        $url1 = 'http://test.com/script1';
        $url2 = 'http://test.com/script2';

        $this->assertEquals($this->cache->getPathFor($url1), $this->cache->getPathFor($url1));
        $this->assertNotEquals($this->cache->getPathFor($url1), $this->cache->getPathFor($url2));
    }

    public function testGetPathForGroupsFilesByHost()
    {
        $out1 = $this->cache->getPathFor('http://google.ch?q=this+is+a+test');
        $out2 = $this->cache->getPathFor('http://google.ch?q=this+is+another+test');
        $a1 = explode('_', $out1);
        $a2 = explode('_', $out2);

        $this->assertNotEquals($out1, $out2);
        $this->assertTrue(is_array($a1) && is_array($a2));
        $this->assertTrue(count($a1) && count($a2));
        $this->assertEquals($a1[0], $a2[0]);
    }
}
