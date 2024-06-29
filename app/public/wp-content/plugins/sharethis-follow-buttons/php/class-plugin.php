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
			new Follow_Buttons( $this, $button_widget ),
			$button_widget,
			new Minute_Control( $this ),
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
			wp_register_script( "{$this->assets_prefix}-mu", "//platform-api.sharethis.com/js/sharethis.js#property={$propertyid[0]}&product=inline-follow-buttons&source=sharethis-follow-buttons-wordpress", null, '1.0.0', true );
		}
	}

	/**
	 * Register admin scripts/styles.
	 *
	 * @action admin_enqueue_scripts
	 */
	public function register_admin_assets() {
		// Check if the ShareThis script is already enqueued from another plugin.
		if ( false === wp_script_is( 'sharethis-follow-buttons-mua', 'registered' ) ) {
			wp_register_script(
				"{$this->assets_prefix}-mua",
				'//platform-api.sharethis.com/js/sharethis.js?product=inline-follow-buttons',
				null,
				SHARETHIS_FOLLOW_BUTTONS_VERSION,
				false
			);
			wp_register_script(
				"{$this->assets_prefix}-admin",
				"{$this->dir_url}js/admin.js",
				array(
					'jquery',
					'jquery-ui-sortable',
					'wp-util',
				),
				time(),
				false
			);
			wp_register_script(
				"{$this->assets_prefix}-meta-box",
				"{$this->dir_url}js/meta-box.js",
				array(
					'jquery',
					'wp-util',
				),
				filemtime( "{$this->dir_path}js/meta-box.js" ),
				false
			);
			wp_register_style(
				"{$this->assets_prefix}-admin",
				"{$this->dir_url}css/admin.css",
				false,
				filemtime( "{$this->dir_path}css/admin.css" )
			);
			wp_register_style(
				"{$this->assets_prefix}-meta-box",
				"{$this->dir_url}css/meta-box.css",
				false,
				filemtime( "{$this->dir_path}css/meta-box.css" )
			);
		}
	}

	/**
	 * Helper to get the formated network image.
	 *
	 * @param string $title The netwokr title.
	 *
	 * @return string
	 */
	public static function getFormattedNetworkImage( $title ) {
		return 'https://platform-cdn.sharethis.com/img/' . self::getPlatformName( $title ) . '.svg';
	}

	/**
	 * Helper to format network title for image retrieval.
	 *
	 * @param string $title The network title.
	 *
	 * @return string
	 */
	public static function getFormattedNetworkTitle( $title ) {
		return sanitize_title(
			str_replace(
				array( ' Share Button', 'Google Bookmarks', 'Yahoo Mail' ),
				array( '', 'Bookmarks', 'YahooMail' ),
				$title
			)
		);
	}

	/**
	 *
	 * Strips name to look like platform name.
	 *
	 * @param string $title Title string.
	 *
	 * @return string Modified title string.
	 */
	public static function getPlatformName( $title ) {
		return str_replace(
			array( '-pin', 'facebook-messenger', 'sina-', '-ru', 'yahoo-mail', 'okru' ),
			array( '', 'messenger', '', 'ru', 'yahoomail', 'odnoklassniki' ),
			$title
		);
	}
}
