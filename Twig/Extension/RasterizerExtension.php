<?php

namespace Liip\RasterizeBundle\Twig\Extension;

use Symfony\Bundle\FrameworkBundle\Routing\Router,
    Liip\RasterizeBundle\Helper\Rasterizer;

class RasterizerExtension extends \Twig_Extension
{
    protected $router;

    public function __construct(Router $router)
    {
        $this->router = $router;
    }

    /**
     * {@inheritdoc}
     */
    public function getFunctions()
    {
        return array(
            'rasterize'  => new \Twig_Function_Method($this, 'rasterize', array('is_safe' => array('html'))),
        );
    }

    public function rasterize($url, $width = null, $height = null)
    {
        if (is_null($width) || is_null($height)) {

            $imgurl = $this->router->generate(
                'LiipRasterizeBundle_rasterize',
                array('url' => $url)
            );

        } else {

            $imgurl = $this->router->generate(
                'LiipRasterizeBundle_rasterize',
                array(
                    'url' => $url,
                    'width' => $width,
                    'height' => $height
                )
            );
        }
        
        return "<img src='$imgurl' />";
    }

    /**
     * Returns the name of the extension.
     *
     * @return string The extension name
     */
    public function getName()
    {
        return 'rasterizer';
    }
}
