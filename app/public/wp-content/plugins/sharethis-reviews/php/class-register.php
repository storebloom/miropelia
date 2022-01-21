<?php
/**
 * ShareThis Reviews.
 *
 * @package ShareThisReviews
 */

namespace ShareThisReviews;

/**
 * Register Class
 *
 * @package ShareThisReviews
 */
class Register
{

    /**
     * Plugin instance.
     *
     * @var object
     */
    public $plugin;

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
     * @var array
     */
    public $setting_sections;

    /**
     * Holds the settings fields.
     *
     * @var array
     */
    public $setting_fields;

    /**
     * The current post types registered.
     *
     * @var array
     */
    public $current_posttypes;

    /**
     * Current reviews.
     *
     * @var array
     */
    public $current_reviews;

    /**
     * Class constructor.
     *
     * @param object $plugin Plugin class.
     * @param object $button_widget Button Widget class.
     */
    public function __construct($plugin)
    {
        $this->plugin            = $plugin;
        $this->current_posttypes = get_post_types(
            array(
                'public' => true,
            )
        );

        $this->review_approval = ('on' === get_option('sharethisreviews_review_section_approval', true));
        $this->menu_slug       = 'sharethisreviews';
        $this->set_settings();
        $this->current_reviews = $this->get_reviews();

        // Configure your buttons notice on activation.
        register_activation_hook($this->plugin->dir_path . '/sharethis-reviews.php', array(
            $this,
            'st_activation_hook',
        ));

        // Clean up plugin information on deactivation.
        register_deactivation_hook($this->plugin->dir_path . '/sharethis-reviews.php', array(
            $this,
            'st_deactivation_hook',
        ));

        if (! is_admin()) {
            $this->current_reviews = $this->get_reviews();
        }
    }

    /**
     * Set the settings sections and fields.
     *
     * @access private
     */
    private function set_settings()
    {
        // Sections config.
        $this->setting_sections = array(
            array(
                'section' => 'review_section',
                'title'   => '',
            ),
            array(
                'section' => 'rating_section',
                'title'   => '',
            ),
            array(
                'section' => 'impression_section',
                'title'   => '',
            ),
        );

        // Setting configs.
        $this->setting_fields = array(
            'review'     => array(
                array(
                    'id_suffix'   => 'title',
                    'description' => '',
                    'callback'    => 'text_field',
                    'section'     => 'review_section',
                    'arg'         => array('review_section', 'title'),
                ),
                array(
                    'id_suffix'   => 'approval',
                    'description' => '',
                    'callback'    => 'check_field',
                    'section'     => 'review_section',
                    'arg'         => array('review_section', 'approval'),
                ),
                array(
                    'id_suffix'   => 'account',
                    'description' => '',
                    'callback'    => 'check_field',
                    'section'     => 'review_section',
                    'arg'         => array('review_section', 'account'),
                ),
                array(
                    'id_suffix'   => 'posttype',
                    'description' => '',
                    'callback'    => 'multi_check',
                    'section'     => 'review_section',
                    'arg'         => array('review_section', $this->current_posttypes, 'posttype'),
                ),
                array(
                    'id_suffix'   => 'ctacopy',
                    'description' => '',
                    'callback'    => 'text_field',
                    'section'     => 'review_section',
                    'arg'         => array('review_section', 'ctacopy'),
                ),
                array(
                    'id_suffix'   => 'ctacolor',
                    'description' => '',
                    'callback'    => 'color_picker',
                    'section'     => 'review_section',
                    'arg'         => array('review_section', 'ctacolor'),
                ),
            ),
            'rating'     => array(
                array(
                    'id_suffix'   => 'title',
                    'description' => '',
                    'callback'    => 'text_field',
                    'section'     => 'rating_section',
                    'arg'         => array('rating_section', 'title'),
                ),
                array(
                    'id_suffix'   => 'symbols',
                    'description' => '',
                    'callback'    => 'radio_field',
                    'section'     => 'rating_section',
                    'arg'         => array('rating_section', array('stars', 'hearts', 'numbers', 'emoji'), 'symbols'),
                ),
                array(
                    'id_suffix'   => 'total',
                    'description' => '',
                    'callback'    => 'check_field',
                    'section'     => 'rating_section',
                    'arg'         => array('rating_section', 'total'),
                ),
                array(
                    'id_suffix'   => 'top',
                    'description' => '',
                    'callback'    => 'check_field',
                    'section'     => 'rating_section',
                    'arg'         => ['rating_section', 'top'],
                ),
                array(
                    'id_suffix'   => 'posttype',
                    'description' => '',
                    'callback'    => 'multi_check',
                    'section'     => 'rating_section',
                    'arg'         => array('rating_section', $this->current_posttypes, 'posttype'),
                ),
            ),
            'impression' => array(
                array(
                    'id_suffix'   => 'symbols',
                    'description' => '',
                    'callback'    => 'radio_field',
                    'section'     => 'impression_section',
                    'arg'         => array('impression_section', array('thumb', 'heart', 'emoji'), 'symbols'),
                ),
                array(
                    'id_suffix'   => 'negative',
                    'description' => '',
                    'callback'    => 'check_field',
                    'section'     => 'impression_section',
                    'arg'         => array('impression_section', 'negative'),
                ),
                array(
                    'id_suffix'   => 'posttype',
                    'description' => '',
                    'callback'    => 'multi_check',
                    'section'     => 'impression_section',
                    'arg'         => array('impression_section', $this->current_posttypes, 'posttype'),
                ),
            ),
        );
    }

    /**
     * Add in ShareThis menu option.
     *
     * @action admin_menu
     */
    public function define_sharethis_menus()
    {
        // Menu base64 Encoded icon.
        $icon = 'data:image/svg+xml;base64,PD94bWwgdmVyc2lvbj0iMS4wIiBlbmNvZGluZz0iVVRGLTgiIHN0YW5kYWxvbmU9Im5vIj8+Cjxzdmcgd2lkdGg9IjE2cHgiIGhlaWdodD0iMTZweCIgdmlld0JveD0iMCAwIDE2IDE2IiB2ZXJzaW9uPSIxLjEiIHhtbG5zPSJodHRwOi8vd3d3LnczLm9yZy8yMDAwL3N2ZyIgeG1sbnM6eGxpbms9Imh0dHA6Ly93d3cudzMub3JnLzE5OTkveGxpbmsiPgogICAgPCEtLSBHZW5lcmF0b3I6IFNrZXRjaCA0NC4xICg0MTQ1NSkgLSBodHRwOi8vd3d3LmJvaGVtaWFuY29kaW5nLmNvbS9za2V0Y2ggLS0+CiAgICA8dGl0bGU+RmlsbCAzPC90aXRsZT4KICAgIDxkZXNjPkNyZWF0ZWQgd2l0aCBTa2V0Y2guPC9kZXNjPgogICAgPGRlZnM+PC9kZWZzPgogICAgPGcgaWQ9IlBhZ2UtMSIgc3Ryb2tlPSJub25lIiBzdHJva2Utd2lkdGg9IjEiIGZpbGw9Im5vbmUiIGZpbGwtcnVsZT0iZXZlbm9kZCI+CiAgICAgICAgPGcgaWQ9IkRlc2t0b3AtSEQiIHRyYW5zZm9ybT0idHJhbnNsYXRlKC0xMC4wMDAwMDAsIC00MzguMDAwMDAwKSIgZmlsbD0iI0ZFRkVGRSI+CiAgICAgICAgICAgIDxwYXRoIGQ9Ik0yMy4xNTE2NDMyLDQ0OS4xMDMwMTEgQzIyLjcyNjg4NzcsNDQ5LjEwMzAxMSAyMi4zMzM1MDYyLDQ0OS4yMjg5OSAyMS45OTcwODA2LDQ0OS40Mzc5ODkgQzIxLjk5NTE0OTksNDQ5LjQzNTA5MyAyMS45OTcwODA2LDQ0OS40Mzc5ODkgMjEuOTk3MDgwNiw0NDkuNDM3OTg5IEMyMS44ODA3NTU1LDQ0OS41MDg5NDMgMjEuNzM1NDY5OCw0NDkuNTQ1NjI2IDIxLjU4OTIxODgsNDQ5LjU0NTYyNiBDMjEuNDUzMTA0LDQ0OS41NDU2MjYgMjEuMzE5ODg1Miw0NDkuNTA3NDk0IDIxLjIwODg2OTYsNDQ5LjQ0NTIyOSBMMTQuODczNzM4Myw0NDYuMDM4OTggQzE0Ljc2NDE3MDcsNDQ1Ljk5MDIzIDE0LjY4NzkwNzgsNDQ1Ljg3ODczMSAxNC42ODc5MDc4LDQ0NS43NTEzMDUgQzE0LjY4NzkwNzgsNDQ1LjYyMzM5NSAxNC43NjUxMzYsNDQ1LjUxMTg5NyAxNC44NzQ3MDM2LDQ0NS40NjI2NjQgTDIxLjIwODg2OTYsNDQyLjA1Njg5NyBDMjEuMzE5ODg1Miw0NDEuOTk1MTE1IDIxLjQ1MzEwNCw0NDEuOTU2NTAxIDIxLjU4OTIxODgsNDQxLjk1NjUwMSBDMjEuNzM1NDY5OCw0NDEuOTU2NTAxIDIxLjg4MDc1NTUsNDQxLjk5MzY2NyAyMS45OTcwODA2LDQ0Mi4wNjQ2MiBDMjEuOTk3MDgwNiw0NDIuMDY0NjIgMjEuOTk1MTQ5OSw0NDIuMDY3MDM0IDIxLjk5NzA4MDYsNDQyLjA2NDYyIEMyMi4zMzM1MDYyLDQ0Mi4yNzMxMzcgMjIuNzI2ODg3Nyw0NDIuMzk5MTE1IDIzLjE1MTY0MzIsNDQyLjM5OTExNSBDMjQuMzY2NTQwMyw0NDIuMzk5MTE1IDI1LjM1MTY4MzQsNDQxLjQxNDQ1NSAyNS4zNTE2ODM0LDQ0MC4xOTk1NTggQzI1LjM1MTY4MzQsNDM4Ljk4NDY2IDI0LjM2NjU0MDMsNDM4IDIzLjE1MTY0MzIsNDM4IEMyMi4wMTYzODc2LDQzOCAyMS4wOTMwMjcyLDQzOC44NjMwMjYgMjAuOTc1MjU0MSw0MzkuOTY3MzkgQzIwLjk3MTM5MjYsNDM5Ljk2MzA0NiAyMC45NzUyNTQxLDQzOS45NjczOSAyMC45NzUyNTQxLDQzOS45NjczOSBDMjAuOTUwNjM3NSw0NDAuMjM5MTM3IDIwLjc2OTE1MTEsNDQwLjQ2NzkyNiAyMC41MzYwMTgzLDQ0MC41ODQyNTEgTDE0LjI3OTU2MzMsNDQzLjk0NzU0MiBDMTQuMTY0MjAzNiw0NDQuMDE3MDQ3IDE0LjAyNDIyNzMsNDQ0LjA1NjE0NCAxMy44Nzk0MjQzLDQ0NC4wNTYxNDQgQzEzLjcwODU1NjgsNDQ0LjA1NjE0NCAxMy41NDgzMDgxLDQ0NC4wMDQ0OTggMTMuNDIwODgxNSw0NDMuOTEwMzc2IEMxMy4wNzUyODUsNDQzLjY4NDk2NiAxMi42NjUwMDk4LDQ0My41NTEyNjQgMTIuMjIxOTEyNiw0NDMuNTUxMjY0IEMxMS4wMDcwMTU1LDQ0My41NTEyNjQgMTAuMDIyMzU1MSw0NDQuNTM2NDA3IDEwLjAyMjM1NTEsNDQ1Ljc1MTMwNSBDMTAuMDIyMzU1MSw0NDYuOTY2MjAyIDExLjAwNzAxNTUsNDQ3Ljk1MDg2MiAxMi4yMjE5MTI2LDQ0Ny45NTA4NjIgQzEyLjY2NTAwOTgsNDQ3Ljk1MDg2MiAxMy4wNzUyODUsNDQ3LjgxNzY0MyAxMy40MjA4ODE1LDQ0Ny41OTIyMzMgQzEzLjU0ODMwODEsNDQ3LjQ5NzYyOSAxMy43MDg1NTY4LDQ0Ny40NDY0NjUgMTMuODc5NDI0Myw0NDcuNDQ2NDY1IEMxNC4wMjQyMjczLDQ0Ny40NDY0NjUgMTQuMTY0MjAzNiw0NDcuNDg1MDc5IDE0LjI3OTU2MzMsNDQ3LjU1NDU4NSBMMjAuNTM2MDE4Myw0NTAuOTE4MzU4IEMyMC43Njg2Njg0LDQ1MS4wMzQyMDEgMjAuOTUwNjM3NSw0NTEuMjYzNDcyIDIwLjk3NTI1NDEsNDUxLjUzNTIxOSBDMjAuOTc1MjU0MSw0NTEuNTM1MjE5IDIwLjk3MTM5MjYsNDUxLjUzOTU2MyAyMC45NzUyNTQxLDQ1MS41MzUyMTkgQzIxLjA5MzAyNzIsNDUyLjYzOTEwMSAyMi4wMTYzODc2LDQ1My41MDI2MDkgMjMuMTUxNjQzMiw0NTMuNTAyNjA5IEMyNC4zNjY1NDAzLDQ1My41MDI2MDkgMjUuMzUxNjgzNCw0NTIuNTE3NDY2IDI1LjM1MTY4MzQsNDUxLjMwMjU2OSBDMjUuMzUxNjgzNCw0NTAuMDg3NjcyIDI0LjM2NjU0MDMsNDQ5LjEwMzAxMSAyMy4xNTE2NDMyLDQ0OS4xMDMwMTEiIGlkPSJGaWxsLTMiPjwvcGF0aD4KICAgICAgICA8L2c+CiAgICA8L2c+Cjwvc3ZnPg==';

        // Main sharethis menu.
        $this->hook_suffix = add_menu_page(
            __('Reviews by ShareThis', 'sharethis-reviews'),
            __('Reviews', 'sharethis-reviews'),
            'manage_options',
            $this->menu_slug . '-general',
            null,
            $icon,
            26
        );

        add_submenu_page(
            $this->menu_slug . '-general',
            __('Review Settings', 'sharethis-reviews'),
            __('Settings', 'sharethis-reviews'),
            'manage_options',
            $this->menu_slug . '-general',
            array($this, 'review_display')
        );

        $this->reviews_hook_suffix = add_submenu_page(
            $this->menu_slug . '-general',
            __('Reviews', 'sharethis-reviews'),
            __('Review Management', 'sharethis-reviews'),
            'manage_options',
            $this->menu_slug . '-reviews',
            array($this, 'review_management_display')
        );
    }

    /**
     * Enqueue main assets.
     *
     * @action wp_enqueue_scripts
     */
    public function enqueue_assets()
    {
        wp_enqueue_script("{$this->plugin->assets_prefix}-mu");
    }

    /**
     * Enqueue admin assets.
     *
     * @action admin_enqueue_scripts
     *
     * @param string $hook_suffix The current admin page.
     */
    public function enqueue_admin_assets($hook_suffix)
    {
        // Enqueue the assets globally throughout the ShareThis Reviews menus.
        wp_enqueue_script("{$this->plugin->assets_prefix}-admin");
        wp_add_inline_script("{$this->plugin->assets_prefix}-admin", sprintf('Review.boot( %s );',
            wp_json_encode(array(
                'nonce'       => wp_create_nonce($this->plugin->meta_prefix),
                'propertySet' => $this->is_property_id_set(),
            ))
        ));
        wp_enqueue_style("{$this->plugin->assets_prefix}-admin");
    }

    /**
     * Call back for displaying reviews settings page.
     */
    public function review_display()
    {
        // Check user capabilities.
        if ( ! current_user_can('manage_options')) {
            return;
        }

        include_once "{$this->plugin->dir_path}/templates/menu/menu-template.php";
    }

    /**
     * Call back for displaying review management settings page.
     */
    public function review_management_display()
    {
        // Check user capabilities.
        if ( ! current_user_can('manage_options')) {
            return;
        }

        include_once "{$this->plugin->dir_path}/templates/menu/review-management-template.php";
    }

    /**
     * Define share button setting sections and fields.
     *
     * @action admin_init
     */
    public function settings_api_init()
    {
        // Register sections.
        foreach ($this->setting_sections as $detail) {
            // Add setting section.
            add_settings_section(
                $detail['section'],
                $detail['title'],
                null,
                $this->menu_slug . '-general'
            );
        }

        // Register setting fields.
        foreach ($this->setting_fields as $section => $settings) {
            foreach ($settings as $setting_field) {
                register_setting($this->menu_slug . '-general',
                    $this->menu_slug . '_' . $setting_field['section'] . '_' . $setting_field['id_suffix']);
                add_settings_field(
                    $setting_field['section'] . '_' . $setting_field['id_suffix'],
                    $setting_field['description'],
                    array($this, $setting_field['callback']),
                    $this->menu_slug . '-general',
                    $setting_field['section'],
                    $setting_field['arg']
                );
            }
        }
    }

    /**
     * Callback function for check box fields.
     *
     * @param string $args The arguments provided to call back.
     */
    public function check_field($args)
    {
        $section     = ! empty($args) && isset($args[0]) ? $args[0] : '';
        $type        = str_replace('_section', '', $section);
        $name        = ! empty($args) && isset($args[1]) ? $args[1] : '';
        $description = $this->get_descriptions($type, $name);

        include "{$this->plugin->dir_path}/templates/menu/check-field.php";
    }

    /**
     * Callback function for color picker fields.
     *
     * @param string $args The arguments provided to call back.
     */
    public function color_picker($args)
    {
        $section     = ! empty($args) && isset($args[0]) ? $args[0] : '';
        $type        = str_replace('_section', '', $section);
        $name        = ! empty($args) && isset($args[1]) ? $args[1] : '';
        $description = $this->get_descriptions($type, $name);

        include "{$this->plugin->dir_path}/templates/menu/color-picker.php";
    }

    /**
     * Callback function for multi check box fields.
     *
     * @param string $args The arguments provided to call back.
     */
    public function multi_check($args)
    {
        $section     = ! empty($args) && isset($args[0]) ? $args[0] : '';
        $checks      = ! empty($args) && isset($args[1]) ? $args[1] : '';
        $type        = str_replace('_section', '', $section);
        $description = $this->get_descriptions($type, $args[2]);

        include "{$this->plugin->dir_path}/templates/menu/multi-check.php";
    }

    /**
     * Callback function for radio fields.
     *
     * @param string $args The arguments provided to call back.
     */
    public function radio_field($args)
    {
        $section    = ! empty($args) && isset($args[0]) ? $args[0] : '';
        $radios     = ! empty($args) && isset($args[1]) ? $args[1] : '';
        $radio_name = ! empty($args) && isset($args[2]) ? $args[2] : '';
        $type       = str_replace('_section', '', $section);

        include "{$this->plugin->dir_path}/templates/menu/radio-field.php";
    }

    /**
     * Callback function for check box fields.
     *
     * @param string $args The arguments provided to call back.
     */
    public function text_field($args)
    {
        $section     = ! empty($args) && isset($args[0]) ? $args[0] : '';
        $name        = ! empty($args) && isset($args[1]) ? $args[1] : '';
        $type        = str_replace('_section', '', $section);
        $description = $this->get_descriptions($type, $name);

        include "{$this->plugin->dir_path}/templates/menu/text-field.php";
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
    private function get_descriptions($type = '', $subtype = '')
    {
        switch ($type) {
            case 'review':
                switch ($subtype) {
                    case 'title':
                        $description = esc_html__('Title for your reviews section:',
                                'sharethis-reviews') . '<span class="st-tooltip-icon"></span><div class="st-tooltip">' . $this->get_tooltip($type,
                                $subtype) . '</div>';
                        break;
                    case 'approval':
                        $description = esc_html__('Require administration approval before user review is published?',
                            'sharethis-reviews');
                        break;
                    case 'account':
                        $description = esc_html__('Require user account to leave reviews. No one without an account can leave reviews',
                            'sharethis-reviews');
                        break;
                    case 'posttype':
                        $description = esc_html__('Where can your users leave reviews on:',
                                'sharethis-reviews') . '<span class="st-tooltip-icon"></span><div class="st-tooltip">' . $this->get_tooltip($type,
                                $subtype) . '</div>';
                        break;
                    case 'ctacopy':
                        $description = esc_html__('The text in the button to leave a review:',
                                'sharethis-reviews') . '<span class="st-tooltip-icon"></span><div class="st-tooltip">' . $this->get_tooltip($type,
                                $subtype) . '</div>';
                        break;
                    case 'ctacolor':
                        $description = esc_html__('The color of the button to leave a review:',
                                'sharethis-reviews') . '<span class="st-tooltip-icon"></span><div class="st-tooltip">' . $this->get_tooltip($type,
                                $subtype) . '</div>';
                        break;
                }
                break;
            case 'rating':
                switch ($subtype) {
                    case 'title':
                        $description = esc_html__('Title for your rating section:',
                                'sharethis-reviews') . '<span class="st-tooltip-icon"></span><div class="st-tooltip">' . $this->get_tooltip($type,
                                $subtype) . '</div>';
                        break;
                    case 'symbols':
                        $description = '';
                        break;
                    case 'total':
                        $description = esc_html__('Display the total rating sum above the rating section',
                            'sharethis-reviews');
                        break;
                    case 'top':
                        $description = esc_html__('Show highest ratings at the top.', 'sharethis-reviews');
                        break;
                    case 'posttype':
                        $description = esc_html__('Where can your users leave ratings on:',
                                'sharethis-reviews') . '<span class="st-tooltip-icon"></span><div class="st-tooltip">' . $this->get_tooltip($type,
                                $subtype) . '</div>';
                        break;
                }
                break;
            case 'impression':
                switch ($subtype) {
                    case 'symbols':
                        $description = '';
                        break;
                    case 'negative':
                        $description = esc_html__('Don’t allow users to leave negative impressions',
                            'sharethis-reviews');
                        break;
                    case 'posttype':
                        $description = esc_html__('Where can your users leave impressions on:',
                                'sharethis-reviews') . '<span class="st-tooltip-icon"></span><div class="st-tooltip">' . $this->get_tooltip($type,
                                $subtype) . '</div>';
                        break;
                }
                break;
        } // End switch().

        return wp_kses_post($description);
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
    private function get_tooltip($type = '', $subtype = '')
    {
        switch ($type) {
            case 'review':
                switch ($subtype) {
                    case 'title':
                        $description = esc_html__('Fill out this field to add a custom title to your reviews section.  You can leave this blank if you choose.',
                            'sharethis-reviews');
                        break;
                    case 'posttype':
                        $description = esc_html__('Any post type you check will display your reviews section.  If reviews and ratings share a selected post type they will combine into a review with a rating.',
                            'sharethis-reviews');
                        break;
                    case 'ctacopy':
                        $description = esc_html__('Defaults to "Add Review / Rating" if you have both active for the posttype.',
                            'sharethis-reviews');
                        break;
                    case 'ctacolor':
                        $description = esc_html__('Defaults to your site\'s button styling', 'sharethis-reviews');
                        break;
                }
                break;
            case 'rating':
                switch ($subtype) {
                    case 'title':
                        $description = esc_html__('Fill out this field to add a custom title to your ratings section.  This will appear on post types you have selected with ratings alone.',
                            'sharethis-reviews');
                        break;
                    case 'posttype':
                        $description = esc_html__('Any post type you check will display your ratings section.  If ratings and reviews share a selected post type they will combine into a review with a rating.',
                            'sharethis-reviews');
                        break;
                }
                break;
            case 'impression':
                switch ($subtype) {
                    case 'posttype':
                        $description = esc_html__('Any post type you check will display your impression section.  If impressions shares a post type selection with the other two types it will show ABOVE the ratings/reviews section.',
                            'sharethis-reviews');
                        break;
                }
                break;
        } // End switch().

        return wp_kses_post($description);
    }

    /**
     * Set the property id and secret key for the user's platform account if query params are present.
     *
     * @action wp_ajax_set_credentials
     */
    public function set_credentials()
    {
        check_ajax_referer($this->plugin->meta_prefix, 'nonce');

        if ( ! isset($_POST['data']) || '' === $_POST['data']) { // WPCS: input var ok.
            wp_send_json_error('Set credentials failed.');
        }

        $data = sanitize_text_field(wp_unslash($_POST['data'])); // WPCS: input var ok.

        // If both variables exist add them to a database option.
        if (false === get_option('sharethis_property_id')) {
            update_option('sharethis_property_id', $data);
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
    private function is_property_id_set($type = '')
    {
        $property_id = get_option('sharethis_property_id');

        // If the property id is set then show the general settings template.
        if (false !== $property_id && null !== $property_id) {
            if ('empty' === $type && '' === $property_id) {
                return false;
            }

            return true;
        }

        return false;
    }

    /**
     * Runs only when the plugin is activated.
     */
    public function st_activation_hook()
    {
        $defaults = array(
            'sharethisreviews_review_section_posttype'    => array(
                'post' => 'on',
                'page' => 'on',
            ),
            'sharethisreviews_rating_section_posttype'    => array(
                'post' => 'on',
                'page' => 'on',
            ),
            'sharethisreviews_rating_section_symbols'     => 'stars',
            'sharethisreviews_impression_section_symbols' => 'thumb',
        );

        foreach ($defaults as $name => $value) {
            update_option($name, $value);
        }
    }

    /**
     * Helper function to get reviews.
     */
    public function get_reviews()
    {
        $reviews    = [];
        $review_ids = get_option('sharethisreview_posts', true);
        $review_ids = ! empty($review_ids) && is_array($review_ids) ? array_unique($review_ids) : [];

        if ([] !== $review_ids) {
            foreach ($review_ids as $review_id) {
                $post_reviews = get_post_meta((int)$review_id, 'sharethisreview_review', true);

                if (false === is_array($post_reviews)) {
                    continue;
                }

                foreach ($post_reviews as $num => $review) {
                    $review['position'] = $num;
                    $reviews[]          = $review;
                }
            }
        }

        // Sort reviews by date.
        usort($reviews, [$this, 'sort_by_date']);

        $reviews = isset($_GET['s']) && 'a' === $_GET['s'] ? array_reverse($reviews) : $reviews; // WPCS: CSRF ok.

        return $reviews;
    }

    /**
     * Helper function to decide what svg to use for symbols.
     *
     * @param string $type The type of symbol needed.
     * @param string $subtype The type of image needed.
     */
    private function get_symbol_svg($type, $subtype)
    {
        $symbols = array();

        if ('rating_section' === $type) {
            switch ($subtype) {
                case 'stars':
                    $symbols = array(
                        'star-icon.svg.php',
                        'star-icon.svg.php',
                        'star-icon.svg.php',
                        'star-icon.svg.php',
                        'star-icon.svg.php',
                    );
                    break;
                case 'emoji':
                    $symbols = array(
                        'crying-icon.svg.php',
                        'sad-icon.svg.php',
                        'neutral-icon.svg.php',
                        'happy-icon.svg.php',
                        'inlove-icon.svg.php',
                    );
                    break;
                case 'hearts':
                    $symbols = array(
                        'heart-icon.svg.php',
                        'heart-icon.svg.php',
                        'heart-icon.svg.php',
                        'heart-icon.svg.php',
                        'heart-icon.svg.php',
                    );
                    break;
                case 'numbers':
                    $symbols = array(
                        '1-icon.svg.php',
                        '2-icon.svg.php',
                        '3-icon.svg.php',
                        '4-icon.svg.php',
                        '5-icon.svg.php',
                    );
                    break;
            }
        } else {
            switch ($subtype) {
                case 'thumb':
                    $symbols = array(
                        'thumbs-up-icon.svg.php',
                        'thumbs-down-icon.svg.php',
                    );
                    break;
                case 'heart':
                    $symbols = array(
                        'heart-icon.svg.php',
                        'strike-heart-icon.svg.php',
                    );
                    break;
                case 'emoji':
                    $symbols = array(
                        'happy-icon.svg.php',
                        'sad-icon.svg.php',
                    );
                    break;
            }
        }

        return $symbols;
    }

    /**
     * Helper function to return ratings in icons.
     *
     * @param string $number How many symbols to return.
     */
    public function get_rating_icons($number, $all = false, $radio = false)
    {
        $number     = intval($number);
        $symbol     = get_option('sharethisreviews_rating_section_symbols');
        $symbol     = isset($symbol) ? $symbol : '';
        $symbol_svg = $this->get_symbol_svg('rating_section', $symbol);
        $start      = ('emoji' !== $symbol && 'numbers' !== $symbol) || $all ? 0 : $number;
        $new_class  = false === $all ? 'rating-average-wrap' : 'rating-wrap';

        $html = '<div class="' . $new_class . '">';

        for ($x = $start; $x < $number; $x++) {
            $rating = min(max($x, 0), 4); // Ensure we stay between our limits.

            ob_start();
            include $this->plugin->dir_path . 'assets/' . $symbol_svg[$rating];
            $symbol_icon = ob_get_clean();
            $checked     = 4 === $rating ? 'checked="checked"' : '';

            $html .= '<div class="rating-icon">';

            if ($radio) {
                $html .= '<input type="radio" id="sharethis-rating-' . $rating . '" name="st-review-rating" value="' . $rating . '" ' . $checked . '>';
                $html .= '<label for="sharethis-rating-' . $rating . '">';
                $html .= '<div class="symbol-icon-wrap">';
                $html .= $symbol_icon;
                $html .= '</div>';
                $html .= '</label>';
                $html .= '</div>';
            } else {
                $html .= $symbol_icon;
                $html .= '</div>';
            }
        }

        $html .= '</div>';

        return $html;
    }

    /**
     * Helper function to return ratings in icons.
     */
    public function get_impression_icons($count)
    {
        $symbol     = get_option('sharethisreviews_impression_section_symbols');
        $symbol     = isset($symbol) ? $symbol : '';
        $negative   = get_option('sharethisreviews_impression_section_negative');
        $negative   = isset($negative) ? ('on' === $negative) : false;
        $symbol_svg = $this->get_symbol_svg('impression_section', $symbol);
        $start      = ('emoji' !== $symbol && 'numbers' !== $symbol) || $all ? 0 : $number;
        $number     = $negative ? 0 : 1;

        $html = '<div class="impression-wrap">';

        for ($x = 0; $x <= $number; $x++) {
            ob_start();
            include $this->plugin->dir_path . 'assets/' . $symbol_svg[$x];
            $symbol_icon = ob_get_clean();

            $html .= '<div class="impression-icon">';

            // Impression section html.
            $html .= '<div data-imp="' . $x . '" class="st-impression">' . $symbol_icon . '</div>';
            $html .= '<div class="overall-impression">';
            $html .= esc_html($count[$x]);
            $html .= '</div>';
            $html .= '</div>';
        }

        $html .= '</div>';

        return $html;
    }

    /**
     * Add metabox if post has rating data.
     *
     * @action admin_init
     */
    public function add_rating_metabox()
    {
        $posttypes = get_option('sharethisreviews_rating_section_posttype', true);
        $posttypes = isset($posttypes) && is_array($posttypes) ? $posttypes : array();

        foreach ($posttypes as $posttype => $status) {

            if ('on' === $status) {
                add_meta_box(
                    'sharethis-rating-metabox',
                    __('ShareThis Reviews'),
                    [$this, 'rating_metabox_display'],
                    $posttype,
                    'normal',
                    'default'
                );
            }
        }
    }

    /**
     * Callback for rating metabox display.
     */
    public function rating_metabox_display()
    {
        global $post;

        $current_ratings = get_post_meta($post->ID, 'sharethisreview_rating', true);
        $hide            = get_post_meta($post->ID, 'sharethis-hide-review-section', true);

        include "{$this->plugin->dir_path}/templates/rating-metabox.php";
    }

    /**
     * Save metabox data.
     *
     * @action save_post
     *
     * @param $post_id
     */
    public function save_postdata($post_id)
    {
        if (array_key_exists('sharethis-hide-review-section', $_POST)) { // WPCS: CSRF ok.
            update_post_meta(
                $post_id,
                'sharethis-hide-review-section',
                $_POST['sharethis-hide-review-section'] // WPCS: CSRF ok.
            );
        } else {
            update_post_meta($post_id, 'sharethis-hide-review-section', 'off');
        }
    }

    /**
     * Helper function to sort by date.
     *
     * @param array $ratings The ratings to sort.
     */
    public function sort_by_date($a, $b)
    {
        return strcmp($b['date'], $a['date']);
    }

    /**
     * Enqueue the custom gutenberg block script.
     *
     * @action enqueue_block_editor_assets
     */
    public function enqueue_custom_blocks()
    {
        wp_enqueue_script("{$this->plugin->assets_prefix}-blocks", "{$this->plugin->dir_url}js/blocks.js",
            array('wp-blocks', 'wp-editor', 'wp-element', 'wp-components'), time(), true);
    }

    /**
     * Register new block category for share buttons.
     *
     * @param array $categories The current block categories.
     *
     * @filter block_categories 999
     */
    public function st_block_category($categories, $post)
    {
        if ( ! is_plugin_active('sharethis-share-buttons/sharethis-share-buttons.php')) {
            return array_merge(
                $categories,
                [
                    [
                        'slug'  => 'st-blocks',
                        'title' => __('ShareThis Blocks', 'sharethis-share-buttons'),
                    ],
                ]
            );
        }

        return $categories;
    }
}
