<?php

namespace Liip\RasterizeBundle\Controller;

use Symfony\Component\HttpFoundation\Request,
    Symfony\Component\HttpFoundation\Response,
    Symfony\Component\Routing\RouterInterface,
    Symfony\Bundle\FrameworkBundle\Templating\EngineInterface,
    Liip\RasterizeBundle\Helper\Rasterizer;


class DefaultController
{
    protected $router;

    protected $rasterizer;

    protected $templating;

    public function __construct(RouterInterface $router, EngineInterface $templating, Rasterizer $rasterizer)
    {
        $this->router = $router;
        $this->rasterizer = $rasterizer;
        $this->templating = $templating;
    }

    public function indexAction($_route, Request $request, $width, $height)
    {
        $url = $request->get('url');

        // Prevent re-entrant calling or apache will crash!
        $selfurl = addcslashes($this->router->generate($_route), ':/?&=.');
        if (preg_match("/$selfurl/", $url)) {
            throw new \InvalidArgumentException("Re-entrant call to rasterize script: $url.");
        }

        $content = $this->rasterizer->rasterize($url, $width, $height);
        return new Response($content, 200, array('Content-Type' => 'image/png'));
    }

    public function demoAction()
    {
        return $this->templating->renderResponse('LiipRasterizeBundle:Default:index.html.twig');
    }
}
