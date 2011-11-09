# LiipRasterizeBundle

## Introduction

**THIS BUNDLE IS STILL UNDER DEVELOPMENT !**

This bundle provides a custom data loader for the LiipImagineBundle that allows to generate
screenshots of web pages on the fly and pass the images to Imagine filters.

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

**This bundle depends on the LiipImagineBundle.**

Please refer to the [install instructions](https://github.com/liip/LiipImagineBundle/blob/master/README.md) of LiipImagineBundle to install it.

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

(This step is optional, if you don't need the demo route, skip this)

Finally, add the following to your routing file:

``` yaml
# app/config/routing.yml

LiipRasterizeBundle:
    resource: "@LiipRasterizeBundle/Resources/config/routing.xml"
    prefix:   /liip-rasterize
```

### Step 5: Create the imagine filter

Create an imagine filter:

``` yaml
# app/config.yml
liip_imagine:
    filter_sets:
        liip_rasterize:
            cache: liip_raterize.cache
            data_loader: liip_rasterize.loader
            format: png
            filters:
                thumbnail: { size: [120, 90], mode: outbound }
                # You can add more filters here...
```
## Basic usage

### Demo

If you enabled the demo route in the step 4 above, you can point a web browser to:

    http://your.application.url/liip-rasterize

### Insert a screenshot in a Twig template

```
{{ 'http://php.net' | imagine_filter('liip_rasterize') }}
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
```

The options are:

 - `phantomjs.binary` - Full path to the PhantomJS binary to use.

 - `phantomjs.rasterize_script` - Full path to the PhantomJS rasterize script. Override if
    you want to use your own.

 - `xvfb.display` - The display number to use with XVFB.

 - `rasterize_viewport.width`, `rasterize_viewport.height` - Dimensions of the viewport to
    use when the screenshots of the webpages are rendered.

### TODO

 - Add viewport options for the twig rasterize function
 - Allow to change the format of images to jpeg since PhantomJS support it
 - Investigate the rendering of Flash
 - Probably nothing works on Mac and Windows without some adaptations to the code
