/*
* Useful script to enable smooth scrolling to an anchor/point on the page.
*
* === Example ====
* 
  $('.some-element').on('click', function(e){
    e.preventDefault();
    var href = $(e.currentTarget).attr('href');
    var offset = 50;
    var speed = 300
    smoothScroll.scroll(href, offset, speed);
  });
*
*/

import $ from 'jquery';

/* 
** @param {String} href - Id of the element to scroll to
** @param {Number} offset - Offset amount to scroll either less or more than the Id
** @param {Number} speed - Animation speed
*/
function smoothScroll(href, offset, speed = 500){

  const $target = $(href);

  $('html, body').animate({
    scrollTop: $(href).offset().top - offset
  }, speed, function () {
      if(history.pushState) {
          history.pushState(null, null, href);
      }
      else {
          location.hash = href;
      }
  });
  $target.focus(); // Setting focus
  if ($target.is(':focus')) { // Checking if the $target was focused
    return false;
  } else {
    $target.attr('tabindex', '-1'); // Adding tabindex for elements not focusable
    $target.focus(); // Setting focus
  }
  return false;
}

export {smoothScroll}