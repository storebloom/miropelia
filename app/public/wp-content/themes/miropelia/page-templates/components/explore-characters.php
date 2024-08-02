<?php
/**
 * Characters panel for game.
 */
$characters = get_user_meta($userid, 'explore_characters', true);

if (true === empty($characters)) {
    return;
}
?>

<div class="characters-form">
    <span class="close-settings">X</span>
    <h2>Crew</h2>
    <div class="character-list">
        <?php foreach( $characters as $character ) :
            $character_post = get_posts(['name' => $character, 'post_type' => 'explore-character', 'post_status' => 'publish', 'posts_per_page' => 1]);
        ?>
            <div class="character-item" data-charactername="<?php echo esc_attr( $character_post[0]->post_name ); ?>">
                <?php echo get_the_post_thumbnail($character_post[0]->ID); ?>
            </div>
        <?php endforeach; ?>
    </div>
</div>