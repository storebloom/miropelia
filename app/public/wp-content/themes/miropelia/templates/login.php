<button id="login" type="button"><?php esc_html_e('Login', 'miropelia'); ?></button>
<div class="login-modal">
	<div class="close-login">
		X
	</div>
	<div class="form-wrapper">
		<h2>
            <?php esc_html_e('Welcome Back!', 'miropelia'); ?>
		</h2>
		<?php echo wp_login_form(); ?>
		<small>
			<a href="/register"><?php esc_html_e('No account? Join now!', 'miropelia'); ?></a>
		</small>
	</div>
</div>
