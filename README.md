# LiipRasterizeBundle

## Introduction

**THIS BUNDLE IS STILL UNDER DEVELOPMENT !**

This bundle provides an easy way to generate screen shots of web pages or render their content
to a PDF file.

## Dependencies

### PhantomJS

The rendering of web pages relies on PhantomJS.

To use the bundle you will need to obtain or compile a PhantomJS binary fitting your server.

On a Debian compatible system you can do the following to compile your version of PhantomJS:

``` bash
sudo apt-get install libqt4-dev libqtwebkit-dev qt4-qmake
git clone git://github.com/ariya/phantomjs.git && cd phantomjs
git checkout 1.3
qmake-qt4 && make
```

The PhantomJS binary should then be in `phantomjs/bin/phantomjs`. Copy this file to
`LiipRasterizeBundle\Resources\bin`.

If you already have PhantomJS on your server you may set the parameter
`liip_rasterize.phantomjs.binary` to point to the binary instead of copying
it.

### XVFB

On a Unix system PhantomJS is not fully headless and still need an X server to work
properly. You will need to install XVFB, an headless X server, in order to use this
bundle. See http://code.google.com/p/phantomjs/wiki/XvfbSetup for the instructions.

Furthermore you will not be able to run several X servers on the same display, you may
have to adapt the script provided on the above page to use another display. In the line
`Xvfb :0 -screen 0 1024x768x24 &` the `:0` parameter indicates that XVFB will use
display 0. To change the used display modify the parameter.

By default this bundle uses the display number 99. You can change this using the
`liip_rasterize.xvfb.display` parameter.

## Installation

Installation depends on how your project is setup:

### Step 1: Installation

Add the following lines to your ``deps`` file

```
[LiipRasterizeBundle]
    git=http://github.com/liip/LiipRasterizeBundle.git
    target=bundles/Liip/RasterizeBundle
```

Next, update your vendors by running:

``` bash
$ ./bin/vendors install
```

### Step 2: Configure the autoloader

Add the following entries to your autoloader:

``` php
<?php
// app/autoload.php

$loader->registerNamespaces(array(
    // ...

    'Liip'      => __DIR__.'/../vendor/bundles',
));
```

### Step 3: Enable the bundle

Finally, enable the bundle in the kernel:

``` php
<?php
// app/AppKernel.php

public function registerBundles()
{
    $bundles = array(
        // ...

        new Liip\RasterizeBundle\LiipRasterizeBundle(),
    );
}
```

### Step 4: Register the bundle's routes

Finally, add the following to your routing file:

``` yaml
# app/config/routing.yml

DreamcraftWebScreenshotBundle:
    resource: "@LiipRasterizeBundle/Resources/config/routing.xml"
    prefix:   /liip-rasterize
```

### Step 5: Test everything works fine

Point a web browser to

    http://your.application.url/liip-rasterize/rasterize/240/180?url=http%3A%2F%2Fphp.net

You should see a thumbnail of a screenshot of the php.net website. Or else... exceptions,
don't hesitate to report them.

## Basic usage

### Demo

Give this URL a try:

    http://your.application.url/liip-rasterize


### Get a screenshot of a web page

To get a screenshot of a webpage you can use the following URL:

    http://your.application.url/liip-rasterize/rasterize/<width>/<height>?url=<url>

The images take some time to load the first time, then they are cached. See below for more details.


### Insert a screenshot in a Twig template

The Twig function `rasterize(url [ , width , height ] )` allows to insert screenshots directly
in a Twig template.

```
{{ rasterize( "http://php.net", 240, 180 ) }}
```

## Configuration

The default configuration for the bundle looks like this:

``` yaml
liip_rasterize:
    phantomjs.binary: %liip_rasterize.root_dir%/Resources/bin/phantomjs
    phantomjs.rasterize_script: %liip_rasterize.root_dir%/Resources/bin/rasterize.js
    xvfb.display: 99
    rasterize_viewport.width: 1024
    rasterize_viewport.height: 768
    cache.path: %kernel.cache_dir%
    cache.extension: png
    cache.ttl: 300
```

The options are:

 - `phantomjs.binary` - Full path to the PhantomJS binary to use.

 - `phantomjs.rasterize_script` - Full path to the PhantomJS rasterize script. Override if
    you want to use your own.

 - `xvfb.display` - The display number to use with XVFB.

 - `rasterize_viewport.width`, `rasterize_viewport.height` - Dimensions of the viewport to
    use when the screenshots of the webpages are rendered.

 - `cache.path` - The path where to store the temporary files. The directory must be
    writtable by Apache. Usually you will use the application cache dir.

 - `cache.extension` - Extension to use for temporary files. This should go away with
    future releases of the bundle.

 - `cache.ttl`- The time to live of temporary files in seconds. Defaults to 5 minutes.


### How images are cached

Rendering a screenshot of a webpage is time consuming because the page needs to be fetched and
rendered by PhantomJS. On the other hand resizing the images is an easy and fast process.

This is why this bundle uses a two step caching mechanism for the images.

What happens exactly when you request a screenshot is the following:

 - the bundle checks if a full size screenshot exists in the cache

 - if not or it has expired, PhantomJS is used to fetch and render the image

 - the bundle checks if a resized version of the image that fits the request exists in the cache

 - if not or it has expired, the bundle creates a resized version of the full size screenshot

 - the resized version of the image is returned


The cache uses sha1 hashing to match URLs with cached files.


### TODO

 - Add viewport options for the twig rasterize function
 - Add redirection support in the Rasterizer
 - Investigate the rendering of Flash
 - Allow rendering of a full webpage as PDF
 - Probably nothing works on Mac and Windows without some adaptations to the code
