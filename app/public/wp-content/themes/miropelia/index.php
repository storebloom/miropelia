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
		<div class="container">
			<div class="row">
				<?php
				if ( have_posts() ) :

					/* Start the Loop */
					while ( have_posts() ) :
						the_post();
					?>
						<div class="col-md-3 col-sm-12">
							<div class="row">
		                        <?php the_title(); ?>
							</div>
							<div class="row">
		                        <?php the_content(); ?>
							</div>
						</div>
					<?php
					endwhile;

				endif;
				?>
			</div>
		</div>
		<div class="container">
			<div class="row">
				<?php
				$args = array(
					'post_type' => 'movie',
					'post_status' => 'publish',
					'posts_per_page' => 100,
				);

				$loop = new WP_Query( $args );

				while ( $loop->have_posts() ) : $loop->the_post();
				?>
					<div class="col-md-3 col-sm-12">
						<div class="row">
							<?php the_title(); ?>
						</div>
						<div class="row">
		                    <?php the_content(); ?>
						</div>
					</div>
				<?php
				endwhile;

				wp_reset_postdata();
				?>
			</div>
		</div>
	</main><!-- #main -->

<?php
get_sidebar();
get_footer();
