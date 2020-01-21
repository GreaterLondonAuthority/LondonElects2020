(function ($, Drupal) {

  Drupal.behaviors.mediaItemClickArea = {
    attach: function(context, settings) {

      $('.views-col', context).once('mediaItemClickArea').on('click', function(e) {

        $checkbox = $(this).find('input');

        // If the click wasn't directly on the checkbox
        if (e.target.tagName != 'INPUT') {
          if ($checkbox.prop('checked')) {
            $checkbox.prop('checked', false);
          } else {
            $checkbox.prop('checked', true);
          }
        }

        if ($checkbox.is(':checked')) {
          $(this).addClass('selected');
        } else {
          $(this).removeClass('selected');
        }

      });
    }
  };

})(jQuery, Drupal);
