import $ from 'jquery';
import 'simplebar/dist/simplebar.min.js';


$('.wysiwyg-table').each(function() {
  var $this = $(this),
      columnWidth = $this.innerWidth(),
      tableWidth = $this.find('table').innerWidth();

  if (tableWidth > columnWidth) {
    $this.attr('data-simplebar', '').attr('data-simplebar-auto-hide', 'false');
  }
});
