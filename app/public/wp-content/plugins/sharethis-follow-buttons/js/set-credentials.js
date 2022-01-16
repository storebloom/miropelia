/**
 * Credentials.
 *
 * @package ShareThisFollowButtons
 */

/* exported Credentials */
var Credentials = ( function( $, wp ) {
	'use strict';

	return {
		/**
		 * Holds data.
		 */
		data: {},

		/**
		 * Boot plugin.
		 *
		 * @param data
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
			this.$connection = $( '.sharethis-connection-wrap' );
			this.$createConfig = '';
			this.listen();
			this.loadPreview( 'initial' );
		},

		/**
		 * Listener.
		 */
		listen: function() {
			var self = this,
				timer = '';

			// Create new account.
			this.$connection.on( 'click', '.create-account', function() {
				var email = $( '#st-email' ).val(),
					pw = $( '#st-password' ).val();

				$( '.st-loading-gif' ).fadeIn();

				// Set default WP config.
				wp.ajax.post( 'set_follow_default_settings', {
					nonce: self.data.nonce
				} ).always( function( link ) {
					self.registerAccount( email, pw );
				}.bind( self ) );
			} );

			// Login to account.
			this.$connection.on( 'click', '.login-account', function( e ) {
				e.preventDefault();

				var email = $( '#st-login-email' ).val(),
					pw = $( '#st-login-password' ).val();

				$( '.st-loading-gif' ).fadeIn();

				// Set default WP config.
				wp.ajax.post( 'set_follow_default_settings', {
					nonce: self.data.nonce
				} ).always( function( link ) {
					self.loginAccount( email, pw );
				}.bind( self ) );
			} );

			this.$connection.on( 'click', '#connect-property', function( e ) {
				e.preventDefault();

				var secret = $( '#sharethis-properties option:selected' ).val(),
					property = $( '#sharethis-properties option:selected' ).attr( 'data-prop' ),
					token = $( '#st-user-cred' ).val(),
					config = $( '#sharethis-properties option:selected' ).attr( 'data-config' ).replace( /'/g, '"' ),
					button = $( '#sharethis-properties option:selected' ).attr( 'data-first' ).replace( '-share-buttons', '' ),
					theData = JSON.stringify( { is_wordpress: true } );

				$( '.st-loading-gif' ).fadeIn();

				wp.ajax.post( 'set_follow_button_config', {
					button: button,
					config: config,
					type: 'login',
					nonce: self.data.nonce
				} ).always( function() {
					$.ajax( {
						url: 'https://platform-api.sharethis.com/v1.0/property/?id=' + property + '&token=' + token,
						method: 'PUT',
						async: false,
						contentType: 'application/json; charset=utf-8',
						data: theData,
						success: function() {
							self.setCredentials( secret, property, token, 'login' );
						}
					} );
				} );
			} );

			// Create property based on site url.
			this.$connection.on( 'click', '#create-new-property', function( e ) {
				e.preventDefault();

				var secret = $( '#sharethis-properties option:selected' ).val(),
					property = $( '#sharethis-properties option:selected' ).attr( 'data-prop' ),
					token = $( '#st-user-cred' ).val(),
					config = $( '#sharethis-properties option:selected' ).attr( 'data-config' ).replace( /'/g, '"' ),
					button = $( '#sharethis-properties option:selected' ).attr( 'data-first' ).replace( '-share-buttons', '' ),
					theData = JSON.stringify( { is_wordpress: true } );

				$( '.st-loading-gif' ).fadeIn();

				wp.ajax.post( 'set_follow_button_config', {
					button: button,
					config: config,
					type: 'login',
					nonce: self.data.nonce
				} ).always( function( results ) {
					$.ajax( {
						url: 'https://platform-api.sharethis.com/v1.0/property/?id=' + property + '&secret=' + secret,
						method: 'PUT',
						async: false,
						contentType: 'application/json; charset=utf-8',
						data: theData,
						success: function() {
							self.$createConfig = JSON.parse( config );
							self.$createButton = button;
							self.createProperty( token, self.data.url, 'create' );
						}
					} );
				} );
			} );

			// Button alignment.
			this.$connection.on( 'click', '.button-alignment .alignment-button', function() {
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
			this.$connection.on( 'click', '.item div.switch', function() {
				self.loadPreview( '' );
			} );

			// CTA text.
			this.$connection.on( 'keyup', '.cta-text input', function() {
				self.loadPreview( '' );
			} );

			// Minimum count.
			this.$connection.on( 'change', '#radius-selector', function() {
				self.loadPreview( '' );
			} );

			// Add profile to network.
			this.$connection.on( 'keyup', '#st-network-urls .center-align .profile_link', function() {
					clearTimeout( timer );

					timer = setTimeout( function() {
						self.loadPreview( '' );
					}.bind( this ), 1000 );
			} );

			// Select or deselect a network.
			this.$connection.on( 'click', '.follow-buttons .follow-button', function() {
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

			// If register button is clicked. submit button configurations.
			this.$connection.on( 'click', '#sharethis-step-one-wrap .st-rc-link', function() {
				$( '.st-loading-gif' ).fadeIn();
				self.loadPreview( 'submit' );
			} );

			// Tooltip.
			this.$connection.on( 'mouseover', '.tooltip-icon', function() {
				var tooltip = $( this ).attr( 'data-tooltip' ),
					position = $( this ).position(),
					leftPos = position.left + 20,
					topPos = position.top - 20;

				$( '.tooltip-message-over' ).fadeIn( 500 ).html( tooltip ).css( { 'top': topPos + 'px', 'left': leftPos + 'px' } );
			} );

			// Tooltip out.
			this.$connection.on( 'mouseleave', '.tooltip-icon', function() {
				$( '.tooltip-message-over' ).fadeOut( 500 );
			} );
		},

		/**
		 * Send hash data to credential setting.
		 *
		 * @param secret
		 * @param propertyid
		 * @param token
		 * @param type
		 */

		setCredentials: function( secret, propertyid, token, type ) {
			var propSecret = propertyid + '-' + secret;

			// If hash exists send it to credential setting.
			wp.ajax.post( 'set_follow_credentials', {
				data: propSecret,
				token: token,
				nonce: this.data.nonce
			} ).always( function( link ) {
				if ( 'login' !== type ) {
					this.setButtonConfig( secret, propertyid, token, type );
				} else {
					window.location = '?page=sharethis-follow-buttons';
				}
			}.bind( this ) );
		},

		/**
		 * Login to your account.
		 *
		 * @param email
		 * @param pw
		 */
		loginAccount: function( email, pw ) {
			var self = this,
				theData = JSON.stringify( {
					email: email,
					password: pw
				} );

			$.ajax( {
				url: 'https://sso.sharethis.com/login',
				method: 'POST',
				async: false,
				contentType: 'application/json; charset=utf-8',
				data: theData,
				success: function( results ) {
					$( '#st-user-cred' ).val( results.token );

					// Get full info.
					self.getProperty( results.token );
				},
				error: function( xhr, status, error ) {
					var message = xhr.responseJSON.message;

					$( '.st-loading-gif' ).hide();
					$( 'div.error-message' ).html( '' );
					$( '.login-account.st-rc-link' ).after(
						'<div class="error-message" style="text-align: center; margin: 1rem 0;">' +
						message +
						'</div>'
					);
				}
			} );
		},

		/**
		 * Register new account.
		 *
		 * @param email
		 * @param pw
		 */
		registerAccount: function( email, pw ) {
			var result = null,
				self = this,
				url = this.data.url,
				button = this.data.firstButton,
				theData = JSON.stringify( {
					email: email,
					password: pw,
					custom: {
						onboarding_product: 'inline-follow-buttons',
						onboarding_domain: url,
						is_wordpress: true
					}
				} );

			$.ajax( {
				url: 'https://sso.sharethis.com/register',
				method: 'POST',
				async: false,
				contentType: 'application/json; charset=utf-8',
				data: theData,
				success: function( results ) {
					result = results;

					// Create property.
					self.createProperty( result, url, '' );
				},
				error: function( xhr, status, error ) {
					var message = xhr.responseJSON.message;

					$( '.st-loading-gif' ).hide();
					$( 'div.error-message' ).html( '' );
					$( '.sharethis-account-creation small' ).after(
						'<div class="error-message" style="text-align: center; margin: 1rem 0;">' +
						message +
						'</div>'
					);
				}
			} );
		},

		/**
		 * Create property for new account.
		 *
		 * @param accountInfo
		 * @param url
		 */
		createProperty: function( accountInfo, url, type ) {
			var result = null,
				self = this,
				token = accountInfo.token,
				button = this.data.firstButton,
				theData;

			if ( 'string' === typeof accountInfo ) {
				token = accountInfo;
			}

			theData = JSON.stringify( {
				token: token,
				product: 'inline-follow-buttons',
				domain: url,
				is_wordpress: true
			} );

			$.ajax( {
				url: 'https://platform-api.sharethis.com/v1.0/property',
				method: 'POST',
				async: false,
				contentType: 'application/json; charset=utf-8',
				data: theData,
				success: function( results ) {
					result = results;

					self.setCredentials( result.secret, result._id, token, type );
				}
			} );
		},

		/**
		 * Load preview buttons.
		 *
		 * @param type
		 */
		loadPreview: function( type ) {
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

			if ( 'initial' === type ) {
				$( '.follow-buttons .follow-button[data-selected="true"]' ).each( function( index ) {
					follows[ index ] = $( this ).attr( 'data-network' );
				} );
			} else {
				$( '.sharethis-selected-networks > div .st-btn' ).each( function( index ) {
					follows[ index ] = $( this ).attr( 'data-network' );
				} );
			}

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
				enabled: true,
				profiles: profiles,
				fade_in: false
			};

			if ( 'submit' === type ) {
				wp.ajax.post( 'set_follow_button_config', {
					button: 'inline-follow',
					config: config,
					nonce: this.data.nonce
				} ).always( function( results ) {
					window.location.href = '?page=sharethis-general&s=2';
				} );
			} else {
				$( '.sharethis-inline-follow-buttons' ).html( '' );

				window.__sharethis__.href = 'https://www.sharethis.com/';
				window.__sharethis__.load( 'inline-follow-buttons', config );

				$( '.sharethis-selected-networks > div' ).sortable( {
					stop: function( event, ui ) {
						self.loadPreview( '' );
					}
				} );
			}
		},

		/**
		 * Get user information and property.
		 *
		 * @param token
		 */
		getProperty: function( token ) {
			$.ajax( {
				url: 'https://platform-api.sharethis.com/v1.0/me?token=' + token,
				method: 'GET',
				async: false,
				contentType: 'application/json; charset=utf-8',
				success: function( result ) {
					$( '#sharethis-login-wrap' ).hide();
					$( '#sharethis-property-select-wrap' ).show();
					$( '#sharethis-properties' ).html( '' );

					$.each( result.properties, function( index, value ) {
						var config = { 'inline-follow': value[ 'inline-follow-buttons' ] },
							firstProduct = value[ 'onboarding_product' ],
							follow = value[ 'inline-follow-buttons' ];

						if ( 'sop' === firstProduct ) {
							firstProduct = 'inline-follow';
						}

						if ( undefined === follow ) {
							firstProduct = 'inline-follow';
							config = {
								'inline-follow': {
									alignment: 'center',
									networks: [ 'facebook', 'twitter', 'instagram', 'youtube' ],
									size: 32,
									radius: 4,
									action_pos: 'top',
									spacing: 8,
									enabled: true
								}
							};
						}
						$( '.st-loading-gif' ).hide();
						$( '#sharethis-properties' ).append( '<option data-first="' + firstProduct + '" data-config="' + JSON.stringify( config ).replace( /"/g, "'" ) + '" data-prop="' + value._id + '" value="' + value.secret + '">' + value.domain + '</option>' );
					} );
				}
			} );
		},

		/**
		 * Set button configurations.
		 */
		setButtonConfig: function( secret, propertyid, token, type ) {
			var button = this.data.firstButton,
				config = this.data.buttonConfig;

			if ( 'create' === type ) {
				config = this.$createConfig;
				button = 'inline-follow';
			}

			// Send new button status value.
			$.ajax( {
				url: 'https://platform-api.sharethis.com/v1.0/property/product',
				method: 'POST',
				async: false,
				contentType: 'application/json; charset=utf-8',
				data: JSON.stringify( {
					'secret': secret,
					'id': propertyid,
					'product': 'inline-follow-buttons',
					'config': config[ button ]
				} )
			} ).always( function( results ) {
				window.location = '?page=sharethis-follow-buttons';
			} );
		}
	};
} )( window.jQuery, window.wp );
