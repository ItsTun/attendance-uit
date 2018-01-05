/*jslint white: true, browser: true, undef: true, nomen: true, eqeqeq: true, plusplus: false, bitwise: true, regexp: true, strict: true, newcap: true, immed: true, maxerr: 14 */
/*global window: false, REDIPS: true */

/* enable strict mode */
"use strict";

// create redips container
var redips = {};


// redips initialization
redips.init = function () {
	var num = 0,			// number of successfully placed elements
	rd = REDIPS.drag;	// reference to the REDIPS.drag lib
	// initialization
	rd.init();
	rd.dropMode = 'single';
	// set hover color
	rd.hover.colorTd = '#9BB3DA';
};

// add onload event listener
if (window.addEventListener) {
	window.addEventListener('load', redips.init, false);
}
else if (window.attachEvent) {
	window.attachEvent('onload', redips.init);
}
