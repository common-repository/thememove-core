'use strict';

let tmc = ( ( $ ) => {

	return {
		init() {
			this.activeLicense();
			this.deactiveLicense();
			this.openLicenseKey();
			this.updateTheme();
			this.refreshTransients();
			this.processPluginActions();
			this.goToChangelog();
			this.applyPatch();
			this.generateChildTheme();
			this.fetchDemoSteps();
			this.selectDemoSteps();
			this.closeImportPopup();
			this.importDemo();
		},
		playLottie( el, path, loop, duration ) {
			let lt = lottie.loadAnimation({
				container: el,
				renderer: 'svg',
				loop: loop,
				autoplay: true,
				path: path
			});

			lt.play();

			if ( ! loop && 0 < duration ) {
				setTimeout( () => {
					lt.stop();
				}, duration );
			}
		},
		humanFileSize( size )  {
			let i = Math.floor( Math.log( size ) / Math.log( 1024 ) );
			return ( size / Math.pow( 1024, i ) ).toFixed( 2 ) * 1 + ' ' + [ 'B', 'kB', 'MB', 'GB', 'TB' ][i];
		},
		activeLicense() {
			$( '.tmc-license-key-form' ).on( 'submit', ( e ) => {
				e.preventDefault();

				let $form = $( e.currentTarget ),
					$icon = $form.find( '.tmc-license-key-form__submit i, .tmc-license-key-form__submit .svg-inline--fa' ),
					$error = $form.find( '.tmc-error-text' ),
					formData = $form.serialize();

				$icon.css( 'display', 'inline-block' );
				$error.hide();

				$.ajax({
					type: 'POST',
					url: tmcVars.ajax_url,
					data: `${formData}&action=activate_license`,
					timeout: 20000
				}).done( ( response ) => {
					$icon.hide();

					if ( response.success && 'valid' === response.data.license ) {
						this.playLottie( $form[0], tmcVars.animation_url + 'success.json', false );

						setTimeout( () => {
							location.reload();
						}, 1000 );
					} else {
						if ( response.data.message ) {
							$error.html( response.data.message );
						}
						$error.show();
					}
				}).fail( ( jqXHR, textStatus ) => {
					console.error( `${jqXHR.responseText}: ${jqXHR.status}` );
					console.error( `${textStatus}` );
				});
			});
		},
		deactiveLicense() {
			$( '.tmc-deactivate-link' ).on( 'click', ( e ) => {
				e.preventDefault();

				let $el = $( e.currentTarget ),
					$error = $( '.tmc-error-text' ),
					ajaxData = {
						'action': 'deactivate_license',
						'license': $el.attr( 'data-license' ),
						'url': $el.attr( 'data-url' ),
						'item_name': $el.attr( 'data-item-name' ),
						'_wpnonce': $el.attr( 'data-nonce' )
					};

				$el.html( '<i class="fal fa-spinner-third tm-spin"></i> Deactivating' );
				$error.hide();

				$.ajax({
					type: 'POST',
					url: tmcVars.ajax_url,
					data: ajaxData,
					timeout: 20000
				}).done( ( response ) => {
					if ( response.success && 'deactivated' === response.data.license ) {
						this.playLottie( $el.closest( '.tmc-box--license' )[0], tmcVars.animation_url + 'dog.json', false );

						setTimeout( () => {
							location.reload();
						}, 1800 );
					} else {
						$el.text( 'Deactivate' );
						$error.show().text( 'There was an error occurs when deactivating license key, please try again.' );
					}
				}).fail( ( jqXHR, textStatus ) => {
					console.error( `${jqXHR.responseText}: ${jqXHR.status}` );
					console.error( `${textStatus}` );
				});
			});
		},
		openLicenseKey() {
			$( '.tmc-open-key' ).on( 'click', ( e ) => {
				e.preventDefault();

				let $this = $( e.currentTarget ),
					$icon = $this.find( 'i, .svg-inline--fa' ),
					$input = $( '.tmc-table__license-key' );

				if ( $icon.hasClass( 'fa-lock-alt' ) ) {
					$icon.removeClass( 'fa-lock-alt' ).addClass( 'fa-unlock-alt' );
					$input.attr( 'type', 'text' );
				} else {
					$icon.addClass( 'fa-lock-alt' ).removeClass( 'fa-unlock-alt' );
					$input.attr( 'type', 'password' );
				}
			});
		},
		updateTheme() {
			$( '.tmc-update-btn' ).on( 'click', ( e ) => {
				$( e.currentTarget )
					.find( 'i, .svg-inline--fa' )
					.removeClass( 'fa-cloud-download' )
					.addClass( 'fa-spinner-third tm-spin' );
			});
		},
		refreshTransients() {
			$( '.tmc-box--update__refresh' ).on( 'click', ( e ) => {
				e.preventDefault();

				let $el = $( $( e.currentTarget ) );
				$el.find( 'i, .svg-inline--fa' ).addClass( 'tm-spin' );

				$.ajax({
					type: 'POST',
					url: tmcVars.ajax_url,
					data: {
						'action': 'refresh_transients',
						'_wpnonce': $el.attr( 'data-nonce' )
					},
					timeout: 20000
				}).done( ( response )=>{

					if ( response.success ) {
						$el.html( '<i class="fal fa-check"></i> Done' );

						setTimeout( () => {
							location.reload();
						}, 800 );
					} else {
						$el.html( '<i class="fal fa-times"></i> Failed' );
					}
				}).fail( ( jqXHR, textStatus ) => {
					console.error( `${jqXHR.responseText}: ${jqXHR.status}` );
					console.error( `${textStatus}` );
				});
			});
		},
		processPluginActions() {
			$( '.tmc-plugin-link' ).on( 'click', ( e ) => {
				e.preventDefault();

				let $el = $( e.currentTarget ),
					$pluginsTable = $( '.tmc-box--plugins table' ),
					$pluginRow = $el.closest( '.tmc-plugin--required' ),
					pluginAction = $el.attr( 'data-plugin-action' ),
					$icon = $pluginRow.find( 'i, .svg-inline--fa' ),
					ajaxData = {
						'action': 'process_plugin_actions',
						'slug': $el.attr( 'data-slug' ),
						'source': $el.attr( 'data-source' ),
						'plugin_action': $el.attr( 'data-plugin-action' ),
						'_wpnonce': $el.attr( 'data-nonce' )
					};

				if ( 'deactivate-plugin' === pluginAction ) {
					$el.html( '<i class="fal fa-spinner-third tm-spin"></i>Deactivating' );
				}

				if ( 'activate-plugin' === pluginAction ) {
					$el.html( '<i class="fal fa-spinner-third tm-spin"></i>Activating' );
				}

				$.ajax({
					type: 'POST',
					url: tmcVars.ajax_url,
					data: ajaxData,
					timeout: 20000
				}).done( ( response ) => {

					if ( response.success ) {

						if ( 'deactivate-plugin' === pluginAction ) {
							$pluginRow.removeClass( 'tmc-plugin--activated' ).addClass( 'tmc-plugin--deactivated' );
							$el.text( 'Activate' )
								.attr( 'data-plugin-action', 'activate-plugin' )
								.attr( 'data-nonce', response.data )
								.removeClass( 'tmc-plugin-link--deactivate' )
								.addClass( 'tmc-plugin-link--activate' );
							$icon.addClass( 'fa-times' ).removeClass( 'fa-check' );
						}

						if ( 'activate-plugin' === pluginAction ) {
							$pluginRow.removeClass( 'tmc-plugin--deactivated' ).addClass( 'tmc-plugin--activated' );
							$el.text( 'Deactivate' )
								.attr( 'data-plugin-action', 'deactivate-plugin' )
								.attr( 'data-nonce', response.data )
								.removeClass( 'tmc-plugin-link--activate' )
								.addClass( 'tmc-plugin-link--deactivate' );
							$icon.addClass( 'fa-check' ).removeClass( 'fa-times' );
						}

						let requiredPluginCount = $pluginsTable.find( '.tmc-plugin--required.tmc-plugin--deactivated' ).length,
							$pluginCount = $( '.tmc-box--plugins .tmc-box__footer span' );

						if ( requiredPluginCount ) {
							$pluginCount.css( 'color', '#dc433f' ).text( 'Please install and activate all required plugins (' + requiredPluginCount + ')' );
						} else {
							$pluginCount.css( 'color', '#6fbcae' ).text( 'All required plugins are activated. Now you can import the demo data.' );
						}
					} else {
						$el.text( 'Error' );
					}
				}).fail( ( jqXHR, textStatus ) => {
					console.error( `${jqXHR.responseText}: ${jqXHR.status}` );
					console.error( `${textStatus}` );
				});
			});
		},
		goToChangelog() {
			$( '#go-to-changelog' ).on( 'click', ( e ) => {
				e.preventDefault();

				$( 'html, body' ).animate({
					scrollTop: $( '.tmc-box--changelog' ).offset().top
				});
			});
		},
		applyPatch() {
			$( '.tmc-apply-patch' ).on( 'click', ( e ) => {
				e.preventDefault();

				let $el = $( e.currentTarget ),
					$error = $( '.tmc-error-text' ),
					ajaxData = {
						'action': 'apply_patch',
						'key': $el.attr( 'data-key' ),
						'_wpnonce': $el.attr( 'data-nonce' )
					};

				if ( $el.attr( 'disabled' ) ) {
					return;
				}

				$( '.tmc-apply-patch' ).attr( 'disabled', true );
				$el.html( '<i class="fal fa-spinner-third tm-spin" style="display:inline-block"></i> Applying' );
				$error.hide();

				$.ajax({
					type: 'POST',
					url: tmcVars.ajax_url,
					data: ajaxData,
					timeout: 20000
				}).done( ( response ) => {

					if ( response.success ) {
						$el.removeAttr( 'disabled' );
						$el.html( '<i class="fal fa-check" style="display:inline-block"></i> Patch Applied' );

						setTimeout( () => {
							location.reload();
						}, 800 );
					} else {
						$el.removeAttr( 'disabled' );
						$el.html( '<i class="far fa-times" style="display:inline-block"></i> Error. Try again.' );
						$error.show().html( response.data.length ? response.data : 'There was an error occurs when applying this patch, please try again.' );
					}
				}).fail( ( jqXHR, textStatus ) => {
					console.error( `${jqXHR.responseText}: ${jqXHR.status}` );
					console.error( `${textStatus}` );
				});
			});
		},
		generateChildTheme() {
			$( '.tmc-child-theme-generator-form' ).on( 'submit', ( e ) => {
				e.preventDefault();

				let $form = $( e.currentTarget ),
					$title = $( '.tmc-box__title' ),
					titleText = $title.text(),
					$desc = $( '.tmc-box__description' ),
					$error = $( '.tmc-error-text' ),
					formData = $form.serialize();

				$form.addClass( 'generating' );
				$title.text( 'Creating child theme folder...' );
				$desc.hide();
				$error.hide();

				this.playLottie( $form[0], tmcVars.animation_url + 'elephant.json', true );

				$.ajax({
					type: 'POST',
					url: tmcVars.ajax_url,
					data: `${formData}&action=generate_child_theme`,
					timeout: 20000
				}).done( ( response ) => {

					if ( response.success ) {

						// Fake Loading
						setTimeout( () => {
							$title.text( 'Creating style.css...' );
						}, 1000 );

						setTimeout( () => {
							$title.text( 'Creating functions.php...' );
						}, 2000 );

						setTimeout( () => {
							$title.text( 'All done!' );

							if ( response.data ) {
								let themeName = $( '#theme-name' ).val();

								$form.hide();
								$desc.show().html( `<a class="button" href="${response.data}">Activate ${themeName}</a>` );
								this.playLottie( $title[0], tmcVars.animation_url + 'success.json', false );
							}
						}, 4000 );
					} else {
						if ( response.data ) {
							$error.html( response.data );
						}
						$title.text( titleText );
						$desc.show();
						$error.show();
						$form.find( 'svg' ).remove();
						$form.removeClass( 'generating' );
					}
				}).fail( ( jqXHR, textStatus ) => {
					console.error( `${jqXHR.responseText}: ${jqXHR.status}` );
					console.error( `${textStatus}` );
				});
			});
		},
		fetchDemoSteps() {
			$( '.tmc-import-demo__button' ).on( 'click', ( e ) => {
				e.preventDefault();

				let $button = $( e.currentTarget ),
					buttonText = $button.html(),
					$error = $( '.tmc-error-text' ),
					ajaxData = {
						'action': 'fetch_demo_steps',
						'demo_slug': $button.attr( 'data-demo-slug' ),
						'_wpnonce': $button.attr( 'data-nonce' )
					};

				if ( $button.attr( 'disabled' ) ) {
					return;
				}

				$button.html( '<i class="fal fa-spinner-third tm-spin" style="display:inline-block"></i> Fetching Data' );
				$button.attr( 'disabled', true ).removeClass( 'error' );
				$error.hide();

				$.ajax({
					type: 'POST',
					url: tmcVars.ajax_url,
					data: ajaxData,
					timeout: 20000
				}).done( ( response ) => {

					if ( response.success ) {

						$button.removeAttr( 'disabled' );
						$button.html( buttonText );

						$( '#tmc-import-demo-popup' ).html( response.data );

						$.magnificPopup.open({
							items: {
								src: '#tmc-import-demo-popup',
								type: 'inline'
							},
							modal: true,
							removalDelay: 300,
							mainClass: 'mfp-fade'
						});

					} else {
						$button.removeAttr( 'disabled' ).addClass( 'error' );
						$button.html( '<i class="far fa-times" style="display:inline-block"></i> Imported Failed' );
						$error.show().html( response.data.length ? response.data : 'There was an error occurs when applying this patch, please try again.' );
					}
				}).fail( ( jqXHR, textStatus ) => {
					console.error( `${jqXHR.responseText}: ${jqXHR.status}` );
					console.error( `${textStatus}` );
				});
			});
		},
		selectDemoSteps() {

			$( document ).on( 'click', '.tmc-demo-steps__svg', ( e ) => {
				$( e.currentTarget ).prev( 'input[type="checkbox"]' ).trigger( 'click' );
			});

			$( document ).on( 'change', '#tmc-all-demo-steps', ( e ) => {

				let $checkbox = $( e.currentTarget );

				if ( $checkbox.is( ':checked' ) ) {
					$( '.tmc-demo-steps__checkbox' ).not( $checkbox ).attr( 'checked', true );
				} else {
					$( '.tmc-demo-steps__checkbox' ).not( $checkbox ).attr( 'checked', false );
				}
			});

			$( document ).on( 'change', '.tmc-demo-steps__checkbox', ( e ) => {

				let $checkbox  = $( e.currentTarget ),
					$checkAll    = $( '#tmc-all-demo-steps' ),
					uncheckCount = 0;

				if ( $checkbox.is( ':checked' ) ) {
					$( '.tmc-demo-steps__checkbox' ).not( $checkAll ).each( ( idx, chkbox ) => {
						if ( ! $( chkbox ).is( ':checked' ) ) {
							uncheckCount++;
						}
					});

					if ( 1 <= uncheckCount ) {
						$checkAll.attr( 'checked', false );
					} else {
						$checkAll.attr( 'checked', true );
					}
				} else {
					$checkAll.attr( 'checked', false );
				}
			});
		},
		closeImportPopup() {
			$( document ).on( 'click', '.tmc-popup__close-button', ( e ) => {
				e.preventDefault();
				$.magnificPopup.close();
			});
		},
		importDemo() {

			$( document ).on( 'submit', '#demo-steps-form', ( e ) => {
				e.preventDefault();

				// Get all steps before submitting the form.
				$( '.tmc-demo-steps__checkbox' ).not( '#tmc-all-demo-steps' ).each( ( idx, chkbox ) => {
					let demoSteps = $( '#selected-steps' ).val();

					if ( $( chkbox ).is( ':checked' ) ) {
						$( '#selected-steps' ).val( `${demoSteps}${$( chkbox ).attr( 'id' )},` );
					}
				});

				let $form = $( e.currentTarget ),
					$popup = $( '#tmc-import-demo-popup' ),
					$error = $form.find( '.tmc-error-text' ),
					formData = $form.serialize();

				$popup.addClass( 'tmc-loading' );
				$error.hide();

				$.ajax({
					type: 'POST',
					url: tmcVars.ajax_url,
					data: `${formData}&action=import_demo`
				}).done( ( response ) => {

					if ( response.success ) {

						// Change HTML for the popup.
						if ( response.data ) {
							$popup.html( response.data );
						}

						// Copy images from local media package.
						if ( $( '#copy-images-form' ).length ) {
							this.copyImages();
							$( '#copy-images-form' ).submit();
						}

						// Download image.
						if ( $( '#download-media-package-form' ).length ) {
							this.downloadMediaPackage();
							$( '#download-media-package-form' ).submit();
						}

						// Import content.
						if ( $( '#import-content-wrapper' ).length ) {
							this.importData();
						}
					} else {
						$error.show().html( response.data.length ? response.data : 'There was an error occurs when importing demo data, please try again.' );
					}

					$popup.removeClass( 'tmc-loading' );
				}).fail( ( jqXHR, textStatus ) => {
					console.error( `${jqXHR.responseText}: ${jqXHR.status}` );
					console.error( `${textStatus}` );
				});
			});

			// Prevent close windows while importing
			window.onbeforeunload = ( e ) => {

				if ( $.magnificPopup.instance.isOpen ) {
					if ( ! e ) {
						e = window.event;
					}

					e.cancelBubble = true;
					e.returnValue = 'The importer is running. Please don\'t navigate away from this page.';

					if ( evt.stopPropagation ) {
						e.stopPropagation();
						e.preventDefault();
					}
				}
			};
		},
		downloadMediaPackage() {
			let self = this;

			$( '#download-media-package-form' ).on( 'submit', ( e ) => {
				e.preventDefault();

				let $form = $( e.currentTarget ),
					$error = $form.find( '.tmc-error-text' ),
					$note = $form.find( '.tmc-popup__note' ),
					$closeButton = $form.find( '.tmc-popup__close-button' ),
					$progressBar = $form.find( '.tmc-progress-bar' ),
					formData = $form.serialize();

				$note.css({
					'opacity': '1',
					'visibility': 'visible'
				});
				$closeButton.css({
					'opacity': '0',
					'visibility': 'hidden'
				});
				$error.hide();
				$progressBar.show();

				$.ajax({
					type: 'POST',
					url: tmcVars.ajax_url,
					data: `${formData}&action=download_media_package`
				}).done( ( response ) => {

					if ( response.success ) {

						// Show progress when download file.
						let downloadPromise = new Promise( ( resolve, reject )  => {
							let xhr = new XMLHttpRequest();
							xhr.open( 'GET', 'https://cors-anywhere.herokuapp.com/' + $( '#media_package_url' ).val(), true );
							xhr.responseType = 'blob';
							xhr.onprogress = ( e ) => {
								if ( 0 < e.total ) {
									let percent = Math.round( e.loaded / e.total * 100 ),
										loaded = self.humanFileSize( e.loaded ),
										total = self.humanFileSize( e.total );

									$progressBar.find( '.tmc-progress-bar__inner' ).css( 'width', `${percent}%` );
									$progressBar.find( '.tmc-progress-bar__text' ).text( `${loaded} / ${total} (${percent}%)` );
								}
							};

							xhr.onload = () => {
								resolve( xhr.response );
							};

							xhr.onerror = () => {
								reject( xhr.response );
							};

							xhr.send();
						});

						downloadPromise.then( () => {
							setTimeout( () => {

								if ( response.data ) {
									$( '#tmc-import-demo-popup' ).html( response.data );
								}

								// Copy images to wp-content/uploads
								if ( $( '#copy-images-form' ).length ) {
									this.copyImages();
									$( '#copy-images-form' ).submit();
								}
							}, 2000 );
						});
					} else {
						$note.css({
							'opacity': '0',
							'visibility': 'hidden'
						});
						$closeButton.css({
							'opacity': '1',
							'visibility': 'visible'
						});
						$progressBar.hide();
						$error.show().html( response.data.length ? response.data : 'There was an error occurs when downloading the media package, please try again.' );
					}

				}).fail( ( jqXHR, textStatus ) => {
					console.error( `${jqXHR.responseText}: ${jqXHR.status}` );
					console.error( `${textStatus}` );
				});
			});
		},
		copyImages() {

			$( '#copy-images-form' ).on( 'submit', ( e ) => {
				e.preventDefault();

				let $popup = $( '#tmc-import-demo-popup' ),
					$form = $( e.currentTarget ),
					$error = $form.find( '.tmc-error-text' ),
					$note = $form.find( '.tmc-popup__note' ),
					$closeButton = $form.find( '.tmc-popup__close-button' ),
					$title = $form.find( '.tmc-popup__title' ),
					formData = $form.serialize();

				$note.css({
					'opacity': '1',
					'visibility': 'visible'
				});
				$closeButton.css({
					'opacity': '0',
					'visibility': 'hidden'
				});
				$error.hide();

				this.playLottie( $title[0], tmcVars.animation_url + 'file-copying.json', true );

				$.ajax({
					type: 'POST',
					url: tmcVars.ajax_url,
					data: `${formData}&action=copy_images`
				}).done( ( response ) => {

					setTimeout( () => {
						$title.find( 'svg' ).remove();

						if ( response.success ) {
							if ( response.data ) {
								$popup.html( response.data );

								if ( $( '#import-content-wrapper' ).length ) {
									this.importData();
								}

								if ( $( '#import-success' ).length ) {
									this.playLottie( $( '#import-success .tmc-popup__subtitle' )[0], tmcVars.animation_url + 'star-success.json', true );
								}
							}
						} else {
							$note.css({
								'opacity': '0',
								'visibility': 'hidden'
							});
							$closeButton.css({
								'opacity': '1',
								'visibility': 'visible'
							});
							$error.show().html( response.data.length ? response.data : 'There was an error occurs when downloading the media package, please try again.' );
						}
					}, 3000 );
				}).fail( ( jqXHR, textStatus ) => {
					console.error( `${jqXHR.responseText}: ${jqXHR.status}` );
					console.error( `${textStatus}` );
				});
			});
		},
		importData() {

			let $firstStep = $( '#import-content-wrapper .tmc-import-content__item:first-child' ),
				$title = $( '#import-content-wrapper .tmc-popup__title' ),
				data = {
					'import_content_steps': $( '#import_content_steps' ).val(),
					'demo_slug': $( '#demo_slug' ).val(),
					'_wpnonce': $firstStep.attr( 'data-nonce' ),
					'action': $firstStep.attr( 'data-action' )
				};

			this.playLottie( $title[0], tmcVars.animation_url + 'import-content.json', true );

			if ( $firstStep.length ) {
				this.runImportContentAjax( data );
			}
		},
		setUpAJAXData( $el, data ) {

			if ( $el.prev().length ) {
				$el.prev().find( 'i, .svg-inline--fa' )
					.removeClass( 'fa-spinner-third tm-spin' )
					.addClass( 'fa-check' );
			} else {
				$el.find( 'i, .svg-inline--fa' )
					.removeClass( 'fa-spinner-third tm-spin' )
					.addClass( 'fa-check' );
			}

			data._wpnonce = $el.attr( 'data-nonce' );
			data.action   = $el.attr( 'data-action' );

			return data;
		},
		runImportContentAjax( data ) {
			let $wrapper = $( '#import-content-wrapper' ),
				$popup = $( '#tmc-import-demo-popup' ),
				$error = $wrapper.find( '.tmc-error-text' ),
				$note = $wrapper.find( '.tmc-popup__note' ),
				$closeButton = $wrapper.find( '.tmc-popup__close-button' ),
				$title = $wrapper.find( '.tmc-popup__title' );

			$note.css({
				'opacity': '1',
				'visibility': 'visible'
			});
			$closeButton.css({
				'opacity': '0',
				'visibility': 'hidden'
			});

			$error.hide();

			$.ajax({
				type: 'POST',
				url: tmcVars.ajax_url,
				data: data
			}).done( ( response ) => {
				if ( 'undefined' !== typeof response.status && 'newAJAX' === response.status ) {
					this.runImportContentAjax( data );
				} else if ( 'undefined' !== typeof response.next_step ) {
					data = this.setUpAJAXData( $( `#import-content-wrapper #${response.next_step}` ), data );
					this.runImportContentAjax( data );
				} else if ( response.success ) {

					// Add checkbox for the last item
					$( '#import-content-wrapper .tmc-import-content__item:last-child' ).find( 'i, .svg-inline--fa' )
						.removeClass( 'fa-spinner-third tm-spin' )
						.addClass( 'fa-check' );
					setTimeout( () => {
						if ( response.data ) {
							$popup.html( response.data );
							if ( $( '#import-success' ).length ) {
								this.playLottie( $( '#import-success .tmc-popup__subtitle' )[0], tmcVars.animation_url + 'star-success.json', true );
							}
						}
					}, 1500 );
				} else {
					$title.find( 'svg' ).remove();
					$( '.tmc-import-content-list' ).hide();
					$note.css({
						'opacity': '0',
						'visibility': 'hidden'
					});
					$closeButton.css({
						'opacity': '1',
						'visibility': 'visible'
					});
					$error.show().html( response.data.length ? response.data : 'There was an error occurs when importing, please try again.' );
				}
			}).fail( ( jqXHR, textStatus ) => {
				console.error( `${jqXHR.responseText}: ${jqXHR.status}` );
				console.error( `${textStatus}` );
			});
		}
	};
})( jQuery );

jQuery( document ).ready( () => tmc.init() );
