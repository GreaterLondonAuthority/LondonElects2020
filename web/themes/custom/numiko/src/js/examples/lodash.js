import _debounce from 'lodash/debounce';
import _throttle from 'lodash/throttle';

'use strict';

window.addEventListener('scroll', _throttle(function() {
  console.log('You will see me every 250ms while scrolling');
}, 250));

window.addEventListener('resize', _debounce(function(event){
  console.log('You will see me 250ms after resize has stopped');
}, 250));
