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
            new Explore( $this ),
			new Register( $this ),
            new Meta_Box( $this ),
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
        wp_register_script( 'explore', "{$this->dir_url}/assets/dist/js/explore.min.js", [$this->assets_prefix], time() );

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
        wp_register_style( $this->assets_prefix . '-admin', "{$this->dir_url}/assets/dist/css/admin.css", null, time());
        wp_register_script( $this->assets_prefix . '-admin', "{$this->dir_url}/assets/dist/js/admin.min.js", null, time());
    }

	/**
	 * Add GTM.
	 *
	 * @action wp_footer
	 */
	public function addGTM()
	{
		?>
		<script>(function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start': new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0], j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src= 'https://www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);})(window,document,'script','dataLayer','GTM-5HJW8P2');</script>
	<?php
	}
}
