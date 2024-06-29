<?php foreach($explore_points as $explore_point):
$height = get_post_meta($explore_point->ID, 'explore-height', true) . 'px';
$width = get_post_meta($explore_point->ID, 'explore-width', true) . 'px';
$map_url = get_the_post_thumbnail_url($explore_point->ID);
$background_url = true === in_array($explore_point->post_type, ['explore-point', 'explore-character', 'explore-enemy'], true) ? "background: url(" . $map_url . ") no-repeat;" : '';
$point_type = 'explore-enemy' === $explore_point->post_type ? '.enemy-item' : '.map-item';
?>

.page-template-explore .container .default-map <?php echo esc_html($point_type); ?>.<?php echo esc_html($explore_point->post_name); ?>-map-item {
<?php echo esc_html($background_url); ?>
    background-size: cover;
    height: <?php echo esc_html($height); ?>;
    width: <?php echo esc_html($width); ?>;
}

<?php endforeach;