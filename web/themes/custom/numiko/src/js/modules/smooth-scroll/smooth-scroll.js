import $ from 'jquery';

$('.js-jump-link').click(function(e){
  const $target = $(this.hash);

  e.preventDefault();

  $('body,html').animate({
    scrollTop: $($target).offset().top - 50
  }, 500);

  $target.focus(); // Setting focus
  if ($target.is(':focus')) { // Checking if the $target was focused
    return false;
  } else {
    $target.attr('tabindex', '-1'); // Adding tabindex for elements not focusable
    $target.focus(); // Setting focus
  }
  return false;

});