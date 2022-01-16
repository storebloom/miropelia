<?php
/**
 * Bootstraps the Sharethis Follow Buttons plugin.
 *
 * @package SharethisFollowButtons
 */

namespace SharethisFollowButtons;

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

		// Global.
		$button_widget = new Button_Widget( $this );

		// Initiate classes.
		$classes = array(
			$button_widget,
			new Follow_Buttons( $this, $button_widget ),
			new Minute_Control( $this ),
		);

		// Add classes doc hooks.
		foreach ( $classes as $instance ) {
			$this->add_doc_hooks( $instance );
		}

		// Define some prefixes to use througout the plugin.
		$this->assets_prefix = strtolower( preg_replace( '/\B([A-Z])/', '-$1', __NAMESPACE__ ) );
		$this->meta_prefix = strtolower( preg_replace( '/\B([A-Z])/', '_$1', __NAMESPACE__ ) );
	}

	/**
	 * Register MU Script
	 *
	 * @action wp_enqueue_scripts
	 */
	public function register_assets() {
		$propertyid = get_option( 'sharethis_property_id' );
		$propertyid = false !== $propertyid && null !== $propertyid ? explode( '-', $propertyid, 2 ) : array();
		$first_prod = get_option( 'sharethis_first_product' );
		$first_prod = false !== $first_prod && null !== $first_prod ? $first_prod : '';

		if ( in_array( $first_prod, array( 'inline', 'sticky' ), true ) ) {
			$first_prod = $first_prod . '-share';
		}

		$sb_active = in_array( 'sharethis-share-buttons/sharethis-share-buttons.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ), true );

		if ( ! $sb_active && is_array( $propertyid ) && array() !== $propertyid ) {
			wp_register_script( "{$this->assets_prefix}-mu", "//platform-api.sharethis.com/js/sharethis.js#property={$propertyid[0]}&product=reviews", null, '1.0.0', true );
		}
	}

	/**
	 * Register admin scripts/styles.
	 *
	 * @action admin_enqueue_scripts
	 */
	public function register_admin_assets() {
		// Check if the ShareThis script is already enqueued from another plugin.
		if ( ! wp_script_is( 'sharethis-follow-buttons-mua', 'registered' ) ) {
			wp_register_script( "{$this->assets_prefix}-mua", '//platform-api.sharethis.com/js/sharethis.js?product=inline-follow-buttons', null, null, false );
			wp_register_script( "{$this->assets_prefix}-admin", "{$this->dir_url}js/admin.js", array(
				'jquery',
				'jquery-ui-sortable',
				'wp-util',
			), time() );
			wp_register_script( "{$this->assets_prefix}-meta-box", "{$this->dir_url}js/meta-box.js", array(
				'jquery',
				'wp-util',
			) );
			wp_register_script( "{$this->assets_prefix}-credentials", "{$this->dir_url}js/set-credentials.js", array(
				'jquery',
				'jquery-ui-sortable',
				'wp-util',
			) );
			wp_register_style( "{$this->assets_prefix}-admin", "{$this->dir_url}css/admin.css", false, time() );
			wp_register_style( "{$this->assets_prefix}-meta-box", "{$this->dir_url}css/meta-box.css", false );
		}
	}
}
