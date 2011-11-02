<?php

namespace Liip\RasterizeBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller,
    Symfony\Component\HttpFoundation\Response,
    Liip\RasterizeBundle\Helper\PhantomJs;


class DefaultController extends Controller
{
    public function indexAction($_route, $width, $height)
    {
        $url = $this->container->get('request')->get('url');

        // Prevent re-entrant calling or apache will crash!
        $selfurl = addcslashes($this->generateUrl($_route), ':/?&=.');
        if (preg_match("/$selfurl/", $url)) {
            throw new \InvalidArgumentException("Re-entrant call to rasterize script: $url.");
        }

        $rasterizer = $this->container->get('liip_rasterize.rasterizer');
        $content = $rasterizer->rasterize($url, $width, $height);
        return new Response($content, 200, array('Content-Type' => 'image/png'));
    }

    public function demoAction()
    {
        return $this->render('LiipRasterizeBundle:Default:index.html.twig');
    }
}
