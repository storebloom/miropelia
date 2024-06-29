<?php
/**
 * Save configuration after the form submission in admin.
 *
 * @package: amazon-product-importer
 */
 
if (! defined('ABSPATH')) {
	exit; // Exit if accessed directly
}

if (!class_exists('NxtalImporterConfigurationSave')) {
	class NxtalImporterConfigurationSave {
	
		public function saveConfiguration() {
			check_admin_referer('nxtal_importer_fields_verify');
			if (!current_user_can('manage_options')) {
				wp_die('You are authorized to edit this configuration.');
			}
			
			$secretKey = '';
			if (isset($_POST['secret_key'])) {
				$secretKey = sanitize_text_field($_POST['secret_key']);
			}
			if (isset($_POST['advance_option'])) {
				$advanceOption = 1;
			} else {
				$advanceOption = 0;
			}
			
			$affiliateId = '';
			if (isset($_POST['affiliate_id'])) {
				$affiliateId = sanitize_text_field($_POST['affiliate_id']);
			}
			
			$replace_texts = '';
			if (isset($_POST['replace_texts'])) {
				$replace_texts = sanitize_text_field($_POST['replace_texts']);
			}
			
			if (isset($_POST['log'])) {
				$log = 1;
			} else {
				$log = 0;
			}
			
			$messageAttribute = 'update';
			if (empty($secretKey)
				|| strlen($secretKey) < 8
			) {
				$messageAttribute = 'error';
			} else {
				update_option(
					'amazon_importer_setting',
					array(
						'secret_key' => $secretKey,
						'advance_option' => $advanceOption,
						'affiliate_id' => $affiliateId,
						'replace_texts' => $replace_texts,
						'log' => $log
					)
				);
			}
			wp_redirect(get_admin_url() . 'edit.php?post_type=product&page=nxtal_importer_configuration&' . $messageAttribute);
		}
	}
}
