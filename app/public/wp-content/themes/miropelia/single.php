<?php
/**
 * Single post template
 */

get_header();
?>
    <main id="primary" class="site-main">
        <div class="container">

        <?php
        while ( have_posts() ) :
            the_post();

            get_template_part( 'template-parts/content', get_post_type() );

            ?>
            <div class="container sub-post-section">
                <?php
                the_post_navigation(
                    array(
                        'prev_text' => '<span class="nav-subtitle">' . esc_html__( 'Previous:', 'miropelia-custom' ) . '</span> <span class="nav-title">%title</span>',
                        'next_text' => '<span class="nav-subtitle">' . esc_html__( 'Next:', 'miropelia-custom' ) . '</span> <span class="nav-title">%title</span>',
                    )
                );
            endwhile; // End of the loop.
            ?>
            </div>
        </div>
    </main><!-- #main -->

<?php
get_footer();
