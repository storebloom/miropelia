( function( wpQuiz, $ ) {
	"use strict";

	$( document ).ready( function() {
		// Flip effect.
		$( document ).on( 'click', '.wq-quiz-flip .card', function() {
			if ( ! $( this ).find( '.front' ).length || ! $( this ).find( '.back' ).length ) {
				return;
			}
			$( this ).closest( '.wq_singleQuestionWrapper' ).toggleClass( 'is-flipped' );
		});

		// Embed toggle.
		$( document ).on( 'click', '.wq-quiz-flip .wq-embed-toggle-btn', function( ev ) {
			ev.preventDefault();
			$( this ).parent().toggleClass( 'active' ).next().slideToggle( 'fast' );
		});

		// FB share.
		$( document ).on( 'click', '.wq-share-fb', ev => {
			if ( typeof FB === 'undefined' ) {
				return;
			}
			ev.preventDefault();

			const shareUrl = ev.currentTarget.dataset.url;
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

			const shareUrl = ev.currentTarget.dataset.url;
			window.open(
				'https://twitter.com/intent/tweet?url=' + encodeURIComponent( shareUrl ),
				'_blank',
				'width=500, height=300'
			);
		});

		// VK.
		$( document ).on( 'click', '.wq-share-vk', ev => {
			ev.preventDefault();

			const shareUrl = ev.currentTarget.dataset.url;
			window.open(
				'http://vk.com/share.php?url=' + encodeURIComponent( shareUrl ),
				'_blank',
				'width=500, height=300'
			);
		});
	});
})( wpQuiz, jQuery );
