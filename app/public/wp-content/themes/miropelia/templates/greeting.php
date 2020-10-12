<?php
/**
 * Greeting template for logged in users.
 */

$user = wp_get_current_user();
?>
<div class="logged-in-greeting">
	<a href="/?logoutuser">
	    <div class="display-name">
	        <?php echo esc_html($user->display_name); ?>
	    </div>
		<div class="logout-user">
			logout
		</div>
	</a>
</div>