/**
 * Follow Buttons.
 *
 * @package ShareThisFollowButtons
 */

/* exported FollowButtons */
var FollowButtons = ( function( $, wp ) {
	'use strict';

	return {
		/**
		 * Holds data.
		 */
		data: {},

		/**
		 * Boot plugin.
		 */
		boot: function( data ) {
			this.data = data;

			$( document ).ready( function() {
				this.init();
			}.bind( this ) );
		},

		/**
		 * Initialize plugin.
		 */
		init: function() {
			this.$container = $( '.sharethis-wrap' );

			// Get and set current accounts platform configurations to global.
			this.$config = this.getConfig();

			this.listen();
			this.createReset();

			// Check if platform has changed its button config.
			this.checkIfChanged();

			// Check if buttons are enabled or disabled on both ends.
			this.markSelected();

			// Check for non WP Share Buttons.
			this.shareButtonsExists();
		},

		/**
		 * Initiate listeners.
		 */
		listen: function() {
			var self = this,
				timer = '';

			// On off button events.
			this.$container.on( 'click', '.share-on, .share-off', function() {

				// Revert to default color.
				$( this ).closest( 'div' ).find( 'div.label-text' ).css( 'color', '#8d8d8d' );

				// Change the input selected color to white.
				$( this ).find( '.label-text' ).css( 'color', '#ffffff' );

				// If turning on show recommendation notice.
				if ( $( this ).hasClass( 'share-on' ) ) {
					self.postPageTriggered();
				}
			} );

			// Copy text from read only input fields.
			this.$container.on( 'click', '#copy-shortcode, #copy-template', function() {
				self.copyText( $( this ).closest( 'div' ).find( 'input' ) );
			} );

			// Open close options and update platform and WP on off status.
			this.$container.on( 'click', '.enable-buttons .share-on, .enable-buttons .share-off', function() {
				var type = $( this ).find( 'div.label-text' ).html();

				self.updateButtons( 'inline-follow', type, 'click' );
			} );

			// Toggle button menus when arrows are clicked.
			this.$container.on( 'click', 'span.st-arrow', function() {
				var type = $( this ).html();

				self.updateButtons( 'inline-follow', type, '' );
			} );

			// Click reset buttons.
			this.$container.on( 'click', 'p.submit #reset', function() {
				var type = $( this )
					.closest( 'p.submit' )
					.prev()
					.find( '.enable-buttons' )
					.attr( 'id' );

				self.setDefaults( type );
			} );

			// Toggle margin control buttons.
			this.$container.on( 'click', 'button.margin-control-button', function() {
				var status = $( this ).hasClass( 'active-margin' );

				self.activateMargin( this, status );
			} );

			// Button alignment.
			this.$container.on( 'click', '.button-alignment .alignment-button', function() {
				$( '.button-alignment .alignment-button[data-selected="true"]' )
					.attr( 'data-selected', 'false' );
				$( this ).attr( 'data-selected', 'true' );

				self.loadPreview( '' );
			} );

			$( 'body' ).on( 'click', '.item label', function() {
				var checked = $( this ).siblings( 'input' ).is( ':checked' );

				$( '.sharethis-inline-follow-buttons' ).removeClass( 'st-has-labels' );

				if ( ! checked ) {
					$( this ).closest( '.st-radio-config' ).find( '.item' ).each( function() {
						$( this ).find( 'input' ).prop( 'checked', false );
					} );

					$( this ).siblings( 'input' ).prop( 'checked', true );
				}

				self.loadPreview( '' );
			} );

			// All levers.
			this.$container.on( 'click', '.item div.switch', function() {
				self.loadPreview( '' );
			} );

			// CTA text.
			this.$container.on( 'keyup', '.cta-text input', function() {
				self.loadPreview( '' );
			} );

			// Minimum count.
			this.$container.on( 'change', '#radius-selector', function() {
				self.loadPreview( '' );
			} );

			// Add profile to network.
			this.$container.on( 'keyup', '#st-network-urls .center-align .profile_link', function() {
				clearTimeout( timer );

				timer = setTimeout( function() {
					self.loadPreview( '' );
				}.bind( this ), 1000 );
			} );

			// Select or deselect a network.
			this.$container.on( 'click', '.follow-buttons .follow-button', function() {
				var selection = $( this ).attr( 'data-selected' ),
					follow = $( this ).attr( 'data-network' );

				if ( 'true' === selection ) {
					$( this ).attr( 'data-selected', 'false' );
					$( '.center-align[data-network="' + follow + '"]' ).attr( 'data-selected', 'false' );
					$( '.sharethis-selected-networks > div > div[data-network="' + follow + '"]' ).remove();
				} else {
					$( this ).attr( 'data-selected', 'true' );
					$( '.center-align[data-network="' + follow + '"]' ).attr( 'data-selected', 'true' );
					$( '.sharethis-selected-networks > div' ).append( '<div class="st-btn" data-network="' + follow + '" style="display: inline-block;"></div>' );
				}

				self.loadPreview( '' );
			} );

			// Add class to preview when scrolled to.
			$( window ).on( 'scroll', function() {
				if ( undefined === $( '.selected-button' ).offset() ) {
					return;
				}

				var stickyTop = $( '.selected-button' ).offset().top;

				if ( $( window ).scrollTop() >= stickyTop ) {
					$( '.sharethis-selected-networks' ).addClass( 'sharethis-prev-stick' );
				} else {
					$( '.sharethis-selected-networks' ).removeClass( 'sharethis-prev-stick' );
				}
			} );

			// Submit configurations.
			$( '.sharethis-wrap form' ).submit( function() {
				self.loadPreview( 'submit', 'inline-follow' );
			} );

			// Tooltip.
			this.$container.on( 'mouseover', '.tooltip-icon', function() {
				var tooltip = $( this ).attr( 'data-tooltip' ),
					position = $( this ).position(),
					leftPos = position.left + 20,
					topPos = position.top - 20;

				$( '.tooltip-message-over' ).fadeIn( 500 ).html( tooltip ).css( { 'top': topPos + 'px', 'left': leftPos + 'px' } );
			} );

			// Tooltip out.
			this.$container.on( 'mouseleave', '.tooltip-icon', function() {
				$( '.tooltip-message-over' ).fadeOut( 500 );
			} );

			// Close notice.
			$( 'body' ).on( 'click', '.notice-dismiss', function() {
				$( this ).parent( '.notice.is-dismissible' ).hide();
			} );
		},

		/**
		 * Change font color of selected buttons.
		 * Also decide whether to update WP enable / disable status or just show / hide menu options.
		 */
		markSelected: function() {
			var iConfigSet = null !== this.$config && undefined !== this.$config[ 'inline-follow-buttons' ],
				iturnOn,
				iturnOff,
				inlineFollowEnable;

			// Check if api call is successful and if inline buttons are enabled.  Use WP data base if not.
			if ( iConfigSet ) {
				inlineFollowEnable = this.$config[ 'inline-follow-buttons' ][ 'enabled' ]; // Dot notation cannot be used due to dashes in name.
			} else {
				if ( undefined !== this.data.buttonConfig[ 'inline-follow' ] ) {
					inlineFollowEnable = this.data.buttonConfig[ 'inline-follow' ][ 'enabled' ];
				}
			}

			// Decide whether to update WP database or just show / hide menu options.
			if ( ! iConfigSet || (
					undefined !== this.data.buttonConfig[ 'inline-follow' ] && this.data.buttonConfig[ 'inline-follow' ][ 'enabled' ] === this.$config[ 'inline-follow-buttons' ][ 'enabled' ] ) ) { // Dot notation cannot be used due to dashes in name.
				iturnOn = 'show';
				iturnOff = 'hide';
			} else {
				iturnOn = 'On';
				iturnOff = 'Off';
			}

			// If enabled show button configuration.
			if ( 'true' === inlineFollowEnable || true === inlineFollowEnable ) {
				$( '.inline-follow-platform' ).css( 'display', 'table-footer-group' );
				this.updateButtons( 'inline-follow', iturnOn );
				$( '#inline-follow label.share-on input' ).prop( 'checked', true );
			} else {
				$( '.inline-follow-platform' ).hide();
				this.updateButtons( 'inline-follow', iturnOff );
				$( '#inline-follow label.share-off input' ).prop( 'checked', true );
			}

			// Change button font color based on status.
			$( '.share-on input:checked, .share-off input:checked' ).closest( 'label' ).find( 'span.label-text' ).css( 'color', '#ffffff' );
		},

		/**
		 * Check the platform has updated the button configs.
		 */
		checkIfChanged: function() {
			var iTs = this.$config[ 'inline-follow-buttons' ],
				myITs = this.data.buttonConfig[ 'inline-follow' ];

			// Set variables if array exists.
			if ( undefined !== iTs ) {
				iTs = iTs[ 'updated_at' ];

				if ( undefined !== iTs ) {
					iTs = iTs.toString();
				}
			}

			if ( undefined !== myITs ) {
				myITs = myITs[ 'updated_at' ];
			}

			// If platform has updated the button config or platform configs are broken use WP config.
			if ( iTs !== myITs || undefined === this.data.buttonConfig ) {
				this.setConfigFields( 'inline-follow', this.$config[ 'inline-follow-buttons' ], 'platform' );
			} else {
				this.loadPreview( 'initial', '' );
			}
		},

		/**
		 * Show button configuration.
		 *
		 * @param button
		 * @param type
		 * @param event
		 */
		updateButtons: function( button, type, event ) {
			var pTypes = [ 'show', 'On', '►', 'true' ],
				aTypes = [ 'show', 'hide', '►', '▼' ],
				timer = '';

			// If not one of the show types then hide.
			if ( -1 !== $.inArray( type, pTypes ) ) {

				// Show the button configs.
				$( '.sharethis-wrap form .form-table tr' ).not( ':eq(0)' ).show();

				// Show the submit / reset buttons.
				$( '.sharethis-wrap form .submit' ).show();

				// Change the icon next to title.
				$( '.sharethis-wrap h2 span' ).html( '&#9660;' );

				// Platform config.
				$( '.inline-follow-platform' ).css( 'display', 'table-footer-group' );

				if ( 'click' === event ) {
					this.loadPreview( 'turnon', button );
				}

				// Set option value for button.
				wp.ajax.post( 'update_buttons', {
					type: button.toLowerCase(),
					onoff: 'On',
					nonce: this.data.nonce
				} ).always( function() {
				} );
			} else {

				// Hide the button configs.
				$( '.sharethis-wrap form .form-table tr' ).not( ':eq(0)' ).hide();

				// Hide the submit / reset buttons.
				$( '.sharethis-wrap form .submit' ).hide();

				// Change the icon next to title.
				$( '.sharethis-wrap h2 span' ).html( '&#9658;' );

				// Platform config.
				$( '.inline-follow-platform' ).hide();

				if ( 'click' === event ) {
					this.loadPreview( 'turnoff', 'inline-follow' );
				}

				// Set option value for button.
				wp.ajax.post( 'update_buttons', {
					type: button.toLowerCase(),
					onoff: 'Off',
					nonce: this.data.nonce
				} ).always( function() {
				} );
			}
		},

		/**
		 * Copy text to clipboard
		 *
		 * @param copiedText
		 */
		copyText: function( copiedText ) {
			copiedText.select();
			document.execCommand( 'copy' );
		},

		/**
		 * Add the reset buttons to share buttons menu
		 */
		createReset: function() {
			var button = '<input type="button" id="reset" class="button button-primary" value="Reset">',
				newButtons = $( '.sharethis-wrap form .submit' ).append( button ).clone();
		},

		/**
		 * Set to default settings when reset is clicked.
		 *
		 * @param type
		 */
		setDefaults: function( type ) {
			wp.ajax.post( 'set_follow_default_settings', {
				type: type,
				nonce: this.data.nonce
			} ).always( function() {
				if ( 'both' !== type ) {
					location.href = location.pathname + '?page=sharethis-follow-buttons&reset=' + type;
				} else {
					location.reload();
				}
			} );
		},

		/**
		 * Get current config data from user.
		 */
		getConfig: function() {
			var result = null,
				callExtra = 'secret=' + this.data.secret;

			if ( 'undefined' === this.data.secret || undefined === this.data.secret ) {
				callExtra = 'token=' + this.data.token;
			}

			$.ajax( {
				url: 'https://platform-api.sharethis.com/v1.0/property/?' + callExtra + '&id=' + this.data.propertyid,
				method: 'GET',
				async: false,
				contentType: 'application/json; charset=utf-8',
				success: function( results ) {
					result = results;
				}
			} );

			return result;
		},

		/**
		 * Activate specified option margin controls and show/hide
		 *
		 * @param marginButton
		 * @param status
		 */
		activateMargin: function( marginButton, status ) {
			if ( ! status ) {
				$( marginButton ).addClass( 'active-margin' ).find( 'span.margin-on-off' ).html( 'On' );
				$( marginButton ).siblings( 'div.margin-input-fields' ).show().find( 'input' ).prop( 'disabled', false );
			} else {
				$( marginButton ).removeClass( 'active-margin' ).find( 'span.margin-on-off' ).html( 'Off' );
				$( marginButton ).siblings( 'div.margin-input-fields' ).hide().find( 'input' ).prop( 'disabled', true );
			}
		},

		/**
		 * Set the settings fields for the button configurations.
		 *
		 * @param button
		 */
		setConfigFields: function( button, config, type ) {
			var size,
				button = 'inline-follow';

			if ( '' === config ) {
				config = this.data.buttonConfig[ button ];
			}

			if ( undefined === config ) {
				return;
			}

			$( '.follow-buttons .follow-button' ).each( function() {
				$( this ).attr( 'data-selected', false );
			} );

			// Follows.
			$.each( config[ 'networks' ], function( index, value ) {
				$( '.follow-buttons .follow-button[data-network="' + value + '"]' ).attr( 'data-selected', 'true' );
				$( '#st-network-urls .center-align[data-network="' + value + '"]' ).attr( 'data-selected', 'true' );
			} );

			// Alignment.
			$( '.button-alignment .alignment-button[data-selected="true"]' ).attr( 'data-selected', 'false' );
			$( '.button-alignment .alignment-button[data-alignment="' + config[ 'alignment' ] + '"]' ).attr( 'data-selected', 'true' );

			// Label Position.
			$( '.label-position .item input' ).prop( 'checked', false );
			$( '.label-position #' + config['action_pos'] ).siblings( 'input' ).prop( 'checked', true );

			// Profiles.
			$.each( config.profiles, function( name, value ) {
				$( '#st-network-urls .center-align[data-network="' + name + '"]' ).find( 'input.profile_link' ).val( value );
			} );

			// CTA.
			$( 'div.call-to-action' ).find( 'input' ).prop( 'checked', ( 'false' !== config['action_enable'] && false !== config['action_enable'] ) );
			$( '.cta-text input' ).val( config.action );

			// Corners.
			if ( parseInt( config.radius.toString().replace( 'px', '' ) ) > $( '#' + button + ' #radius-selector' ).attr( 'max' ) ) {
				$( '#' + button + ' #radius-selector' ).attr( 'max', config.radius.toString().replace( 'px', '' ) );
				$( '#' + button + ' #radius-selector' ).val( config.radius.toString().replace( 'px', '' ) );
			} else {
				$( '#' + button + ' #radius-selector' ).val( config.radius.toString().replace( 'px', '' ) );
			}

			// Size.
			$( '.button-size .item input' ).prop( 'checked', false );

			if ( '32' === config.size.toString() ) {
				size = '#small';
			}

			if ( '40' === config.size.toString() ) {
				size = '#medium';
			}

			if ( '48' === config.size.toString() ) {
				size = '#large';
			}

			$( '.button-size ' + size ).siblings( 'input' ).prop( 'checked', true );

			// Extra spacing.
			$( 'div.extra-spacing' ).find( 'input' ).prop( 'checked', ( 0 !== config.spacing && '0' !== config.spacing ) );

			if ( 'platform' === type ) {
				this.loadPreview( 'initial-platform', button );
			}
		},

		/**
		 * Check if share buttons are active and plugin doesn't exist.
		 */
		shareButtonsExists: function() {
			var needPlugin = ( ( undefined !== this.$config[ 'inline-share-buttons' ] || undefined !== this.$config[ 'sticky-share-buttons' ] ) && false === this.data.shareButtons );

			if ( needPlugin ) {
				this.$container.before(
					'<div class="notice notice-error is-dismissible">' +
					'<p>' +
					'It appears you have share buttons enabled in your account, but do not have the ' +
					'<strong>' +
					'ShareThis Share Buttons' +
					'</strong>' +
					' WordPress plugin installed or activated!' +
					'</p>' +
					'<p>' +
					'Please go here: ' +
					'<a href="https://wordpress.org/plugins/sharethis-share-buttons/" target="_blank">' +
					'https://wordpress.org/plugins/sharethis-share-buttons/' +
					'</a>' +
					' to download our plugin and utilize our Share Buttons with the power of WordPress!' +
					'</p>' +
					'</div>'
				);
			}
		},

		/**
		 * Check if share buttons are active and plugin doesn't exist.
		 */
		postPageTriggered: function() {
			if ( 0 === $( '.notice.follow-notice' ).length ) {
				this.$container.before(
					'<div class="notice notice-info is-dismissible follow-notice">' +
					'<p>' +
					'We recommending adding Follow Buttons to the header, footer or if available your sidebars if you are also using Share Buttons.  You can do this with our' +
					' <a href="' + this.data.url + '/wp-admin/widgets.php">' +
					'Widget' +
					'</a>' +
					', Shortcode, or Template code.' +
					'</p>' +
					'<button type="button" class="notice-dismiss"><span class="screen-reader-text">Dismiss this notice.</span></button>' +
					'</div>'
				);
			}
		},

		/**
		 * Load preview buttons.
		 *
		 * @param type
		 * @param button
		 */
		loadPreview: function( type, button ) {
			if ( 'initial' === type ) {
				this.setConfigFields( 'inline-follow', '', '' );
			}

			var bAlignment = $( '.button-alignment .alignment-button[data-selected="true"]' ).attr( 'data-alignment' ),
				self = this,
				bSize = $( '.button-size .item input:checked' ).siblings( 'label' ).html(),
				actionCopy = $( '.cta-text input' ).val(),
				enableAction = $( '.cta-on-off' )
					.find( 'input' )
					.is( ':checked' ),
				extraSpacing = $( '.extra-spacing' )
					.find( 'input' )
					.is( ':checked' ),
				bRadius = $( '#radius-selector' ).val() + 'px',
				lPosition = $( '.label-position .item input:checked' ).siblings( 'label' ).html(),
				follows = [],
				size,
				spacing = 0,
				padding,
				fontSize,
				config,
				beforeConfig,
				theFirst = false,
				wpConfig,
				timer = '',
				upConfig,
				theData,
				enabled = false,
				networkName,
				networkProfile,
				profiles = {};

			if ( undefined !== lPosition ) {
				lPosition = lPosition.toLowerCase();
			}

			$( '#st-network-urls .center-align[data-selected="true"]' ).each( function( index ) {
				networkName = $( this ).attr( 'data-network' );
				networkProfile = $( this ).find( '.profile_link' ).val();
				profiles[ networkName ] = networkProfile;
			} );

			if ( 'Small' === bSize ) {
				size = 32;
				fontSize = 11;
				padding = 8;

				$( '#radius-selector' ).attr( 'max', 16 );
			}

			if ( 'Medium' === bSize ) {
				size = 40;
				fontSize = 12;
				padding = 10;

				$( '#radius-selector' ).attr( 'max', 20 );
			}

			if ( 'Large' === bSize ) {
				size = 48;
				fontSize = 16;
				padding = 12;

				$( '#radius-selector' ).attr( 'max', 26 );
			}

			if ( extraSpacing ) {
				spacing = 8;
			}

			if ( 'initial' === type && undefined !== this.data.buttonConfig[ 'inline-follow' ][ 'networks' ] ) {
				follows = this.data.buttonConfig[ 'inline-follow' ][ 'networks' ];
			} else {
				$( '.sharethis-selected-networks > div .st-btn' ).each( function( index ) {
					follows[ index ] = $( this ).attr( 'data-network' );
				} );
			}

			if ( 'sync-platform' === type && undefined !== this.$config[ 'inline-follow-buttons' ] ) {
				follows = this.$config[ 'inline-follow-buttons' ][ 'networks' ];
			}

			// If newly turned on use selected networks.
			if ( 'turnon' === type || undefined !== this.data.buttonConfig[ button ] && undefined === this.data.buttonConfig[ button ]['networks'] ) {
				follows = [];

				$( '.follow-buttons .follow-button[data-selected="true"]' ).each( function( index ) {
					follows[ index ] = $( this ).attr( 'data-network' );
				} );
			}

			if ( 'submit' === type ) {
				follows = [];

				$( '.sharethis-selected-networks > div .st-btn' ).each( function( index ) {
					follows[ index ] = $( this ).attr( 'data-network' );
				} );
			}

			// If submited or turned on make sure enabled setting is set properly.
			if ( undefined !== this.$config[ 'inline-follow-buttons' ] && undefined !== this.$config[ 'inline-follow-buttons' ][ 'enabled' ] ) {
				enabled = 'true' === this.$config[ 'inline-follow-buttons' ][ 'enabled' ] ||
						  true === this.$config[ 'inline-follow-buttons' ][ 'enabled' ] ||
						  true === this.$tempEnable;
			} else {
				enabled = false;
			}

			config = {
				action: actionCopy,
				action_enable: enableAction,
				alignment: bAlignment,
				networks: follows,
				size: size,
				radius: bRadius,
				padding: padding,
				action_pos: lPosition,
				fontsize: fontSize,
				spacing: spacing,
				enabled: enabled,
				profiles: profiles,
				fade_in: false
			};

			// Set config for initial post.
			beforeConfig = config;

			if ( 'submit' === type || 'initial-platform' === type || 'turnon' === type || 'turnoff' === type ) {

				// If submiting WP keep platform timestamp if exists.
				if ( 'submit' === type && undefined !== this.$config[ 'inline-follow-buttons' ] && undefined !== this.$config[ 'inline-follow-buttons' ][ 'updated_at' ] ) {
					config[ 'updated_at' ] = this.$config[ 'inline-follow-buttons' ][ 'updated_at' ];
				}

				// If platform different from WP.
				if ( 'initial-platform' === type ) {
					config = this.$config[ 'inline-follow-buttons' ];

					if ( undefined === this.data.buttonConfig || true === this.data.buttonConfig ) {
						theFirst = 'upgrade';
					}
				}

				// If first load ever.
				if ( 'initial-platform' === type && undefined !== this.data.buttonConfig[ 'inline-follow' ] && undefined === this.data.buttonConfig[ 'inline-follow' ][ 'updated_at' ] && undefined !== this.$config[ 'inline-follow-buttons' ][ 'updated_at' ] ) {
					config = beforeConfig;
					config.updated_at = this.$config[ 'inline-follow-buttons' ][ 'updated_at' ];
					config.networks = this.data.buttonConfig[ 'inline-follow' ][ 'networks' ];
				}

				if ( 'turnon' === type ) {
					config.enabled = true;
					config.networks = [ 'facebook', 'twitter', 'instagram', 'youtube' ];

					$.each( config.networks, function( index, value ) {
						$( '.follow-buttons .follow-button[data-selected="' + value + '"]' ).attr( 'data-selected', 'true' );
					} );

					// Set temp enable to true.
					this.$tempEnable = true;
				}

				if ( 'turnoff' === type ) {
					config.enabled = false;

					// Set temp enable to false.
					this.$tempEnable = false;
				}

				if ( 'upgrade' === theFirst ) {
					upConfig = {
						'inline-follow': this.$config[ 'inline-follow-buttons' ]
					};

					wp.ajax.post( 'set_follow_button_config', {
						button: 'platform',
						config: upConfig,
						first: theFirst,
						type: 'login',
						nonce: this.data.nonce
					} ).always( function( results ) {
						location.reload();
					}.bind( this ) );
				} else {
					wp.ajax.post( 'set_follow_button_config', {
						button: button,
						config: config,
						first: false,
						nonce: this.data.nonce
					} ).always( function( results ) {

						if ( 'initial-platform' !== type || (
								undefined !== this.data.buttonConfig[ button ] && undefined === this.data.buttonConfig[ button ][ 'updated_at' ]
							) ) {
							config.enabled = 'true' === config.enabled || true === config.enabled;

							delete config.container;
							delete config.id;
							delete config[ 'has_spacing' ];
							delete config[ 'fade_in' ];
							delete config[ 'show_mobile_buttons' ];

							theData = JSON.stringify( {
								'secret': this.data.secret,
								'id': this.data.propertyid,
								'product': 'inline-follow-buttons',
								'config': config
							} );

							// Send new button status value.
							$.ajax( {
								url: 'https://platform-api.sharethis.com/v1.0/property/product',
								method: 'POST',
								async: false,
								contentType: 'application/json; charset=utf-8',
								data: theData,
								success: function() {
									if ( 'turnon' === type ) {
										location.reload();
									}
								}
							} );
						}
					}.bind( this ) );
				}
			}

			$( '.sharethis-inline-follow-buttons' ).html( '' );

			window.__sharethis__.href = 'https://www.sharethis.com/';
			window.__sharethis__.load( 'inline-follow-buttons', config );

			$( '.sharethis-selected-networks > div .st-btn' ).show();

			$( '.sharethis-selected-networks > div' ).sortable( {
				stop: function( event, ui ) {
					self.loadPreview( '' );
				}
			} );
		}
	};
} )( window.jQuery, window.wp );
