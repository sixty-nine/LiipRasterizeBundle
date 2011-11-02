var page = new WebPage(),
    address, output, size;

if (phantom.args.length < 2) {
    console.log('Usage: rasterize.js URL filename [ width height ]');
    phantom.exit();
} else {
    address = phantom.args[0];
    output = phantom.args[1];
    w = phantom.args[2] || 1024;
    h = phantom.args[3] || 768;
    page.viewportSize = { width: w, height: h };
    page.open(address, function (status) {
        if (status !== 'success') {
            console.log('Unable to load the address!');
        } else {
            window.setTimeout(function () {
		page.clipRect = { top: 0, left: 0, width: w, height: h };
                page.render(output);
                phantom.exit();
            }, 200);
        }
    });
}

