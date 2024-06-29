/**
 * Admin functions
 *
 * @since 2.0.0
 */
( function( wpQuizAdmin, $ ) {
	"use strict";

	wpQuizAdmin.helpers = wpQuizAdmin.helpers || {};

	/**
	 * Gets random string.
	 *
	 * @param {Number} length The length of string.
	 * @returns {String}
	 */
	wpQuizAdmin.helpers.getRandomString = length => {
		if ( ! length ) {
			length = 5;
		}

		let str = '';
		const possible = 'abcdefghijklmnopqrstuvwxyz0123456789';

		for ( let i = 0; i < length; i++ ) {
			str += possible.charAt( Math.floor( Math.random() * possible.length ) );
		}

		return str;
	};

	/**
	 * Gets REST API request.
	 *
	 * @param {Object} options jQuery.ajax() options.
	 * @return {Object}        jQuery.ajax() request object.
	 */
	wpQuizAdmin.helpers.getRequest = options => {
		if ( ! options.beforeSend ) {
			options.beforeSend = xhr => {
				xhr.setRequestHeader( 'X-WP-Nonce', wpQuizAdmin.restNonce );
			};
		}
		return $.ajax( options );
	};

	wpQuizAdmin.helpers.reinitCodemirror = textarea => {
		const $textarea = $( textarea );
		$textarea.next( '.CodeMirror' ).remove();
		const editor = wp.codeEditor.initialize(
			$textarea.attr( 'id' ),
			CMB2.codeEditorArgs( $textarea.data( 'codeeditor' ) )
		);
		editor.codemirror.on( 'change', ev => editor.codemirror.save() );
	};

	const functions = {
		// Settings Tabs
		tabs: function() {
			var settingTabWrapper = $( '.wp-quiz-tab-wrapper' );
			if ( ! settingTabWrapper.length ) {
				return;
			}

			settingTabWrapper.each( function() {
				var wrapper = $( this ),
					container = wrapper.parent(),
					nav = wrapper.find( '> a' ),
					panels = container.find( '> .wp-quiz-tab-content-wrapper > .wp-quiz-setting-panel' ),
					activeClass = wrapper.data( 'active-class' ) || 'active',
					target = $( '#message.updated' ).length ? localStorage.getItem( container.attr( 'id' ) ) : null;

				if ( $( '#auto_draft' ).length ) {
					target = '';
				}

				// Click Event
				nav.on( 'click', function() {
					var $this = $( this ),
						target = $this.attr( 'href' );

					nav.removeClass( activeClass );
					panels.hide();

					$this.addClass( activeClass );
					$( target ).show();

					// Save in localStorage
					localStorage.setItem( container.attr( 'id' ), target );

					$( document ).trigger( 'wp-quiz-activated-tab', $this );

					return false;
				});

				if ( null === target ) {
					nav.eq( 0 ).trigger( 'click' );
				} else {
					target = $( 'a[href="' + target + '"]', wrapper );
					if ( target.length ) {
						target.trigger( 'click' );
					} else {
						nav.eq( 0 ).trigger( 'click' );
					}
				}

				// Set min height
				settingTabWrapper.next().css( 'min-height', wrapper.outerHeight() );
			});

			settingTabWrapper.on( 'click', '> .button-primary', function() {
				$( '.cmb-form > .button-primary' ).trigger( 'click' );
				return false;
			});
		},

		dependencyManager: function() {
			var self = this;

			// Group correction
			var elem = $( '.cmb-form, .wp-quiz-meta-box-wrap' );
			$( '.cmb-repeat-group-wrap', elem ).each( function() {
				var $this = $( this ),
					dep = $this.next( '.wp-quiz-cmb-dependency.hidden' );

				if ( dep.length ) {
					$this.find( '> .cmb-td' ).append( dep );
				}
			});

			$( '.wp-quiz-cmb-dependency', elem ).each( function() {
				self.loopDependencies( $( this ) );
			});

			$( 'input, select', elem ).on( 'change', function() {
				var fieldName = $( this ).attr( 'name' );

				$( 'span[data-field="' + fieldName + '"]' ).each( function() {
					self.loopDependencies( $( this ).closest( '.wp-quiz-cmb-dependency' ) );
				});
			});
		},

		checkDependency: function( currentValue, desiredValue, comparison ) {

			// Multiple values
			if ( 'string' === typeof desiredValue && desiredValue.includes( ',' ) && '=' === comparison ) {
				return desiredValue.includes( currentValue );
			}
			if ( 'string' === typeof desiredValue && desiredValue.includes( ',' ) && '!=' === comparison ) {
				return ! desiredValue.includes( currentValue );
			}
			if ( '=' === comparison && currentValue === desiredValue ) {
				return true;
			}
			if ( '==' === comparison && currentValue === desiredValue ) {
				return true;
			}
			if ( '>=' === comparison && currentValue >= desiredValue ) {
				return true;
			}
			if ( '<=' === comparison && currentValue <= desiredValue ) {
				return true;
			}
			if ( '>' === comparison && currentValue > desiredValue ) {
				return true;
			}
			if ( '<' === comparison && currentValue < desiredValue ) {
				return true;
			}
			if ( '!=' === comparison && currentValue !== desiredValue ) {
				return true;
			}

			return false;
		},

		loopDependencies: function( $container ) {
			var self     = this,
				relation = $container.attr( 'data-relation' ),
				passed;

			$container.find( 'span' ).each( function() {

				var $this      = $( this ),
					value      = $this.attr( 'data-value' ),
					comparison = $this.attr( 'data-comparison' ),
					field      = $( '[name=\'' + $this.attr( 'data-field' ) + '\']' ),
					fieldValue = field.val();

				if ( field.is( ':radio' ) ) {
					fieldValue = field.filter( ':checked' ).val();
				}

				if ( field.is( ':checkbox' ) ) {
					fieldValue = field.is( ':checked' );
				}

				var result = self.checkDependency( fieldValue, value, comparison );

				if ( 'or' === relation && result ) {
					passed = true;
					return false;
				} else if ( 'and' === relation ) {

					if ( undefined === passed ) {
						passed = result;
					} else {
						passed = passed && result;
					}
				}
			});

			var hideMe = $container.closest( '.wp-quiz-cmb-group' );

			if ( ! hideMe.length ) {
				hideMe = $container.closest( '.cmb-row:not(.cmb-repeat-row):not(.empty-row)' );
			}

			if ( passed ) {
				hideMe.slideDown( 300 );
			} else {
				hideMe.hide();
			}
		},

		dismissNotices: function() {
			$( document ).on( 'click', '.wp-quiz-notice .notice-dismiss', function( ev ) {
				const $notice = $( this ).closest( '.wp-quiz-notice' );
				const optionName = $notice.attr( 'data-option-name' );
				$.ajax({
					url: ajaxurl,
					type: 'post',
					data: {
						action: 'wp_quiz_dismiss_notice',
						option: optionName
					}
				});
			});
		},

		fixTextareaCode: function() {
			// When add ad code.
			$( '.cmb-repeat-table' ).each( ( index, element ) => {
				$( element ).on( 'cmb2_add_row', ( ev, $row ) => {
					const textareaCode = $row.prev( '.cmb-repeat-row' ).find( '.cmb2-textarea-code' );
					if ( ! textareaCode.length ) {
						return;
					}
					wpQuizAdmin.helpers.reinitCodemirror( textareaCode );
				})
			});
		},

		showPlayerTrackingDetail: function() {
			$( document ).on( 'click', '.wp-quiz-toggle-player-tracking-data', ev => {
				ev.preventDefault();
				const id = ev.currentTarget.dataset.id;
				const $detail = $( ev.currentTarget ).next( '.wp-quiz-tracking' );
				const $tr = $( ev.currentTarget ).closest( 'tr' );
				if ( ! $tr.next( `.tr-player-tracking-${id}` ).length ) {
					$tr.after( $( `<tr class="tr-player-tracking-${id}">` ).append( $( `<td colspan="${ $tr.children( 'td' ).length + 1 }">` ).append( $detail ) ) );
				} else {
					$tr.next( `.tr-player-tracking-${id}` ).toggle();
				}
			});
		},

		selectAll: function() {
			$( '#selectall' ).on( 'change', ev => {
				$( ev.currentTarget ).closest( 'table' ).find( ':checkbox' ).prop( 'checked', ev.currentTarget.checked );
			});
		},

		importQuizzes: function() {
			const total = parseInt( $( '#import-total' ).text() );

			// Get import progress.
			const getProgress = () => {
				const url = wpQuizAdmin.restUrl + 'wp-quiz/v2/admin/import-quizzes-progress';
				const request = wpQuizAdmin.helpers.getRequest({
					url: url,
					method: 'GET'
				});

				request.done( response => {
					$( '#import-done' ).text( total - response.remain );
					if ( response.remain > 0 ) {
						getProgress();
					} else {
						$( '#wq-import-progress' ).fadeOut( 'fast', () => {
							$( '#wq-import-done' ).fadeIn( 'fast' );
						});
					}
				});

				request.fail( response => {
					console.error( response );
				});
			};

			// Send import request.
			const handleSubmit = ev => {
				ev.preventDefault();
				const form = $( ev.currentTarget );
				form.find( '.spinner' ).css( 'visibility', 'visible' );
				form.find( ':submit' ).prop( 'disabled', true );
				const data = {
					quizzes: window.wqImportQuizzes.quizzes,
					download_images: $( '#wq-download-images' ).prop( 'checked' ),
					force_new: $( '#wq-force-new-quizzes' ).prop( 'checked' )
				};
				const url = wpQuizAdmin.restUrl + 'wp-quiz/v2/admin/import-quizzes';
				const request = wpQuizAdmin.helpers.getRequest({
					url: url,
					method: 'POST',
					data: data
				});

				request.done( response => {
					getProgress();
					form.fadeOut( 'fast', () => {
						$( '#wq-import-progress' ).fadeIn( 'fast' );
					});
				});

				request.fail( response => {
					console.error( response );
				});
			};

			$( document ).on( 'submit', '#wq-import-options', ev => handleSubmit( ev ) );
		},

		aweberOptions: function() {
			const $fields = $( '.cmb-type-aweber' );
			if ( ! $fields.length ) {
				return;
			}

			const getAuthUrl = appId => {
				return `https://auth.aweber.com/1.0/oauth/authorize_app/${appId}`;
			};

			$fields.each( ( index, el ) => {
				const $el = $( el );
				const optionId = $el.find( '.aweber-wrapper' ).attr( 'data-option-id' );
				const appIdInput = $el.find( '.aweber-app-id' );
				const getAuthCodeButton = $el.find( '.aweber-get-auth-code-button' );
				const authButton = $el.find( '.aweber-auth-button' );
				const authCodeInput = $el.find( '.aweber-auth-code' );
				const disconnectButton = $el.find( '.aweber-disconnect-button' );

				const handleChangeAppId = ev => {
					const appId = ev.currentTarget.value.trim();
					getAuthCodeButton.attr( 'href', getAuthUrl( appId ) );
				};

				const handleClickAuthLink = ev => {
					if ( ! appIdInput.val() ) {
						alert( wpQuizAdmin.i18n.appIdMustNotEmpty );
						appIdInput.focus();
						ev.preventDefault();
					}

					$el.find( '.aweber-auth-code-step' ).fadeIn( 'fast' );
				};

				const handleClickAuthButton = ev => {
					const authCode = authCodeInput.val();
					ev.currentTarget.disabled = true;
					if ( ! authCode ) {
						alert( wpQuizAdmin.i18n.authCodeMustNotEmpty );
						authCodeInput.focus();
						return;
					}

					const url = wpQuizAdmin.restUrl + 'wp-quiz/v2/admin/connect-aweber';
					const request = wpQuizAdmin.helpers.getRequest({
						url: url,
						method: 'POST',
						data: {
							auth_code: authCode,
							option_id: optionId
						}
					});

					request.done( response => {
						if ( ! response.success ) {
							console.log( response.data );
							return;
						}

						Object.keys( response.data ).forEach( key => {
							$el.find( `.aweber-${key}` ).val( response.data[ key ] );
						});

						$el.find( '.aweber-app-id-step, .aweber-auth-code-step' ).hide();
						$el.find( '.aweber-list-id-step' ).fadeIn( 'fast' );
					});

					request.fail( response => {
						console.error( response );
					});
				};

				const handleClickDisconnectButton = ev => {
					ev.preventDefault();

					const url = wpQuizAdmin.restUrl + 'wp-quiz/v2/admin/disconnect-aweber';
					const request = wpQuizAdmin.helpers.getRequest({
						url: url,
						method: 'POST',
						data: {
							option_id: optionId
						}
					});

					request.done( response => {
						if ( ! response.success ) {
							return;
						}

						$el.find( 'input[type="hidden"][name]' ).val( '' );

						$el.find( '.aweber-list-id-step, .aweber-auth-code-step' ).hide();
						$el.find( '.aweber-app-id-step' ).fadeIn( 'fast' );
					});

					request.fail( response => {
						console.error( response );
					});
				};

				appIdInput.on( 'change', ev => handleChangeAppId( ev ) );
				getAuthCodeButton.on( 'click', ev => handleClickAuthLink( ev ) );
				authButton.on( 'click', ev => handleClickAuthButton( ev ) );
				disconnectButton.on( 'click', ev => handleClickDisconnectButton( ev ) );
			});
		},

		exportSelectedPlayersAndLeads: function() {
			const exportButton = $( '#wq-export-button' );
			const exportForm = $( '#wq-export-form' );
			const exportIds = $( '#wq-export-ids' );
			const idCheckboxes = $( '#the-list .check-column :checkbox' );

			if ( ! exportForm.length || ! exportIds.length || ! idCheckboxes.length ) {
				return;
			}

			const getSelectedIds = () => {
				const ids = [];
				idCheckboxes.each( ( index, idCheckbox ) => {
					if ( idCheckbox.checked ) {
						ids.push( idCheckbox.value );
					}
				});
				return ids;
			};

			const handleClickExportButton = ev => exportForm.submit();

			const handleChooseItem = ev => {
				const ids = getSelectedIds();
				exportIds.val( ids.join( ',' ) );
			};

			exportButton.on( 'click', ev => handleClickExportButton( ev ) );
			idCheckboxes.on( 'change', ev => handleChooseItem( ev ) );
			$( '#cb-select-all-1' ).on( 'change', ev => handleChooseItem( ev ) );
			$( '#cb-select-all-2' ).on( 'change', ev => handleChooseItem( ev ) );
		},

		ready: function() {
			this.tabs();
			this.dependencyManager();
			this.dismissNotices();
			this.fixTextareaCode();
			// this.addSubscriptionGetListsButton();
			this.showPlayerTrackingDetail();
			this.selectAll();
			this.importQuizzes();
			this.aweberOptions();
			this.exportSelectedPlayersAndLeads();
		}
	};

	$( document ).ready( function() {
		functions.ready();
	});
})( wpQuizAdmin, jQuery );
