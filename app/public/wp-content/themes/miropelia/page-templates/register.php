<?php
/**
 * Template Name: Register
 * Register form template.
 */

if (is_user_logged_in()) {
    wp_safe_redirect('/');
}
get_header();
?>
<main id="primary" class="site-main">
	<div class="container">
        <?php the_content(); ?>
	</div>
</main>
<?php
get_footer();
