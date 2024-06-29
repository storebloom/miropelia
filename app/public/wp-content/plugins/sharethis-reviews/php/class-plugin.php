<?php
/**
 * Bootstraps the ShareThis Reviews plugin.
 *
 * @package ShareThisReviews
 */

namespace ShareThisReviews;

/**
 * Main plugin bootstrap file.
 */
class Plugin extends Plugin_Base {

	/**
	 * Plugin assets prefix.
	 *
	 * @var string Lowercased dashed prefix.
	 */
	public $assets_prefix;

	/**
	 * Plugin meta prefix.
	 *
	 * @var string Lowercased underscored prefix.
	 */
	public $meta_prefix;

	/**
	 * Plugin constructor.
	 */
	public function __construct() {
		parent::__construct();

		// Global variables.
		$register = new Register( $this );

		// Initiate classes.
		$classes = array(
			$register,
			new Reviews( $this, $register ),
		);

		// Add classes doc hooks.
		foreach ( $classes as $instance ) {
			$this->add_doc_hooks( $instance );
		}

		// Define some prefixes to use througout the plugin.
		$this->assets_prefix = strtolower( preg_replace( '/\B([A-Z])/', '-$1', __NAMESPACE__ ) );
		$this->meta_prefix   = strtolower( preg_replace( '/\B([A-Z])/', '_$1', __NAMESPACE__ ) );
	}

	/**
	 * Register admin scripts/styles.
	 *
	 * @action admin_enqueue_scripts
	 */
	public function register_admin_assets() {
		wp_register_script( "{$this->assets_prefix}-admin", "{$this->dir_url}js/admin.js", array(
			'jquery',
			'wp-util',
		), '1.0.0', true );
		wp_enqueue_script( 'wp-color-picker' );
		wp_enqueue_style( 'wp-color-picker' );
		wp_register_style( "{$this->assets_prefix}-admin", "{$this->dir_url}css/admin.css", false, time() );
	}

	/**
	 * Register scripts/styles.
	 *
	 * @action wp_enqueue_scripts
	 */
	public function register_assets() {
		$propertyid = get_option( 'sharethis_property_id' );
		$propertyid = false !== $propertyid && null !== $propertyid ? explode( '-', $propertyid, 2 ) : array();
		$first_prod = get_option( 'sharethis_first_product' );
		$first_prod = false !== $first_prod && null !== $first_prod ? $first_prod : '';
		$sb_active  = in_array( 'sharethis-share-buttons/sharethis-share-buttons.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ), true );

		if ( ! $sb_active && is_array( $propertyid ) && array() !== $propertyid ) {
			wp_register_script( "{$this->assets_prefix}-mu", "//platform-api.sharethis.com/js/sharethis.js#property={$propertyid[0]}&product=reviews&source=sharethis-reviews-wordpress", null, '1.0.0', true );
		}

		wp_register_script( "{$this->assets_prefix}-review", "{$this->dir_url}js/review.js", array(
			'jquery',
			'wp-util',
		), time(), true );
		wp_register_style( "{$this->assets_prefix}-review", "{$this->dir_url}css/sharethisreviews.css", false, '1.0.5' );
	}
}
