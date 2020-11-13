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
			this.$managementContainer = $( '.review-management-wrap' );
			this.$container = $( '#review-menu-wrap' );
			this.$metaContainer = $( '.rating-metabox' );
			this.listen();
			
			$( '.toplevel_page_sharethisreviews-general .form-table:first-of-type' ).addClass( 'active-tab-content' );

			if ( false === this.data.propertySet ) {
				this.createProperty();
			}

			// Add color picker to setting input.
			$('#sharethisreviews_review_section_ctacolor').wpColorPicker();
		},

		/**
		 * Initiate listeners.
		 */
		listen: function () {
			var self = this,
				timer = '';

			this.$managementContainer.on( 'click', '.approve-review', function() {
				var postid = $( this ).attr( 'data-id' ),
					pos = $( this ).attr( 'data-pos' );

				self.approveReview( postid, pos );
			} );

			this.$managementContainer.on( 'click', '.retract-review', function() {
				var postid = $( this ).attr( 'data-id' ),
					pos = $( this ).attr( 'data-pos' );

				self.retractReview( postid, pos );
			} );

			this.$managementContainer.on( 'click', '.remove-review', function() {
				var postid = $( this ).attr( 'data-id' ),
					pos = $( this ).attr( 'data-pos' );

				self.removeReview( postid, pos, this );
			} );

			this.$metaContainer.on( 'click', '.remove-rating', function() {
				var postid = $( this ).attr( 'data-id' ),
					pos = $( this ).attr( 'data-pos' );

				self.removeRating( postid, pos, this );
			} );

			this.$container.on( 'click', '.str-tab-wrap li', function() {
				var tab = $( this ).attr( 'data-tab' );

				$( '.str-tab-wrap li' ).removeClass( 'active-tab' );
				$( this ).addClass( 'active-tab' );
				$( this ).closest( '.str-tab-wrap' ).siblings( 'form' ).find( '.form-table' ).removeClass( 'active-tab-content' );

				if ( 'reviews-wrap' === tab ) {
					$( this ).closest( '.str-tab-wrap' ).siblings( 'form' ).find( '.form-table:first-of-type' ).addClass( 'active-tab-content' );
				}

				if ( 'ratings-wrap' === tab ) {
					$( this ).closest( '.str-tab-wrap' ).siblings( 'form' ).find( '.form-table:nth-of-type(2)' ).addClass( 'active-tab-content' );
				}

				if ( 'impressions-wrap' === tab ) {
					$( this ).closest( '.str-tab-wrap' ).siblings( 'form' ).find( '.form-table:nth-of-type(3)' ).addClass( 'active-tab-content' );
				}
			} );

			// Show tool tip.
			this.$container.on( 'mouseover', '.st-tooltip-icon', function() {
				var leftPos = $( this ).offset().left - 140,
					topPos = $( this ).offset().top - 48;

				$( this ).siblings( '.st-tooltip' ).css( {'left': leftPos  + 'px', 'top': topPos + 'px'} ).fadeIn();
			} );

			// Hide tool tip.
			this.$container.on( 'mouseout', '.st-tooltip-icon', function() {
				$( this ).siblings( '.st-tooltip' ).fadeOut();
			} );
		},

		/**
		 * Add review to current post.
		 *
		 * @param postid
		 * @param pos
		 */
		approveReview: function( postid, pos ) {
			wp.ajax.post( 'approve_review', {
				postid: postid,
				pos: pos,
				nonce: this.data.nonce
			} ).always( function() {
				window.location.reload();
			} );
		},

		/**
		 * Retract review to current post.
		 *
		 * @param postid
		 * @param pos
		 */
		retractReview: function( postid, pos ) {
			wp.ajax.post( 'retract_review', {
				postid: postid,
				pos: pos,
				nonce: this.data.nonce
			} ).always( function() {
				window.location.reload();
			} );
		},

		/**
		 * Remove review from current post.
		 *
		 * @param postid
		 * @param pos
		 * @param item
		 */
		removeReview: function( postid, pos, item ) {
			var confirmed = confirm('Are you sure you want to remove this review?');

			if ( confirmed ) {
				wp.ajax.post( 'remove_review', {
					postid: postid,
					pos: pos,
					nonce: this.data.nonce
				} ).always( function () {
					$( item ).closest( '.review-item' ).remove();
				} );
			}
		},

		/**
		 * Remove rating from current post.
		 *
		 * @param postid
		 * @param pos
		 * @param item
		 */
		removeRating: function( postid, pos, item ) {
			var confirmed = confirm('Are you sure you want to remove this rating?');

			if ( confirmed ) {
				wp.ajax.post( 'remove_rating', {
					postid: postid,
					pos: pos,
					nonce: this.data.nonce
				} ).always( function() {
					$( item ).closest( '.rating-item' ).remove();
				} );
			}
		},

		/**
		 * Create property.
		 */
		createProperty: function() {
			var theData = JSON.stringify( {
				product: 'reviews',
			} ),
			self = this;

			$.ajax( {
				url: 'https://platform-api.sharethis.com/v1.0/property',
				method: 'POST',
				async: false,
				contentType: 'application/json; charset=utf-8',
				data: theData,
				success: function( results ) {
					self.setCredentials(results._id);
				}
			} );
		},

		/**
		 * Set credentials in options.
		 *
		 * @param propid
		 */
		setCredentials: function( propid ) {
			wp.ajax.post( 'set_credentials', {
				data: propid,
				nonce: this.data.nonce
			} ).always( function() {
			}.bind( this ) );
		}
	};
} )( window.jQuery, window.wp );
