/**
 * Personality quiz
 *
 * @package WPQuiz
 */
( function( wpQuiz, $ ) {
	"use strict";

	const Personality = class Personality extends wpQuiz.Quiz {

		isShowNextButton() {
			return false;
		}

		addPoint( resultId, point ) {
			if ( ! this.points[ resultId ] ) {
				this.points[ resultId ] = 0;
			}
			this.points[ resultId ] += parseInt( point );
		}

		afterClickAnswer( answerEl, questionEl ) {
			const answerId = answerEl.attr( 'data-id' );
			const questionIndex = questionEl.attr( 'data-index' );
			const question = this.questions[ questionIndex ];
			const answer = question.answers[ answerId ];

			for ( let i in answer.results ) {
				this.addPoint( i, answer.results[ i ] );
			}

			super.afterClickAnswer( answerEl, questionEl );
		}

		findResultId() {
			let max = 0;
			let maxResultId = '';

			for ( let i in this.points ) {
				if ( this.points[ i ] > max ) {
					max = this.points[ i ];
					maxResultId = i;
				}
			}

			this.resultId = maxResultId;
			this.result   = this.quizData.results[ this.resultId ];
		}

		trackAnswer( questionEl ) {
			const questionIndex = questionEl.attr( 'data-index' );
			const question = this.questions[ questionIndex ];
			const data = {
				answers: []
			};
			questionEl.find( '.wq-answer.chosen' ).each( ( index, el ) => data.answers.push( el.dataset.id ) );
			this.tracker[ question.id ] = data;
		}

		showResult() {
			// Redirect to another URL.
			if ( this.result.redirect_url ) {
				setTimeout( () => {
					window.location.href = this.result.redirect_url;
				}, 500 );
				return;
			}

			const resultsEl  = this.$wrapper.find( '.wq-results' );
			const resultEl   = this.$wrapper.find( `.wq-result[data-id="${ this.resultId }"]` );
			const questionEl = this.$wrapper.find( `.wq-question[data-index="${ this.lastQuestionIndex }"]` );

			resultEl.show();

			if ( 'multiple' === this.settings.question_layout && questionEl.is( ':visible' ) ) {
				questionEl.animateCss( this.settings.animation_out, () => {
					questionEl.hide();
					const offset = questionEl.offset().top;

					questionEl.hide();

					if ( 'on' === this.settings.auto_scroll ) {
						// Scroll to the begin of question.
						$( 'html, body' ).animate({
							scrollTop: offset - 95
						});
					}

					resultsEl.show().animateCss( this.settings.animation_in );
				});
			} else {
				resultsEl.show();
			}
		}

		savePlayData() {
			const url = wpQuiz.restUrl + 'wp-quiz/v2/play_data';
			const request = wpQuiz.helpers.getRequest({
				url: url,
				method: 'POST',
				data: {
					quiz_id: this.quizId,
					quiz_data: JSON.stringify( this.quizData ),
					answered: JSON.stringify( this.tracker ),
					result_id: this.resultId,
					current_url: window.location.href
				}
			});

			request.done( response => {
				if ( typeof response === 'number' ) {
					this.$wrapper.find( '.wq-subscribe-form' ).attr( 'data-tracking-id', response );
					this.$wrapper.find( '.wq-share button' ).attr( 'data-tracking-id', response );
					$( '.mfp-wrap .wq-popup .wq-share button' ).attr( 'data-tracking-id', response );
					this.$wrapper.find( '.wq_forceShareFB' ).attr( 'data-tracking-id', response );
				}
			});

			request.fail( response => {
				console.error( response );
			});
		}
	};

	$( document ).ready( function() {
		$( '.wq-quiz-personality' ).each( function() {
			const quizId = $( this ).attr( 'data-quiz-id' );
			if ( 'undefined' !== typeof window[ 'personalityQuiz' + quizId ] ) {
				new Personality( $( this ), window[ 'personalityQuiz' + quizId ] );
			}
		});
	});
})( wpQuiz, jQuery );
