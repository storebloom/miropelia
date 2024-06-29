/**
 * Trivia quiz
 *
 * @version 2.0.0
 * @author  MyThemeShop
 */
( function( wpQuizAdmin, $ ) {
	"use strict";

	wpQuizAdmin.Trivia = class Trivia extends wpQuizAdmin.Quiz {
		get name() {
			return 'trivia';
		}

		get videoUpload() {
			return true;
		}

		/**
		 * Has question media type.
		 *
		 * @return {Boolean}
		 */
		get questionMediaType() {
			return true;
		}

		/**
		 * Has answer type.
		 *
		 * @return {Boolean}
		 */
		get answerType() {
			return true;
		}
	};

	$( document ).ready( function() {
		$( '.wp-quiz-backend[data-type="trivia"]' ).each( function() {
			new wpQuizAdmin.Trivia( $( this ) );
		});
	});
})( wpQuizAdmin, jQuery );
