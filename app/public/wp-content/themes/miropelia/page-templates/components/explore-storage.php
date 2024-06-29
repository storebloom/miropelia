<?php
/**
 * Settings panel for game.
 */
$storage = get_user_meta($userid, 'explore_storage', true);
$storage = false === empty($storage) ? $storage : ['items' => [], 'weapons' => [], 'gear' => []];
$storage_limit = get_user_meta($userid, 'storage_limit', true);
$storage_limit = false === empty($storage_limit ) ? $storage_limit : 11;
$current_explore_gear = get_user_meta($userid, 'explore_current_gear', true) ?? [];
?>
<div class="storage-form">
    <span class="close-settings">X</span>
    <h2>Retrieval Points</h2>
    <div class="retrieval-points">
        <div class="menu-tabs">
            <div class="items-tab engage">Items</div>
            <div class="weapons-tab">Weapons</div>
            <div class="gear-tab">Gear</div>
        </div>
        <?php foreach($storage as $storage_type => $storage_items):?>
            <div data-menu="<?php echo esc_attr($storage_type); ?>" class="storage-menu <?php echo 'items' === $storage_type ? 'engage' : ''; ?>">
                <?php for ( $x = 0; $x <= intval($storage_limit); $x++ ) :
                    $item_id = false === empty($storage_items[$x]["id"]) ? esc_attr($storage_items[$x]["id"]) : '';
                    $current_gear = false;
                    ?>
                    <span
                            data-empty="<?php echo false === empty( $storage_items[$x]["type"] ) ? 'false' : 'true'; ?>"
                            data-type="<?php echo false === empty( $storage_items[$x]["type"] ) ? esc_attr($storage_items[$x]["type"]) : ''; ?>"
                            <?php if (false === empty( $storage_items[$x]["subtype"]) && false === empty($current_explore_gear[$storage_items[$x]["subtype"]])):
                                if (true === is_array($current_explore_gear[$storage_items[$x]["subtype"]])) {
                                    foreach ($current_explore_gear[$storage_items[$x]["subtype"]] as $current_array) {
                                        if (true === in_array(intval($item_id), array_keys($current_array), true)) {
                                            $current_gear = true;
                                        }
                                    }
                                }
                                ?>
                                data-subtype="<?php echo esc_attr($storage_items[$x]["subtype"]); ?>"
                            <?php endif; ?>
                            data-id="<?php echo esc_attr($item_id); ?>"
                            data-value="<?php echo false === empty($storage_items[$x]["value"]) ? esc_attr($storage_items[$x]["value"]) : ''; ?>"
                            title="<?php echo false === empty($storage_items[$x]["name"]) ? esc_attr($storage_items[$x]["name"]) : ''; ?>"
                            <?php echo false === empty($storage_items[$x]["count"]) ? 'data-count="' . intval($storage_items[$x]["count"]) . '"' : ''; ?>
                            class="storage-item<?php echo $current_gear ? ' equipped' : ''; ?>">
                        <?php if (false === empty($storage_items[$x]["type"]) && 'gear' === $storage_items[$x]["type"]) : ?>
                            <img src="<?php echo esc_url(get_the_post_thumbnail_url($item_id)); ?>" width="30px" height="30px" />
                        <?php endif; ?>
                    </span>
                <?php endfor; ?>
            </div>
        <?php endforeach; ?>
        <div id="item-description">
        </div>
    </div>
</div>