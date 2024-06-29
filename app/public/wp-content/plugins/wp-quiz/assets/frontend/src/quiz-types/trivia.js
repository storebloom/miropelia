/**
 * Trivia quiz
 *
 * @package WPQuiz
 */
( function( wpQuiz, $ ) {
	"use strict";

	const Trivia = class Trivia extends wpQuiz.Quiz {

		/**
		 * Quiz constructor.
		 *
		 * @param {Object} $wrapper Wrapper element.
		 * @param {Object} quizData Quiz data.
		 */
		constructor( $wrapper, quizData ) {
			super( $wrapper, quizData );

			this.checkedAnswersList = '';
		}

		/*showAnswered() {
			const answers = this.quizData.playData.answered_data;
			const result = this.quizData.playData.result;

			// Show or hide elements.
			this.$wrapper
				.removeClass( 'multiple' )
				.removeClass( 'wq-layout-multiple' )
				.addClass( 'single' )
				.addClass( 'wq-layout-single' );
			this.$wrapper.find( '.wq-quiz-intro-container' ).hide();
			this.$wrapper.find( '.wq-questions' ).show();

			// Show answer data.
			this.questions.forEach( ( question, index ) => {
				const questionEl = this.$wrapper.find( `.wq-question[data-index="${index}"]` );
				if ( answers[ question.id ] && answers[ question.id ].answers ) {
					for ( let id in answers[ question.id ].answers ) {
						questionEl.find( `.wq-answer[data-id="${id}"]` ).addClass( 'chosen' );
					}
				}
				this.checkQuestion( questionEl );
				questionEl.addClass( 'wq_questionAnswered' );
			});

			// Show the result.
			this.resultId = result.id;
			this.showResult();

			// Add sharer attribute.
			this.$wrapper.find( '.wq-share button' ).attr( 'data-tracking-id', this.quizData.playData.id );
		}*/

		isFullyAnswered( questionEl ) {
			const index = questionEl.attr( 'data-index' );
			return parseInt( this.questions[ index ].totalCorrects ) <= questionEl.find( '.wq-answer.chosen' ).length;
		}

		afterClickAnswer( answerEl, questionEl ) {
			// Check the correction of answer.
			if ( 'multiple' === this.settings.question_layout || 'off' === this.settings.end_answers ) {
				this.checkQuestion( questionEl );
			}

			super.afterClickAnswer( answerEl, questionEl );
		}

		checkQuestion( questionEl ) {
			// Check answers and show colors.
			const questionIndex = questionEl.attr( 'data-index' );
			const question = this.questions[ questionIndex ];
			if ( ! question ) {
				return;
			}
			let correct = questionEl.find( '.wq-answer.chosen' ).length > 0;

			this.checkedAnswersList += `<div><p><strong>${ question.title }</strong></p>`;

			// Highlight the correct and incorrect answer.
			Object.keys( question.answers ).forEach( answerId => {
				const answer = question.answers[ answerId ];
				const answerEl = questionEl.find( `.wq-answer[data-id="${answerId}"]` );
				if ( answer.isCorrect && parseInt( answer.isCorrect ) ) {
					answerEl.addClass( 'wq_correctAnswer' );
				} else if ( answerEl.hasClass( 'chosen' ) ) {
					answerEl.addClass( 'wq_incorrectAnswer' );
					correct = false;
				}

				if ( answerEl.hasClass( 'chosen' ) ) {
					this.checkedAnswersList += `<p class="${ correct ? 'is-correct' : 'is-incorrect' }">${ answer.title }</p>`;
				}

				if ( 'on' === this.settings.end_answers && 'multiple' === this.settings.question_layout ) {
					answerEl.removeClass( 'wq_correctAnswer' ).removeClass( 'wq_incorrectAnswer' );
				}
			});

			this.checkedAnswersList += '</div>';

			if ( correct ) {
				this.totalCorrects++;
				questionEl.addClass( 'is-correct' );
			} else {
				questionEl.addClass( 'is-incorrect' );
			}

			// Show explanation.
			if ( 'off' === this.settings.end_answers || 'single' === this.settings.question_layout || questionEl.find( '.wq-trivia-question-explanation' ).length ) {
				questionEl.find( '.wq-trivia-question-explanation' ).fadeIn();
			}
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

		complete() {
			const self = this;
			if ( 'single' === this.settings.question_layout && 'on' === this.settings.end_answers ) {
				this.$wrapper.find( '.wq-question' ).each( function() {
					self.checkQuestion( $( this ) );
				});
			}

			super.complete();
		}

		findResultId() {
			const score = this.totalCorrects;
			let found = false;
			this.results.forEach( result => {
				if ( found ) {
					return;
				}
				if ( score >= result.min && score <= result.max ) {
					found = true;
					this.resultId = result.id;
					this.result = result;
				}
			});
		}

		showResult() {
			const score = this.totalCorrects;
			const total = this.totalQuestions;

			// Redirect to another URL.
			if ( this.result.redirect_url ) {
				setTimeout( () => {
					window.location.href = this.result.redirect_url.replace( '%%score%%', score ).replace( '%%total%%', total );
				}, this.explanationDelay );
				return;
			}

			const resultsEl  = this.$wrapper.find( '.wq-results' );
			const resultEl   = this.$wrapper.find( `.wq-result[data-id="${ this.resultId }"]` );
			const text       = wpQuiz.i18n.resultScore.replace( '%%score%%', score ).replace( '%%total%%', total );
			const questionEl = this.$wrapper.find( `.wq-question[data-index="${ this.lastQuestionIndex }"]` );
			resultEl.find( '.wq-result-score' ).text( text );
			resultEl.find( '.wq-checked-answers-list' ).html( this.checkedAnswersList );
			resultEl.show();

			if ( 'multiple' === this.settings.question_layout && questionEl.is( ':visible' ) ) {
				questionEl.animateCss( this.settings.animation_out, () => {
					questionEl.hide();
					const offset = questionEl.offset().top;

					questionEl.hide();

					/*if ( 'on' === this.settings.auto_scroll ) {
						// Scroll to the begin of question.
						$( 'html, body' ).animate({
							scrollTop: offset - 95
						});
					}*/

					resultsEl.show().animateCss( this.settings.animation_in );
				});
			} else {
				if ( 'on' === this.settings.end_answers ) {
					this.scrollToQuestion( 0 );
				}
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
					corrects: this.totalCorrects,
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

		restartQuiz() {
			super.restartQuiz();

			this.$wrapper.find( '.wq_correctExplanationHead' ).removeClass( 'wq_correctExplanationHead' );
			this.$wrapper.find( '.wq_wrongExplanationHead' ).removeClass( 'wq_wrongExplanationHead' );
			this.$wrapper.find( '.wq_incorrectAnswer' ).removeClass( 'wq_incorrectAnswer' );
			this.$wrapper.find( '.wq_correctAnswer' ).removeClass( 'wq_correctAnswer' );
			this.checkedAnswersList = '';
			this.$wrapper.find( '.wq-checked-answers-list' ).html( '' );
		}
	};

	$( document ).ready( function() {
		$( '.wq-quiz-trivia' ).each( function() {
			const quizId = $( this ).attr( 'data-quiz-id' );
			if ( 'undefined' !== typeof window[ 'triviaQuiz' + quizId ] ) {
				new Trivia( $( this ), window[ 'triviaQuiz' + quizId ] );
			}
		});
	});
})( wpQuiz, jQuery );
