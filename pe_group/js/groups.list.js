(function ($, Drupal) {

  "use strict";

  /**
   * Filters the view listing tables by a text input search string.
   *
   * Text search input: input.groups-filter-text
   * Target table:      input.groups-filter-text[data-table]
   * Source text:       .groups-table-filter-text-source
   */
  Drupal.behaviors.groupTableFilterByText = {
    attach: function (context, settings) {
      var $input = $('input.groups-table-filter-text').once('groups-table-filter-text');
      var $table = $($input.attr('data-table'));
      var $rows;

      function filterGroupList(e) {
        var query = $(e.target).val().toLowerCase();

        function showGroupRow(index, row) {
          var $row = $(row);
          var $sources = $row.find('.groups-table-filter-text-source');
          var textMatch = $sources.text().toLowerCase().indexOf(query) !== -1;
          $row.closest('tr').toggle(textMatch);
        }

        // Filter if the length of the query is at least 2 characters.
        if (query.length >= 2) {
          $rows.each(showGroupRow);
        }
        else {
          $rows.show();
        }
      }

      if ($table.length) {
        console.log('table.length():');
        $rows = $table.find('tbody tr');
        console.log($rows);
        $input.on('keyup', filterGroupList);
      }
    }
  };

}(jQuery, Drupal));
