import { breakpoints } from 'breakpoints';
  
'use strict';

console.log("TESTING BREAKPOINTS...Resize your browser to small and large.");

// Test query above large breakpoint
breakpoints.check({
  breakpoint: '>=large', 
  deferSetup: true,
  setup: function() {
    console.log('Large screen setup');
  },
  match: function(){
    console.log('Large screen matched');
  },
  unmatch: function() {
    console.log('Large screen unmatched');
  }
});

// Test Query below medium breakpoint
breakpoints.check({
  breakpoint: '<medium', 
  deferSetup: true,
  setup: function() {
    console.log('Small screen setup');
  },
  match: function(){
    console.log('Small screen matched');
  },
  unmatch: function() {
    console.log('Small screen unmatced');
  }
});
