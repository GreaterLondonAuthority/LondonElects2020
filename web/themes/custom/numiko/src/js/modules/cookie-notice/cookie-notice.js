/*
* Dependencies: js-cookie https://github.com/js-cookie/js-cookie
*/

import $ from 'jquery';
import Cookies from 'js-cookie';

const cookiePrefix = 'alert';
const expireLength = 7;


/**
 * Set unique cookie id for user configurable alert messages as there is the facility to configure different alerts throughout the site
 */
function uniqueAlertPanel() {
  let $alertBar = $(this);
  let $closeButton = $alertBar.find('.js-alert-close');
  let alertContent = $alertBar.find('.js-alert-text').text();
  let messageString = btoa(alertContent);
  let cookieName = `${cookiePrefix}_${messageString}`;
  let cookieValue = Cookies.get(cookieName);

  // If the cookie isn't already set, show the associated message
  if(cookieValue === undefined) {
    $alertBar.show();
  }

  $closeButton.on('click', () => {
    Cookies.set(cookieName, true, { expires: expireLength });
    $alertBar.slideUp();
  });

}

$('.js-alert').each(uniqueAlertPanel);