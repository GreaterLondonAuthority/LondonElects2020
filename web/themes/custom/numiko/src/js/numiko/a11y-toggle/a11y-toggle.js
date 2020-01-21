/* 
** @param {jQuery Object} $el - Object for element to have aria attributes toggled
** @param {String} ariaString - e.g. "aria-hidden", "aria-expanded"
** @param {Boolean} value - e.g. true
*/
function ariaToggle($el, ariaString, value) {
  if( $el == undefined) {
    console.warn('ariaToggle(): Parameter missing: Expected jQuery object to attach aria attribute to.')
  } 
  else if(ariaString == undefined) {
    console.warn('ariaToggle(): Parameter missing. Expected string for aria attribute e.g. "aria-hidden".')
  } 
  else if(value == undefined) {
    console.warn('ariaToggle(): Parameter missing. Expected truthy value for 3rd parameter.')
  } 
  else {
    $el.attr(ariaString, value);
  }
}

export { ariaToggle };