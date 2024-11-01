(
	function( $ ) {
		'use strict';
		$( document ).ready( function() {
			$( '.cmb-type-slider' ).each( function() {
				let $this   = $( this ),
					$value    = $this.find( '.cmb2-slider-value' ),
					$slider   = $this.find( '.tmc-cmb2-slider' ),
					$text     = $this.find( '.cmb2-slider-value-text' ),
					slideData = $value.data();

				$slider.slider({
					range: 'min',
					value: slideData.start,
					min: slideData.min,
					step: slideData.step,
					max: slideData.max,
					slide: function( event, ui ) {
						$value.val( ui.value );
						$text.text( ui.value );
					}
				});

				// Initiate the display
				$value.val( $slider.slider( 'value' ) );
				$text.text( $slider.slider( 'value' ) );

			});
		});
	}
	( jQuery ) );

jQuery.noConflict();
