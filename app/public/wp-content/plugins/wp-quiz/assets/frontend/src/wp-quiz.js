( function( wpQuiz, $ ) {
	"use strict";

	wpQuiz.helpers = wpQuiz.helpers || {};

	if ( 'undefined' === typeof Object.values ) {
		Object.values = obj => Object.keys( obj ).map( e => obj[ e ] );
	}

	/**
	 * Loads embed content.
	 *
	 * @param {Object} $el     Embed element.
	 * @param {Object} options Embed options.
	 */
	wpQuiz.helpers.loadEmbed = function( $el, options ) {
		if ( $el.hasClass( 'active' ) ) {
			return;
		}

		const data = {
			post_ID: 0,
			type: 'embed',
			shortcode: '[embed]' + options.url + '[/embed]',
			maxwidth: 1000
		};

		const request = wp.ajax.post( 'parse-embed', data );
		request.done( response => {
			const $iframe = $( response.body );
			$iframe.attr( 'width', '100%' );
			$iframe.attr( 'height', '100%' );
			if ( options.placeholder ) {
				const src = $iframe.attr( 'src' );
				$iframe.attr( 'src', src.indexOf( '?' ) >= 0 ? src + '&autoplay=1' : src + '?autoplay=1' );
			}
			$iframe.wrap( '<div class="wq-embed-content"></div>' ).appendTo( $el );
			$el.addClass( 'active' );
		});
		request.fail( response => {
			console.error( response.message );
		});
	};

	/**
	 * Gets REST API request.
	 *
	 * @param {Object} options jQuery.ajax() options.
	 * @return {Object}        jQuery.ajax() request object.
	 */
	wpQuiz.helpers.getRequest = function( options ) {
		if ( ! options.beforeSend ) {
			options.beforeSend = function( xhr ) {
				xhr.setRequestHeader( 'X-WP-Nonce', wpQuiz.restNonce );
			};
		}

		if ( options.method && ! options.type ) {
			options.type = options.method;
		} else if ( ! options.method && options.type ) {
			options.method = options.type;
		}

		return $.ajax( options );
	};

	/**
	 * Shows loading on element.
	 *
	 * @param {Object} el HTML element or jQuery object.
	 */
	wpQuiz.helpers.showLoading = el => {
		const $el = $( el );
		const loadingEl = $el.find( '.wq-loading' );
		if ( ! loadingEl.length ) {
			$el.css( 'position', 'relative' );
			$el.append( '<div class="wq-loading"><span class="wq-icon wq-icon-spinner animate-spin wq-spinner"></span></div>' )
		}
		$el.addClass( 'wq-is-loading' );
	};

	/**
	 * Hides loading on element.
	 *
	 * @param {Object} el HTML element or jQuery object.
	 */
	wpQuiz.helpers.hideLoading = el => {
		$( el ).removeClass( 'wq-is-loading' );
	};


	/**
	 * Adds custom events when animation ends.
	 */
	$.fn.extend({
		animateCss: function( animationName, callback ) {
			const animationEnd = ( function( el ) {
				const animations = {
					animation: 'animationend',
					OAnimation: 'oAnimationEnd',
					MozAnimation: 'mozAnimationEnd',
					WebkitAnimation: 'webkitAnimationEnd',
				};

				for ( let t in animations ) {
					if ( el.style[ t ] !== undefined ) {
						return animations[ t ];
					}
				}
			})( document.createElement( 'div' ) );

			this.addClass( 'animated ' + animationName ).one( animationEnd, function() {
				$( this ).removeClass( 'animated ' + animationName );

				if ( typeof callback === 'function' ) {
					callback();
				}
			});

			return this;
		},
	});


	/**
	 * Quiz class.
	 */
	wpQuiz.Quiz = class Quiz {

		/**
		 * Quiz constructor.
		 *
		 * @param {Object} $wrapper Wrapper element.
		 * @param {Object} quizData Quiz data.
		 */
		constructor( $wrapper, quizData ) {
			this.$wrapper = $wrapper;
			$wrapper.data( 'wpQuiz', this );
			this.quizData = quizData;
			this.quizId = quizData.id;
			this.questions = quizData.questions ? Object.values( quizData.questions ) : [];
			this.results = quizData.results ? Object.values( quizData.results ) : [];
			this.settings = quizData.settings || {};
			this.shareUrl = quizData.shareUrl;
			this.currentQuestionIndex = 0;
			this.totalQuestions = this.questions.length;
			this.lastQuestionIndex = this.totalQuestions - 1;
			this.totalAnswered = 0;
			this.totalCorrects = 0;
			this.tracker = {};
			this.resultId = null;
			this.result = null;
			this.isSavedPlayer = false;
			this.points = {};
			this.explanationDelay = 5000;

			this.loadCommonEvents();
			this.loadEvents();
		}

		isEmpty( value ) {
			return undefined === value || '' === value || false === value || 0 === value || '' === value || {} === value || [] === value;
		}

		isShowPlayData() {
			return ! this.isEmpty( this.quizData.playData );
		}

		isShowNextButton() {
			return this.settings.show_next_button !== 'off';
		}

		isMultipleLayout() {
			return 'multiple' === this.settings['question_layout'];
		}

		/**
		 * Shows answered data.
		 */
		showAnswered() {}

		/**
		 * Loads events which run when quiz answered or unanswered.
		 */
		loadCommonEvents() {
			// Embed toggle.
			this.$wrapper.on( 'click', '.wq-embed-toggle-btn', ev => this.handleClickToggleEmbed( ev ) );

			// Video embed.
			this.$wrapper.on( 'click', '.wq-embed:not(.active)', ev => {
				const img = ev.currentTarget.querySelector( 'img' );
				if ( ! img ) {
					return;
				}

				const iframe = ev.currentTarget.querySelector( 'iframe' );
				const iframeSrc = iframe.src;
				ev.currentTarget.classList.add( 'active' );
				if ( iframe ) {
					iframe.src = iframeSrc.indexOf( '?' ) >= 0 ? iframeSrc + '&autoplay=1' : iframeSrc + '?autoplay=1'
				}
			});
		}

		/**
		 * Loads events which run when quiz unanswered.
		 */
		loadEvents() {
			// On click answer.
			this.$wrapper.on( 'click.answer', '.wq-answer', ev => this.handleClickAnswer( ev ) );

			// On click next question button.
			this.$wrapper.on( 'click.nextQuestion', '.wq-continue-btn', ev => this.handleClickNextQuestionButton( ev ) );

			// On click Play again button.
			this.$wrapper.on( 'click.retakeQuiz', '.wq-retake-quiz-btn', ev => this.handleClickRestartQuizButton( ev ) );
		}

		/**
		 * Handles click answer.
		 *
		 * @param {Object} ev Event object.
		 */
		handleClickAnswer( ev ) {
			const $this = $( ev.currentTarget );

			if ( $this.hasClass( 'chosen' ) ) {
				return;// Prevent clicking to chosen answer.
			}

			const questionEl = $this.closest( '.wq-question' );

			if ( this.isAnswered( questionEl ) ) {
				return; // Prevent clicking answered question.
			}

			// Mark this answer as answered.
			$this.closest( '.wq-answer' ).addClass( 'chosen' );

			if ( ! this.isFullyAnswered( questionEl ) ) { // User needs to answer more.
				return;
			}

			questionEl.addClass( 'wq_questionAnswered' );

			this.trackAnswer( questionEl );

			// Increase total answered.
			this.totalAnswered++;
			this.currentQuestionIndex = parseInt( questionEl.attr( 'data-index' ) );

			// Do something after clicking answer.
			this.afterClickAnswer( $this, questionEl );

			// Go to next question.
			this.nextQuestion();
		}

		/**
		 * Handles click next question button.
		 *
		 * @param {Object} ev Event object.
		 */
		handleClickNextQuestionButton( ev ) {
			ev.preventDefault();
			const questionEl = $( ev.currentTarget ).closest( '.wq-question' );
			this.showTheNextQuestion( questionEl );
		}

		/**
		 * Handles click restart quiz button.
		 *
		 * @param {Object} ev Event object.
		 */
		handleClickRestartQuizButton( ev ) {
			ev.preventDefault();
			this.restartQuiz();
		}

		/**
		 * Handles click toggle embed button.
		 *
		 * @param {Object} ev Event object.
		 */
		handleClickToggleEmbed( ev ) {
			ev.preventDefault();
			$( ev.currentTarget ).parent().toggleClass( 'active' ).next().slideToggle( 'fast' );
		}


		/**
		 * Handles click begin quiz button.
		 *
		 * @param {Object} ev Event object.
		 */
		handleClickBeginQuiz( ev ) {
			ev.preventDefault();
			this.$wrapper.find( '.wq-quiz-intro-container' ).fadeOut( 'fast', () => {
				this.$wrapper.find( '.wq-progress-bar-container, .wq-questions, .wq-embed-toggle, .wq_promoteQuizCtr' ).fadeIn( 'fast' );
			});
		}

		/**
		 * Shows the next question. Apply for the multiple page layout.
		 *
		 * @param {Object} questionEl jQuery question element.
		 */
		showTheNextQuestion( questionEl ) {
			const runAnimation = questionEl => {
				// Show animation out on current question.
				questionEl.animateCss( this.settings['animation_out'], () => {
					const nextQuestionEl = questionEl.next( '.wq-question' );
					const offset = questionEl.offset().top;

					questionEl.hide();

					if ( 'on' === this.settings['auto_scroll'] ) {
						// Scroll to the begin of question.
						$( 'html, body' ).animate({
							scrollTop: offset - 95
						});
					}

					// Show animation in on next question.
					nextQuestionEl.show().animateCss( this.settings['animation_in'] );
				});
			};

			if ( ! this.isAnswered( questionEl ) ) { // Must answer before going to next question.
				return;
			}

			if ( this.totalAnswered < this.totalQuestions ) {
				if ( 'trivia' === this.quizData.type && ! this.isShowNextButton() ) {
					setTimeout( () => {
						runAnimation( questionEl );
					}, this.explanationDelay );
				} else {
					runAnimation( questionEl );
				}
			}

			// Pause the embed video.
			if ( questionEl.find( '.wq-embed iframe' ).length ) {
				const iframe = questionEl.find( '.wq-embed iframe' );
				const videoUrl = iframe.attr( 'src' );
				iframe.attr( 'src', '' );
				iframe.attr( 'src', videoUrl.replace( '&autoplay=1', '' ) );
			}
			if ( questionEl.find( '.wq-question-video video' ).length && questionEl.find( '.wq-question-video video' )[0].player ) {
				questionEl.find( '.wq-question-video video' )[0].player.pause();
			}
		}

		/**
		 * Checks if question if fully answered.
		 *
		 * @param {Object} questionEl JQuery question element.
		 * @return {Boolean}
		 */
		isFullyAnswered( questionEl ) {
			return true;
		}

		/**
		 * Does something after clicking the answer.
		 *
		 * @param {Object} answerEl   JQuery answer element.
		 * @param {Object} questionEl JQuery question element.
		 */
		afterClickAnswer( answerEl, questionEl ) {
			if ( this.isMultipleLayout() ) {
				// Update progress bar.
				if ( this.$wrapper.find( '.wq_quizProgressBar' ).length ) {
					this.updateProgressBar();
				}

				if ( this.isShowNextButton() ) {
					const nextButton = questionEl.find( '.wq_continue' );
					if ( this.totalAnswered < this.totalQuestions && questionEl.find( '.wq_continue' ).length ) {
						nextButton.show();

						/*if ( 'on' === this.settings.auto_scroll ) {
							// Scroll to view the scroll button.
							$( 'html, body' ).animate({
								scrollTop: nextButton.offset().top - 250
							}, 750 );
						}*/
					}
				} else {
					this.showTheNextQuestion( questionEl );
				}
			}
		}

		/**
		 * Updates the progress bar.
		 */
		updateProgressBar( percent ) {
			if ( 'undefined' === typeof percent ) {
				percent = parseInt( this.totalAnswered * 100 / this.totalQuestions );
			}
			this.$wrapper.find( '.wq_quizProgressValue' ).animate({
				width: percent + '%'
			}).text( percent + '%' );
		}

		/**
		 * Processes to the next question.
		 */
		nextQuestion() {
			if ( this.totalAnswered === this.totalQuestions ) {
				return this.complete();
			}
			if ( this.currentQuestionIndex === this.lastQuestionIndex ) {
				this.currentQuestionIndex = -1; // Go back to first to find unanswered question.
				return this.nextQuestion();
			}
			this.currentQuestionIndex++;
			if ( this.isAnswered( this.currentQuestionIndex ) ) {
				return this.nextQuestion();
			}

			if ( ! this.isMultipleLayout() && 'on' === this.settings.auto_scroll ) {
				this.scrollToQuestion( this.currentQuestionIndex );
			}
		}

		/**
		 * Processes to complete.
		 */
		complete() {
			if ( this.isAnswerOnly ) {
				return;
			}

			this.$wrapper.addClass( 'is-completed' );

			this.findResultId();

			if ( ! this.isSavedPlayer ) {
				this.savePlayData();
				this.isSavedPlayer = true;
			}

			const forceActionEl = this.$wrapper.find( '.wq-force-action-container' );
			const questionEl = this.$wrapper.find( `.wq-question[data-index="${ this.lastQuestionIndex }"]` );

			if ( this.results.length ) {
				this.showResult();
			}
			if ( this.$wrapper.find( '.wq-retake-quiz' ).length ) {
				this.$wrapper.find( '.wq-retake-quiz' ).show();
			}

			if ( ! this.isMultipleLayout() ) {
				if ( 'on' === this.settings.end_answers ) {
					this.scrollToQuestion( 0 );
				} else if ( this.results.length && 'on' === this.settings['auto_scroll'] ) {
					this.scrollToResults();
				}
			}

			this.$wrapper.trigger( 'wp_quiz_complete_quiz', [ this ] );
		}

		/**
		 * Finds the result ID want to show base on answered data.
		 */
		findResultId() {}

		/**
		 * Tracks question answer.
		 *
		 * @param {Object} questionEl JQuery question element.
		 */
		trackAnswer( questionEl ) {}

		/**
		 * Saves player data.
		 */
		savePlayData() {}

		/**
		 * Shows the result.
		 */
		showResult() {}

		/**
		 * Scrolls to results.
		 */
		scrollToResults() {
			const scrollTo = this.$wrapper.find( '.wq-force-action-container' ).length ? this.$wrapper.find( '.wq-force-action-container' ) : this.$wrapper.find( '.wq-results' );
			$( 'html, body' ).animate({
				scrollTop: scrollTo.offset().top - 95
			});
		}

		/**
		 * Scrolls to a question.
		 *
		 * @param {Number} index Question index.
		 */
		scrollToQuestion( index ) {
			$( 'html, body' ).animate({
				scrollTop: this.$wrapper.find( `.wq-question[data-index="${index}"]` ).offset().top - 95
			});
		}

		/**
		 * Scrolls to the quiz.
		 */
		scrollToQuiz() {
			$( window ).scrollTop( $( '#wp-quiz-' + this.quizId ).offset().top - 95 );
		}

		/**
		 * Processes to restart quiz.
		 */
		restartQuiz() {
			this.currentQuestionIndex = 0;
			this.totalAnswered = 0;
			this.totalCorrects = 0;
			this.tracker = {};
			this.isSavedPlayer = false;

			if ( this.isMultipleLayout() ) {
				this.$wrapper.find( '.wq-question' ).hide();
				this.$wrapper.find( '.wq-question[data-index="0"]' ).show();
			}

			this.scrollToQuestion( 0 );
			this.updateProgressBar();

			this.$wrapper.removeClass( 'is-completed' );
			this.$wrapper.removeClass( 'show-played' );
			this.$wrapper.find( '.wq_questionAnswered' ).removeClass( 'wq_questionAnswered' );
			this.$wrapper.find( '.is-correct' ).removeClass( 'is-correct' );
			this.$wrapper.find( '.is-incorrect' ).removeClass( 'is-incorrect' );
			this.$wrapper.find( '.chosen' ).removeClass( 'chosen' );
			this.$wrapper.find( '.wq-question:not(.wq-is-ad) .wq_continue, .wq-results, .wq-result, .wq-retake-quiz' ).hide();
			this.$wrapper.find( '.wq-question' ).removeClass( 'force-show' );
		}

		/**
		 * Checks if a question is answered.
		 *
		 * @param {Number|Object} question Question index or JQuery question element.
		 * @return {Boolean}
		 */
		isAnswered( question ) {
			if ( 'function' === typeof question.hasClass ) {
				return question.hasClass( 'wq_questionAnswered' ) || question.hasClass( 'wq-is-ad' );
			}
			return this.$wrapper.find( `.wq-question[data-index="${question}"]` ).hasClass( 'wq_questionAnswered' );
		}
	};

	$( document ).ready( function() {
		// FB share.
		$( document ).on( 'click', '.wq-share-fb', ev => {
			if ( typeof FB === 'undefined' ) {
				return;
			}
			ev.preventDefault();

			let shareUrl = ev.currentTarget.dataset.url;
			if ( ev.currentTarget.dataset.trackingId ) {
				const trackingId = ev.currentTarget.dataset.trackingId;
				shareUrl = -1 === shareUrl.indexOf( '?' ) ? `${shareUrl}?wqtid=${trackingId}` : `${shareUrl}&wqtid=${trackingId}`;
			} else if ( ev.currentTarget.dataset.imageFile ) {
				const imageFile = ev.currentTarget.dataset.imageFile;
				shareUrl = -1 === shareUrl.indexOf( '?' ) ? `${shareUrl}?wqimg=${imageFile}` : `${shareUrl}&wqimg=${imageFile}`;
			}

			FB.ui({
				method: 'share',
				href: shareUrl,
			}, response => {
				console.log( response );
			});
		});

		// Tweet.
		$( document ).on( 'click', '.wq-share-tw', ev => {
			ev.preventDefault();

			let shareUrl = ev.currentTarget.dataset.url;
			if ( ev.currentTarget.dataset.trackingId ) {
				const trackingId = ev.currentTarget.dataset.trackingId;
				shareUrl = -1 === shareUrl.indexOf( '?' ) ? `${shareUrl}?wqtid=${trackingId}` : `${shareUrl}&wqtid=${trackingId}`;
			} else if ( ev.currentTarget.dataset.imageFile ) {
				const imageFile = ev.currentTarget.dataset.imageFile;
				shareUrl = -1 === shareUrl.indexOf( '?' ) ? `${shareUrl}?wqimg=${imageFile}` : `${shareUrl}&wqimg=${imageFile}`;
			}

			shareUrl = 'https://twitter.com/intent/tweet?url=' + encodeURIComponent( shareUrl );

			const score       = $( ev.currentTarget ).closest( '.wq-quiz' ).find( '.wq-result:visible .wq-result-score' );
			const resultTitle = $( ev.currentTarget ).closest( '.wq-quiz' ).find( '.wq-result:visible .wq-result-title' );
			if ( score.length && score.text() ) {
				shareUrl += `&text=${ encodeURIComponent( score.text() ) }`;
			} else if ( resultTitle.length && resultTitle.text() ) {
				shareUrl += `&text=${ encodeURIComponent( resultTitle.text() ) }`;
			}

			window.open( shareUrl, '_blank', 'width=500, height=300' );
		});

		// VK.
		$( document ).on( 'click', '.wq-share-vk', ev => {
			ev.preventDefault();

			let shareUrl = ev.currentTarget.dataset.url;
			if ( ev.currentTarget.dataset.trackingId ) {
				const trackingId = ev.currentTarget.dataset.trackingId;
				shareUrl = -1 === shareUrl.indexOf( '?' ) ? `${shareUrl}?wqtid=${trackingId}` : `${shareUrl}&wqtid=${trackingId}`;
			} else if ( ev.currentTarget.dataset.imageFile ) {
				const imageFile = ev.currentTarget.dataset.imageFile;
				shareUrl = -1 === shareUrl.indexOf( '?' ) ? `${shareUrl}?wqimg=${imageFile}` : `${shareUrl}&wqimg=${imageFile}`;
			}

			window.open(
				'http://vk.com/share.php?url=' + encodeURIComponent( shareUrl ),
				'_blank',
				'width=500, height=300'
			);
		});

		$( document ).on( 'click', '.wq-question-hint-button', function( ev ) {
			ev.stopPropagation();
			ev.preventDefault();
			$( this ).prev( '.wq-question-hint-content' ).slideToggle();
			return false;
		});
		$( document ).on( 'click', '.wq-question-hint-content', ev => ev.stopPropagation() );
		$( document ).on( 'click', ev => $( '.wq-question-hint-content' ).hide() );
	});

})( wpQuiz, jQuery );
