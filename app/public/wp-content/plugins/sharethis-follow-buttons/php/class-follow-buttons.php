<?php
/**
 * Follow Buttons.
 *
 * @package ShareThisFollowButtons
 */

namespace ShareThisFollowButtons;

/**
 * Follow Buttons Class
 *
 * @package ShareThisFollowButtons
 */
class Follow_Buttons {

	/**
	 * Plugin instance.
	 *
	 * @var object
	 */
	public $plugin;

	/**
	 * Button Widget instance.
	 *
	 * @var object
	 */
	public $button_widget;

	/**
	 * Menu slug.
	 *
	 * @var string
	 */
	public $menu_slug;

	/**
	 * Menu hook suffix.
	 *
	 * @var string
	 */
	private $hook_suffix;

	/**
	 * Holds the settings sections.
	 *
	 * @var string
	 */
	public $setting_sections;

	/**
	 * Holds the settings fields.
	 *
	 * @var string
	 */
	public $setting_fields;

	/**
	 * Holds the follow settings fields.
	 *
	 * @var string
	 */
	public $follow_setting_fields;

	/**
	 * Class constructor.
	 *
	 * @param object $plugin Plugin class.
	 * @param object $button_widget Button Widget class.
	 */
	public function __construct( $plugin, $button_widget ) {
		$this->button_widget = $button_widget;
		$this->plugin        = $plugin;
		$this->menu_slug     = 'sharethis';
		$this->set_settings();

		// Configure your buttons notice on activation.
		register_activation_hook( $this->plugin->dir_path . '/sharethis-follow-buttons.php', array( $this, 'st_activation_hook' ) );

		// Clean up plugin information on deactivation.
		register_deactivation_hook( $this->plugin->dir_path . '/sharethis-follow-buttons.php', array( $this, 'st_deactivation_hook' ) );
	}

	/**
	 * Set the settings sections and fields.
	 *
	 * @access private
	 */
	private function set_settings() {
		// Sections config.
		$this->setting_sections = array(
			esc_html__( 'Follow Buttons', 'sharethis-follow-buttons' ),
		);

		// Setting configs.
		$this->setting_fields = array(
			array(
				'id_suffix'   => 'widget',
				'description' => $this->get_descriptions( '', 'widget' ),
				'callback'    => 'widget_cb',
				'section'     => 'follow_button_section',
				'arg'         => '',
			),
			array(
				'id_suffix'   => 'shortcode',
				'description' => $this->get_descriptions( '', 'shortcode' ),
				'callback'    => 'shortcode_template',
				'section'     => 'follow_button_section',
				'arg'         => array(
					'type'  => 'shortcode',
					'value' => '[sharethis-follow-buttons]',
				),
			),
			array(
				'id_suffix'   => 'template',
				'description' => $this->get_descriptions( '', 'template' ),
				'callback'    => 'shortcode_template',
				'section'     => 'follow_button_section',
				'arg'         => array(
					'type'  => 'template',
					'value' => '<?php echo sharethis_follow_buttons(); ?>',
				),
			),
			array(
				'id_suffix'   => 'inline-follow_settings',
				'description' => $this->get_descriptions(),
				'callback'    => 'config_settings',
				'section'     => 'follow_buttons_section',
				'arg'         => 'inline-follow',
			),
		);

		// Inline setting array.
		$this->follow_setting_fields = array(
			array(
				'id_suffix' => 'inline-follow_post_top',
				'title'     => esc_html__( 'Top of post body', 'sharethis-follow-buttons' ),
				'callback'  => 'onoff_cb',
				'type'      => '',
				'default'   => array(
					'true'   => '',
					'false'  => 'checked="checked"',
					'margin' => true,
				),
			),
			array(
				'id_suffix' => 'inline-follow_post_bottom',
				'title'     => esc_html__( 'Bottom of post body', 'sharethis-follow-buttons' ),
				'callback'  => 'onoff_cb',
				'type'      => '',
				'default'   => array(
					'true'   => '',
					'false'  => 'checked="checked"',
					'margin' => true,
				),
			),
			array(
				'id_suffix' => 'inline-follow_page_top',
				'title'     => esc_html__( 'Top of page body', 'sharethis-follow-buttons' ),
				'callback'  => 'onoff_cb',
				'type'      => '',
				'default'   => array(
					'true'   => '',
					'false'  => 'checked="checked"',
					'margin' => true,
				),
			),
			array(
				'id_suffix' => 'inline-follow_page_bottom',
				'title'     => esc_html__( 'Bottom of page body', 'sharethis-follow-buttons' ),
				'callback'  => 'onoff_cb',
				'type'      => '',
				'default'   => array(
					'true'   => '',
					'false'  => 'checked="checked"',
					'margin' => true,
				),
			),
			array(
				'id_suffix' => 'excerpt',
				'title'     => esc_html__( 'Include in excerpts', 'sharethis-follow-buttons' ),
				'callback'  => 'onoff_cb',
				'type'      => '',
				'default'   => array(
					'true'   => '',
					'false'  => 'checked="checked"',
					'margin' => true,
				),
			),
		);
	}

	/**
	 * Add in ShareThis menu option.
	 *
	 * @action admin_menu
	 */
	public function define_sharethis_menus() {
		$this->share_buttons_settings();
	}

	/**
	 * Add Follow Buttons settings page.
	 */
	public function share_buttons_settings() {
		// Check if the share this menu is already registered.
		if ( true === empty( $GLOBALS['admin_page_hooks']['sharethis-share-buttons'] ) ) {
			// Menu base64 Encoded icon.
			$icon = 'data:image/svg+xml;base64,PD94bWwgdmVyc2lvbj0iMS4wIiBlbmNvZGluZz0iVVRGLTgiIHN0YW5kYWxvbmU9Im5vIj8+Cjxzdmcgd2lkdGg9IjE2cHgiIGhlaWdodD0iMTZweCIgdmlld0JveD0iMCAwIDE2IDE2IiB2ZXJzaW9uPSIxLjEiIHhtbG5zPSJodHRwOi8vd3d3LnczLm9yZy8yMDAwL3N2ZyIgeG1sbnM6eGxpbms9Imh0dHA6Ly93d3cudzMub3JnLzE5OTkveGxpbmsiPgogICAgPCEtLSBHZW5lcmF0b3I6IFNrZXRjaCA0NC4xICg0MTQ1NSkgLSBodHRwOi8vd3d3LmJvaGVtaWFuY29kaW5nLmNvbS9za2V0Y2ggLS0+CiAgICA8dGl0bGU+RmlsbCAzPC90aXRsZT4KICAgIDxkZXNjPkNyZWF0ZWQgd2l0aCBTa2V0Y2guPC9kZXNjPgogICAgPGRlZnM+PC9kZWZzPgogICAgPGcgaWQ9IlBhZ2UtMSIgc3Ryb2tlPSJub25lIiBzdHJva2Utd2lkdGg9IjEiIGZpbGw9Im5vbmUiIGZpbGwtcnVsZT0iZXZlbm9kZCI+CiAgICAgICAgPGcgaWQ9IkRlc2t0b3AtSEQiIHRyYW5zZm9ybT0idHJhbnNsYXRlKC0xMC4wMDAwMDAsIC00MzguMDAwMDAwKSIgZmlsbD0iI0ZFRkVGRSI+CiAgICAgICAgICAgIDxwYXRoIGQ9Ik0yMy4xNTE2NDMyLDQ0OS4xMDMwMTEgQzIyLjcyNjg4NzcsNDQ5LjEwMzAxMSAyMi4zMzM1MDYyLDQ0OS4yMjg5OSAyMS45OTcwODA2LDQ0OS40Mzc5ODkgQzIxLjk5NTE0OTksNDQ5LjQzNTA5MyAyMS45OTcwODA2LDQ0OS40Mzc5ODkgMjEuOTk3MDgwNiw0NDkuNDM3OTg5IEMyMS44ODA3NTU1LDQ0OS41MDg5NDMgMjEuNzM1NDY5OCw0NDkuNTQ1NjI2IDIxLjU4OTIxODgsNDQ5LjU0NTYyNiBDMjEuNDUzMTA0LDQ0OS41NDU2MjYgMjEuMzE5ODg1Miw0NDkuNTA3NDk0IDIxLjIwODg2OTYsNDQ5LjQ0NTIyOSBMMTQuODczNzM4Myw0NDYuMDM4OTggQzE0Ljc2NDE3MDcsNDQ1Ljk5MDIzIDE0LjY4NzkwNzgsNDQ1Ljg3ODczMSAxNC42ODc5MDc4LDQ0NS43NTEzMDUgQzE0LjY4NzkwNzgsNDQ1LjYyMzM5NSAxNC43NjUxMzYsNDQ1LjUxMTg5NyAxNC44NzQ3MDM2LDQ0NS40NjI2NjQgTDIxLjIwODg2OTYsNDQyLjA1Njg5NyBDMjEuMzE5ODg1Miw0NDEuOTk1MTE1IDIxLjQ1MzEwNCw0NDEuOTU2NTAxIDIxLjU4OTIxODgsNDQxLjk1NjUwMSBDMjEuNzM1NDY5OCw0NDEuOTU2NTAxIDIxLjg4MDc1NTUsNDQxLjk5MzY2NyAyMS45OTcwODA2LDQ0Mi4wNjQ2MiBDMjEuOTk3MDgwNiw0NDIuMDY0NjIgMjEuOTk1MTQ5OSw0NDIuMDY3MDM0IDIxLjk5NzA4MDYsNDQyLjA2NDYyIEMyMi4zMzM1MDYyLDQ0Mi4yNzMxMzcgMjIuNzI2ODg3Nyw0NDIuMzk5MTE1IDIzLjE1MTY0MzIsNDQyLjM5OTExNSBDMjQuMzY2NTQwMyw0NDIuMzk5MTE1IDI1LjM1MTY4MzQsNDQxLjQxNDQ1NSAyNS4zNTE2ODM0LDQ0MC4xOTk1NTggQzI1LjM1MTY4MzQsNDM4Ljk4NDY2IDI0LjM2NjU0MDMsNDM4IDIzLjE1MTY0MzIsNDM4IEMyMi4wMTYzODc2LDQzOCAyMS4wOTMwMjcyLDQzOC44NjMwMjYgMjAuOTc1MjU0MSw0MzkuOTY3MzkgQzIwLjk3MTM5MjYsNDM5Ljk2MzA0NiAyMC45NzUyNTQxLDQzOS45NjczOSAyMC45NzUyNTQxLDQzOS45NjczOSBDMjAuOTUwNjM3NSw0NDAuMjM5MTM3IDIwLjc2OTE1MTEsNDQwLjQ2NzkyNiAyMC41MzYwMTgzLDQ0MC41ODQyNTEgTDE0LjI3OTU2MzMsNDQzLjk0NzU0MiBDMTQuMTY0MjAzNiw0NDQuMDE3MDQ3IDE0LjAyNDIyNzMsNDQ0LjA1NjE0NCAxMy44Nzk0MjQzLDQ0NC4wNTYxNDQgQzEzLjcwODU1NjgsNDQ0LjA1NjE0NCAxMy41NDgzMDgxLDQ0NC4wMDQ0OTggMTMuNDIwODgxNSw0NDMuOTEwMzc2IEMxMy4wNzUyODUsNDQzLjY4NDk2NiAxMi42NjUwMDk4LDQ0My41NTEyNjQgMTIuMjIxOTEyNiw0NDMuNTUxMjY0IEMxMS4wMDcwMTU1LDQ0My41NTEyNjQgMTAuMDIyMzU1MSw0NDQuNTM2NDA3IDEwLjAyMjM1NTEsNDQ1Ljc1MTMwNSBDMTAuMDIyMzU1MSw0NDYuOTY2MjAyIDExLjAwNzAxNTUsNDQ3Ljk1MDg2MiAxMi4yMjE5MTI2LDQ0Ny45NTA4NjIgQzEyLjY2NTAwOTgsNDQ3Ljk1MDg2MiAxMy4wNzUyODUsNDQ3LjgxNzY0MyAxMy40MjA4ODE1LDQ0Ny41OTIyMzMgQzEzLjU0ODMwODEsNDQ3LjQ5NzYyOSAxMy43MDg1NTY4LDQ0Ny40NDY0NjUgMTMuODc5NDI0Myw0NDcuNDQ2NDY1IEMxNC4wMjQyMjczLDQ0Ny40NDY0NjUgMTQuMTY0MjAzNiw0NDcuNDg1MDc5IDE0LjI3OTU2MzMsNDQ3LjU1NDU4NSBMMjAuNTM2MDE4Myw0NTAuOTE4MzU4IEMyMC43Njg2Njg0LDQ1MS4wMzQyMDEgMjAuOTUwNjM3NSw0NTEuMjYzNDcyIDIwLjk3NTI1NDEsNDUxLjUzNTIxOSBDMjAuOTc1MjU0MSw0NTEuNTM1MjE5IDIwLjk3MTM5MjYsNDUxLjUzOTU2MyAyMC45NzUyNTQxLDQ1MS41MzUyMTkgQzIxLjA5MzAyNzIsNDUyLjYzOTEwMSAyMi4wMTYzODc2LDQ1My41MDI2MDkgMjMuMTUxNjQzMiw0NTMuNTAyNjA5IEMyNC4zNjY1NDAzLDQ1My41MDI2MDkgMjUuMzUxNjgzNCw0NTIuNTE3NDY2IDI1LjM1MTY4MzQsNDUxLjMwMjU2OSBDMjUuMzUxNjgzNCw0NTAuMDg3NjcyIDI0LjM2NjU0MDMsNDQ5LjEwMzAxMSAyMy4xNTE2NDMyLDQ0OS4xMDMwMTEiIGlkPSJGaWxsLTMiPjwvcGF0aD4KICAgICAgICA8L2c+CiAgICA8L2c+Cjwvc3ZnPg==';

			// Main sharethis menu.
			$this->hook_suffix = add_menu_page(
				__( 'Share Buttons by ShareThis', 'sharethis-follow-buttons' ),
				__( 'ShareThis', 'sharethis-share-buttons' ),
				'manage_options',
				$this->menu_slug . '-share-buttons',
				array( $this, 'share_button_display' ),
				$icon,
				26
			);
		} else {
			$this->hook_suffix = add_submenu_page(
				$this->menu_slug . '-share-buttons',
				$this->get_descriptions( '', 'follow_buttons' ),
				__( 'Follow Buttons', 'sharethis-follow-buttons' ),
				'manage_options',
				$this->menu_slug . '-follow-buttons',
				array( $this, 'share_button_display' )
			);
		}
	}

	/**
	 * Enqueue main MU script.
	 *
	 * @action wp_enqueue_scripts
	 */
	public function enqueue_mu() {
		if ( ! wp_script_is( 'sharethis-follow-buttons-mu', 'enqueued' ) ) {
			wp_enqueue_script( "{$this->plugin->assets_prefix}-mu" );
		}
	}

	/**
	 * Enqueue admin assets.
	 *
	 * @action admin_enqueue_scripts
	 * @param string $hook_suffix The current admin page.
	 */
	public function enqueue_admin_assets( $hook_suffix ) {
		$follow              = get_option( 'sharethis_follow' );
		$first_exists        = get_option( 'sharethis_first_product' );
		$first_button        = false !== $first_exists && null !== $first_exists ? $first_exists : '';
		$first_exists        = false === $first_exists || null === $first_exists || '' === $first_exists ? true : false;
		$propertyid          = explode( '-', get_option( 'sharethis_property_id' ), 2 );
		$property_id         = isset( $propertyid[0] ) ? $propertyid[0] : '';
		$secret              = isset( $propertyid[1] ) ? $propertyid[1] : '';
		$token               = get_option( 'sharethis_token' );
		$full_url            = site_url();
		$admin_url           = str_replace( 'http://', '', str_replace( 'https://', '', site_url() ) );
		$button_config       = get_option( 'sharethis_button_config', true );
		$button_config       = false !== $button_config && null !== $button_config ? $button_config : '';
		$share_buttons_exist = is_plugin_active( 'sharethis-share-buttons/sharethis-share-buttons.php' );

		// If sharethis share buttons are already enqueueing script don't re-enqueue it.
		if ( ! wp_script_is( 'sharethis-follow-buttons-mua', 'enqueued' ) ) {
			wp_enqueue_script( "{$this->plugin->assets_prefix}-mua" );
		}

		// Only enqueue these scripts on share buttons plugin admin menu.
		if ( '' === $property_id ) {
			wp_register_script(
				"{$this->plugin->assets_prefix}-credentials",
				$this->plugin->dir_url . 'js/set-credentials.js',
				array( 'wp-util' ),
				filemtime( "{$this->plugin->dir_path}js/set-credentials.js" ),
				false
			);

			// Only enqueue this script on the general settings page for credentials.
			wp_enqueue_script( "{$this->plugin->assets_prefix}-credentials" );
			wp_add_inline_script(
				"{$this->plugin->assets_prefix}-credentials",
				sprintf(
					'Credentials.boot( %s );',
					wp_json_encode(
						array(
							'nonce'        => wp_create_nonce( $this->plugin->meta_prefix ),
							'email'        => get_bloginfo( 'admin_email' ),
							'url'          => str_replace( 'http://', '', str_replace( 'https://', '', site_url() ) ),
							'buttonConfig' => $button_config,
						)
					)
				)
			);
		}

		// Only enqueue these scripts on share buttons plugin admin menu.
		if ( $hook_suffix === $this->hook_suffix ) {
			if ( false === empty( $first_exists ) ) {
				update_option( 'sharethis_first_product', 'inline-follow' );
			}

			wp_enqueue_style( "{$this->plugin->assets_prefix}-admin" );
			wp_enqueue_script( "{$this->plugin->assets_prefix}-admin" );
			wp_add_inline_script(
				"{$this->plugin->assets_prefix}-admin",
				sprintf(
					'FollowButtons.boot( %s );',
					wp_json_encode(
						array(
							'followEnabled' => $follow,
							'propertyid'    => $property_id,
							'secret'        => $secret,
							'url'           => $full_url,
							'buttonConfig'  => $button_config,
							'shareButtons'  => $share_buttons_exist,
							'token'         => $token,
							'nonce'         => wp_create_nonce( $this->plugin->meta_prefix ),
						)
					)
				)
			);
		}
	}

	/**
	 * Call back for displaying Follow Buttons settings page.
	 */
	public function share_button_display() {
		// Check user capabilities.
		if ( ! current_user_can( 'manage_options' ) ) {
			return;
		}

		$description = $this->get_descriptions( '', 'follow_buttons' );

		include_once "{$this->plugin->dir_path}templates/follow-buttons/follow-button-settings.php";
	}

	/**
	 * Define follow button setting sections and fields.
	 *
	 * @action admin_init
	 */
	public function settings_api_init() {
		// Register sections.
		foreach ( $this->setting_sections as $index => $title ) {
			// Since the index starts at 0, let's increment it by 1.
			$i       = $index + 1;
			$section = 'follow_button_section';

			// Add setting section.
			add_settings_section(
				$section,
				$title,
				array( $this, 'social_button_link' ),
				$this->menu_slug . '-follow-buttons'
			);
		}

		// Register setting fields.
		foreach ( $this->setting_fields as $setting_field ) {
			register_setting( $this->menu_slug . '-follow-buttons', $this->menu_slug . '_' . $setting_field['id_suffix'] );
			add_settings_field(
				$this->menu_slug . '_' . $setting_field['id_suffix'],
				$setting_field['description'],
				array( $this, $setting_field['callback'] ),
				$this->menu_slug . '-follow-buttons',
				'follow_button_section',
				$setting_field['arg']
			);
		}
	}

	/**
	 * Call back function for on / off buttons.
	 *
	 * @param string $type The setting type.
	 */
	public function config_settings( $type = 'inline-follow' ) {
		$config_array = $this->follow_setting_fields;

		// Display on off template for inline settings.
		foreach ( $config_array as $setting ) {
			$option       = 'sharethis_' . $setting['id_suffix'];
			$title        = isset( $setting['title'] ) ? $setting['title'] : '';
			$option_value = get_option( 'sharethis_inline-follow_settings' );
			$default      = isset( $setting['default'] ) ? $setting['default'] : '';
			$allowed      = array(
				'li'    => array(
					'class' => array(),
				),
				'span'  => array(
					'id'    => array(),
					'class' => array(),
				),
				'input' => array(
					'id'    => array(),
					'name'  => array(),
					'type'  => array(),
					'value' => array(),
				),
			);

			// Margin control variables.
			$margin = isset( $setting['default']['margin'] ) ? $setting['default']['margin'] : false;
			$mclass = isset( $option_value[ $option . '_margin_top' ] ) && 0 !== (int) $option_value[ $option . '_margin_top' ] || isset( $option_value[ $option . '_margin_bottom' ] ) && 0 !== (int) $option_value[ $option . '_margin_bottom' ] ? 'active-margin' : '';
			$onoff  = '' !== $mclass ? __( 'On', 'sharethis-follow-buttons' ) : __( 'Off', 'sharethis-follow-buttons' );
			$active = array(
				'class' => $mclass,
				'onoff' => esc_html( $onoff ),
			);

			if ( isset( $option_value[ $option ] ) && false !== $option_value[ $option ] && null !== $option_value[ $option ] ) {
				$default = array(
					'true'  => '',
					'false' => '',
				);
			}

			// Display the list call back if specified.
			if ( 'onoff_cb' === $setting['callback'] ) {
				include "{$this->plugin->dir_path}/templates/follow-buttons/onoff-buttons.php";
			} else {
				$current_omit = $this->get_omit( $setting['type'] );

				$this->list_cb( $setting['type'], $current_omit, $allowed );
			}
		}
	}

	/**
	 * Callback function for onoff buttons
	 *
	 * @param array $id The setting type.
	 */
	public function enable_cb( $id ) {
		include "{$this->plugin->dir_path}/templates/follow-buttons/enable-buttons.php";
	}

	/**
	 * Callback function for omitting fields.
	 *
	 * @param array $type The type of list to return for exlusion.
	 * @param array $current_omit The currently omited items.
	 * @param array $allowed The allowed html that an omit item can echo.
	 */
	public function list_cb( $type, $current_omit, $allowed ) {
		include "{$this->plugin->dir_path}/templates/follow-buttons/list.php";
	}

	/**
	 * Callback function for the widget link.
	 */
	public function widget_cb() {
		include "{$this->plugin->dir_path}/templates/follow-buttons/widget.php";
	}

	/**
	 * Callback function for the shortcode and template code fields.
	 *
	 * @param string $type The type of template to pull.
	 */
	public function shortcode_template( $type ) {
		include "{$this->plugin->dir_path}/templates/follow-buttons/shortcode-templatecode.php";
	}

	/**
	 * Callback function for the login buttons.
	 *
	 * @param string $button The specific product to link to.
	 */
	public function social_button_link( $button ) {
		$networks = $this->get_networks();
		$enabled  = 'true' !== get_option( 'sharethis_inline-follow' ) ? 'Disabled' : 'Enabled';

		include "{$this->plugin->dir_path}/templates/follow-buttons/button-config.php";
	}

	/**
	 * Callback function for random gif field.
	 *
	 * @access private
	 * @return string
	 */
	private function random_gif() {
		$random_gif_response = wp_safe_remote_get( 'http://api.giphy.com/v1/gifs/random?api_key=dc6zaTOxFJmzC&rating=g' );

		$http_code = wp_remote_retrieve_response_code( $random_gif_response );

		if ( false === is_wp_error( $random_gif_response ) && 200 === $http_code ) {
			$content = wp_remote_retrieve_body( $random_gif_response );
			$content = json_decode( $content );

			$url = $content->data->images->original->url;

			return '<div id="random-gif-container"><img src="' . esc_url( $url ) . '"/></div>';
		} else {
			return esc_html__(
				'Sorry we couldn\'t show you a funny gif.  Refresh if you can\'t live without it.',
				'sharethis-follow-buttons'
			);
		}
	}

	/**
	 * Define setting descriptions.
	 *
	 * @param string $type Type of button.
	 * @param string $subtype Setting type.
	 *
	 * @access private
	 * @return string|void
	 */
	private function get_descriptions( $type = '', $subtype = '' ) {
		global $current_user;

		$description = '';

		switch ( $subtype ) {
			case '':
				$description  = esc_html__( 'WordPress Display Settings', 'sharethis-follow-buttons' );
				$description .= '<span>';
				$description .= esc_html__( 'Use these settings to automatically include or restrict the display of ', 'sharethis-follow-buttons' ) . esc_html( $type ) . esc_html__( ' Follow Buttons on specific pages of your site.', 'sharethis-follow-buttons' );
				$description .= '</span>';
				break;
			case 'widget':
				$description  = esc_html__( 'Add to sidebar/footer', 'sharethis-follow-buttons' );
				$description .= '<span>';
				$description .= esc_html__( 'Go to your theme\'s widget options to add the Follow Button\'s widget to your sidebars or footer.', 'sharethis-follow-buttons' );
				$description .= '</span>';
				break;
			case 'shortcode':
				$description  = esc_html__( 'Shortcode', 'sharethis-follow-buttons' );
				$description .= '<span>';
				$description .= esc_html__( 'Use this shortcode to deploy your follow buttons in a WYSIWYG editor.', 'sharethis-follow-buttons' );
				$description .= '</span>';
				break;
			case 'template':
				$description  = esc_html__( 'PHP', 'sharethis-follow-buttons' );
				$description .= '<span>';
				$description .= esc_html__( 'Use this PHP snippet to include your follow buttons anywhere else in your template.', 'sharethis-follow-buttons' );
				$description .= '</span>';
				break;
			case 'property':
				$description  = esc_html__( 'Property ID', 'sharethis-follow-buttons' );
				$description .= '<span>';
				$description .= esc_html__( 'We use this unique ID to identify your property. Copy it from your ', 'sharethis-follow-buttons' );
				$description .= '<a class="st-support" href="https://platform.sharethis.com/settings?utm_source=sharethis-plugin&utm_medium=sharethis-plugin-page&utm_campaign=property-settings" target="_blank">';
				$description .= esc_html__( 'ShareThis platform settings', 'sharethis-follow-buttons' );
				$description .= '</a></span>';
				break;
			case 'follow_buttons':
				$description  = '<h1>';
				$description .= esc_html__( 'Follow Buttons by ShareThis', 'sharethis-follow-buttons' );
				$description .= '</h1>';
				$description .= '<h3>';
				$description .= esc_html__( 'Welcome aboard, ', 'sharethis-follow-buttons' ) . esc_html( $current_user->display_name ) . '! ';
				$description .= esc_html__( 'Use the settings panels below for complete control over where and how follow buttons appear on your site.', 'sharethis-follow-buttons' );
				break;
		}

		return wp_kses_post( $description );
	}

	/**
	 * Set the property id and secret key for the user's platform account if query params are present.
	 *
	 * @action wp_ajax_set_follow_credentials
	 */
	public function set_follow_credentials() {
		check_ajax_referer( $this->plugin->meta_prefix, 'nonce' );

		if ( ! isset( $_POST['data'], $_POST['token'] ) || '' === $_POST['data'] ) { // WPCS: input var ok.
			wp_send_json_error( 'Set credentials failed.' );
		}

		$data  = sanitize_text_field( wp_unslash( $_POST['data'] ) ); // WPCS: input var ok.
		$token = sanitize_text_field( wp_unslash( $_POST['token'] ) ); // WPCS: input var ok.

		// If both variables exist add them to a database option.
		if ( false === get_option( 'sharethis_property_id' ) ) {
			update_option( 'sharethis_property_id', $data );
			update_option( 'sharethis_token', $token );
		}
	}

	/**
	 * Helper function to determine if property ID is set.
	 *
	 * @param string $type Should empty count as false.
	 *
	 * @access private
	 * @return bool
	 */
	private function is_property_id_set( $type = '' ) {
		$property_id = get_option( 'sharethis_property_id' );

		// If the property id is set then show the general settings template.
		if ( false !== $property_id && null !== $property_id ) {
			if ( 'empty' === $type && '' === $property_id ) {
				return false;
			}

			return true;
		}

		return false;
	}

	/**
	 * AJAX Call back to update status of buttons
	 *
	 * @action wp_ajax_update_follow_buttons
	 */
	public function update_follow_buttons() {
		check_ajax_referer( $this->plugin->meta_prefix, 'nonce' );

		$type = filter_input( INPUT_POST, 'type', FILTER_SANITIZE_STRING );

		$onoff = filter_input( INPUT_POST, 'onoff', FILTER_SANITIZE_STRING );
		$onoff = sanitize_text_field( wp_unslash( $onoff ) );

		if ( true === empty( $type ) || true === empty( $onoff ) ) {
			wp_send_json_error( 'Update buttons failed.' );
		}

		if ( 'On' === $onoff ) {
			update_option( 'sharethis_inline-follow', 'true' );
		} elseif ( 'Off' === $onoff ) {
			update_option( 'sharethis_inline-follow', 'false' );
		}
	}

	/**
	 * AJAX Call back to set defaults when rest button is clicked.
	 *
	 * @action wp_ajax_set_follow_default_settings
	 */
	public function set_follow_default_settings() {
		check_ajax_referer( $this->plugin->meta_prefix, 'nonce' );

		$this->set_the_defaults();
	}

	/**
	 * Helper function to set the default button options.
	 */
	private function set_the_defaults() {
		$default = array(
			'follow_settings' => array(
				'sharethis_inline-follow_post_top'    => 'false',
				'sharethis_inline-follow_post_bottom' => 'false',
				'sharethis_inline-follow_page_top'    => 'false',
				'sharethis_inline-follow_page_bottom' => 'false',
				'sharethis_excerpt'                   => 'false',
				'sharethis_inline-follow_post_top_margin_top' => 0,
				'sharethis_inline-follow_post_top_margin_bottom' => 0,
				'sharethis_inline-follow_post_bottom_margin_top' => 0,
				'sharethis_inline-follow_post_bottom_margin_bottom' => 0,
				'sharethis_inline-follow_page_top_margin_top' => 0,
				'sharethis_inline-follow_page_top_margin_bottom' => 0,
				'sharethis_inline-follow_page_bottom_margin_top' => 0,
				'sharethis_inline-follow_page_bottom_margin_bottom' => 0,
				'sharethis_excerpt_margin_top'        => 0,
				'sharethis_excerpt_margin_bottom'     => 0,
			),
		);

		update_option( 'sharethis_inline-follow_settings', $default['follow_settings'] );
	}

	/**
	 * Display custom admin notice.
	 *
	 * @action admin_notices
	 */
	public function connection_made_admin_notice() {
		$screen = get_current_screen();

		$reset = filter_input( INPUT_GET, 'reset', FILTER_UNSAFE_RAW );
		$reset = sanitize_text_field( wp_unslash( $reset ) );

		if ( 'sharethis_page_sharethis-follow-buttons' === $screen->base ) {
			if ( false === empty( $reset ) ) {
				?>
					<div class="notice notice-success is-dismissible">
						<p>
							<?php
							// translators: The type of button.
							esc_html_e( 'Successfully reset your follow button position display options!', 'sharethis-follow-buttons' );
							?>
						</p>
					</div>
				<?php
			};
		}
	}

	/**
	 * Runs only when the plugin is activated.
	 */
	public function st_activation_hook() {
		// Create transient data.
		set_transient( 'st-follow-activation', true, 5 );
		set_transient( 'st-follow-connection', true, 360 );

		// Set the default optons.
		$this->set_the_defaults();
	}

	/**
	 * Admin Notice on Activation.
	 *
	 * @action admin_notices
	 */
	public function activation_inform_notice() {
		$screen  = get_current_screen();
		$product = get_option( 'sharethis_first_product' );
		$product = null !== $product && false !== $product ? ucfirst( $product ) : 'your';
		$gen_url = '<a href="' . esc_url( admin_url( 'admin.php?page=sharethis-share-buttons&nft' ) ) . '">configuration</a>';
		$nft     = filter_input( INPUT_GET, 'nft', FILTER_UNSAFE_RAW );

		if ( false === $this->is_property_id_set() ) {
			$gen_url = '<a href="' . esc_url( admin_url( 'admin.php?page=sharethis-share-buttons' ) ) . '">configuration</a>';
		}

		// Check transient, if available display notice.
		if ( get_transient( 'st-follow-activation' ) ) {
			?>
			<div class="updated notice is-dismissible">
				<p>
					<?php
					// translators: The general settings url.
					printf( esc_html__( 'Your ShareThis Follow Button plugin requires %1$s', 'sharethis-follow-buttons' ), wp_kses_post( $gen_url ) );
					?>
					.
				</p>
			</div>
			<?php
			// Delete transient, only display this notice once.
			delete_transient( 'st-follow-activation' );
		}

		if ( 'sharethis_page_sharethis-follow-buttons' === $screen->base
			&& get_transient( 'st-follow-connection' )
			&& true === empty( $nft )
		) {
			?>
			<div class="notice notice-success is-dismissible">
				<p>
					<?php
					// translators: The product type.
					esc_html_e(
						'Congrats! You’ve activated Follow Buttons. Sit tight, they’ll appear on your site in just a few minutes!',
						'sharethis-follow-buttons'
					);
					?>
				</p>
			</div>
			<?php
			delete_transient( 'st-follow-connection' );
		}
	}

	/**
	 * Remove all database information when plugin is deactivated.
	 */
	public function st_deactivation_hook() {
		foreach ( wp_load_alloptions() as $option => $value ) {
			if ( strpos( $option, 'sharethis_' ) === 0 ) {
				delete_option( $option );
			}
		}
	}

	/**
	 * Register the button widget.
	 *
	 * @action widgets_init
	 */
	public function register_widgets() {
		register_widget( $this->button_widget );
	}

	/**
	 * Return the set up steps.
	 */
	private function get_setup_steps() {
		$steps = array(
			1 => esc_html__( 'Design your Follow Buttons', 'sharethis-follow-buttons' ),
			2 => esc_html__( 'Register with ShareThis', 'sharethis-follow-buttons' ),
			3 => esc_html__( 'Configure WordPress Settings', 'sharethis-follow-buttons' ),
		);

		return $steps;
	}

	/**
	 * Return network array with info.
	 */
	private function get_networks() {
		$networks = array(
			'facebook'      => array(
				'color'      => '#3B5998',
				'color-rgba' => '59, 89, 152',
				'path'       => 'm21.7 16.7h5v5h-5v11.6h-5v-11.6h-5v-5h5v-2.1c0-2 0.6-4.5 1.8-5.9 1.3-1.3 2.8-2 4.7-2h3.5v5h-3.5c-0.9 0-1.5 0.6-1.5 1.5v3.5z',
				'selected'   => 'true',
				'url'        => 'facebook.com/',
			),
			'twitter'       => array(
				'color'      => '#000000',
				'color-rgba' => '85, 172, 238',
				'path'       => 'm31.5 11.7c1.3-0.8 2.2-2 2.7-3.4-1.4 0.7-2.7 1.2-4 1.4-1.1-1.2-2.6-1.9-4.4-1.9-1.7 0-3.2 0.6-4.4 1.8-1.2 1.2-1.8 2.7-1.8 4.4 0 0.5 0.1 0.9 0.2 1.3-5.1-0.1-9.4-2.3-12.7-6.4-0.6 1-0.9 2.1-0.9 3.1 0 2.2 1 3.9 2.8 5.2-1.1-0.1-2-0.4-2.8-0.8 0 1.5 0.5 2.8 1.4 4 0.9 1.1 2.1 1.8 3.5 2.1-0.5 0.1-1 0.2-1.6 0.2-0.5 0-0.9 0-1.1-0.1 0.4 1.2 1.1 2.3 2.1 3 1.1 0.8 2.3 1.2 3.6 1.3-2.2 1.7-4.7 2.6-7.6 2.6-0.7 0-1.2 0-1.5-0.1 2.8 1.9 6 2.8 9.5 2.8 3.5 0 6.7-0.9 9.4-2.7 2.8-1.8 4.8-4.1 6.1-6.7 1.3-2.6 1.9-5.3 1.9-8.1v-0.8c1.3-0.9 2.3-2 3.1-3.2-1.1 0.5-2.3 0.8-3.5 1z',
				'selected'   => 'true',
				'url'        => 'x.com/',
			),
			'instagram'     => array(
				'color'      => '#bc2a8d',
				'color-rgba' => '188, 42, 141',
				'path'       => 'M30.9612095,2 L13.0383173,2 C6.95179283,2 2,6.95202943 2,13.0385539 L2,30.9614461 C2,37.0482072 6.95179283,42 13.0383173,42 L30.9612095,42 C37.0482072,42 42,37.0479706 42,30.9614461 L42,13.0385539 C42.0002366,6.95202943 37.0482072,2 30.9612095,2 Z M38.4512427,30.9614461 C38.4512427,35.091292 35.091292,38.4510061 30.9614461,38.4510061 L13.0383173,38.4510061 C8.90870805,38.4512427 5.54899386,35.091292 5.54899386,30.9614461 L5.54899386,13.0385539 C5.54899386,8.90894465 8.90870805,5.54899386 13.0383173,5.54899386 L30.9612095,5.54899386 C35.0910554,5.54899386 38.4510061,8.90894465 38.4510061,13.0385539 L38.4510061,30.9614461 L38.4512427,30.9614461 Z M22,11.6934852 C16.3166412,11.6934852 11.693012,16.3171144 11.693012,22.0004732 C11.693012,27.6835954 16.3166412,32.306988 22,32.306988 C27.6833588,32.306988 32.306988,27.6835954 32.306988,22.0004732 C32.306988,16.3171144 27.6833588,11.6934852 22,11.6934852 Z M22,28.7577575 C18.273793,28.7577575 15.2420059,25.7264436 15.2420059,22.0002366 C15.2420059,18.273793 18.2735564,15.2422425 22,15.2422425 C25.7264436,15.2422425 28.7579941,18.273793 28.7579941,22.0002366 C28.7579941,25.7264436 25.726207,28.7577575 22,28.7577575 Z M32.7392554,8.68417504 C32.0554826,8.68417504 31.3837764,8.96099656 30.9008766,9.44602572 C30.4156108,9.92868888 30.1366599,10.6006317 30.1366599,11.2867705 C30.1366599,11.97078 30.4158474,12.6424862 30.9008766,13.1275153 C31.3835398,13.6101785 32.0554826,13.889366 32.7392554,13.889366 C33.4253942,13.889366 34.0949711,13.6101785 34.5800002,13.1275153 C35.0650294,12.6424862 35.3418509,11.9705434 35.3418509,11.2867705 C35.3418509,10.6006317 35.0650294,9.92868888 34.5800002,9.44602572 C34.0973371,8.96099656 33.4253942,8.68417504 32.7392554,8.68417504 Z M38.4512427,30.9614461 C38.4512427,35.091292 35.091292,38.4510061 30.9614461,38.4510061 L13.0383173,38.4510061 C8.90870805,38.4512427 5.54899386,35.091292 5.54899386,30.9614461 L5.54899386,13.0385539 C5.54899386,8.90894465 8.90870805,5.54899386 13.0383173,5.54899386 L30.9612095,5.54899386 C35.0910554,5.54899386 38.4510061,8.90894465 38.4510061,13.0385539 L38.4510061,30.9614461 L38.4512427,30.9614461 Z M30.9612095,2 L13.0383173,2 C6.95179283,2 2,6.95202943 2,13.0385539 L2,30.9614461 C2,37.0482072 6.95179283,42 13.0383173,42 L30.9612095,42 C37.0482072,42 42,37.0479706 42,30.9614461 L42,13.0385539 C42.0002366,6.95202943 37.0482072,2 30.9612095,2 Z',
				'selected'   => 'true',
				'url'        => 'instagram.com/',
				'viewbox'    => '50',
			),
			'youtube'       => array(
				'color'      => '#FF0000',
				'color-rgba' => '255, 0 , 0',
				'path'       => 'M32.6925126,6 L9.3074874,6 C4.71938456,6 1,9.71938456 1,14.3074874 L1,25.9975271 C1,30.58563 4.71938456,34.3050145 9.3074874,34.3050145 L32.6925126,34.3050145 C37.2806154,34.3050145 41,30.58563 41,25.9975271 L41,14.3074874 C41,9.71938456 37.2806154,6 32.6925126,6 Z M27.0742168,20.7212696 L16.1362795,25.9380045 C15.8448268,26.0770063 15.5081681,25.8645122 15.5081681,25.5416496 L15.5081681,14.7821068 C15.5081681,14.4546454 15.8536771,14.2424116 16.1457372,14.3904373 L27.0836744,19.9332453 C27.4088798,20.0980171 27.4032399,20.5643936 27.0742168,20.7212696 Z',
				'selected'   => 'true',
				'url'        => 'youtube.com/',
				'tooltip'    => 'Supported Youtube URLs:<br/><br/>• youtube.com/channel/<br/>• youtube.com/user/<br/>',
			),
			'linkedin'      => array(
				'color'      => '#0077b5',
				'color-rgba' => '0, 119, 181',
				'path'       => 'm13.3 31.7h-5v-16.7h5v16.7z m18.4 0h-5v-8.9c0-2.4-0.9-3.5-2.5-3.5-1.3 0-2.1 0.6-2.5 1.9v10.5h-5s0-15 0-16.7h3.9l0.3 3.3h0.1c1-1.6 2.7-2.8 4.9-2.8 1.7 0 3.1 0.5 4.2 1.7 1 1.2 1.6 2.8 1.6 5.1v9.4z m-18.3-20.9c0 1.4-1.1 2.5-2.6 2.5s-2.5-1.1-2.5-2.5 1.1-2.5 2.5-2.5 2.6 1.2 2.6 2.5z',
				'selected'   => 'false',
				'url'        => 'linkedin.com/',
				'tooltip'    => 'Supported LinkedIn URLs:<br/><br/>• linkedin.com/in/<br/>• linkedin.com/company/<br/>• linkedin.com/groups/<br/>• linkedin.com/school/<br/>',
			),
			'blogger'       => array(
				'color'      => '#ff8000',
				'color-rgba' => '235, 73, 36',
				'path'       => 'M27.5,30 L12.5,30 C11.125,30 10,28.875 10,27.5 C10,26.125 11.125,25 12.5,25 L27.5,25 C28.875,25 30,26.125 30,27.5 C30,28.875 28.875,30 27.5,30 M12.5,10 L20,10 C21.375,10 22.5,11.125 22.5,12.5 C22.5,13.875 21.375,15 20,15 L12.5,15 C11.125,15 10,13.875 10,12.5 C10,11.125 11.125,10 12.5,10 M37.41375,15 L35.21875,15 L35.17125,15 C33.7975,15 32.59375,13.8375 32.5,12.5 C32.5,5.365 26.7475,0 19.5625,0 L13.0075,0 C5.8275,0 0.005,5.78125 0,12.91625 L0,27.08875 C0,34.22375 5.8275,40 13.0075,40 L27.0075,40 C34.1925,40 40,34.22375 40,27.08875 L40,17.93375 C40,16.5075 38.85,15 37.41375,15',
				'selected'   => 'false',
				'url'        => 'blogger.com/',
			),
			'digg'          => array(
				'color'      => '#262626',
				'color-rgba' => '38, 38, 38',
				'path'       => 'm6.4 8.1h3.9v19.1h-10.3v-13.6h6.4v-5.5z m0 15.9v-7.2h-2.4v7.2h2.4z m5.5-10.4v13.6h4v-13.6h-4z m0-5.5v3.9h4v-3.9h-4z m5.6 5.5h10.3v18.3h-10.3v-3.1h6.4v-1.6h-6.4v-13.6z m6.4 10.4v-7.2h-2.4v7.2h2.4z m5.5-10.4h10.4v18.3h-10.4v-3.1h6.4v-1.6h-6.4v-13.6z m6.4 10.4v-7.2h-2.4v7.2h2.4z',
				'selected'   => 'false',
				'url'        => 'digg.com/',
			),
			'flipboard'     => array(
				'color'      => '#e12828',
				'color-rgba' => '225, 40, 40',
				'path'       => 'M0,0 L13.3333333,0 L13.3333333,13.3333333 L0,13.3333333 L0,0 Z M0,13.3333333 L13.3333333,13.3333333 L13.3333333,26.6666667 L0,26.6666667 L0,13.3333333 Z M13.3333333,13.3333333 L26.6666667,13.3333333 L26.6666667,26.6666667 L13.3333333,26.6666667 L13.3333333,13.3333333 Z M0,26.6666667 L13.3333333,26.6666667 L13.3333333,40 L0,40 L0,26.6666667 Z M13.3333333,0 L26.6666667,0 L26.6666667,13.3333333 L13.3333333,13.3333333 L13.3333333,0 Z M26.6666667,0 L40,0 L40,13.3333333 L26.6666667,13.3333333 L26.6666667,0 Z',
				'selected'   => 'false',
				'url'        => 'flipboard.com/',
			),
			'github'        => array(
				'color'      => '#333333',
				'color-rgba' => '51, 51, 51',
				'path'       => 'M39.3171343,12.2106855 C37.5286789,9.06954026 35.1027532,6.58269119 32.0387189,4.74957732 C28.9742285,2.91636994 25.6287185,2 21.9998176,2 C18.3713727,2 15.0247682,2.91665044 11.9609163,4.74957732 C8.89660834,6.58259769 6.47077388,9.06954026 4.68231839,12.2106855 C2.89413653,15.3517373 2,18.7817901 2,22.5007505 C2,26.9680656 3.27147437,30.9851899 5.81506159,34.5531516 C8.35837518,38.1213939 11.6439598,40.5904784 15.6715419,41.9606857 C16.1403638,42.0498824 16.4874198,41.9871455 16.7130746,41.7740645 C16.9388206,41.560703 17.0515567,41.2934869 17.0515567,40.9735381 C17.0515567,40.920151 17.0470874,40.4398538 17.0384224,39.5320856 C17.0294838,38.6243175 17.0252881,37.8323928 17.0252881,37.1566857 L16.4263088,37.262899 C16.0444104,37.3346116 15.5626365,37.3649984 14.9809872,37.3563966 C14.3996114,37.3480753 13.7960716,37.2856189 13.1711884,37.1694949 C12.5460317,37.0543993 11.9645647,36.7874637 11.4263316,36.369249 C10.888372,35.9510342 10.5064737,35.4036058 10.2807277,34.7278051 L10.0203217,34.1135259 C9.84674812,33.7045675 9.57348146,33.2502626 9.20015688,32.7522944 C8.8268323,32.2538587 8.44931204,31.9159584 8.06741368,31.7380325 L7.88508389,31.6042374 C7.7635915,31.5153212 7.65085533,31.4080795 7.54660172,31.2836342 C7.44243933,31.1591889 7.36445436,31.0346501 7.31237316,30.9099243 C7.26020075,30.785105 7.30343453,30.6827251 7.44253054,30.6024107 C7.58162656,30.5220963 7.8330027,30.4831078 8.19775349,30.4831078 L8.71838302,30.5628612 C9.06562139,30.6341999 9.49513164,30.8472809 10.007461,31.2034133 C10.5195168,31.5592651 10.9404532,32.0218912 11.2703616,32.5911046 C11.6698636,33.3209468 12.1511814,33.8770705 12.7156832,34.2597562 C13.2797289,34.6424418 13.8484264,34.8334574 14.4212283,34.8334574 C14.9940303,34.8334574 15.488756,34.7889526 15.905588,34.7004104 C16.3219639,34.6114006 16.7126185,34.4776056 17.0773693,34.2997732 C17.2336129,33.1069308 17.6590187,32.1905609 18.3532218,31.5500088 C17.3637703,31.4434216 16.4741943,31.2828862 15.6840377,31.0694312 C14.8943372,30.8556957 14.0782769,30.5088196 13.2364039,30.027868 C12.394075,29.5475708 11.6953113,28.9511497 11.1399306,28.2396329 C10.5844586,27.5277422 10.1285886,26.5931403 9.77295882,25.4366685 C9.41714666,24.2797292 9.23919498,22.9451446 9.23919498,21.4325404 C9.23919498,19.2788233 9.92509794,17.4460834 11.2966302,15.9332923 C10.6541435,14.3141009 10.7147984,12.4989386 11.4787776,10.4879923 C11.9822596,10.3276439 12.7289087,10.4479753 13.7183602,10.8482385 C14.7079941,11.2486887 15.4325704,11.5917314 15.8928185,11.8761511 C16.3530667,12.1604773 16.7218308,12.4014206 16.999658,12.5968306 C18.6145399,12.134298 20.2810469,11.9029849 21.9996352,11.9029849 C23.7182234,11.9029849 25.3850953,12.134298 27.0000684,12.5968306 L27.9896111,11.9564655 C28.6663018,11.5291815 29.465397,11.1376136 30.3849813,10.7816682 C31.3051128,10.4259099 32.0087106,10.3279244 32.495045,10.4882728 C33.2759893,12.4993126 33.3455829,14.3143814 32.7029137,15.9335728 C34.0743548,17.4463639 34.7605314,19.2795713 34.7605314,21.4328209 C34.7605314,22.945425 34.5819412,24.2842171 34.2266763,25.4500387 C33.8709554,26.6160472 33.4111633,27.5497142 32.8471175,28.2530031 C32.2824333,28.9561985 31.5792004,29.5479448 30.7373274,30.0281485 C29.8952721,30.5087261 29.0789381,30.8556022 28.2892376,31.0693377 C27.4991723,31.2830732 26.6095963,31.4437021 25.6201448,31.5504763 C26.5225814,32.3510028 26.973891,33.6146228 26.973891,35.3407754 L26.973891,40.9727901 C26.973891,41.2927389 27.0824315,41.5598615 27.2996949,41.7733165 C27.5166847,41.9863975 27.8593625,42.0491344 28.3281845,41.9598442 C32.3563138,40.589824 35.6418985,38.1206459 38.1851208,34.5524037 C40.7280696,30.9844419 42,26.9673177 42,22.5000025 C41.9990879,18.7815096 41.1044953,15.3517373 39.3171343,12.2106855 Z',
				'selected'   => 'false',
				'url'        => 'github.com/',
				'viewbox'    => '50',
			),
			'medium'        => array(
				'color'      => '#333333',
				'color-rgba' => '51, 51, 51',
				'path'       => 'M41.00463,10.2861159 L38.7972071,10.2861159 C38.2224289,10.2861159 36.7184314,10.9105009 36.7184314,11.4420385 L36.7184314,30.7446487 C36.7184314,31.2775091 38.2224289,31.7143802 38.7972071,31.7143802 L41.00463,31.7143802 L41.00463,36 L26.7171913,36 L26.7171913,31.7143802 L29.5753735,31.7143802 L29.5753735,11.7139668 L29.077982,11.7139668 L22.2003208,35.9999173 L16.8753545,35.9999173 L10.0866549,11.7139668 L9.57247976,11.7139668 L9.57247976,31.7142975 L12.4294218,31.7142975 L12.4294218,35.9999173 L1,36 L1,31.7142975 L2.58518739,31.7142975 C3.20750552,31.7142975 3.85702475,31.2775091 3.85702475,30.744566 L3.85702475,11.4420385 C3.85702475,10.9104183 3.2075882,10.2861159 2.58518739,10.2861159 L1,10.2861159 L1,6 L15.8121967,6 L20.6485354,23.1426445 L20.7786707,23.1426445 L25.6627147,6 L41.00463,6 L41.00463,10.2861159 Z',
				'selected'   => 'false',
				'url'        => 'medium.com/',
			),
			'messenger'     => array(
				'color'      => '#448AFF',
				'color-rgba' => '68, 138, 255',
				'path'       => 'M25,2C12.3,2,2,11.6,2,23.5c0,6.3,2.9,12.2,8,16.3v8.8l8.6-4.5c2.1,0.6,4.2,0.8,6.4,0.8c12.7,0,23-9.6,23-21.5 C48,11.6,37.7,2,25,2z M27.3,30.6l-5.8-6.2l-10.8,6.1l12-12.7l5.9,5.9l10.5-5.9L27.3,30.6z',
				'selected'   => 'false',
				'url'        => 'messenger.com/',
				'viewbox'    => '50',
			),
			'odnoklassniki' => array(
				'color'      => '#d7772d',
				'color-rgba' => '215, 119, 45',
				'path'       => 'm19.8 20.2q-4.2 0-7.2-2.9t-2.9-7.2q0-4.2 2.9-7.1t7.2-3 7.1 3 3 7.1q0 4.2-3 7.2t-7.1 2.9z m0-15.1q-2.1 0-3.5 1.5t-1.5 3.5q0 2.1 1.5 3.5t3.5 1.5 3.5-1.5 1.5-3.5q0-2-1.5-3.5t-3.5-1.5z m11.7 16.4q0.3 0.6 0.3 1.1t-0.1 0.9-0.6 0.8-0.9 0.9-1.4 0.9q-2.6 1.6-7 2.1l1.6 1.6 5.9 6q0.7 0.7 0.7 1.6t-0.7 1.6l-0.2 0.3q-0.7 0.7-1.7 0.7t-1.6-0.7q-1.5-1.5-6-6l-6 6q-0.6 0.7-1.6 0.7t-1.6-0.7l-0.3-0.3q-0.7-0.6-0.7-1.6t0.7-1.6l7.6-7.6q-4.6-0.5-7.1-2.1-0.9-0.6-1.4-0.9t-0.9-0.9-0.6-0.8-0.1-0.9 0.3-1.1q0.2-0.5 0.6-0.8t1-0.5 1.2 0 1.5 0.8q0.1 0.1 0.3 0.3t1 0.5 1.5 0.7 2.1 0.5 2.5 0.3q2 0 3.9-0.6t2.6-1.1l0.9-0.6q0.7-0.5 1.4-0.8t1.3 0 0.9 0.5 0.7 0.8z',
				'selected'   => 'false',
				'url'        => 'ok.ru/',
			),
			'pinterest'     => array(
				'color'      => '#CB2027',
				'color-rgba' => '203, 32, 39',
				'path'       => 'm37.3 20q0 4.7-2.3 8.6t-6.3 6.2-8.6 2.3q-2.4 0-4.8-0.7 1.3-2 1.7-3.6 0.2-0.8 1.2-4.7 0.5 0.8 1.7 1.5t2.5 0.6q2.7 0 4.8-1.5t3.3-4.2 1.2-6.1q0-2.5-1.4-4.7t-3.8-3.7-5.7-1.4q-2.4 0-4.4 0.7t-3.4 1.7-2.5 2.4-1.5 2.9-0.4 3q0 2.4 0.8 4.1t2.7 2.5q0.6 0.3 0.8-0.5 0.1-0.1 0.2-0.6t0.2-0.7q0.1-0.5-0.3-1-1.1-1.3-1.1-3.3 0-3.4 2.3-5.8t6.1-2.5q3.4 0 5.3 1.9t1.9 4.7q0 3.8-1.6 6.5t-3.9 2.6q-1.3 0-2.2-0.9t-0.5-2.4q0.2-0.8 0.6-2.1t0.7-2.3 0.2-1.6q0-1.2-0.6-1.9t-1.7-0.7q-1.4 0-2.3 1.2t-1 3.2q0 1.6 0.6 2.7l-2.2 9.4q-0.4 1.5-0.3 3.9-4.6-2-7.5-6.3t-2.8-9.4q0-4.7 2.3-8.6t6.2-6.2 8.6-2.3 8.6 2.3 6.3 6.2 2.3 8.6z',
				'selected'   => 'false',
				'url'        => 'pinterest.com/',
			),
			'quora'         => array(
				'color'      => '#a62100',
				'color-rgba' => '166, 33, 0',
				'path'       => 'M30.4569361,35.3530667 C35.5835842,32.1772706 39,26.4992644 39,20.0209156 C39,10.0684324 30.9403547,2 20.9991055,2 C11.0577621,2 3,10.0684324 3,20.0209156 C3,29.9740498 11.0577621,38.0418312 20.9991055,38.0418312 C22.4616022,38.0418312 23.8819152,37.8629956 25.2424364,37.533131 C26.9510681,40.6034671 29.5543161,42.7761845 35.0800153,41.7371154 L35.0800153,38.6824029 C35.0800153,38.6824959 31.5362948,37.8027328 30.4569361,35.3530667 Z M31,22.3974352 C31,25.5035112 29.9454925,28.3228089 28.23243,30.4054314 C26.080399,28.257811 22.7077367,26.4927856 18.4279481,26.7178302 L18.4279481,27.0951447 L18.4279481,30.3741325 C18.4279481,30.3741325 21.3312235,30.4816623 23.3185645,33.6836511 C22.5738749,33.8889178 21.7984596,34 20.9994879,34 C15.4757164,34 11,28.8058291 11,22.3965711 C11,21.574544 11,18.4261281 11,17.6034289 C11,11.1947469 15.4757164,6 20.9994879,6 C26.5230545,6 31,11.1947469 31,17.6034289 C31,18.4265121 31,21.574928 31,22.3974352 Z',
				'selected'   => 'false',
				'url'        => 'quora.com/',
			),
			'reddit'        => array(
				'color'      => '#ff4500',
				'color-rgba' => '255, 69, 0',
				'path'       => 'm40 18.9q0 1.3-0.7 2.3t-1.7 1.7q0.2 1 0.2 2.1 0 3.5-2.3 6.4t-6.5 4.7-9 1.7-8.9-1.7-6.4-4.7-2.4-6.4q0-1.1 0.2-2.1-1.1-0.6-1.8-1.6t-0.7-2.4q0-1.8 1.3-3.2t3.1-1.3q1.9 0 3.3 1.4 4.8-3.3 11.5-3.6l2.6-11.6q0-0.3 0.3-0.5t0.6-0.1l8.2 1.8q0.4-0.8 1.2-1.3t1.8-0.5q1.4 0 2.4 1t0.9 2.3-0.9 2.4-2.4 1-2.4-1-0.9-2.4l-7.5-1.6-2.3 10.5q6.7 0.2 11.6 3.6 1.3-1.4 3.2-1.4 1.8 0 3.1 1.3t1.3 3.2z m-30.7 4.4q0 1.4 1 2.4t2.4 1 2.3-1 1-2.4-1-2.3-2.3-1q-1.4 0-2.4 1t-1 2.3z m18.1 8q0.3-0.3 0.3-0.6t-0.3-0.6q-0.2-0.2-0.5-0.2t-0.6 0.2q-0.9 0.9-2.7 1.4t-3.6 0.4-3.6-0.4-2.7-1.4q-0.2-0.2-0.5-0.2t-0.6 0.2q-0.3 0.2-0.3 0.6t0.3 0.6q1 0.9 2.6 1.5t2.8 0.6 2 0.1 2-0.1 2.8-0.6 2.6-1.6z m-0.1-4.6q1.4 0 2.4-1t1-2.4q0-1.3-1-2.3t-2.4-1q-1.3 0-2.3 1t-1 2.3 1 2.4 2.3 1z',
				'selected'   => 'false',
				'url'        => 'reddit.com/',
			),
			'snapchat'      => array(
				'color'      => '#fffc00',
				'color-rgba' => '255, 252, 0',
				'path'       => 'M40.0972733,31.9943572 C35.0567281,31.1636801 32.7601595,26.0658711 32.5148547,25.4872173 C32.5101673,25.4758895 32.490871,25.4351877 32.4855587,25.4232349 C32.2329104,24.9098917 32.1702561,24.4932641 32.3022051,24.18593 C32.5668843,23.559934 33.6881773,23.2052576 34.4061235,22.9779991 C34.6081484,22.9126886 34.7974394,22.8526905 34.9481378,22.7926924 C36.4001235,22.219351 37.1287726,21.4867177 37.1133824,20.6134638 C37.0993985,19.9208292 36.5560562,19.2848335 35.7513939,19.000155 C35.4740588,18.884143 35.1440691,18.8208637 34.8227511,18.8208637 C34.6007268,18.8208637 34.2707371,18.8521909 33.9554345,18.99953 C33.3407663,19.2868647 32.7941428,19.4415474 32.3807964,19.4595156 C32.1968178,19.4521721 32.0574471,19.421548 31.9568253,19.3874866 C31.9694811,19.1695247 31.9841682,18.9454692 31.9981521,18.7168045 L32.0054956,18.6001675 C32.1768184,15.8809558 32.389468,12.4969994 31.4841838,10.4690942 C28.806924,4.466626 23.1251489,4 21.4471546,4 L20.6645229,4.00734352 C18.9898878,4.00734352 13.3187374,4.47334454 10.6441337,10.4724534 C9.73681842,12.505046 9.95149919,15.8875962 10.1235251,18.6061829 C10.1401652,18.8735183 10.1568834,19.1341351 10.1714923,19.3881897 C10.0568084,19.4274853 9.89150107,19.4621717 9.66682061,19.4621717 C9.18550757,19.4621717 8.60888501,19.306161 7.95351492,18.99953 C7.00620086,18.5555595 5.274302,19.1488222 5.04024684,20.3781587 C4.91290708,21.0434503 5.18094555,22.0027953 7.17947668,22.7927705 C7.33478431,22.8540967 7.53016881,22.916751 7.77016129,22.9927642 C8.4401403,23.2054138 9.56143329,23.5607152 9.82673748,24.1853831 C9.95806148,24.4940453 9.8954072,24.9106729 9.61877524,25.4753427 C9.52010646,25.7053355 7.14619647,31.1098536 1.90300139,31.9731078 C1.35903406,32.0624019 0.970999347,32.5451212 1.00170151,33.0944008 C1.01037311,33.2470523 1.04568451,33.4004069 1.11099496,33.5550895 C1.55168428,34.5857603 3.22960045,35.3010504 6.38817335,35.8023628 C6.45746805,35.9816541 6.53816865,36.3496894 6.58418283,36.5630421 C6.65082137,36.8677201 6.72081917,37.1823196 6.81620681,37.508325 C6.91221943,37.8383147 7.19752299,38.3916567 8.02280963,38.3916567 C8.30209775,38.3916567 8.62544699,38.3282993 8.97676411,38.2603327 C9.48206078,38.1610389 10.1727423,38.0263556 11.029356,38.0263556 C11.5046536,38.0263556 11.9993256,38.0690106 12.5000131,38.1516642 C13.444671,38.3082999 14.2733169,38.8942972 15.2346149,39.5736509 C16.649883,40.5743227 17.9365615,41.3396112 20.5097621,41.3396112 C20.5777287,41.3396112 20.6450704,41.33758 20.7110839,41.3329708 C20.8063934,41.3376581 20.902406,41.3396112 20.9997467,41.3396112 C23.2536605,41.3396112 25.2375827,40.7443174 26.8975307,39.5716979 C27.8148457,38.9224213 28.6814592,38.3090812 29.626742,38.1517423 C30.1287576,38.0690887 30.6240545,38.0264338 31.0993521,38.0264338 C31.9206545,38.0264338 32.5759465,38.131743 33.1606157,38.2464269 C33.5559158,38.3243932 33.8652811,38.3617358 34.1505847,38.3617358 C34.7265822,38.3617358 35.1505533,38.0451051 35.3125795,37.4924661 C35.4059359,37.1711481 35.4759338,36.8658451 35.5439004,36.5538237 C35.5799149,36.3878132 35.6665528,35.9904819 35.7399099,35.7991598 C38.8531717,35.3038628 40.4431219,34.6165406 40.8831081,33.588526 C40.9491216,33.4405618 40.9877923,33.283848 40.998417,33.1172126 C41.0279473,32.5676986 40.6412406,32.0850574 40.0972733,31.9943572 Z',
				'selected'   => 'false',
				'url'        => 'snapchat.com/',
			),
			'soundcloud'    => array(
				'color'      => '#ff8800',
				'color-rgba' => '255, 136, 0',
				'path'       => 'M1.61295238,24.952127 C1.59961905,25.0438095 1.53396825,25.1075556 1.45295238,25.1075556 C1.36952381,25.1075556 1.30361905,25.0433016 1.29257143,24.9512381 L1,22.767746 L1.29244444,20.5462857 C1.30349206,20.4543492 1.36939683,20.3898413 1.4528254,20.3898413 C1.53396825,20.3898413 1.59974603,20.4538413 1.6128254,20.5455238 L1.9592381,22.767746 L1.61295238,24.952127 Z M3.09777778,26.2813968 C3.08431746,26.376254 3.01612698,26.4426667 2.93180952,26.4426667 C2.84685714,26.4426667 2.77688889,26.3746032 2.76546032,26.2805079 L2.37219048,22.767746 C2.37219048,22.767746 2.76546032,19.176127 2.76546032,19.175746 C2.77688889,19.0820317 2.84685714,19.0140952 2.93180952,19.0140952 C3.01612698,19.0140952 3.08431746,19.080254 3.09790476,19.175619 L3.54539683,22.767873 L3.09777778,26.2813968 Z M4.70336508,26.8754286 C4.69104762,26.9888254 4.60685714,27.0712381 4.5032381,27.0712381 C4.3975873,27.0712381 4.31326984,26.9888254 4.30260317,26.8746667 L3.92939683,22.768254 C3.92939683,22.768254 4.30260317,18.5065397 4.30260317,18.5061587 C4.31314286,18.392381 4.39746032,18.3098413 4.5032381,18.3098413 C4.60698413,18.3098413 4.69104762,18.392127 4.70361905,18.5060317 L5.12761905,22.768254 L4.70336508,26.8754286 Z M6.32190476,27.0057143 C6.31073016,27.1366349 6.20990476,27.2358095 6.08761905,27.2358095 C5.96368254,27.2358095 5.86260317,27.1366349 5.85269841,27.0057143 L5.49980952,22.7690159 L5.85269841,18.3897143 C5.86260317,18.2582857 5.96355556,18.1591111 6.08761905,18.1591111 C6.21003175,18.1591111 6.31085714,18.2580317 6.32190476,18.3889524 L6.72279365,22.7690159 L6.32190476,27.0057143 Z M7.95326984,27.0407619 C7.94273016,27.1914921 7.82730159,27.3056508 7.68457143,27.3056508 C7.54044444,27.3056508 7.42488889,27.1914921 7.416,27.0410159 L7.08292063,22.7695238 L7.416,18.7073016 C7.42501587,18.5560635 7.54044444,18.4420317 7.68457143,18.4420317 C7.82742857,18.4420317 7.94298413,18.5559365 7.95326984,18.7056508 L8.33066667,22.7695238 L7.95326984,27.0407619 Z M9.59746032,27.0412698 C9.58793651,27.2088889 9.45498413,27.3408254 9.29473016,27.3408254 C9.13320635,27.3408254 9,27.2088889 8.99187302,27.0421587 L8.67911111,22.7710476 C8.67911111,22.7710476 8.99187302,16.1619048 8.99187302,16.1617778 C9,15.9937778 9.13320635,15.8619683 9.29473016,15.8619683 C9.45498413,15.8619683 9.58793651,15.9933968 9.59746032,16.1613968 L9.9512381,22.7709206 C9.9512381,22.7710476 9.59746032,27.0425397 9.59746032,27.0412698 Z M11.2295873,27.0370794 C11.2209524,27.224381 11.0728889,27.3710476 10.8928254,27.3710476 C10.7113651,27.3710476 10.5634286,27.2245079 10.5558095,27.0387302 L10.2631111,22.7941587 C10.2631111,22.7941587 10.5554286,14.6739048 10.5554286,14.6733968 C10.5633016,14.4859683 10.7113651,14.3394286 10.8928254,14.3394286 C11.0730159,14.3394286 11.2209524,14.4860952 11.2295873,14.6733968 L11.560254,22.7941587 C11.560254,22.7941587 11.2293333,27.0394921 11.2295873,27.0370794 Z M12.9244444,26.9719365 C12.9161905,27.1786667 12.7532698,27.3399365 12.5532698,27.3399365 C12.352127,27.3399365 12.1890794,27.1781587 12.1820952,26.9735873 L11.9097143,22.7726984 C11.9097143,22.7726984 12.1819683,13.9744762 12.1819683,13.9742222 C12.1892063,13.767619 12.352127,13.6055873 12.5532698,13.6055873 C12.7532698,13.6055873 12.9161905,13.7673651 12.9244444,13.9740952 L13.231619,22.7726984 C13.231619,22.7728254 12.9243175,26.9744762 12.9244444,26.9719365 Z M14.6071111,26.9462857 C14.599746,27.1716825 14.4220952,27.3484444 14.2020317,27.3484444 C13.9812063,27.3484444 13.8031746,27.1716825 13.7968254,26.9481905 L13.5445079,22.7733333 L13.7965714,13.6786032 C13.8030476,13.4525714 13.9810794,13.2755556 14.2019048,13.2755556 C14.4220952,13.2755556 14.599873,13.4524444 14.6071111,13.6783492 L14.8911746,22.7737143 L14.6071111,26.9462857 Z M16.3028571,26.9069206 C16.296254,27.151873 16.1034921,27.344254 15.863619,27.344254 C15.6227302,27.344254 15.4297143,27.151746 15.424254,26.9089524 L15.192127,22.7740952 L15.424,13.9112381 C15.4295873,13.6661587 15.6227302,13.4741587 15.863619,13.4741587 C16.1034921,13.4741587 16.296254,13.6659048 16.3028571,13.9107302 L16.5635556,22.7740952 C16.5635556,22.7740952 16.3028571,26.9098413 16.3028571,26.9069206 Z M18.0115556,26.880381 C18.0058413,27.1450159 17.7978413,27.351746 17.5381587,27.351746 C17.2777143,27.351746 17.0695873,27.1450159 17.0647619,26.8833016 L16.8529524,22.7744762 L17.0646349,14.2353016 C17.0697143,13.9707937 17.2778413,13.7635556 17.5382857,13.7635556 C17.7979683,13.7635556 18.0059683,13.9706667 18.0116825,14.2344127 L18.2488889,22.7749841 C18.2487619,22.7748571 18.0115556,26.8838095 18.0115556,26.880381 Z M19.7542857,26.4513016 L19.7330794,26.8549841 C19.7304127,26.9941587 19.672381,27.1205079 19.5805714,27.2119365 C19.4886349,27.3034921 19.3629206,27.360254 19.2252698,27.360254 C19.0703492,27.360254 18.9304127,27.2885079 18.8369524,27.1765079 C18.767746,27.0937143 18.7245714,26.9886984 18.7187302,26.8746667 C18.7184762,26.8689524 18.7177143,26.8632381 18.7175873,26.8573968 C18.7175873,26.8573968 18.5259683,22.7785397 18.5259683,22.7724444 L18.7158095,12.7095873 L18.7175873,12.6137143 C18.720381,12.4364444 18.8135873,12.280381 18.951746,12.1899683 C19.0309841,12.1381587 19.1248254,12.1076825 19.2251429,12.1076825 C19.328381,12.1076825 19.4248889,12.1395556 19.5056508,12.1942857 C19.639873,12.2852063 19.7299048,12.4391111 19.7329524,12.6133333 L19.9467937,22.775619 L19.7542857,26.4513016 Z M21.4468571,26.7946667 C21.4427937,27.0925714 21.199746,27.3349841 20.9055238,27.3349841 C20.6102857,27.3349841 20.3673651,27.0925714 20.3634286,26.7989841 L20.2539683,24.8152381 L20.1412063,22.7767619 L20.3621587,11.7492063 L20.3633016,11.6934603 C20.3655873,11.5259683 20.4434286,11.375873 20.5635556,11.2766984 C20.6571429,11.1994921 20.776254,11.1530159 20.9053968,11.1530159 C21.0058413,11.1530159 21.0999365,11.1817143 21.1809524,11.2308571 C21.3372698,11.3254603 21.4439365,11.4968889 21.4467302,11.6925714 C21.4467302,11.6925714 21.687873,22.7766349 21.687873,22.7767619 C21.688,22.7766349 21.4468571,26.7993651 21.4468571,26.7946667 Z M36.080381,27.3462857 C36.080381,27.3462857 22.4551111,27.3475556 22.4425397,27.3462857 C22.1485714,27.3168254 21.9150476,27.0829206 21.9111111,26.7817143 C21.9111111,26.7817143 21.9111111,11.1667302 21.9111111,11.1664762 C21.9146667,10.8793651 22.0132063,10.7319365 22.3846349,10.5879365 C23.3403175,10.2182857 24.4220952,10 25.5320635,10 C30.0676825,10 33.7859048,13.4783492 34.1773968,17.912127 C34.7630476,17.6667937 35.4063492,17.5301587 36.080381,17.5301587 C38.7974603,17.5301587 41,19.7329524 41,22.4504127 C41,25.168127 38.7974603,27.3462857 36.080381,27.3462857 Z',
				'selected'   => 'false',
				'url'        => 'soundcloud.com/',
			),
			'spotify'       => array(
				'color'      => '#1ED760',
				'color-rgba' => '30, 215, 96',
				'path'       => 'M20.000107,0.000106951872 C8.9719286,0.000106951872 0,8.97191751 0,19.9995717 C0,31.0278682 8.9719286,40.000107 20.000107,40.000107 C31.0280714,40.000107 40,31.0278682 40,19.9995717 C40,8.97191751 31.0280714,0.000106951872 20.000107,0.000106951872 Z M20.000107,36.7888547 C10.7425941,36.7888547 3.21121785,29.2573978 3.21121785,19.9995717 C3.21121785,10.7423879 10.7425941,3.2111451 20.000107,3.2111451 C29.2574059,3.2111451 36.7887821,10.7423879 36.7887821,19.9995717 C36.7887821,29.2573978 29.2574059,36.7888547 20.000107,36.7888547 Z M30.71851,14.0728846 C22.3680591,10.2136017 10.6398352,11.9260555 10.1446654,12.0007706 C9.26821697,12.1332883 8.66515026,12.9508731 8.79723835,13.8273309 C8.92932645,14.7037887 9.74775884,15.3085745 10.623565,15.1760568 C10.7355294,15.1591442 21.903717,13.5368196 29.3712971,16.9878453 C29.5892317,17.0886786 29.8185127,17.1364193 30.0439402,17.1364193 C30.6506463,17.1364193 31.2312344,16.7908885 31.5024753,16.2040857 C31.8745484,15.3989177 31.5234553,14.4449617 30.71851,14.0728846 Z M29.4605689,20.1136782 C21.9007198,16.6200499 11.2994193,18.1683016 10.8517755,18.2357379 C9.97532714,18.3682556 9.37226043,19.1858404 9.50434852,20.0622982 C9.63622254,20.9389701 10.4544408,21.5428996 11.3306752,21.4112382 C11.4308652,21.3960383 21.4365918,19.9432678 28.113356,23.028853 C28.3317188,23.1296863 28.5605716,23.1774269 28.7859991,23.1774269 C29.3927052,23.1772129 29.9732934,22.8318962 30.2447483,22.2448793 C30.6166073,21.4399254 30.2655142,20.4857553 29.4605689,20.1136782 Z M27.5897134,25.895859 C20.9330729,22.8194794 11.6158313,24.180194 11.2225642,24.2394951 C10.3456876,24.3720128 9.74219273,25.1900258 9.87470898,26.0669117 C10.0072252,26.9437976 10.8258717,27.5466567 11.702106,27.4147813 C11.789023,27.4015081 20.450962,26.1341339 26.2425005,28.8108197 C26.4604351,28.911653 26.6897161,28.9593936 26.9151436,28.9593936 C27.5218497,28.9591795 28.1024378,28.6138629 28.3736787,28.0270601',
				'selected'   => 'false',
				'url'        => 'spotify.com/',
			),
			'tumblr'        => array(
				'color'      => '#32506d',
				'color-rgba' => '50, 80, 109',
				'path'       => 'm25.9 29.9v-3.5c-1.1 0.8-2.2 1.1-3.3 1.1-0.5 0-1-0.1-1.6-0.4-0.4-0.3-0.6-0.5-0.7-0.9-0.2-0.3-0.3-1.1-0.3-2.4v-5.5h5v-3.3h-5v-5.6h-3c-0.2 1.3-0.5 2.2-0.7 2.8-0.3 0.7-0.8 1.3-1.5 1.9-0.7 0.5-1.4 0.9-2.1 1.2v3h2.3v7.6c0 0.8 0.1 1.6 0.4 2.2 0.2 0.5 0.5 1 1.1 1.5 0.4 0.4 1 0.8 1.8 1.1 1 0.3 1.9 0.4 2.7 0.4 0.8 0 1.6-0.1 2.4-0.3 0.8-0.2 1.7-0.5 2.5-0.9z',
				'selected'   => 'false',
				'url'        => 'tumblr.com/',
			),
			'twitch'        => array(
				'color'      => '#6441A4',
				'color-rgba' => '100, 65, 164',
				'path'       => 'M26.3137255,23.5384615 L29.4509804,23.5384615 C29.8839216,23.5384615 30.2352941,23.1946154 30.2352941,22.7692308 L30.2352941,12.7692308 C30.2352941,12.3438462 29.8839216,12 29.4509804,12 L26.3137255,12 C25.8807843,12 25.5294118,12.3438462 25.5294118,12.7692308 L25.5294118,22.7692308 C25.5294118,23.1946154 25.8807843,23.5384615 26.3137255,23.5384615 Z M27.0980392,13.5384615 L28.6666667,13.5384615 L28.6666667,22 L27.0980392,22 L27.0980392,13.5384615 Z M18.4705882,23.5384615 L21.6078431,23.5384615 C22.0407843,23.5384615 22.3921569,23.1946154 22.3921569,22.7692308 L22.3921569,12.7692308 C22.3921569,12.3438462 22.0407843,12 21.6078431,12 L18.4705882,12 C18.0376471,12 17.6862745,12.3438462 17.6862745,12.7692308 L17.6862745,22.7692308 C17.6862745,23.1946154 18.0376471,23.5384615 18.4705882,23.5384615 Z M19.254902,13.5384615 L20.8235294,13.5384615 L20.8235294,22 L19.254902,22 L19.254902,13.5384615 Z M41.2156863,2 L5.52941176,2 C5.25019608,2 4.99215686,2.14538462 4.85176471,2.38153846 L2.10666667,6.99692308 C2.03686275,7.11461538 2,7.24846154 2,7.38461538 L2,35.8461538 C2,36.2715385 2.35137255,36.6153846 2.78431373,36.6153846 L11.4117647,36.6153846 L11.4117647,41.2307692 C11.4117647,41.6561538 11.7631373,42 12.1960784,42 L17.6862745,42 C17.88,42 18.0666667,41.9292308 18.2109804,41.8023077 L24.0878431,36.6153846 L31.4117647,36.6153846 C31.6086275,36.6153846 31.7984314,36.5423077 31.9427451,36.4123077 L41.7466667,27.5661538 C41.9082353,27.4207692 42,27.2146154 42,27 L42,2.76923077 C42,2.34384615 41.6486275,2 41.2156863,2 Z M40.4313725,26.6630769 L31.1058824,35.0769231 L23.7866667,35.0769231 C23.5929412,35.0769231 23.4062745,35.1476923 23.2619608,35.2746154 L17.385098,40.4615385 L12.9803922,40.4615385 L12.9803922,35.8461538 C12.9803922,35.4207692 12.6290196,35.0769231 12.1960784,35.0769231 L3.56862745,35.0769231 L3.56862745,7.59307692 L5.97960784,3.53846154 L40.4313725,3.53846154 L40.4313725,26.6630769 Z M9.05882353,30.4615385 L16.9019608,30.4615385 L16.9019608,34.3076923 C16.9019608,34.6184615 17.0933333,34.9 17.3858824,35.0184615 C17.4831373,35.0584615 17.585098,35.0769231 17.6862745,35.0769231 C17.8901961,35.0769231 18.0909804,34.9984615 18.2407843,34.8515385 L22.7168627,30.4615385 L31.4117647,30.4615385 C31.6196078,30.4615385 31.8196078,30.3807692 31.9662745,30.2361538 L37.0643137,25.2361538 C37.2117647,25.0923077 37.2941176,24.8969231 37.2941176,24.6923077 L37.2941176,6.61538462 C37.2941176,6.19 36.9427451,5.84615385 36.5098039,5.84615385 L9.05882353,5.84615385 C8.62588235,5.84615385 8.2745098,6.19 8.2745098,6.61538462 L8.2745098,29.6923077 C8.2745098,30.1176923 8.62588235,30.4615385 9.05882353,30.4615385 Z M9.84313725,7.38461538 L35.7254902,7.38461538 L35.7254902,24.3738462 L31.0870588,28.9230769 L22.3921569,28.9230769 C22.1843137,28.9230769 21.9843137,29.0038462 21.8376471,29.1484615 L18.4705882,32.4507692 L18.4705882,29.6923077 C18.4705882,29.2669231 18.1192157,28.9230769 17.6862745,28.9230769 L9.84313725,28.9230769 L9.84313725,7.38461538 Z',
				'selected'   => 'false',
				'url'        => 'twitch.tv/',
				'viewbox'    => '50',
			),
			'vk'            => array(
				'color'      => '#4c6c91',
				'color-rgba' => '76, 108, 145',
				'path'       => 'm39.8 12.2q0.5 1.3-3.1 6.1-0.5 0.7-1.4 1.8-1.6 2-1.8 2.7-0.4 0.8 0.3 1.7 0.3 0.4 1.6 1.7h0.1l0 0q3 2.8 4 4.6 0.1 0.1 0.1 0.3t0.2 0.5 0 0.8-0.5 0.5-1.3 0.3l-5.3 0.1q-0.5 0.1-1.1-0.1t-1.1-0.5l-0.4-0.2q-0.7-0.5-1.5-1.4t-1.4-1.6-1.3-1.2-1.1-0.3q-0.1 0-0.2 0.1t-0.4 0.3-0.4 0.6-0.4 1.1-0.1 1.6q0 0.3-0.1 0.5t-0.1 0.4l-0.1 0.1q-0.4 0.4-1.1 0.5h-2.4q-1.5 0.1-3-0.4t-2.8-1.1-2.1-1.3-1.5-1.2l-0.5-0.5q-0.2-0.2-0.6-0.6t-1.4-1.9-2.2-3.2-2.6-4.4-2.7-5.6q-0.1-0.3-0.1-0.6t0-0.3l0.1-0.1q0.3-0.4 1.2-0.4l5.7-0.1q0.2 0.1 0.5 0.2t0.3 0.2l0.1 0q0.3 0.2 0.5 0.7 0.4 1 1 2.1t0.8 1.7l0.3 0.6q0.6 1.3 1.2 2.2t1 1.4 0.9 0.8 0.7 0.3 0.5-0.1q0.1 0 0.1-0.1t0.3-0.5 0.3-0.9 0.2-1.7 0-2.6q-0.1-0.9-0.2-1.5t-0.3-1l-0.1-0.2q-0.5-0.7-1.8-0.9-0.3-0.1 0.1-0.5 0.4-0.4 0.8-0.7 1.1-0.5 5-0.5 1.7 0.1 2.8 0.3 0.4 0.1 0.7 0.3t0.4 0.5 0.2 0.7 0.1 0.9 0 1.1-0.1 1.5 0 1.7q0 0.3 0 0.9t-0.1 1 0.1 0.8 0.3 0.8 0.4 0.6q0.2 0 0.4 0t0.5-0.2 0.8-0.7 1.1-1.4 1.4-2.2q1.2-2.2 2.2-4.7 0.1-0.2 0.2-0.4t0.3-0.2l0 0 0.1-0.1 0.3-0.1 0.4 0 6 0q0.8-0.1 1.3 0t0.7 0.4z',
				'selected'   => 'false',
				'url'        => 'vk.com/',
			),
			'wechat'        => array(
				'color'      => '#4EC034',
				'color-rgba' => '78, 192, 52',
				'path'       => 'M27.7561832,11.4320611 C24.059542,11.6251908 20.8450382,12.7458015 18.2352672,15.2775573 C15.5984733,17.8354198 14.3948092,20.969771 14.7238168,24.8552672 C13.2789313,24.6763359 11.9629008,24.4793893 10.6393893,24.3679389 C10.1822901,24.3294656 9.63984733,24.3841221 9.25267176,24.6025954 C7.96748092,25.3277863 6.73541985,26.1465649 5.2751145,27.0593893 C5.54305344,25.8474809 5.71648855,24.7862595 6.02351145,23.7654962 C6.24931298,23.0152672 6.14473282,22.5977099 5.45358779,22.1091603 C1.01603053,18.9761832 -0.854503817,14.2874809 0.545343511,9.46030534 C1.84045802,4.99465649 5.02091603,2.28641221 9.34244275,0.874656489 C15.240916,-1.05206107 21.869771,0.913282443 25.4564885,5.59633588 C26.7519084,7.28793893 27.5462595,9.18656489 27.7561832,11.4320611 Z M10.7429008,9.92793893 C10.7769466,9.04503817 10.0119084,8.24961832 9.10320611,8.22305344 C8.17282443,8.19572519 7.40763359,8.90671756 7.38045802,9.82351145 C7.3529771,10.7526718 8.06366412,11.4972519 9.00076336,11.5210687 C9.92977099,11.5445802 10.7085496,10.8326718 10.7429008,9.92793893 Z M19.6193893,8.22244275 C18.7073282,8.23923664 17.9366412,9.01603053 17.9528244,9.90244275 C17.9694656,10.8212214 18.7254962,11.54 19.6633588,11.5287023 C20.6036641,11.5174046 21.3167939,10.7909924 21.3079389,9.85282443 C21.3001527,8.9319084 20.5474809,8.20549618 19.6193893,8.22244275 Z M36.0612214,34.4778626 C34.890687,33.9566412 33.8169466,33.1746565 32.6737405,33.0552672 C31.5349618,32.9363359 30.3378626,33.5932824 29.1464122,33.7151145 C25.5172519,34.0864122 22.2659542,33.0749618 19.5850382,30.5957252 C14.4862595,25.8796947 15.2148092,18.6485496 21.1138931,14.7838168 C26.3567939,11.3490076 34.0458015,12.4940458 37.7422901,17.26 C40.9680916,21.4187786 40.5890076,26.9393893 36.6509924,30.4331298 C35.5114504,31.4442748 35.101374,32.2763359 35.8325191,33.609313 C35.9674809,33.8554198 35.9829008,34.1670229 36.0612214,34.4778626 L36.0612214,34.4778626 Z M22.7369466,21.5772519 C23.4821374,21.5780153 24.0957252,20.9948092 24.1239695,20.2587786 C24.1537405,19.479542 23.5270229,18.8259542 22.7467176,18.8227481 C21.9741985,18.8192366 21.3270229,19.4819847 21.3538931,20.2496183 C21.3792366,20.9830534 21.9970992,21.5763359 22.7369466,21.5772519 Z M31.3264122,18.8258015 C30.6033588,18.8207634 29.9890076,19.4126718 29.959542,20.1432061 C29.9282443,20.9244275 30.5354198,21.5659542 31.3085496,21.5679389 C32.0563359,21.5703817 32.6471756,21.0048855 32.6743511,20.2607634 C32.7033588,19.4777099 32.0958779,18.831145 31.3264122,18.8258015 L31.3264122,18.8258015 Z',
				'selected'   => 'false',
				'url'        => 'wechat.com/',
			),
			'weibo'         => array(
				'color'      => '#ff9933',
				'color-rgba' => '255, 153, 51',
				'path'       => 'm15.1 28.7q0.4-0.8 0.2-1.6t-1-1.1q-0.8-0.3-1.6 0t-1.4 1q-0.5 0.8-0.3 1.5t1 1.2 1.7 0 1.4-1z m2.1-2.7q0.1-0.3 0-0.6t-0.3-0.4q-0.4-0.2-0.7 0t-0.5 0.4q-0.3 0.7 0.3 1 0.3 0.1 0.7 0t0.5-0.4z m3.8 2.3q-1 2.3-3.5 3.4t-5 0.3q-2.4-0.8-3.3-2.9t0.2-4.1q1-2.1 3.4-3.1t4.7-0.5q2.4 0.7 3.5 2.7t0 4.2z m7-3.5q-0.2-2.2-2-3.8t-4.6-2.5-6.2-0.4q-4.9 0.5-8.2 3.1t-3 5.9q0.2 2.2 2 3.8t4.7 2.5 6.1 0.4q5-0.5 8.3-3.1t2.9-5.9z m6.9 0.1q0 1.5-0.8 3.1t-2.5 3-3.7 2.7-5.1 1.8-6 0.7-6.2-0.7-5.3-2.1-3.9-3.4-1.4-4.4q0-2.6 1.6-5.5t4.4-5.8q3.7-3.7 7.6-5.2t5.5 0.1q1.4 1.4 0.4 4.7-0.1 0.3 0 0.4t0.2 0.2 0.4 0 0.3-0.1l0.1-0.1q3.1-1.3 5.5-1.3t3.4 1.4q1 1.4 0 4 0 0.3-0.1 0.4t0.1 0.3 0.3 0.2 0.3 0.1q1.3 0.4 2.3 1t1.8 1.9 0.8 2.6z m-1.7-14q1 1.1 1.3 2.5t-0.2 2.6q-0.2 0.5-0.7 0.7t-0.9 0.1q-0.6-0.1-0.8-0.6t-0.1-1q0.5-1.4-0.5-2.5t-2.4-0.8q-0.6 0.1-1-0.2t-0.6-0.8q-0.1-0.5 0.2-1t0.8-0.5q1.4-0.3 2.7 0.1t2.2 1.4z m4.1-3.6q1.9 2.1 2.5 5t-0.3 5.4q-0.2 0.6-0.8 0.8t-1.1 0.1-0.9-0.7-0.1-1.2q0.6-1.8 0.2-3.8t-1.8-3.5q-1.4-1.6-3.3-2.2t-3.9-0.2q-0.6 0.2-1.1-0.2t-0.7-1 0.2-1.1 1-0.7q2.7-0.5 5.4 0.3t4.7 3z',
				'selected'   => 'false',
				'url'        => 'weibo.com/',
			),
			'yelp'          => array(
				'color'      => '#d32323',
				'color-rgba' => '211, 35, 35',
				'path'       => 'M4.1482892,28.8927037 C3.85912566,26.5858934 4.0340317,23.3341573 4.32102922,22.0616211 C4.63889252,20.6515427 5.42136691,20.3006476 6.75834217,20.8367373 C9.40034393,21.8948376 12.0342231,22.9702661 14.6670193,24.0484021 C15.4987706,24.3895501 15.8648278,24.9797903 15.8280055,25.8375338 C15.7933492,26.650874 15.3899281,27.1653035 14.5267695,27.4403879 C11.7526406,28.3241237 8.98013623,29.210567 6.19896775,30.0699351 C5.09267351,30.4116246 4.39575688,30.0352788 4.1482892,28.8927037 Z M19.1100659,40.3872254 C19.0986943,41.5027252 18.5257822,42.0956729 17.420571,41.987372 C14.9550998,41.7436948 12.6791553,40.9032794 10.6382238,39.5034896 C9.71333368,38.868846 9.63860602,38.0533398 10.3539338,37.1912643 C12.2459515,34.9142367 14.1569218,32.6518298 16.0738487,30.3948379 C16.6104799,29.7629019 17.3290567,29.6221106 18.1012425,29.9296853 C18.8241514,30.2161414 19.1571768,30.7164918 19.1615088,31.5688203 C19.1690899,33.0384642 19.1636749,34.5081081 19.1636749,35.9782936 C19.1474297,35.9782936 19.1306431,35.9782936 19.1143979,35.9782936 C19.1143979,37.447396 19.1268525,38.9175815 19.1100659,40.3872254 Z M19.1068169,19.9194282 C18.7678349,21.1459365 17.5483661,21.5049542 16.5817801,20.6683293 C16.1913552,20.3298889 15.8588713,19.9015586 15.5799963,19.4634812 C12.9082118,15.2668193 10.2526724,11.0604103 7.59767456,6.85454277 C6.83036231,5.63940607 6.98306665,4.90729163 8.27022348,4.25423688 C10.8163789,2.9611235 13.5249857,2.18027363 16.3814234,2.00915812 C17.7438494,1.9273909 18.2219981,2.38442093 18.3162199,3.73276779 C18.6508698,8.54133011 18.9681916,13.3515169 19.3044661,18.3355268 C19.2568137,18.755193 19.2606042,19.3595123 19.1068169,19.9194282 Z M22.3623435,21.7020619 C24.1038228,19.3411012 25.8339306,16.9709348 27.588406,14.6191796 C28.3394731,13.6119807 29.1566038,13.5145099 30.0852845,14.3776685 C31.8116017,15.9805226 33.0993,17.9039475 33.9245533,20.1105794 C34.3696702,21.3045974 33.9759962,22.0020556 32.7446144,22.3188359 C29.9472008,23.0384957 27.1411231,23.7283728 24.3382944,24.4252894 C24.1693449,24.4675268 23.9933559,24.4772739 23.8531061,24.4973095 C23.0912089,24.4751078 22.5708228,24.1133827 22.2367144,23.4922767 C21.908021,22.8798348 21.9410528,22.2744325 22.3623435,21.7020619 Z M33.95217,33.4359287 C33.0002046,35.3138672 31.7097988,36.933508 30.1286049,38.3192187 C29.8854692,38.5336546 29.6017207,38.7177662 29.3087666,38.8574744 C28.6205141,39.1861678 27.980997,39.0497086 27.5797419,38.4134405 C25.9184053,35.7860594 24.2738553,33.1473067 22.650424,30.4950163 C22.2329239,29.8121788 22.4446522,29.1336733 22.9309235,28.5520972 C23.3955346,27.9948888 24.0009369,27.7853265 24.6989366,28.0170905 C27.5672873,28.9647239 30.42914,29.9269778 33.2931587,30.8854413 C33.946755,31.1031262 34.3198517,31.5314564 34.3604646,32.2706105 C34.2267129,32.6610354 34.1357401,33.0709545 33.95217,33.4359287 Z',
				'selected'   => 'false',
				'url'        => 'yelp.com/',
			),
			'whatsapp'      => array(
				'color'    => '#25d366',
				'url'      => 'https://wa.me/',
				'selected' => 'false',
			),
			'flickr'        => array(
				'color'    => '#ff0084',
				'url'      => 'https://www.flickr.com/people/',
				'selected' => 'false',
			),
			'slideshare'    => array(
				'color'    => '#057eb0',
				'url'      => 'https://www.slideshare.net/',
				'selected' => 'false',
			),
			'dribbble'      => array(
				'color'    => '#e44786',
				'url'      => 'https://dribbble.com/',
				'selected' => 'false',
			),
			'behance'       => array(
				'color'    => '#135bf3',
				'url'      => 'https://www.behance.net/',
				'selected' => 'false',
			),
			'deviantart'    => array(
				'color'    => '#21ce4e',
				'url'      => '',
				'selected' => 'false',
			),
			'skype'         => array(
				'color'    => '#00aff0',
				'url'      => '',
				'selected' => 'false',
			),
			'trello'        => array(
				'color'    => '#0D63DE',
				'url'      => 'https://trello.com/',
				'selected' => 'false',
			),
			'xing'          => array(
				'color'    => '#1a7576',
				'url'      => 'https://www.xing.com/profile/',
				'selected' => 'false',
			),
			'mix'           => array(
				'color'    => '#ff8226',
				'url'      => 'https://mix.com/',
				'selected' => 'false',
			),
			'meetup'        => array(
				'color'    => '#e61b3e',
				'url'      => 'https://www.meetup.com/members/',
				'selected' => 'false',
			),
			'bandcamp'      => array(
				'color'    => '#1ca0c3',
				'url'      => '',
				'selected' => 'false',
			),
			'googlemaps'    => array(
				'color'    => '#4185f3',
				'url'      => '',
				'selected' => 'false',
			),
			'zomato'        => array(
				'color'    => '#e23844',
				'url'      => '',
				'selected' => 'false',
			),
			'gitlab'        => array(
				'color'    => '#ff492c',
				'url'      => 'https://gitlab.com/',
				'selected' => 'false',
			),
			'bitbucket'     => array(
				'color'    => '#2584ff',
				'url'      => 'https://bitbucket.org/',
				'selected' => 'false',
			),
			'stackoverflow' => array(
				'color'    => '#c66516',
				'url'      => 'https://stackoverflow.com/users/',
				'selected' => 'false',
			),
			'threads'       => array(
				'color'    => '#000000',
				'url'      => 'https://www.threads.net/@',
				'selected' => 'false',
			),
			'shares'        => array(
				'color'    => '#0eb2b2',
				'url'      => 'https://shar.es/@',
				'selected' => 'false',
			),
		);

		return $networks;
	}

	/**
	 * AJAX Call back to save the set up button config for setup.
	 *
	 * @action wp_ajax_set_follow_button_config
	 */
	public function set_follow_button_config() {
		check_ajax_referer( $this->plugin->meta_prefix, 'nonce' );

		if ( ! isset( $_POST['config'] ) || '' === $_POST['config'] ) { // WPCS: input var ok.
			wp_send_json_error( 'Follow Config Set Failed' );
		}

		$post = filter_input_array(
			INPUT_POST,
			array(
				'button' => FILTER_SANITIZE_STRING,
				'config' => array(
					'filter' => FILTER_DEFAULT,
					'flags'  => FILTER_REQUIRE_ARRAY,
				),
				'first'  => FILTER_SANITIZE_STRING,
				'nonce'  => FILTER_SANITIZE_STRING,
				'type'   => FILTER_SANITIZE_STRING,
			)
		);

		$first    = ( true === isset( $post['first'] ) && 'upgrade' !== $post['first'] );
		$type     = ( false === empty( $post['type'] ) );
		$button   = sanitize_text_field( wp_unslash( $post['button'] ) );
		$config   = $post['config'];
		$networks = array_map( 'sanitize_text_field', wp_unslash( $config['networks'] ) );

		// If user doesn't have a sharethis account already.
		if ( false !== $type ) {
			$config = 'platform' !== $button ? json_decode( str_replace( '\\', '', $config ), true ) : $config;
		} else {
			$newconfig[ strtolower( $button ) ] = $config;
			$config                             = $newconfig;
		}

		if ( false === $first ) {
			$current_config                              = get_option( 'sharethis_button_config', true );
			$current_config                              = false !== $current_config && null !== $current_config ? $current_config : array();
			$current_config['inline-follow']             = $post['config']; // WPCS: input var ok.
			$current_config['inline-follow']['networks'] = $networks;
			$config                                      = $current_config;
		}

		// Make sure bool is "true" or "false".
		if ( true === isset( $config['inline-follow'] ) ) {
			$config['inline-follow']['enabled'] = true === $config['inline-follow']['enabled'] || '1' === $config['inline-follow']['enabled'] || 'true' === $config['inline-follow']['enabled'] ? 'true' : 'false';
		}

		update_option( 'sharethis_button_config', $config );

		if ( $first && 'platform' !== $button ) {
			update_option( 'sharethis_first_product', 'inline-follow' );
		}
	}
}
