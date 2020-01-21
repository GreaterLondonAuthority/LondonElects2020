/*
* Sample accordion - Adding aria attributes
* Dependencies: a11y-toggle
*/

import $ from 'jquery';
import { ariaToggle } from 'numiko/a11y-toggle/a11y-toggle'; 

let $accordionBtn = $('.js-accordion-btn');

$accordionBtn.on('click', (e) => {
  e.preventDefault();
  let $this = $(e.currentTarget),
      accordionBtnControls = $this.attr('aria-controls'),
      $accordionContent = $('#'+accordionBtnControls);

  $accordionContent.stop().slideToggle(() => {
    ariaToggle($this, "aria-expanded", $accordionContent.is(':visible'));
    ariaToggle($accordionContent, "aria-hidden", $accordionContent.is(':hidden'));
    $accordionContent.toggleClass('is-active');
    $this.toggleClass('is-active');
  });
});