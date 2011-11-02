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


