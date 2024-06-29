/**
 * Flip quiz
 *
 * @version 2.0.0
 * @author  MyThemeShop
 */
( function( wpQuizAdmin, $ ) {
	"use strict";

	wpQuizAdmin.Flip = class Flip extends wpQuizAdmin.Quiz {
		get name() {
			return 'flip';
		}

		/**
		 * Answers can be sortable or not.
		 *
		 * @return {Boolean}
		 */
		get answerSortable() {
			return false;
		}


		/**
		 * Results can be sortable or not.
		 *
		 * @return {Boolean}
		 */
		get resultSortable() {
			return false;
		}

		/**
		 * Has video upload.
		 *
		 * @return {Boolean}
		 */
		get videoUpload() {
			return false;
		}

		/**
		 * Has question media type.
		 *
		 * @return {Boolean}
		 */
		get questionMediaType() {
			return false;
		}

		/**
		 * Has answer type.
		 *
		 * @return {Boolean}
		 */
		get answerType() {
			return false;
		}

		/**
		 * Parses question data.
		 *
		 * @param  {Object} question Question data.
		 * @return {Object}
		 */
		parseQuestion( question ) {
			question = super.parseQuestion( question );
			if ( ! question.color ) {
				question.color = this.store.defaultQuestion.color;
			}
			return question;
		}

		getQuestionTmplData( question, index ) {
			return {
				question: question,
				baseName: this.questionsBaseName,
				index: index,
				listType: this.store.settings.list_type || '',
				i18n: wpQuizAdmin.i18n
			};
		}

		loadEvents() {
			super.loadEvents();

			// Flip.
			this.$wrapper.on( 'click', '.wp-quiz-question-flip-back-btn', function( ev ) {
				ev.preventDefault();
				$( this ).addClass( 'is-active' );
				$( this ).prev().removeClass( 'is-active' );
				$( this ).closest( '.wp-quiz-question' ).find( '.wp-quiz-question-flip-container' ).addClass( 'is-flipped' );
			});

			this.$wrapper.on( 'click', '.wp-quiz-question-flip-front-btn', function( ev ) {
				ev.preventDefault();
				$( this ).addClass( 'is-active' );
				$( this ).next().removeClass( 'is-active' );
				$( this ).closest( '.wp-quiz-question' ).find( '.wp-quiz-question-flip-container' ).removeClass( 'is-flipped' );
			});
		}
	};

	$( document ).ready( function() {
		$( '.wp-quiz-backend[data-type="flip"]' ).each( function() {
			new wpQuizAdmin.Flip( $( this ) );
		});
	});
})( wpQuizAdmin, jQuery );
