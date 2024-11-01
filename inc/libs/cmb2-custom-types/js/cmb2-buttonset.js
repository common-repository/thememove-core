'use strict';

window.CMB2 = ( function( window, document, $, undefined ) {
	$( '.cmb2-buttonset-label' ).click( function() {
		let parent = $( this ).parents( '.cmb2-buttonset' );

		$( '.cmb2-buttonset-label', parent ).removeClass( 'selected' );
		$( this ).addClass( 'selected' );
	});

}( window, document, jQuery ) );
