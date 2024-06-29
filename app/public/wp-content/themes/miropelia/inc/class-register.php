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
class Register
{

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
	public function __construct($theme)
    {
		$this->theme = $theme;
	}

    /**
     * Enqueue Assets for admin.
     *
     * @action admin_enqueue_scripts
     *
     * @param string $hook
     */
    public function enqueueAdminAssets($hook)
    {
        if ('post.php' === $hook) {
            wp_enqueue_style($this->theme->assets_prefix . '-admin');
            wp_enqueue_script($this->theme->assets_prefix . '-admin');
        }
    }

	/**
	 * Enqueue Assets for front ui.
	 *
	 * @action wp_enqueue_scripts
	 */
	public function enqueueAssets()
    {
		global $post;

		wp_enqueue_style($this->theme->assets_prefix);
        wp_enqueue_style("{$this->theme->assets_prefix}-font", 'https://fonts.googleapis.com/css2?family=Roboto+Slab:wght@300;400;600&display=swap');

        if (is_page(['register', 'explore'])) {
	        $explore_points = get_user_meta(get_current_user_id(), 'explore_points', true);
	        $explore_points = isset($explore_points) ? $explore_points: [];

            if ('' === $explore_points) {
                $explore_points = [
                    'health' => ['points' => 100, 'positions' => []],
                    'mana' => ['points' => 100, 'positions' => []],
                    'point' => ['points' => 0, 'positions' => []],
                    'gear' => ['positions' => []],
                    'weapons' => ['positions' => []]
                ];
            }

            wp_add_inline_script(
                'explore',
                'const restApiKey ="' . REST_API_KEY . '";' .
                'const currentUserId ="' . get_current_user_id() . '";' .
                'const explorePoints = ' . wp_json_encode($explore_points) . ';' .
                'const siteUrl = "' . get_home_url() . '";' .
                'const wpThemeURL = "' . str_replace(['https://', 'http://', 'www'], '', get_home_url()) . '";' .
                'const levelMaps = "' . wp_json_encode(Explore::getLevelMap()) . '";'
            );
        } else {
            wp_enqueue_script($this->theme->assets_prefix);
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
	public function hideAdminBar()
    {
        if (!current_user_can('administrator') && !is_admin()) {
            show_admin_bar(false);
        }

        if (is_admin() && !current_user_can('administrator') &&
             ! (wp_doing_ajax())) {
            wp_redirect(home_url());
            exit;
        }
    }

    /**
     * Don't allow access to wp-login.php
     *
     * @action login_init
     */
    public function noLoginPage()
    {
        if (is_admin() && !current_user_can('administrator') &&
            ! (wp_doing_ajax())) {
            $url = filter_input(INPUT_GET, 'redirect_to', FILTER_UNSAFE_RAW);
            $url = false === empty($url) ? esc_url($url) : esc_url(home_url());
            wp_redirect($url);
        }
    }

    /**
     * Auto log user in or out.
     *
     * @action init
     */
    public function logUserInOut()
    {
		$logout = filter_input(INPUT_GET, 'logoutuser', FILTER_SANITIZE_STRING);
		$login = filter_input(INPUT_GET, 'loginuser', FILTER_SANITIZE_STRING);

		if (false !== is_null($logout) && false !== is_null($login)) {
            return;
        }

		if (false === is_null($login)) {
			$user = get_user_by( 'login', $login );

			// If no error received, set the WP Cookie
			if ( false === is_wp_error( $user ) ) {
				wp_clear_auth_cookie();
				wp_set_current_user( $user->ID ); // Set the current user detail
				wp_set_auth_cookie( $user->ID ); // Set auth details in cookie
			}
		}

        if (false === is_null($logout)) {
            wp_logout();
            wp_safe_redirect('/');
        }
    }

    /**
     * Redirect if user gets to free chapters without query.
     * @action wp_head
     */
    public function noChaptersForYou()
    {
        $free_chapters = filter_input(INPUT_GET, 'theyearnedit', FILTER_UNSAFE_RAW);
        if (is_page('thank-you-for-signing-up-enjoy-the-first-two-chapters-or-orbem') && 'true' !== $free_chapters) {
            wp_safe_redirect('/register');
        }
    }

    /**
     * Dequeue recaptcha.
     *
     * @action wp_enqueue_scripts 999
     * @action wp_head 999
     */
    public function dequeueGoogle()
    {
        wp_dequeue_script('google-recaptcha');
    }

    /**
     * Register form shortcode.
     *
     * @shortcode register-form
     */
    public function registerForm($atts = '')
    {
        $explore = false === empty($atts['explore']) && 'true' === $atts['explore'];
        $html = '<div class="form-wrapper">
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
		        ';

        if ( false === $explore ) {
            $html .=
                '<p>
		            <label for="two_chapters" style="font-size: 20px;">Read the first two chapters of Orbem for FREE</label>
		            <span style="display: flex;flex-wrap: nowrap;gap:.5rem;margin-bottom: .5rem;"><span style="flex-basis: 30px; font-size: 18px;">Yes</span><input style="width: 20px;" type="radio" name="two_chapters" value="yes"></span>
		            <span style="display: flex;flex-wrap: nowrap;gap:.5rem;"><span style="flex-basis: 30px;">No</span><input style="width: 20px;font-size: 18px;" type="radio" name="two_chapters" value="no" checked="checked"></span>
                </p>';
        }

        $html .= '<p class="submit">
		            <button type="button" id="register-submit">' . esc_html('Join') . '</button>
		        </p>
		    </div>';


        return $html;
    }
}