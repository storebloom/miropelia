<?php
/**
 * The template for displaying the footer
 *
 * Contains the closing of the #content div and all content after.
 *
 * @link https://developer.wordpress.org/themes/basics/template-files/#template-partials
 *
 * @package Miropelia
 */

?>

	<footer id="colophon" class="site-footer">
		<div class="footer-detail">
			© <?php echo date("Y"); ?> OrbemOrder.com
		</div>
        <?php echo wp_nav_menu(['menu' => 'footer']); ?>
	</footer><!-- #colophon -->
</div><!-- #page -->

<?php wp_footer(); ?>

</body>
</html>
