<?php
/**
 * Review Management template.
 */

$selected_asc_sort  = isset($_GET['s']) && 'a' === $_GET['s'] ? ' selected-sort' : ''; // WPCS: CSRF ok.
$selected_desc_sort = (isset($_GET['s']) && 'd' === $_GET['s']) || !isset($_GET['s']) ? ' selected-sort' : ''; // WPCS: CSRF ok.
?>

<div class="review-management-wrap">
    <h1><?php echo esc_html__('Review Management', 'sharethis-reviews'); ?></h1>

    <div class="sort-wrap">
        sort:
        <div class="sort-option<?php echo esc_attr($selected_desc_sort); ?>">
            <a href="admin.php?page=sharethisreviews-reviews&s=d">
                DESC
            </a>
        </div>
        |
        <div class="sort-option<?php echo esc_attr($selected_asc_sort); ?>">
            <a href="admin.php?page=sharethisreviews-reviews&s=a">
                ASC
            </a>
        </div>
    </div>

    <ul class="labels-for-items">
        <li><?php echo esc_html__('Name', 'sharethis-reviews'); ?></li>
        <li><?php echo esc_html__('Title', 'sharethis-reviews'); ?></li>
        <li><?php echo esc_html__('Review', 'sharethis-reviews'); ?></li>
        <li><?php echo esc_html__('Rating', 'sharethis-reviews'); ?></li>
        <li><?php echo esc_html__('Date', 'sharethis-reviews'); ?></li>
        <li><?php echo esc_html__('Post', 'sharethis-reviews'); ?></li>
    </ul>

    <div class="review-list">
        <?php
        if (is_array($this->current_reviews) && [] !== $this->current_reviews) :
            foreach ($this->current_reviews as $review) :
                $title = isset($review['title']) ? $review['title'] : '';
                $message = isset($review['message']) ? $review['message'] : '';
                $date = isset($review['date']) ? $review['date'] : '';
                $approved = isset($review['approved']) ? $review['approved'] : '';
                $name = isset($review['name']) ? $review['name'] : '';
                $id = isset($review['postid']) ? $review['postid'] : '';
                $num = $review['position'];
                $rating = isset($review['rating']) ? $this->get_rating_icons($review['rating']) : '';
                $post = get_post(intval($id));
                $count = 1 + $num;

                ?>
                <div class="review-item">
                    <div class="review-name review-info">
                        <?php echo esc_html($name); ?>
                    </div>
                    <div class="review-title review-info">
                        <?php echo esc_html($title); ?>
                    </div>
                    <div class="review-message review-info">
                        <?php echo esc_html($message); ?>
                    </div>
                    <div class="review-rating review-info" data-rating="<?php echo esc_attr($review['rating']); ?>">
                        <?php echo $rating; // WPCS: XSS ok.
                        ?>
                    </div>
                    <div class="review-date review-info">
                        <?php echo esc_html($date); ?>
                    </div>
                    <div class="review-post review-info">
                        <a href="<?php echo esc_url(get_post_permalink($post->ID)); ?>">
                            <?php echo esc_html__('view review', 'sharethis-reviews'); ?>
                        </a>
                    </div>

                    <?php if ($this->review_approval && 'false' === $approved) : ?>
                        <div class="review-approval review-info">
                            <button data-id="<?php echo esc_attr($id); ?>" data-pos="<?php echo esc_attr($num); ?>"
                                    class="approve-review">
                                <?php echo esc_html__('Approve', 'sharethis-reviews'); ?>
                            </button>
                        </div>
                    <?php elseif ('false' !== $approved) : ?>
                        <div class="review-retract review-info">
                            <button data-id="<?php echo esc_attr($id); ?>" data-pos="<?php echo esc_attr($num); ?>"
                                    class="retract-review">
                                <?php echo esc_html__('Retract', 'sharethis-reviews'); ?>
                            </button>
                        </div>
                    <?php endif; ?>

                    <button data-id="<?php echo esc_attr($id); ?>" data-pos="<?php echo esc_attr($num); ?>"
                            class="remove-review">
                        <?php echo esc_html__('X', 'sharethis-reviews'); ?>
                    </button>
                </div>
            <?php
            endforeach;
        endif;
        ?>
    </div>
</div>
