<?php
/**
 * The function response will display in admin for extesnion cofiguration.
 *
 * @package: amazon-product-importer
 */
 
if (! defined('ABSPATH')) {
	exit; // Exit if accessed directly
}

if (!function_exists('nxtal_importer_configuration_callback')) {
	function nxtal_importer_configuration_callback() { ?>
		<style>
		.form-group {
			margin-bottom: 15px;
		}
		.form-group *{
			-webkit-box-sizing: border-box;
			box-sizing: border-box;
		}
		.input-group {
			border-collapse: separate;
			display: table;
			position: relative;
			width: 100%;
		}
		.input-group input[type=text], .input-group textarea{
			float: left;
			margin-bottom: 0;
			position: relative;
			width: 100%;
			z-index: 2;
			display: table-cell;
			border: 1px solid #c7d6db;
			border-radius: 3px;
			color: #555;
			font-size: 12px;
			height: 31px;
			line-height: 1.42857;
			padding: 6px 8px;
			border-bottom-right-radius: 0;
			border-top-right-radius: 0;
			background: #fff;
			transition: border-color .15s ease-in-out,box-shadow .15s ease-in-out,-webkit-box-shadow .15s ease-in-out;
		}
		.input-group textarea{
			height: 100px;
		}
		.input-group-addon{
			vertical-align: middle;
			white-space: nowrap;
			width: 1%;
			background-color: #f5f8f9;
			border: 1px solid #c7d6db;
			border-radius: 3px;
			color: #555;
			font-size: 12px;
			font-weight: 400;
			line-height: 1;
			padding: 6px 8px;
			text-align: center;
			border-bottom-left-radius: 0;
			border-top-left-radius: 0;
			cursor: pointer;
			display: table-cell;
			border-left: none;
		}
		
		#nxtalimporter-toggle-content {
			cursor: pointer;
		}
		.ellipsis {
			white-space: nowrap;
			overflow: hidden;
			text-overflow: ellipsis;
			cursor: pointer;
		}
		.hide{
			display:none !important;
		}
		.log-content{
			background: rgb(255, 255, 255);
			padding: 10px;
			margin-top: 10px;
			border: 1px solid rgb(126, 137, 147);
			max-height: 1000px;
			overflow: auto;
			min-height: 100px;
			color: #444;
		}
		</style>
		<div class="wrap">
			<h1><?php echo esc_html(__('Configuration', 'amazon-product-importer')); ?></h1>
			<?php if (isset($_GET['update'])) { ?>
			<div class="notice notice-success settings-error is-dismissible"> 
				<p><strong><?php echo esc_html(__('The setting has been updated successfully.', 'amazon-product-importer')); ?></strong></p>
				<button type="button" class="notice-dismiss">
					<span class="screen-reader-text"><?php echo esc_html(__('Dismiss this notice.', 'amazon-product-importer')); ?></span>
				</button>
			</div>	
				<?php
			}
			if (isset($_GET['error'])) {
				?>
			<div class="notice notice-error settings-error is-dismissible"> 
				<p><strong><?php echo esc_html(__('Invalid Secret key.', 'amazon-product-importer')); ?></strong></p>
				<button type="button" class="notice-dismiss">
					<span class="screen-reader-text"><?php echo esc_html(__('Dismiss this notice.', 'amazon-product-importer')); ?></span>
				</button>
			</div>	
				<?php
			}
			if (isset($_GET['clear'])) {
				?>
			<div class="notice notice-success settings-error is-dismissible"> 
				<p><strong><?php echo esc_html(__('Log cleared successfully.', 'amazon-product-importer')); ?></strong></p>
				<button type="button" class="notice-dismiss">
					<span class="screen-reader-text"><?php echo esc_html(__('Dismiss this notice.', 'amazon-product-importer')); ?></span>
				</button>
			</div>	
				<?php
			} 
			?>
			<div class="notice notice-warning"> 
				<p>
					<strong><?php echo esc_html(__('Use this credentials to connect the plugin with Chrome extension.', 'amazon-product-importer')); ?></strong>
					<div id="nxtalimporter-toggle-content" class="ellipsis" onClick="this.classList.toggle('ellipsis');" title="<?php echo esc_html(__('Click to expand', 'amazon-product-importer')); ?>">
					<?php echo esc_html(__('You can import products from', 'amazon-product-importer')); ?>
					<?php echo wp_kses(AmazonProductImporter::getImportHostNames(), array('b' => array())); ?>
					</div>
				
				</p>
			</div>
			<form method="post" action="admin-post.php">
				<input type="hidden" name="action" value="nxtal_importer_save_configuration">
				<?php wp_nonce_field('nxtal_importer_fields_verify'); ?>
				<?php $configuration = get_option('amazon_importer_setting'); ?>
				<table class="form-table" role="presentation">
					<tbody>
						<tr>
							<th scope="row">
								<label for="import_link"><?php echo esc_html(__('Import link', 'amazon-product-importer')); ?></label>
							</th>
							<td>
								<div class="form-group">
									<div class="input-group">
										<?php 
											$importLink = get_rest_url(null, 'nxtal/amazonproductimporter'); 
										?>
										<input type="text"
											id="import_link"
											value="<?php echo esc_html( $importLink ); ?>"
											readonly="readonly"
										>
										<span class="input-group-addon" onClick="copyToClipboard('import_link');"><?php echo esc_html(__('Copy', 'amazon-product-importer')); ?></span>
									</div>
									<p class="description"><?php echo esc_html(__('Use this link to connect.', 'amazon-product-importer')); ?></p>
								</div>
							</td>
						</tr>
						<tr>
							<th scope="row">
								<label for="secret_key"><?php echo esc_html(__('Secret key', 'amazon-product-importer')); ?></label>
							</th>
							<td>
								<?php
								$secret_key = '';
								if (isset($configuration['secret_key'])) {
									$secret_key = $configuration['secret_key'];
								}
								?>
								<div class="form-group">
									<div class="input-group">
										<input type="text" id="secret_key" name="secret_key" value="<?php echo esc_html($secret_key); ?>" required>
										<span class="input-group-addon" onClick="copyToClipboard('secret_key');"><?php echo esc_html(__('Copy', 'amazon-product-importer')); ?></span>
									</div>
									<p class="description"><?php echo esc_html(__('The secret key can be any random string at least 8 characters long.', 'amazon-product-importer')); ?></p>
								</div>
							</td>
						</tr>
						<tr>
							<th scope="row">
								<label>&nbsp;</label>
							</th>
							<td>
								<input type="button" onclick="return changeKey();" id="nxt-generateHashKey" class="button button-default" value="<?php echo esc_html(__('Generate new key', 'amazon-product-importer')); ?>">
							</td>
						</tr>
						<tr>
							<th scope="row">
								<label for="advance_option">
									<?php echo esc_html(__('Advanced options', 'amazon-product-importer')); ?>
								</label>
							</th>
							<td>
								<p class="description">
								<input name="advance_option" type="checkbox" id="advance_option" value="1"
								<?php
								if (isset($configuration['advance_option']) && $configuration['advance_option']) {
									?>
									 checked 
									 <?php
								} 
								?>
								>
								<?php echo esc_html(__('Enable advanced option to import. After changing this option you will need to refresh the Chrome extension settings.', 'amazon-product-importer')); ?></p>
							</td>
						</tr>
						<tr>
							<th scope="row">
								<label for="affiliate_id">
									<?php echo esc_html(__('Affiliate ID', 'amazon-product-importer')); ?>
								</label>
							</th>
							<td>
									<div class="input-group">
									<?php
										$affiliate_id = '';
									if (isset($configuration['affiliate_id'])) {
										$affiliate_id = $configuration['affiliate_id'];
									}
									?>
									<input type="text" id="affiliate_id" name="affiliate_id" value="<?php echo esc_html($affiliate_id); ?>">
									</div>
									<p class="description"><?php echo esc_html(__('Your affiliate url parameters (eg: tag=nxtal). It can be changed when importing the product.', 'amazon-product-importer')); ?></p>
							
							</td>
						</tr>
						<tr>
							<th scope="row">
								<label for="replace_texts">
									<?php echo esc_html(__('Replace texts', 'amazon-product-importer')); ?>
								</label>
							</th>
							<td>
								<div class="input-group">
								<?php 
									$replace_texts = '';
								if (isset($configuration['replace_texts'])) {
									$replace_texts = $configuration['replace_texts'];								
								} 
								?>
								<textarea id="replace_texts" name="replace_texts" placeholder="<?php echo esc_html(__('Find:Replace', 'amazon-product-importer')); ?>"><?php echo esc_html($replace_texts); ?></textarea>
								</div>
								<p class="description"><?php echo esc_html(__('Add the text you want to replace with the imported product information (eg: Find:Replace, Amazon:Nxtal). You can add multiple text separated by commas.', 'amazon-product-importer')); ?></p>
							
							</td>
						</tr>
						<tr>
							<th scope="row">
								<label for="log"><?php echo esc_html(__('Log', 'amazon-product-importer')); ?></label>
							</th>
							<td>					
									<p class="description">
										<input name="log" type="checkbox" id="log" value="1"
										<?php if (isset($configuration['log']) && $configuration['log']) { ?>
										 checked <?php } ?>>
										<?php echo esc_html(__('Enable debug logger.', 'amazon-product-importer')); ?>
									</p>
								
							</td>
						</tr>
						<?php if (isset($configuration['log']) && $configuration['log'] && file_exists(AMAZON_PRODUCT_IMPORTER_DIR . '/debug.log')) { ?>
						<tr>
							<th scope="row">
								<label>&nbsp;</label>
							</th>
							<td>
								<input type="button" onclick="return toggleSection('log-section', 'log-show');" id="log-show" class="button button-default" value="<?php echo esc_html(__('Show log', 'amazon-product-importer')); ?>">
								<div id="log-section" class="hide">
									<input type="button" onclick="return toggleSection('log-show', 'log-section');" class="button button-default" value="<?php echo esc_html(__('Hide log', 'amazon-product-importer')); ?>">
									<a href="<?php echo esc_html(admin_url('edit.php?post_type=product&page=nxtal_importer_configuration&clear_log=1')); ?>" class="button button-default"><?php echo esc_html(__('Clear log', 'amazon-product-importer')); ?></a>
									<div class="log-content">
										<pre><?php echo esc_html(file_get_contents(AMAZON_PRODUCT_IMPORTER_DIR . '/debug.log')); ?></pre>
									</div>
								</div>
							</td>
						</tr>
						<?php } ?>
					</tbody>
				</table>
				<p class="submit">
					<input type="submit" name="submit" id="submit" class="button button-primary" value="<?php echo esc_html(__('Save Changes', 'amazon-product-importer')); ?>">
				</p>
				<script>
					function randomHashKey(length) {
						var chars = "abcdefghijklmnopqrstuvwxyz!@ABCDEFGHIJKLMNOP1234567890";
						var hashkey = "";
						for (var x = 0; x < length; x++) {
							var i = Math.floor(Math.random() * chars.length);
							hashkey += chars.charAt(i);
						}
						return hashkey;
					}
					function changeKey() {
						document.getElementById('secret_key').value = randomHashKey(55);
					}
					function toggleSection(show, hide) {
						document.getElementById(show).classList.remove('hide');
						document.getElementById(hide).classList.add('hide');
					}
					
					function copyToClipboard(element) {
					  var copyText = document.getElementById(element);
					  copyText.select();
					  copyText.setSelectionRange(0, 99999);
					  document.execCommand("copy");
					}
				</script>
			</form>
		</div>
		<?php
	}
}
