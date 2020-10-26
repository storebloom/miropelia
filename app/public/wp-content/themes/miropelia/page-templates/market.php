<?php
/**
 * Template Name: Market
 * Market page template.
 */
get_header();
?>
<main id="primary" class="site-main">
	<div class="container">
		<div id="market-wrap">
		    <?php the_content(); ?>
			<div class="market-sign">
				<img src="<?php echo get_template_directory_uri() . '/assets/src/images/market-sign.png'; ?>" />
			</div>
			<div class="market-menu">
                <?php echo wp_nav_menu(['menu' => 'market']); ?>
			</div>
		</div>
	</div>
</main>
<?php
get_footer();
