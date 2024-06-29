/**
 * Personality quiz
 *
 * @version 2.0.0
 * @author  MyThemeShop
 */
( function( wpQuizAdmin, $ ) {
	"use strict";

	wpQuizAdmin.Personality = class Personality extends wpQuizAdmin.Trivia {

		initTemplates() {
			super.initTemplates();
			this.templates.answerResult = wp.template( 'wp-quiz-personality-answer-result-tpl' );
		}

		get name() {
			return 'personality';
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

		getAnswerTmplData( answer, index, baseName ) {
			return {
				answer: answer,
				baseName: baseName,
				index: index,
				results: this.store.results,
				i18n: wpQuizAdmin.i18n
			};
		}

		loadEvents() {
			super.loadEvents();

			this.$wrapper.on( 'change', '.wp-quiz-result-title', ev => {
				const $input = $( ev.currentTarget );
				const title = $input.val();
				const id = $input.closest( '.wp-quiz-result' ).attr( 'data-id' );
				this.store.results[ id ].title = title;
				this.$wrapper.find( '.wp-quiz-answer-result[data-id="' + id + '"]' ).find( '.wp-quiz-answer-result-title' ).text( title );
			});
		}

		addedAnswer( $el, answer, index ) {
			let output = '';
			const tmpl = this.templates.answerResult;
			const answerResultsEl = $el.find( '.wp-quiz-answer-results' );
			Object.values( this.store.results ).forEach( ( result, index ) => {
				result.point = answer.results[ result.id ] ? answer.results[ result.id ] : '';
				const tmplData = {
					result: result,
					baseName: answerResultsEl.attr( 'data-base-name' )
				};
				output += tmpl( tmplData );
			});
			answerResultsEl.append( output );
		}

		addResult( result, index, isManual ) {
			super.addResult( result, index, isManual );

			// Add result to results list.
			this.store.results[ this.tracker.lastAddedResult.id ] = this.tracker.lastAddedResult;

			// Add result in answer area.
			const tmpl = this.templates.answerResult;
			const tmplData = {
				result: this.tracker.lastAddedResult,
				baseName: ''
			};
			this.$wrapper.find( '.wp-quiz-answer-results' ).each( function() {
				if ( $( this ).find( `.wp-quiz-answer-result[data-id="${tmplData.result.id}"]` ).length ) {
					return;
				}
				const answerResult = $( this ).attr( 'data-answer-results' ) ? JSON.parse( $( this ).attr( 'data-answer-results' ) ) : {};
				const baseName = $( this ).attr( 'data-base-name' );
				tmplData.result.point = answerResult[ tmplData.result.id ] ? answerResult[ tmplData.result.id ] : '';
				tmplData.baseName = baseName;
				$( this ).append( tmpl( tmplData ) );
			});
		}

		removeResult( $el ) {
			super.removeResult( $el );
			delete this.store.results[ this.tracker.lastRemovedResultId ];
			this.$wrapper.find( '.wp-quiz-answer-result[data-id="' + this.tracker.lastRemovedResultId + '"]' ).remove();
		}
	};

	$( document ).ready( function() {
		$( '.wp-quiz-backend[data-type="personality"]' ).each( function() {
			new wpQuizAdmin.Personality( $( this ) );
		});
	});
})( wpQuizAdmin, jQuery );
