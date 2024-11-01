'use strict';

window.CMB2 = ( function( window, document, $, undefined ) {
	$( '.cmb2-enable' ).click( function() {
		let parent = $( this ).parents( '.cmb2-switch' );
		$( '.cmb2-disable', parent ).removeClass( 'selected' );
		$( this ).addClass( 'selected' );
	});

	$( '.cmb2-disable' ).click( function() {
		let parent = $( this ).parents( '.cmb2-switch' );
		$( '.cmb2-enable', parent ).removeClass( 'selected' );
		$( this ).addClass( 'selected' );
	});
}( window, document, jQuery ) );
