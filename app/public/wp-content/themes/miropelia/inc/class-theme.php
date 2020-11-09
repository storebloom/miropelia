<?php
/**
 * Bootstraps the Supra Custom theme.
 *
 * @package Miropelia
 */

namespace Miropelia;

/**
 * Main plugin bootstrap file.
 */
class Theme extends Theme_Base
{

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
	public function __construct()
    {
		parent::__construct();

		// Initiate classes.
		$classes = array(
			new Register( $this ),
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
	 * Register Front Assets
	 *
	 * @action wp_enqueue_scripts
	 */
	public function registerAssets()
    {
		global $post;

		wp_register_style( $this->assets_prefix, "{$this->dir_url}/assets/dist/css/app.css", null, time() );
        wp_register_script( $this->assets_prefix, "{$this->dir_url}/assets/dist/js/app.min.js", [], time() );

        // reCaptcha.
        wp_register_script('recaptcha', 'https://www.google.com/recaptcha/api.js', [], '1', false);
	}

	/**
     * Register admin assets.
     *
     * @action admin_enqueue_scripts
     */
	public function registerAdminAssets()
    {
        wp_enqueue_style( $this->assets_prefix . '-admin', "{$this->dir_url}/assets/dist/css/admin.css", null, time());
    }
}
