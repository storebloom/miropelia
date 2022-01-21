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
            <?php if (!is_front_page()) : ?>
                <h1><?php the_title(); ?></h1>
            <?php endif;
	        if ( have_posts() ) :
	            /* Start the Loop */
	            while ( have_posts() ) :
	                the_post();
	                the_content();
	            endwhile;
	        endif;
	        ?>
	    </div>
    </main><!-- #main -->
<?php
get_footer();
