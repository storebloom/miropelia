<?php
/**
 * The main template file
 *
 * This is the most generic template file in a WordPress theme
 * and one of the two required files for a theme (the other being style.css).
 * It is used to display a page when nothing more specific matches a query.
 * E.g., it puts together the home page when no home.php file exists.
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/
 *
 * @package Miropelia
 */

get_header();
?>

	<main id="primary" class="site-main">
		<div class="news-header">
			<h1><?php esc_html_e('Orbem News', 'miropelia'); ?></h1>
		</div>
		<div class="container">
			<div class="post-list">
				<?php
				if ( have_posts() ) :

					/* Start the Loop */
					while ( have_posts() ) :
						the_post();
					?>
						<div class="post-item">
							<a href="<?php the_permalink(); ?>">
		                        <h3>
			                        <?php the_title(); ?>
		                        </h3>
							</a>
							<div class="post-thumb">
								<?php the_post_thumbnail('large'); ?>
							</div>
							<div class="post-content">
		                        <?php the_excerpt(); ?>
							</div>
						</div>
					<?php
					endwhile;

				endif;
				?>
			</div>
		</div>
	</main><!-- #main -->
<?php
get_footer();
