/**
 * Quiz class
 *
 * @since 2.0.0
 */
( function( wpQuizAdmin, $ ) {
	"use strict";

	wpQuizAdmin.helpers = wpQuizAdmin.helpers || {};

	wpQuizAdmin.helpers.resizeTextarea = function( $el ) {
		const baseHeight = $el.outerHeight();
		$el.css( 'overflowY', 'scroll' );
		const height = $el.prop( 'scrollHeight' );
		$el.css( 'overflowY', 'auto' );
		$el.css( 'height', 'auto' ).css( 'height', height > baseHeight ? height + 2 : baseHeight );
	};

	/**
	 * Quiz class
	 */
	wpQuizAdmin.Quiz = class Quiz {
		/**
		 * Constructor.
		 *
		 * @param {Object} $wrapper Wrapper element.
		 */
		constructor( $wrapper ) {
			this.$wrapper = $wrapper;

			// Set properties.
			this.questionsEl = $wrapper.find( '.wp-quiz-questions' );
			this.resultsEl = $wrapper.find( '.wp-quiz-results' );
			this.questionsBaseName = 'quizData[questions]';
			this.resultsBaseName = 'quizData[results]';

			this.store = window[ this.name + 'Quiz' ];

			this.tracker = {
				lastAddedQuestion: null,
				lastRemovedQuestionId: null,
				lastAddedAnswer: null,
				lastRemovedAnswerId: null,
				lastAddedResult: null,
				lastRemovedResultId: null
			};

			this.initTemplates();

			// Load output.
			this.loadQuestions();

			this.loadResults();

			// Load events.
			this.loadEvents();
		}

		/**
		 * Quiz type name.
		 *
		 * @return {String}
		 */
		get name() {
			return '';
		}

		/**
		 * Questions can be sortable or not.
		 *
		 * @return {Boolean}
		 */
		get questionSortable() {
			return true;
		}


		/**
		 * Answers can be sortable or not.
		 *
		 * @return {Boolean}
		 */
		get answerSortable() {
			return true;
		}

		/**
		 * Results can be sortable or not.
		 *
		 * @return {Boolean}
		 */
		get resultSortable() {
			return true;
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

		initTemplates() {
			this.templates = {
				question: wp.template( `wp-quiz-${this.name}-question-tpl` ),
				answer: wp.template( `wp-quiz-${this.name}-answer-tpl` ),
				result: wp.template( `wp-quiz-${this.name}-result-tpl` )
			};
		}

		/**
		 * Parses question data.
		 *
		 * @param  {Object} question Question data.
		 * @return {Object}
		 */
		parseQuestion( question ) {
			question = $.extend( {}, this.store.defaultQuestion, question );
			if ( ! question.id ) {
				question.id = wpQuizAdmin.helpers.getRandomString();
			}
			return question;
		}

		/**
		 * Parses answer data.
		 *
		 * @param  {Object} answer Answer data.
		 * @return {Object}
		 */
		parseAnswer( answer ) {
			answer = $.extend( {}, this.store.defaultAnswer, answer );
			if ( ! answer.id ) {
				answer.id = wpQuizAdmin.helpers.getRandomString();
			}
			return answer;
		}

		/**
		 * Parses result data.
		 *
		 * @param  {Object} result Answer data.
		 * @return {Object}
		 */
		parseResult( result ) {
			result = $.extend( {}, this.store.defaultResult, result );
			if ( ! result.id ) {
				result.id = wpQuizAdmin.helpers.getRandomString();
			}
			return result;
		}

		loadQuestions() {
			Object.values( this.store.questions ).forEach( ( question, index ) => {
				this.addQuestion( question, index, false );

				// Color picker.
				this.$wrapper.find( '.wq-color-picker' ).wpColorPicker();

				this.resizeTextareas();
			});
		}

		addQuestion( question, index, isManual ) {
			question = this.parseQuestion( question );
			const tmplData = this.getQuestionTmplData( question, index );

			// Get question output.
			const questionOutput = this.templates.question( tmplData );
			if ( ! questionOutput ) {
				return;
			}

			const questionEl = $( questionOutput );
			// Add question attributes.
			if ( this.questionMediaType ) {
				questionEl.attr( 'data-media-type', question.mediaType );
			}

			// Load answers list in question output.
			const answersEl = questionEl.find( '.wp-quiz-answers' );
			if ( answersEl.length ) {
				if ( this.answerType ) {
					answersEl.attr( 'data-type', question.answerType );
				}
				this.loadAnswers( answersEl, question.answers || {} );
			}

			// Append loaded question to questions list.
			this.questionsEl.find( '.wp-quiz-questions-list' ).append( questionEl );

			if ( this.answerSortable ) {
				this.sortAnswers( questionEl );
			}

			if ( isManual ) {
				// Color picker.
				questionEl.find( '.wq-color-picker' ).wpColorPicker();

				this.resizeTextareas();
			}

			this.tracker.lastAddedQuestion = question;
			this.addedQuestion( questionEl, question, index );
		}

		getQuestionTmplData( question, index ) {
			return {
				question: question,
				baseName: this.questionsBaseName,
				index: index,
				i18n: wpQuizAdmin.i18n
			};
		}

		addedQuestion( $el, question, index ) {}

		removeQuestion( $el ) {
			this.tracker.lastRemovedQuestionId = $el.attr( 'data-id' );
			$el.remove();
			this.reIndexQuestions();
		}

		loadAnswers( $el, answers ) {
			const baseName = $el.attr( 'data-base-name' );
			Object.values( answers ).forEach( ( answer, index ) => {
				this.addAnswer( $el, answer, index, baseName, false );
			});
		}

		addAnswer( $el, answer, index, baseName, isManual ) {
			answer = this.parseAnswer( answer );
			const tmplData = this.getAnswerTmplData( answer, index, baseName );
			const answerOutput = this.templates.answer( tmplData );
			if ( ! answerOutput ) {
				return;
			}
			const answerEl = $( answerOutput );
			$el.find( '.wp-quiz-answers-list' ).append( answerEl );

			if ( isManual ) {
				this.resizeTextareas();
			}

			this.tracker.lastAddedAnswer = answer;
			this.addedAnswer( answerEl, answer, index );
		}

		getAnswerTmplData( answer, index, baseName ) {
			return {
				answer: answer,
				baseName: baseName,
				index: index,
				i18n: wpQuizAdmin.i18n
			};
		}

		addedAnswer( $el, answer, index ) {}

		removeAnswer( $el ) {
			this.tracker.lastRemovedAnswerId = $el.attr( 'data-id' );
			$el.remove();
		}

		loadResults() {
			Object.values( this.store.results ).forEach( ( result, index ) => {
				this.addResult( result, index, false );
			});
		}

		addResult( result, index, isManual ) {
			result = this.parseResult( result );
			const tmplData = this.getResultTmplData( result, index );
			const resultOutput = this.templates.result( tmplData );
			if ( ! resultOutput ) {
				return;
			}
			const resultEl = $( resultOutput );
			this.resultsEl.find( '.wp-quiz-results-list' ).append( resultEl );

			if ( isManual ) {
				this.resizeTextareas();
			}

			this.tracker.lastAddedResult = result;
			this.addedResult( resultEl, result, index );
		}

		addedResult( $el, result, index ) {}

		getResultTmplData( result, index ) {
			return {
				result: result,
				baseName: this.resultsBaseName,
				index: index,
				i18n: wpQuizAdmin.i18n
			};
		}

		removeResult( $el ) {
			this.tracker.lastRemovedResultId = $el.attr( 'data-id' );
			$el.remove();
		}

		loadEvents() {
			this.$wrapper.on( 'click.addQuestion', '.wp-quiz-add-question-btn', ev => this.handleClickAddQuestion( ev ) );

			this.$wrapper.on( 'click.removeQuestion', '.wp-quiz-remove-question-btn', ev => this.handleClickRemoveQuestion( ev ) );

			this.$wrapper.on( 'click.addAnswer', '.wp-quiz-add-answer-btn', ev => this.handleClickAddAnswer( ev ) );

			this.$wrapper.on( 'click.removeAnswer', '.wp-quiz-remove-answer-btn', ev => this.handleClickRemoveAnswer( ev ) );

			this.$wrapper.on( 'click.addResult', '.wp-quiz-add-result-btn', ev => this.handleClickAddResult( ev ) );

			this.$wrapper.on( 'click.removeResult', '.wp-quiz-remove-result-btn', ev => this.handleClickRemoveResult( ev ) );

			this.$wrapper.on( 'click.uploadImage', '.wp-quiz-image-upload-btn', ev => this.handleClickUploadImage( ev ) );

			this.$wrapper.on( 'click.removeImage', '.wp-quiz-image-upload-remove-btn', ev => this.handleClickRemoveImage( ev ) );

			if ( this.questionMediaType ) {
				this.$wrapper.on( 'click.setMediaType', '.wp-quiz-set-question-type-btn', ev => this.handleClickSetQuestionType( ev ) );
			}

			if ( this.answerType ) {
				this.$wrapper.on( 'click.setAnswerType', '.wp-quiz-set-answer-type-btn', ev => this.handleClickSetAnswerType( ev ) );
			}

			if ( this.videoUpload ) {
				this.$wrapper.find( '.wp-quiz-video-upload' ).each( ( index, el ) => {
					this.loadVideoPreview( $( el ) );
				});

				this.$wrapper.on( 'click.loadVideoPreview', '.wp-quiz-load-video-preview-btn', ev => {
					this.loadVideoPreview( $( ev.currentTarget ).closest( '.wp-quiz-video-upload' ) );
				});

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

				this.$wrapper.on( 'change', '.wp-quiz-video-upload-url', ev => this.handleChangeVideoData( ev ) );
				this.$wrapper.on( 'change', '.wp-quiz-video-upload .wp-quiz-image-upload-url', ev => this.handleChangeVideoData( ev ) );

				this.$wrapper.on( 'click.uploadVideo', '.wp-quiz-upload-video-btn', ev => this.handleClickUploadVideoBtn( ev ) );
			}

			if ( this.questionSortable ) {
				this.sortQuestions();
			}

			if ( this.resultSortable ) {
				this.sortResults();
			}
		}

		handleClickAddQuestion( ev ) {
			ev.preventDefault();
			// const question = this.parseQuestion( {} );
			const index = this.questionsEl.find( '.wp-quiz-question' ).length;
			this.addQuestion( {}, index, true );
		}

		handleClickRemoveQuestion( ev ) {
			ev.preventDefault();
			const questionEl = $( ev.currentTarget ).closest( '.wp-quiz-question' );
			this.removeQuestion( questionEl );
		}

		handleClickAddAnswer( ev ) {
			ev.preventDefault();
			const answersEl = $( ev.currentTarget ).closest( '.wp-quiz-answers' );
			// const answer = this.parseAnswer( {} );
			const baseName = answersEl.attr( 'data-base-name' );
			const index = answersEl.find( '.wp-quiz-answer' ).length;
			this.addAnswer( answersEl, {}, index, baseName, true );
		}

		handleClickRemoveAnswer( ev ) {
			ev.preventDefault();
			const answerEl = $( ev.currentTarget ).closest( '.wp-quiz-answer' );
			this.removeAnswer( answerEl );
		}

		handleClickRemoveResult( ev ) {
			ev.preventDefault();
			const resultEl = $( ev.currentTarget ).closest( '.wp-quiz-result' );
			this.removeResult( resultEl );
		}

		handleClickUploadImage( ev ) {
			ev.preventDefault();

			const $this = $( ev.currentTarget );
			const $el = $this.closest( '.wp-quiz-image-upload' );
			let $imageUrl;

			if ( $this.closest( '.wp-quiz-video-upload' ).length ) {
				const questionId = $this.closest( '.wp-quiz-question' ).attr( 'data-id' );
				$imageUrl = this.$wrapper.find( `.wp-quiz-question[data-id="${ questionId }"] .wp-quiz-image-upload-url` );
			} else {
				$imageUrl = $el.find( '.wp-quiz-image-upload-url' );
			}

			const $imageId = $el.find( '.wp-quiz-image-upload-id' );
			const $imagePreview = $el.find( '.wp-quiz-image-upload-preview' );
			const editText = $el.attr( 'data-edit-text' );

			const imageUploadFrame = wp.media({
				multiple: false,
				library: {
					type: [ 'image' ]
				}
			});

			imageUploadFrame.on( 'open', () => {
				let id, selection, attachment;
				if ( $imageId.length ) {
					id = $imageId.val();
				}
				if ( ! id ) {
					return;
				}

				selection = imageUploadFrame.state().get( 'selection' );
				attachment = wp.media.attachment( id );
				attachment.fetch();
				selection.add( attachment );
			});

			imageUploadFrame.on( 'select', () => {
				const attachment = imageUploadFrame.state().get( 'selection' ).first().toJSON();
				if ( $imageId.length ) {
					$imageId.val( attachment.id ).trigger( 'change' );
				}
				if ( $imageUrl.length ) {
					$imageUrl.val( attachment.url ).trigger( 'change' );
				}
				if ( $imagePreview.length ) {
					$imagePreview.html( '<img src="' + attachment.url + '">' );
				}
				if ( editText ) {
					$this.text( editText );
				}
				$el.removeClass( 'no-image' );
			});

			imageUploadFrame.open();
		}

		handleClickRemoveImage( ev ) {
			ev.preventDefault();

			const $this = $( ev.currentTarget );
			const $el = $this.closest( '.wp-quiz-image-upload' );
			const $imageUrl = $el.find( '.wp-quiz-image-upload-url' );
			const $imageId = $el.find( '.wp-quiz-image-upload-id' );
			const $imagePreview = $el.find( '.wp-quiz-image-upload-preview' );
			const $uploadBtn = $el.find( '.wp-quiz-image-upload-btn' );
			const uploadText = $el.attr( 'data-upload-text' );

			if ( $imageId.length ) {
				$imageId.val( '' );
			}
			if ( $imageUrl.length ) {
				$imageUrl.val( '' );
			}
			if ( $imagePreview.length ) {
				$imagePreview.html( '' );
			}
			if ( uploadText ) {
				$uploadBtn.text( uploadText );
			}
			$el.addClass( 'no-image' );
		}

		handleClickSetQuestionType( ev ) {
			ev.preventDefault();
			const $this = $( ev.currentTarget );
			const questionEl = $this.closest( '.wp-quiz-question' );
			const type = $this.attr( 'data-type' );
			questionEl.find( '.wp-quiz-question-media-type' ).val( type );
			questionEl.attr( 'data-media-type', type );
		}

		handleClickSetAnswerType( ev ) {
			ev.preventDefault();
			const $this = $( ev.currentTarget );
			const type = $this.attr( 'data-type' );
			$this.closest( '.wp-quiz-question' ).find( '.wp-quiz-question-answer-type' ).val( type );
			$this.closest( '.wp-quiz-answers' ).attr( 'data-type', type );
		}

		handleClickUploadVideoBtn( ev ) {
			ev.preventDefault();

			const questionId = $( ev.currentTarget ).closest( '.wp-quiz-question' ).attr( 'data-id' );
			const $videoUrl = this.$wrapper.find( `.wp-quiz-question[data-id="${ questionId }"] .wp-quiz-video-upload-url` );
			const frame = wp.media({
				multiple: false,
				library: {
					type: [ 'video' ]
				}
			});

			frame.on( 'select', () => {
				const attachment = frame.state().get( 'selection' ).first().toJSON();
				if ( $videoUrl.length ) {
					$videoUrl.val( attachment.url ).trigger( 'change' );
					// this.$wrapper.find( '.wp-quiz-video-upload-url' ).val( attachment.url ).trigger( 'change' );
				}
			});

			frame.open();
		}

		handleChangeVideoData( ev ) {
			const $el = $( ev.currentTarget ).closest( '.wp-quiz-video-upload' );
			this.loadVideoPreview( $el );
		}

		resizeTextareas() {
			this.$wrapper.find( 'textarea[data-autoresize]' ).each( function() {
				wpQuizAdmin.helpers.resizeTextarea( $( this ) );
			});
		}

		reIndexQuestions() {
			this.$wrapper.find( '.wp-quiz-question' ).each( function( index ) {
				$( this ).find( '.wp-quiz-question-number' ).text( index + 1 );
			});
		}

		loadVideoPreview( $el ) {
			const $videoPreview = $el.find( '.wp-quiz-video-upload-preview' );
			let videoUrl = $el.find( '.wp-quiz-video-upload-url' ).val() || '';
			let imageUrl = $el.find( '.wp-quiz-image-upload-url' ).val() || '';
			const $error = $el.find( '.wp-quiz-video-upload-error' );

			videoUrl = videoUrl.trim();
			imageUrl = imageUrl.trim();

			$error.html( '' );
			$videoPreview.html( '' );
			$el.addClass( 'no-video' );

			if ( ! videoUrl ) {
				return;
			}

			const data = {
				video_url: videoUrl,
				poster_url: imageUrl,
			};
			const url = wpQuizAdmin.restUrl + 'wp-quiz/v2/video-content';
			const request = wpQuizAdmin.helpers.getRequest({
				url: url,
				method: 'GET',
				data: data
			});

			request.done( response => {
				$videoPreview.html( response );
				$el.removeClass( 'no-video' );
			});

			request.fail( response => {
				$error.text( JSON.stringify( response ) );
			});
		}

		sortAnswers( $el ) {
			$el.find( '.wp-quiz-answers-list' ).sortable({
				items: '> .wp-quiz-answer',
				placeholder: 'wp-quiz-answer wp-quiz-answer-placeholder',
				start: ( ev, ui ) => {
					ui.placeholder.height( ui.item.height() );
				}
			});
		}

		sortQuestions() {
			this.$wrapper.find( '.wp-quiz-questions-list' ).sortable({
				// handle: '.wp-quiz-question-number',
				items: '> .wp-quiz-question',
				placeholder: 'wp-quiz-question wp-quiz-question-placeholder',
				start: ( ev, ui ) => {
					ui.placeholder.height( ui.item.height() );
				},
				update: ( ev, ui ) => {
					this.sortAnswers( this.$wrapper );
					this.reIndexQuestions();
				}
			});
		}

		handleClickAddResult( ev ) {
			ev.preventDefault();
			// const result = this.parseResult( {} );
			const index = this.resultsEl.find( '.wp-quiz-result' ).length;
			this.addResult( {}, index, true );
		}

		sortResults() {
			this.$wrapper.find( '.wp-quiz-results-list' ).sortable({
				items: '> .wp-quiz-result',
				placeholder: 'wp-quiz-result wp-quiz-result-placeholder',
				start: ( ev, ui ) => {
					ui.placeholder.height( ui.item.height() );
				}
			});
		}
	};

	/**
	 * Meta boxes functions.
	 */
	const functions = {
		saveQuiz: function() {
			const $form = $( 'form#post' );
			if ( ! $form.length ) {
				return;
			}
			$form.on( 'submit', function( ev ) {
				const formData = $form.serializeObject();
				window.onbeforeunload = null; // Remove leaving page confirmation.
				if ( undefined === formData.quizData ) {
					return;
				}
				const quizData = formData.quizData;
				$form.append( '<textarea name="post_content" style="display: none;">' + JSON.stringify( quizData ) + '</textarea>' );
				$form.find( '.wp-quiz-content-settings *[name]' ).removeAttr( 'name' );
			});
		},

		autoresizeTextarea: function() {
			$( document ).on( 'keyup input', 'textarea[data-autoresize]', function() {
				wpQuizAdmin.helpers.resizeTextarea( $( this ) );
			});
			$( document ).on( 'wp-quiz-activated-tab', function() {
				$( 'textarea[data-autoresize]' ).each( function() {
					wpQuizAdmin.helpers.resizeTextarea( $( this ) );
				});
			});
		},

		leavingPageConfirmation: function() {
			if ( window.onbeforeunload ) {
				return;
			}

			$( '.wp-quiz-meta-box-wrap' ).on( 'change, click', ev => {
				if ( window.onbeforeunload ) {
					return;
				}
				window.onbeforeunload = ev => {
					ev.returnValue = window.postL10n.saveAlert;
					return window.postL10n.saveAlert;
				};
			});
		},

		changeColorsWhenSwitchSkin: function() {
			const traditionalBgColor = '#f2f2f2';
			const flatBgColor = '#ecf0f1';

			$( document ).on( 'change', '#wp_quiz_skin1', function() {
				if ( $( this ).prop( 'checked' ) ) {
					$( '#wp_quiz_background_color' ).iris( 'color', traditionalBgColor );
				}
			});

			$( document ).on( 'change', '#wp_quiz_skin2', function() {
				if ( $( this ).prop( 'checked' ) ) {
					$( '#wp_quiz_background_color' ).iris( 'color', flatBgColor );
				}
			});
		},

		ready: function() {
			this.saveQuiz();
			this.autoresizeTextarea();
			this.leavingPageConfirmation();
			this.changeColorsWhenSwitchSkin();
		}
	};

	$( document ).ready( function() {
		functions.ready();
	});
})( wpQuizAdmin, jQuery );
