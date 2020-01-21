import $ from 'jquery';
import {breakpoints} from 'core/breakpoints';

var $mainMenuExpand = $('.js-menu-revealer');
var $hamburgerBtn = $('.js-hamburger');
var $hamburgerLabel = $('.js-hamburger-label');
var slideSpeed = 300;



/*
* Set up handlers on mobile
*/
function initMobileMenu() {
  $hamburgerBtn.attr('aria-hidden', 'false').attr('aria-expanded', 'false');

  $hamburgerBtn.on('click.hamburger', function(e){
    e.preventDefault();
    $mainMenuExpand.stop().slideToggle(slideSpeed);
    $('body').toggleClass('menu-active');
    $hamburgerBtn.toggleClass('is-active');

    if($hamburgerBtn.hasClass('is-active')) {
      $(this).attr('aria-expanded', 'true');
      $hamburgerLabel.text('Close');
    } else {
      $(this).attr('aria-expanded', 'false');
      $hamburgerLabel.text('Menu');
    }
  })

}

/*
* Destroy mobile handlers, reset.
*/
function destroyMobileMenu() {
  $hamburgerBtn.off('click');
  $('body').removeClass('menu-active');
  
  // Reset hamburger
  $hamburgerBtn.removeClass('is-active').attr('aria-hidden', 'true').attr('aria-expanded', 'true');
  $hamburgerLabel.text('Menu');

  $mainMenuExpand.removeAttr('style');
}


/*
* Mobile init/match
*/
breakpoints.check({
  breakpoint: '<large',
  match: function(){
    if($mainMenuExpand.length) {
      initMobileMenu();
    }
  },
  unmatch: function() {
    destroyMobileMenu();
  }
});