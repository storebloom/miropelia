<?php if(!$demo_enabled) : ?>
	<div class="demo-ad ga-panel ga-panel-default">
		<div class="ga-panel-heading">
			<strong>
				<?php esc_html_e('Get Demographic Data!'); ?>
				<button id="demographic-popup">
					<?php esc_html_e('Click Here To Enable', 'googleanalytics'); ?>
				</button>
			</strong>
		</div>
		<img src="<?php echo trailingslashit(get_home_url()) . 'wp-content/plugins/googleanalytics/assets/images/demographics-ad.png'; ?>"/>
	</div>
<?php elseif ($need_account_demo_enable) : ?>
    <div class="demo-ad ga-panel ga-panel-default">
        <div class="ga-panel-heading">
            <strong>
                <?php esc_html_e(
                    "If no demographics data is shown, you'll most likely need to do one of the following:",
                    'googleanalytics'
                ); ?>
                <br>
                <ol>
                    <li><?php esc_html_e('Enable "Demographics" within Google Analytics.', 'googleanalytics'); ?></li>
                    <li><?php esc_html_e('If you just enabled this feature, it may require a refresh prior to seeing data.',
                            'googleanalytics'); ?></li>
                    <li><?php esc_html_e('Wait until there\'s sufficient Demographic data to display.',
                            'googleanalytics'); ?></li>
                </ol>

                <a href="<?php echo esc_url($demographic_page_url); ?>/" class="view-report" target="_blank">
                    <?php echo esc_html__('Go to my account', 'googleanalytics'); ?>
                </a>
            </strong>
        </div>
    </div>
<?php else: ?>
	<div class="filter-choices">
		<?php Ga_Template::load( 'templates/date_custom_range_filter', [
			'date_from' => $date_range['from'],
			'date_to'   => $date_range['to']
		] ); ?>
	</div>
	<div class="demo-ad ga-panel ga-panel-default">
		<div class="ga-panel-heading">
			<strong>
				<?php esc_html_e('Demographic by sessions'); ?>
			</strong>
		</div>
		<div class="ga-demo-chart">
			<div class="ga-panel-body ga-chart gender">
				<div id="demo_chart_gender_div" style="width: 100%;"></div>
				<div class="ga-loader-wrapper stats-page">
					<div class="ga-loader stats-page-loader"></div>
				</div>
			</div>
			<div class="ga-panel-body ga-chart gender">
				<div id="demo_chart_age_div" style="width: 100%;"></div>
				<div class="ga-loader-wrapper stats-page">
					<div class="ga-loader stats-page-loader"></div>
				</div>
			</div>
		</div>
        <div class="ga-demo-chart">
            <div class="ga-panel-body ga-chart device">
                <div id="demo_chart_device_div" style="width: 100%;"></div>
                <div class="ga-loader-wrapper stats-page">
                    <div class="ga-loader stats-page-loader"></div>
                </div>
            </div>
        </div>
	</div>
	<a href="<?php echo esc_url( $demographic_page_url ); ?>/" class="view-report" target="_blank">
		<?php echo esc_html__('View Full Report' ); ?>
	</a>
<hr>
<?php
endif;
