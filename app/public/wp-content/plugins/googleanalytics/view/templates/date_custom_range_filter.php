<?php
/**
 * Custom Date Range Filter template.
 */
$args = isset($args) ? $args : [];

$props = wp_parse_args($args, [
    'date_from' => date('Y-m-d', strtotime('-1 week')),
    'date_to'   => date('Y-m-d'),
]);

$date_from = false === empty($props['date_from']) ? $props['date_from'] : date('Y-m-d', strtotime('-1 week'));
$date_to   = false === empty($props['date_to']) ? $props['date_to'] : date('Y-m-d');
?>
<form action="<?php echo esc_url(admin_url('admin.php')); ?>" method="GET">
    <input type="hidden" name="page" value="googleanalytics"/>
    <label>
        <?php esc_html_e('From:', 'googleanalytics'); ?>
        <input name="date_from" type="date" value="<?php echo esc_attr($date_from); ?>">
    </label>
    <label>
        <?php esc_html_e('To:', 'googleanalytics'); ?>
        <input name="date_to" type="date" value="<?php echo esc_attr($date_to); ?>">
    </label>
    <button><?php esc_html_e('Filter', 'googleanalytics'); ?></button>
</form>
