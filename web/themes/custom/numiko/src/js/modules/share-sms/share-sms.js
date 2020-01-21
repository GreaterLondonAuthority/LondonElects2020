import $ from 'jquery';
import { breakpoints } from 'core/breakpoints';

breakpoints.check({
  breakpoint: '<medium',
  match: function() {
    const $shareSMS = $('#js-share-sms')
    const isIPhone = !!navigator.platform && /iPhone/.test(navigator.platform);
    
    $shareSMS.attr('href', `sms:${isIPhone ? '&' : '?'}body=${$shareSMS.attr('data-share-sms-url')}`);
  }
});