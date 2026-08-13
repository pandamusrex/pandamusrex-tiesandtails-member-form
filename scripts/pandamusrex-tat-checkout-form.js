jQuery( document ).ready( function( $ ) {
    console.log( "TiesAndTails Checkout Form Helper. DOM is fully loaded and ready!" );

    var $dialog = $('#tat-terms-modal');

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
        buttons: {
            "Confirm": function() {
                alert("Action Confirmed!");
                $(this).dialog("close");
            }
        },
        open: function() {
            // Hide body scrollbars when the dialog opens
            $( "body" ).css( "overflow", "hidden" );

            // Scroll the content to the top
            $(this).scrollTop(0);
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

// https://stackoverflow.com/questions/9879571/how-to-resize-jquery-ui-dialog-with-browser

