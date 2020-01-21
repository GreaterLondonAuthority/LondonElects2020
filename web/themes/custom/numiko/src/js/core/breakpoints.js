/*
* Numiko Breakpoints
* Bring together unison.js, enquire.js and 'include media' (https://include-media.com/) 
* so that we can use the same style of syntax to *target breakpoints in sass and js.
* USAGE: (see tests/test-breakpoints.js)
*/

import Unison from '../libs/unison';
import enquire from 'enquire.js';

'use strict';

/*
* Store all breakpoint data
*/
var bp = Unison.fetch.all();
window.bpp = bp;

/*
* Check if the breakpoint has an '='
*/
function isBreakpointInclusive(breakpoint) {
    var bpInclusive = breakpoint.substr(1,1);
    if (bpInclusive == '=') {
    return true;
    } else {
      return false;
    }
}

/*
* Check the operator to set whether its 'min' or 'max' for the media query
*/
function breakpointMinOrMax(breakpoint) {
  var bpOperator = breakpoint.substr(0,1); // e.g. '<'
  if (bpOperator == '>') {
    return 'min'
  } else if (bpOperator == '<') {
    return 'max'
  } else {
    console.error("Breakpoint condition didn't match either '>', '>=', '<=', <'");
  }
}
 
  
var breakpoints = {
  check: function(obj) {
    var breakpoint = obj.breakpoint; 
    var breakpointMinMax = breakpointMinOrMax(breakpoint);
    var breakpointInclusive = isBreakpointInclusive(breakpoint) 
    var breakpointSanitized = breakpoint.replace(/<|>|=|px/g,''); // Remove '<','>', '=' and 'px' e.g. 'large' or 1200
    var isBreakpointNumber =  /^\d+$/.test(breakpointSanitized); 

    /*
    * Get the media query value - either directly from a number value or by a keyword match from the unison breakpoints
    */
    var breakpointNumber;
    if (isBreakpointNumber) {
      breakpointNumber = parseInt(breakpointSanitized);
    } else {
      breakpointNumber = parseInt(bp[breakpointSanitized]);
    }
      
    /*
    * Adjust media query value if its not inclusive
    */
    if (breakpointMinMax == 'min' && !breakpointInclusive ) {
      breakpointNumber ++;
    } 
    else if (breakpointMinMax == 'max' && !breakpointInclusive) {
      breakpointNumber --;
    } 


    enquire.register("screen and (" + breakpointMinMax + "-width:" + breakpointNumber + "px)", {
      deferSetup: obj.deferSetup,
      setup: obj.setup,
      match: obj.match,
      unmatch: obj.unmatch,
      destroy: obj.destroy,
    });
  }
}


export { breakpoints };