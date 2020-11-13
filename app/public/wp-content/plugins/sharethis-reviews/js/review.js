/**
 * ShareThis Reviews
 *
 * @package ShareThis_Reviews
 */

/* exported Review */
var Review = ( function( $, wp ) {
	'use strict';

	return {
		/**
		 * Holds data.
		 */
		data: {},

		/**
		 * Boot plugin.
		 */
		boot: function ( data ) {
			this.data = data;

			$( document ).ready( function () {
				this.init();
			}.bind( this ) );
		},

		/**
		 * Initialize plugin.
		 */
		init: function () {
			this.$container = $( '.review-section-wrap' );
			this.$impContainer = $( '.impression-wrap' );
			this.listen();
		},

		/**
		 * Initiate listeners.
		 */
		listen: function () {
			var self = this,
				timer = '';

			this.$container.on( 'click', '#submit-user-review', function() {
				var review = $( this ).siblings( 'textarea' ).val(),
					title = $( this ).siblings( '#title' ).val(),
					name = 0 !== $( '#name' ).length ? $( this ).siblings( '#name' ).val() : '',
					rating = 0 !== $( 'input[name="st-review-rating"]' ).length ? $( 'input[name="st-review-rating"]:checked' ).val() : '';

				self.addReview( review, title, rating, name );
			} );

			this.$container.on( 'click', '#submit-user-rating', function() {
				var name = 0 !== $( '#name' ).length ? $( this ).siblings( '#name' ).val() : '',
					rating = $( 'input[name="st-review-rating"]:checked' ).val();

				self.addRating( rating, name );
			} );

			this.$impContainer.on( 'click', '.st-impression', function() {
				var impression = $( this ).attr( 'data-imp' ),
					count = parseInt( $( this ).siblings( '.overall-impression' ).html() ) + 1,
					impressioned = self.getCookie( 'st-impression=' );

				if ( '' === impressioned ) {

					// Add one to count.
					$( this ).siblings( '.overall-impression' ).html( count );

					self.addImpression( impression );
				}
			} );

			this.$container.on( 'click', '.stars input[type="radio"], .hearts input[type="radio"]', function() {
				var number = parseInt( $( this ).val() ) + 1,
					i;

				$( '.review-section-wrap .rating-wrap .rating-icon svg path' ).attr( 'style', '' );

				for ( i = number; i <= 4; i++ ) {
					$( '#sharethis-rating-' + i ).siblings( 'label' ).find( '.symbol-icon-wrap svg path' ).css( 'fill', 'currentColor' );
				}
			} );

			// Open review form.
			this.$container.on( 'click', '#open-review-form', function() {
				$( this ).fadeOut();

				setTimeout(function() {
					$( '.review-hidden-wrap' ).fadeIn();
				}, 500);
			} );
		},

		/**
		 * Add review to current post.
		 *
		 * @param review
		 * @param title
		 * @param rating
		 * @param name
		 */
		addReview: function( review, title, rating, name ) {
			wp.ajax.post( 'add_review', {
				postid: this.data.postid,
				rating: rating,
				review: review,
				title: title,
				name: name,
				nonce: this.data.nonce
			} ).always( function( results ) {
				$( 'input[name="st-review-rating"]' ).prop( 'checked', false );
				$( '.review-section-wrap textarea' ).val( '' );
				$( '.review-section-wrap input' ).val( '' );
				window.location.reload();
			} );
		},

		/**
		 * Add rating to current post.
		 *
		 * @param rating
		 * @param name
		 */
		addRating: function( rating, name ) {
			wp.ajax.post( 'add_rating', {
				postid: this.data.postid,
				name: name,
				rating: rating,
				nonce: this.data.nonce
			} ).always( function() {
				$( 'input[name="st-review-rating"]' ).prop( 'checked', false );
				$( '.review-section-wrap input' ).val( '' );
				window.location.reload();
			} );
		},

		/**
		 * Add impression to current post.
		 *
		 * @param impression
		 */
		addImpression: function( impression ) {
			var d = new Date(),
				expires;

			d.setTime(d.getTime() + (30*24*60*60*1000));
			expires = "expires="+ d.toUTCString();

			wp.ajax.post( 'add_impression', {
				postid: this.data.postid,
				impression: impression,
				nonce: this.data.nonce
			} ).always( function() {
				document.cookie = 'st-impression=true; ' + expires + '; path=/';
			} );
		},

		/**
		 * Helper function to read cookies.
		 *
		 * @param name
		 */
		getCookie: function( name ) {
			var decodedCookie = decodeURIComponent( document.cookie ),
				ca = decodedCookie.split( ';' ),
				c,
				i;

			for( i = 0; i < ca.length; i++ ) {
				c = ca[ i ];
				while ( c.charAt(0) === ' ' ) {
					c = c.substring(1);
				}
				if ( c.indexOf( name ) === 0 ) {
					return c.substring( name.length, c.length );
				}
			}
			return '';
		}
	};
} )( window.jQuery, window.wp );
