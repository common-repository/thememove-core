(
	function( $ ) {

		$( document ).ready( function() {

			var $tabs            = $( '.tmc-cmb2-tabs' ),
				CookiesInsightCore = Cookies.noConflict(),
				postID             = $( '#post_ID' ).val(),
				cookieName         = 'ic_metabox_tab_' + postID;

			if ( ! $tabs.length ) {
				return;
			}

			$tabs.css( 'opacity', 1 );

			// init jQuery UI Tabs
			$tabs.tabs();

			// check cookies
			let id = CookiesInsightCore.get( cookieName );
			if ( id ) {
				$tabs.find( 'a.ui-tabs-anchor#' + id ).trigger( 'click' );
			}

			// Set cookies
			$tabs.find( 'a.ui-tabs-anchor' ).on( 'click', function() {

				var id = $( this ).attr( 'id' );
				CookiesInsightCore.set( cookieName, id, {path: '/', expires: 30});
			});
		});
	}
	( jQuery ) );

jQuery.noConflict();
