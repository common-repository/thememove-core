jQuery( document ).ready( function( $ ) {

	function isInt( n ) {
		return 0 === n % 1;
	}

	$( '.plus, .minus' ).on( 'click', function() {

		// Get values
		let $number = $( this ).closest( '.cmb2_number' ).find( '.cmb_text_small' ),
			currentVal = parseFloat( $number.val() ),
			max = parseFloat( $number.attr( 'max' ) ),
			min = parseFloat( $number.attr( 'min' ) ),
			step = $number.attr( 'step' );

		// Format values
		if ( ! currentVal || '' === currentVal || 'NaN' === currentVal ) {
			currentVal = 0;
		}
		if ( '' === max || 'NaN' === max ) {
			max = '';
		}
		if ( '' === min || 'NaN' === min ) {
			min = 0;
		}
		if ( 'any' === step || '' === step || step === undefined || 'NaN' === parseFloat( step ) ) {
			step = 1;
		}

		// Change the value
		if ( $( this ).is( '.plus' ) ) {

			if ( max && ( max == currentVal || currentVal > max ) ) {
				$number.val( max );
			} else {

				if ( isInt( step ) ) {
					$number.val( currentVal + parseFloat( step ) );
				} else {
					$number.val( ( currentVal + parseFloat( step ) ).toFixed( 1 ) );
				}
			}

		} else {

			if ( min && ( min == currentVal || currentVal < min ) ) {
				$number.val( min );
			} else if ( 0 < currentVal ) {
				if ( isInt( step ) ) {
					$number.val( currentVal - parseFloat( step ) );
				} else {
					$number.val( ( currentVal - parseFloat( step ) ).toFixed( 1 ) );
				}
			}

		}

		// Trigger change event
		$number.trigger( 'change' );
	});
});
