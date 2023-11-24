var FXPP = ( function( FXPP, $ ) {
    $( window  ).on( 'load', function() {
        $.post( FXPP.ajaxurl, {
            post_id: FXPP.pid,
            post_type: FXPP.posttype,
            action: 'update_view_count'
        });
    } );
    return FXPP;
})( FXPP || {}, jQuery );