( function( $ ) {
	'use strict';

	$( '.tmc_select' ).each( function() {
		$( this ).select2({
			allowClear: true
		});
	});

	$.fn.extend({
		select2Sortable: function() {
			let select = $( this );
			$( select ).select2();

			let ul = $( select ).next( '.select2-container' ).first( 'ul.select2-selection__rendered' );
			ul.sortable({
				containment: 'parent',
				items: 'li:not(.select2-search--inline)',
				tolerance: 'pointer',
				stop: function() {
					$( $( ul ).find( '.select2-selection__choice' ).get().reverse() ).each( function() {
						let id   = $( this ).data( 'data' ).id,
							option = select.find( 'option[value="' + id + '"]' )[0];
						$( select ).prepend( option );
					});
				}
			});
		}
	});

	$( '.tmc_multiselect' ).each( function() {
		$( this ).select2Sortable();
	});

	// Before a new group row is added, destroy Select2. We'll reinitialise after the row is added
	$( '.cmb-repeatable-group' ).on( 'cmb2_add_group_row_start', function( event, instance ) {
		let $table = $( document.getElementById( $( instance ).data( 'selector' ) ) ),
			$oldRow  = $table.find( '.cmb-repeatable-grouping' ).last();

		$oldRow.find( '.tmc_select2' ).each( function() {
			$( this ).select2( 'destroy' );
		});
	});

	// When a new group row is added, clear selection and initialise Select2
	$( '.cmb-repeatable-group' ).on( 'cmb2_add_row', function( event, newRow ) {
		$( newRow ).find( '.tmc_select' ).each( function() {
			$( 'option:selected', this ).removeAttr( 'selected' );
			$( this ).select2({
				allowClear: true
			});
		});

		$( newRow ).find( '.tmc_multiselect' ).each( function() {
			$( 'option:selected', this ).removeAttr( 'selected' );
			$( this ).select2Sortable();
		});

		// Reinitialise the field we previously destroyed
		$( newRow ).prev().find( '.tmc_select' ).each( function() {
			$( this ).select2({
				allowClear: true
			});
		});

		// Reinitialise the field we previously destroyed
		$( newRow ).prev().find( '.tmc_multiselect' ).each( function() {
			$( this ).select2Sortable();
		});
	});

	// Before a group row is shifted, destroy Select2. We'll reinitialise after the row shift
	$( '.cmb-repeatable-group' ).on( 'cmb2_shift_rows_start', function( event, instance ) {
		let groutmrap = $( instance ).closest( '.cmb-repeatable-group' );
		groutmrap.find( '.tmc_select2' ).each( function() {
			$( this ).select2( 'destroy' );
		});

	});

	// When a group row is shifted, reinitialise Select2
	$( '.cmb-repeatable-group' ).on( 'cmb2_shift_rows_complete', function( event, instance ) {
		let groutmrap = $( instance ).closest( '.cmb-repeatable-group' );
		groutmrap.find( '.tmc_select' ).each( function() {
			$( this ).select2({
				allowClear: true
			});
		});

		groutmrap.find( '.tmc_multiselect' ).each( function() {
			$( this ).select2Sortable();
		});
	});

	// Before a new repeatable field row is added, destroy Select2. We'll reinitialise after the row is added
	$( '.cmb-add-row-button' ).on( 'click', function( event ) {
		let $table = $( document.getElementById( $( event.target ).data( 'selector' ) ) ),
			$oldRow  = $table.find( '.cmb-row' ).last();

		$oldRow.find( '.tmc_select2' ).each( function() {
			$( this ).select2( 'destroy' );
		});
	});

	// When a new repeatable field row is added, clear selection and initialise Select2
	$( '.cmb-repeat-table' ).on( 'cmb2_add_row', function( event, newRow ) {

		// Reinitialise the field we previously destroyed
		$( newRow ).prev().find( '.tmc_select' ).each( function() {
			$( 'option:selected', this ).removeAttr( 'selected' );
			$( this ).select2({
				allowClear: true
			});
		});

		// Reinitialise the field we previously destroyed
		$( newRow ).prev().find( '.tmc_multiselect' ).each( function() {
			$( 'option:selected', this ).removeAttr( 'selected' );
			$( this ).select2Sortable();
		});
	});
}( jQuery ) );
