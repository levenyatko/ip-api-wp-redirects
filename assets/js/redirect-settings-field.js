/**
 * Locations to Redirect field scripts
 */
(function( $ ) {
    'use strict';

    $(function(){

        $("#ipapi-redirect-locations-add").on( 'click', function(e){
            e.preventDefault();
            if ( $('.ipapi_locations_to_redirect--group').length < 10 ) {
                add_repeater_new_row();
            }
        });

        /*
        * Delete row on click
        */

        $( document ).on( 'click', '.ipapi-row-remove', function(e){
            e.preventDefault();
            $(this).closest('tr').remove();
            if ( $('.ipapi_locations_to_redirect--group').length == 0 ) {
                add_repeater_new_row();
            }
        });

        /**
         * Both fields in row are required or empty
         */
        $( document ).on( 'input', '.both-required-fields input[type=text]', function(e) {

            let all_fields_in_row = $(this).closest('.both-required-fields').find('input[type=text]');

            if ($(this).val().length > 0) {
                all_fields_in_row.attr("required", true);
            } else {
                all_fields_in_row.attr("required", false);
            }

        });

        $( document ).on( 'click', '.ipapi-fake-checkbox', function(e) {

            let sibling = $(this).siblings();
            if ($(this).is(':checked')) {
                sibling.val(1);
            } else {
                sibling.val(0);
            }

        });


    });

    function add_repeater_new_row() {

        var template = wp.template('redirect-locations-row'),
            html     = template();

        $("#ipapi-redirect-locations-list").append( html );
    }

})( jQuery );