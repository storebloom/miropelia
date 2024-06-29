/* global WP_Quiz_Pro_Buttons, tinymce */
( function( $ ) {
	"use strict";

	if ( 'undefined' === typeof tinymce ) {
		return;
	}

	/**
	 * Gets shortcode.
	 *
	 * @param {String} name  Shortcode name.
	 * @param {Object} attrs Shortcode attributes.
	 * @return {string}
	 */
	const getShortcode = function( name, attrs ) {
		let output = '[' + name;
		for ( let k in attrs ) {
			if ( ! attrs[ k ] ) {
				continue;
			}
			output += ' ' + k + '="' + attrs[ k ] + '"';
		}
		output += ']';
		return output;
	};

	/*
	 * Register buttons.
	 */

	const getWPQuizProButton = editor => {
		const shortcode = 'wp_quiz';

		return {
			text: WP_Quiz_Pro_Buttons['i18n']['quizShortcode'],
			onclick: function() {
				const dialog = editor.windowManager.open({
					title: WP_Quiz_Pro_Buttons['i18n']['quizShortcode'],
					body: [
						{
							type: 'listbox',
							name: 'id',
							label: WP_Quiz_Pro_Buttons['i18n']['selectQuiz'],
							values: WP_Quiz_Pro_Buttons['quizChoices'],
						},
						{
							type: 'textbox',
							name: 'question',
							label: WP_Quiz_Pro_Buttons['i18n']['showQuestions'],
							tooltip: WP_Quiz_Pro_Buttons['i18n']['showQuestionsDesc'],
						}
					],
					buttons: [
						{
							id: 'wp-quiz-insert-shortcode',
							classes: 'widget btn primary first abs-layout-item',
							text: WP_Quiz_Pro_Buttons['i18n']['insert'],
							onclick: function() {
								dialog.submit();
							}
						},
						{
							id: 'wp-quiz-cancel-shortcode',
							text: WP_Quiz_Pro_Buttons['i18n']['cancel'],
							onclick: function() {
								dialog.close();
							}
						}
					],
					onsubmit: function( e ) {
						editor.insertContent( getShortcode( shortcode, e.data ) );
					}
				});
			}
		};
	};

	const getWPQuizListingButton = editor => {
		const shortcode = 'wp_quiz_listing';

		return {
			text: WP_Quiz_Pro_Buttons['i18n']['quizzesShortcode'],
			onclick: function() {
				const dialog = editor.windowManager.open({
					title: WP_Quiz_Pro_Buttons['i18n']['quizzesShortcode'],
					body: [
						{
							type: 'textbox',
							name: 'num',
							label: WP_Quiz_Pro_Buttons['i18n']['numberOfQuizzes']
						}
					],
					buttons: [
						{
							id: 'wp-quiz-insert-shortcode',
							classes: 'widget btn primary first abs-layout-item',
							text: WP_Quiz_Pro_Buttons['i18n']['insert'],
							onclick: function() {
								dialog.submit();
							}
						},
						{
							id: 'wp-quiz-cancel-shortcode',
							text: WP_Quiz_Pro_Buttons['i18n']['cancel'],
							onclick: function() {
								dialog.close();
							}
						}
					],
					onsubmit: function( e ) {
						editor.insertContent( getShortcode( shortcode, e.data ) );
					}
				});
			}
		};
	};

	// Create plugin.
	tinymce.create( 'tinymce.plugins.WP_Quiz_Pro', {
		/**
		 * Initializes the plugin, this will be executed after the plugin has been created.
		 * This call is done before the editor instance has finished it's initialization so use the onInit event
		 * of the editor instance to intercept that event.
		 *
		 * @param {tinymce.Editor} ed Editor instance that the plugin is initialized in.
		 * @param {string} url Absolute URL to where the plugin is located.
		 */
		init : function( ed, url ) {
			ed.addButton( 'wp_quiz', {
				type: 'menubutton',
				icon: 'dashicons dashicons-before dashicons-editor-help',
				menu: [
					getWPQuizProButton( ed ),
					getWPQuizListingButton( ed )
				]
			});
		},

		/**
		 * Creates control instances based in the incoming name. This method is normally not
		 * needed since the addButton method of the tinymce.Editor class is a more easy way of adding buttons
		 * but you sometimes need to create more complex controls like listboxes, split buttons etc then this
		 * method can be used to create those.
		 *
		 * @param {String} n Name of the control to create.
		 * @param {tinymce.ControlManager} cm Control manager to use inorder to create new control.
		 * @return {tinymce.ui.Control} New control instance or null if no control was created.
		 */
		createControl : function(n, cm) {
			return null;
		},

		/**
		 * Returns information about the plugin as a name/value array.
		 * The current keys are longname, author, authorurl, infourl and version.
		 *
		 * @return {Object} Name/value array containing information about the plugin.
		 */
		getInfo : function() {
			return {
				longname : 'WP Quiz Shortcodes',
				author : 'MTS',
				authorurl : 'https://mythemeshop.com',
				version : '2.0.0'
			};
		}
	});

	// Register plugin.
	tinymce.PluginManager.add( 'wp_quiz', tinymce.plugins.WP_Quiz_Pro );
})( jQuery );
