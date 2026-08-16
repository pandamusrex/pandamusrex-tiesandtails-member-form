jQuery( document ).ready( function( $ ) {
    var $dialog = $('#tat-terms-modal');
    if ( ! $dialog.length ) {
        return;
    }

    console.log( "TiesAndTails Member Form Helper. Dialog found." );

    function update_submit_button_state() {
        $all_checkboxes_checked = true;

        $( '#tat-terms-modal input[type="checkbox"]' ).each( function() {
            if ( ! $( this ).is( ':checked' ) ) {
                $all_checkboxes_checked = false;
            }
        } );

        if ( $all_checkboxes_checked ) {
            $('#tat-terms-submit').button("enable");
        } else {
            $('#tat-terms-submit').button("disable");
        }
    }

    $( '#tat-terms-modal input[type="checkbox"]' ).on( 'click', function() {
        update_submit_button_state();
    } );

    $dialog.dialog( {
        autoOpen: true,
        modal: true,
        width: getResponsiveWidth(),
        height: getResponsiveHeight(),
        resize: function() {
            $( this ).dialog( "option", "position", { my: "center", at: "center", of: window } );
        },
        resizable: false,
        draggable: false,
        buttons: [
            {
                text: "Submit",
                id: "tat-terms-submit",
                click: function() {
                    $( this ).dialog( "close" );
                }
            }
        ],
        open: function() {
            // Hide body scrollbars when the dialog opens
            $( "body" ).css( "overflow", "hidden" );

            // Scroll the content to the top
            $(this).scrollTop(0);

            // Disable the button for starters
            update_submit_button_state();
        },
        close: function() {
            // Restore body scrollbars when the dialog closes
            $( "body" ).css( "overflow", "auto" );
        }
    } );

    $( ".ui-widget-overlay" ).css( "z-index", 10000 );
    $dialog.parent().css( "z-index", 10001 );

    function getResponsiveWidth() {
        return $( window ).width() * 0.85; 
    }

    function getResponsiveHeight() {
        return $( window ).height() * 0.85;
    }

    // https://stackoverflow.com/questions/9879571/how-to-resize-jquery-ui-dialog-with-browser
    $( window ) .on( "resize", function() {
        if ( $dialog.dialog( "isOpen" ) ) {
            $dialog.dialog( "option", {
                width: getResponsiveWidth(),
                height: getResponsiveHeight(),
                position: { my: "center", at: "center", of: window }
            } );
        }
    } );
} );
