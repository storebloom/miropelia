<?php
/**
 * Platform button configurations
 *
 * The template wrapper for the platform button configurations.
 *
 * @package ShareThisFollowButtons
 */

use SharethisFollowButtons\Plugin;
?>
<div data-enabled="<?php echo esc_attr( $enabled ); ?>" class="inline-follow-platform platform-config-wrapper">
	<?php if ( 'Disabled' === $enabled ) : ?>
		<button class="enable-tool" data-button="inline-follow">Enable Tool</button>
	<?php endif; ?>

	<div class="sharethis-selected-networks">
		<div id="inline-follow-8" class="sharethis-inline-follow-buttons"></div>
	</div>

	<p class="st-preview-message">
		⇧ <?php echo esc_html__( 'Preview: click and drag to reorder' ); ?> ⇧
	</p>

	<div id="inline-follow" class="button-configuration-wrap selected-button">
		<div class="inline-follow-network-list follow-buttons">
			<h3><?php echo esc_html__( 'Social networks', 'sharethis-follow-buttons' ); ?></h3>

			<span class="config-desc">Click a network to add or remove it from your preview. We've already included the most popular networks.</span>

			<div class="inline-follow-network-list follow-buttons">
				<?php
				foreach ( $networks as $network_name => $network_info ) :
					$social_image = Plugin::getFormattedNetworkImage( $network_name );
					$network_name = Plugin::getPlatformName( $network_name );
					?>
					<div class="follow-button" data-color="<?php echo esc_attr( $network_info['color'] ); ?>"
						data-selected="<?php echo esc_attr( true === isset( $network_info['selected'] ) ? $network_info['selected'] : '' ); ?>"
						data-network="<?php echo esc_attr( str_replace( 'pocket', 'getpocket', $network_name ) ); ?>"
						title="<?php echo esc_attr( $network_name ); ?>"
						style="background-color: <?php echo esc_attr( $network_info['color'] ); ?>;">
						<img alt="<?php echo esc_attr( $network_name ); ?>" src="<?php echo esc_url( $social_image ); ?>" />
					</div>
				<?php endforeach; ?>
			</div>

			<hr>

			<span class="config-desc">Enter the profile URL for each channel where you want to add followers.</span>

			<div id="st-network-urls">
				<?php foreach ( $networks as $network_name => $network_info ) : ?>
					<div class="center-align" data-network="<?php echo esc_attr( $network_name ); ?>" data-selected="<?php echo esc_attr( $network_info['selected'] ); ?>">
						<div class="domain"><?php echo esc_html( $network_info['url'] ); ?></div>

						<?php if ( isset( $network_info['tooltip'] ) ) : ?>
							<span>
									<span class="tooltip-icon tooltipped" data-position="right" data-tooltip="<?php echo esc_attr( $network_info['tooltip'] ); ?>">
										<svg fill="#fff" preserveAspectRatio="xMidYMid meet" height="1em" width="1em" viewBox="0 0 40 40">
											<g>
												<path d="m23.2 28v5.4q0 0.4-0.3 0.6t-0.6 0.3h-5.3q-0.4 0-0.7-0.3t-0.2-0.6v-5.4q0-0.3 0.2-0.6t0.7-0.3h5.3q0.4 0 0.6 0.3t0.3 0.6z m7.1-13.4q0 1.2-0.4 2.3t-0.8 1.7-1.2 1.3-1.3 1-1.3 0.8q-0.9 0.5-1.6 1.4t-0.6 1.5q0 0.4-0.2 0.8t-0.7 0.3h-5.3q-0.4 0-0.6-0.4t-0.2-0.8v-1q0-1.9 1.4-3.5t3.2-2.5q1.3-0.6 1.9-1.2t0.5-1.7q0-0.9-1-1.7t-2.4-0.7q-1.4 0-2.4 0.7-0.8 0.5-2.4 2.5-0.3 0.4-0.7 0.4-0.2 0-0.5-0.2l-3.7-2.8q-0.3-0.2-0.3-0.5t0.1-0.6q3.5-6 10.3-6 1.8 0 3.6 0.7t3.3 1.9 2.4 2.8 0.9 3.5z"></path>
											</g>
										</svg>
									</span>
								</span>
						<?php endif; ?>

						<input type="text" class="profile_link" value="">
					</div>
				<?php endforeach; ?>

				<div class="tooltip-message-over"></div>
			</div>

			<hr>

			<div class="button-alignment">
				<h3><?php echo esc_html__( 'Alignment', 'sharethis-follow-buttons' ); ?></h3>

				<div class="alignment-button" data-alignment="left" data-selected="false">
					<div class="top">
						<div class="box"></div>
						<div class="box"></div>
						<div class="box"></div>
					</div>
					<div class="bottom"><?php echo esc_html__( 'Left', 'sharethis-follow-buttons' ); ?></div>
				</div>

				<div class="alignment-button" data-alignment="center" data-selected="true">
					<div class="top">
						<div class="box"></div>
						<div class="box"></div>
						<div class="box"></div>
					</div>
					<div class="bottom"><?php echo esc_html__( 'Center', 'sharethis-follow-buttons' ); ?></div>
				</div>

				<div class="alignment-button" data-alignment="right" data-selected="false">
					<div class="top">
						<div class="box"></div>
						<div class="box"></div>
						<div class="box"></div>
					</div><div class="bottom"><?php echo esc_html__( 'Right', 'sharethis-follow-buttons' ); ?></div>
				</div>
			</div>

			<hr>

			<div class="row">
				<div class="st-radio-config button-config-third button-size">
					<h3><?php echo esc_html__( 'Size', 'sharethis-follow-buttons' ); ?></h3>

					<div class="item">
						<input type="radio" class="with-gap" value="on" checked="checked">

						<label id="small"><?php echo esc_html__( 'Small', 'sharethis-follow-buttons' ); ?></label>
					</div>
					<div class="item">
						<input type="radio" class="with-gap" value="on">
						<label id="medium"><?php echo esc_html__( 'Medium', 'sharethis-follow-buttons' ); ?></label>
					</div>
					<div class="item">
						<input type="radio" class="with-gap" value="on">
						<label id="large"><?php echo esc_html__( 'Large', 'sharethis-follow-buttons' ); ?></label>
					</div>
				</div>
				<div class="button-config-third call-to-action">
					<h3><?php echo esc_html__( 'Call to Action', 'sharethis-follow-buttons' ); ?></h3>

					<div class="item">
						<span class="lbl"><?php echo esc_html__( 'Off/On', 'sharethis-follow-buttons' ); ?></span>

						<div class="switch cta-on-off">
							<label>
								<input type="checkbox" value="on" checked="checked">

								<span class="lever"></span>
							</label>
						</div>
					</div>
					<div class="item cta-text">
						<input type="text" value="Follow us:">
					</div>
				</div>
				<div class="st-radio-config button-config-third label-position">
					<h3><?php echo esc_html__( 'Label position', 'sharethis-follow-buttons' ); ?></h3>

					<div class="item">
						<input type="radio" class="with-gap" value="on">

						<label id="left"><?php echo esc_html__( 'Left', 'sharethis-follow-buttons' ); ?></label>
					</div>
					<div class="item">
						<input type="radio" class="with-gap" value="on" checked="checked">

						<label id="top"><?php echo esc_html__( 'Top', 'sharethis-follow-buttons' ); ?></label>
					</div>
					<div class="item">
						<input type="radio" class="with-gap" value="on">

						<label id="right"><?php echo esc_html__( 'Right', 'sharethis-follow-buttons' ); ?></label>
					</div>
				</div>

				<hr>

				<div class="button-config-half">
					<h3 class="center"><?php echo esc_html__( 'Corners', 'sharethis-follow-buttons' ); ?></h3>

					<span><?php echo esc_html__( 'Square', 'sharethis-follow-buttons' ); ?></span>
					<span class="range-field">
							<input type="range" min="0" max="16" value="0" id="radius-selector" style="width: 200px; margin: 5px;">
							<span class="thumb">
								<span class="value"></span>
							</span>
						</span>
					<span><?php echo esc_html__( 'Rounded', 'sharethis-follow-buttons' ); ?></span>
				</div>
				<div class="button-config-half button-config">
					<h3><?php echo esc_html__( 'Extras', 'sharethis-follow-buttons' ); ?></h3>

					<div class="item">
						<span class="lbl"><?php echo esc_html__( 'Add Spacing', 'sharethis-follow-buttons' ); ?></span>

						<div class="switch extra-spacing">
							<label>
								<input type="checkbox" value="on" checked="checked">

								<span class="lever"></span>
							</label>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
	<?php if ( 'Enabled' === $enabled ) : ?>
		<button class="disable-tool" data-button="inline-follow">Disable Tool</button>
	<?php endif; ?>
</div>
