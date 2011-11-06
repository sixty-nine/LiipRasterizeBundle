<?php

namespace Liip\RasterizeBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Templating\EngineInterface;


class DefaultController
{
    protected $templating;

    public function __construct(EngineInterface $templating)
    {
        $this->templating = $templating;
    }

    public function demoAction()
    {
        return $this->templating->renderResponse('LiipRasterizeBundle:Default:index.html.twig');
    }
}
