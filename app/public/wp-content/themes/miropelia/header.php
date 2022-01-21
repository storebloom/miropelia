<?php
/**
 * The header for our theme
 *
 * This is the template that displays all of the <head> section and everything up until <div id="content">
 *
 * @link https://developer.wordpress.org/themes/basics/template-files/#template-partials
 *
 * @package Miropelia
 */

?>
<!doctype html>
<html <?php language_attributes(); ?>>
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<link rel="profile" href="https://gmpg.org/xfn/11">

	<?php wp_head(); ?>
</head>

<body <?php body_class(); ?>>
<?php wp_body_open(); ?>
<div id="page" class="site">
	<a style="display: none;" class="skip-link screen-reader-text" href="#primary"><?php esc_html_e( 'Skip to content', 'miropelia' ); ?></a>
	<header>
		<div class="logo">
			<span class="logo-icon">
				<a href="/">
                    <?php echo get_template_part('assets/src/images/icon', 'oologo.svg'); ?>
				</a>
			</span>

			<div class="login">
                <?php
                if (is_user_logged_in()) {
                    include get_template_directory() . '/templates/greeting.php';
                } else {
                    include get_template_directory() . '/templates/login.php';
                }
                ?>
			</div>
            <button class="menu-toggle">
                <span></span><span></span><span></span>
            </button>
		</div>
		<?php echo wp_nav_menu(['menu' => 'main']); ?>
	</header>
