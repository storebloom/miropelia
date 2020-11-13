<?php
/**
 * Configure tool template for gdpr onboarding
 */
?>
<div class="gdpr-platform platform-config-wrapper">
	<hr>

	<h4 style="text-align: left; font-size: 15px;"><?php echo esc_html__( 'Configure', 'sharethis-share-buttons' ); ?></h4>
	<div class="st-design-message"><?php echo esc_html__( 'Use the settings below to configure your GDPR compliance tool popup.', 'sharethis-share-buttons' ); ?></div>

	<div id="starter-questions">
		<label>
			<?php echo esc_html__('PUBLISHER NAME * (this will be displayed in the consent tool)',
				'sharethis-share-buttons'); ?>
		</label>

		<input type="text" id="sharethis-publisher-name" placeholder="Enter your company name">

		<label>
			<?php echo esc_html__('WHICH USERS SHOULD BE ASKED FOR CONSENT?',
				'sharethis-share-buttons'); ?>
		</label>

		<select id="sharethis-user-type">
			<?php foreach ($user_types as $user_value => $name) : ?>
				<option value="<?php echo esc_attr($user_value); ?>">
					<?php echo esc_html($name); ?>
				</option>
			<?php endforeach; ?>
		</select>

		<label>
			<?php echo esc_html__('CONSENT SCOPE', 'sharethis-share-buttons'); ?>
		</label>

		<select id="sharethis-consent-type">
			<?php foreach ($consent_types as $consent_value => $c_name) : ?>
				<option
					value="<?php echo esc_attr($consent_value); ?>">
					<?php echo esc_html($c_name); ?>
				</option>
			<?php endforeach; ?>
		</select>

		<label>
			<?php echo esc_html__('SELECT LANGUAGE', 'sharethis-share-buttons'); ?>
		</label>

		<select id="st-language">
			<?php foreach ($languages as $language => $code) : ?>
				<option value="<?php echo esc_attr($code); ?>">
					<?php echo esc_html($language); ?>
				</option>
			<?php endforeach; ?>
		</select>

		<p class="form-color">
			<label>
				<?php echo esc_html__(
					'CHOOSE FORM COLOR',
					'gdpr-complianc-tool'
				); ?>
			</label>
		<div id="sharethis-form-color">
			<?php foreach ($colors as $color) : ?>
				<div class="color"
				     data-value="<?php echo esc_attr($color); ?>"
				     style="max-width: 30px; max-height: 30px; overflow: hidden;">
					<span style="content: ' '; background-color:<?php echo esc_html($color); ?>; padding: 40px;"></span>
				</div>
			<?php endforeach; ?>
		</div>
		</p>
	</div>
	<div id="purposes">
		<label>
			<?php echo esc_html__('WHY ARE YOU COLLECTING CUSTOMER DATA?', 'sharethis-share-buttons'); ?>
		</label>

		<div id="publisher-purpose" class="switch">
			<div class="empty-choices">
				<a id="see-st-choices" class="st-rc-link medium-btn" href="#">See ShareThis Choices</a>
				<a id="clear-choices" class="st-rc-link medium-btn" href="#">Clear Choices</a>
			</div>
			<div class="purpose-item">
				<div class="title">
					<?php echo esc_html__(
						'1) Store and/or access information on a device (Do you collect information on users on your site through cookies or site identifiers?)',
						'gdpr-complianc-tool'
					); ?>
				</div>
				<label>
					<?php echo esc_html__('Consent', 'sharethis-share-buttons'); ?>
					<input data-id="1" type="checkbox" name="purposes[1]" value="consent" checked/>
					<span class="lever"></span>
				</label>
			</div>
			<div class="purpose-item">
				<div class="title">
					<?php echo esc_html__(
						'2) Select basic ads (Do you serve ads on your site?)',
						'gdpr-complianc-tool'
					); ?>
				</div>
				<label>
					<?php echo esc_html__('Consent', 'sharethis-share-buttons'); ?>
					<input data-id="2" type="radio" name="purposes[2]" value="consent"/>
					<span class="lever"></span>
				</label>
				<label>
					<?php echo esc_html__('Legitimate Interest', 'sharethis-share-buttons'); ?>
					<input data-id="2" type="radio" name="purposes[2]" value="legitimate"/>
					<span class="lever"></span>
				</label>
			</div>
			<div class="purpose-item">
				<div class="title">
					<?php echo esc_html__(
						'3) Create a personalised ads profile (Do you create personalised advertising profiles associated with users on your site (ie: profiles based on demographic information, location, user’s activity)?)',
						'gdpr-complianc-tool'
					); ?>
				</div>
				<label>
					<?php echo esc_html__('Consent', 'sharethis-share-buttons'); ?>
					<input data-id="3" type="radio" name="purposes[3]" value="consent" checked/>
					<span class="lever"></span>
				</label>
				<label>
					<?php echo esc_html__('Legitimate Interest', 'sharethis-share-buttons'); ?>
					<input data-id="3" type="radio" name="purposes[3]" value="legitimate"/>
					<span class="lever"></span>
				</label>
			</div>
			<div class="purpose-item">
				<div class="title">
					<?php echo esc_html__(
						'4) Select personalised ads (Do you show ads to users based on this user profile)',
						'gdpr-complianc-tool'
					); ?>
				</div>
				<label>
					<?php echo esc_html__('Consent', 'sharethis-share-buttons'); ?>
					<input data-id="4" type="radio" name="purposes[4]" value="consent"/>
					<span class="lever"></span>
				</label>
				<label>
					<?php echo esc_html__('Legitimate Interest', 'sharethis-share-buttons'); ?>
					<input data-id="4" type="radio" name="purposes[4]" value="legitimate"/>
					<span class="lever"></span>
				</label>
			</div>
			<div class="purpose-item">
				<div class="title">
					<?php echo esc_html__(
						'5) Create a personalised content profile (Do you build a personalized content profile associated with users on your site  based on the type of content they have viewed?)',
						'gdpr-complianc-tool'
					); ?>
				</div>
				<label>
					<?php echo esc_html__('Consent', 'sharethis-share-buttons'); ?>
					<input data-id="5" type="radio" name="purposes[5]" value="consent" checked />
					<span class="lever"></span>
				</label>
				<label>
					<?php echo esc_html__('Legitimate Interest', 'sharethis-share-buttons'); ?>
					<input data-id="5" type="radio" name="purposes[5]" value="legitimate"/>
					<span class="lever"></span>
				</label>
			</div>
			<div class="purpose-item">
				<div class="title">
					<?php echo esc_html__(
						'6) Select personalised content (Do you serve content to the user on your site based on your recorded content interests)',
						'gdpr-complianc-tool'
					); ?>
				</div>
				<label>
					<?php echo esc_html__('Consent', 'sharethis-share-buttons'); ?>
					<input data-id="6" type="radio" name="purposes[6]" value="consent" checked />
					<span class="lever"></span>
				</label>
				<label>
					<?php echo esc_html__('Legitimate Interest', 'sharethis-share-buttons'); ?>
					<input data-id="6" type="radio" name="purposes[6]" value="legitimate"/>
					<span class="lever"></span>
				</label>
			</div>
			<div class="purpose-item">
				<div class="title">
					<?php echo esc_html__(
						'7) Measure ad performance (Do you measure the performance of advertisements on your site)',
						'gdpr-complianc-tool'
					); ?>
				</div>
				<label>
					<?php echo esc_html__('Consent', 'sharethis-share-buttons'); ?>
					<input data-id="7" type="radio" name="purposes[7]" value="consent"/>
					<span class="lever"></span>
				</label>
				<label>
					<?php echo esc_html__('Legitimate Interest', 'sharethis-share-buttons'); ?>
					<input data-id="7" type="radio" name="purposes[7]" value="legitimate"/>
					<span class="lever"></span>
				</label>
			</div>
			<div class="purpose-item">
				<div class="title">
					<?php echo esc_html__(
						'8) Measure content performance  (Do you measure the performance of content served to your site visitors?)',
						'gdpr-complianc-tool'
					); ?>
				</div>
				<label>
					<?php echo esc_html__('Consent', 'sharethis-share-buttons'); ?>
					<input data-id="8" type="radio" name="purposes[8]" value="consent"/>
					<span class="lever"></span>
				</label>
				<label>
					<?php echo esc_html__('Legitimate Interest', 'sharethis-share-buttons'); ?>
					<input data-id="8" type="radio" name="purposes[8]" value="legitimate"/>
					<span class="lever"></span>
				</label>
			</div>
			<div class="purpose-item">
				<div class="title">
					<?php echo esc_html__(
						'9) Apply market research to generate audience insights (Do you aggregate reporting on the ads or content show to your site visitors to advertisers)',
						'gdpr-complianc-tool'
					); ?>
				</div>
				<label>
					<?php echo esc_html__('Consent', 'sharethis-share-buttons'); ?>
					<input data-id="9" type="radio" name="purposes[9]" value="consent"/>
					<span class="lever"></span>
				</label>
				<label>
					<?php echo esc_html__('Legitimate Interest', 'sharethis-share-buttons'); ?>
					<input data-id="9" type="radio" name="purposes[9]" value="legitimate" checked />
					<span class="lever"></span>
				</label>
			</div>
			<div class="purpose-item">
				<div class="title">
					<?php echo esc_html__(
						'10) Develop and improve products (Do you use data collected on your site visitors to improve your systems or software or create new products?)',
						'gdpr-complianc-tool'
					); ?>
				</div>
				<label>
					<?php echo esc_html__('Consent', 'sharethis-share-buttons'); ?>
					<input data-id="10" type="radio" name="purposes[10]" value="consent"/>
					<span class="lever"></span>
				</label>
				<label>
					<?php echo esc_html__('Legitimate Interest', 'sharethis-share-buttons'); ?>
					<input data-id="10" type="radio" name="purposes[10]" value="legitimate" checked/>
					<span class="lever"></span>
				</label>
			</div>
		</div>
	</div>
</div>

