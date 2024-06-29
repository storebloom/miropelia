<?php
/**
 * Add extension configuration menu link in admin side bar navigation.
 *
 * @package: amazon-product-importer
 */
 
if (! defined('ABSPATH')) {
	exit; // Exit if accessed directly
}

if (!class_exists('NxtalImporterConfiguration')) {
	class NxtalImporterConfiguration {
	
		public function getConfigurationPage() {
			add_submenu_page(
				'edit.php?post_type=product',
				__('Importer Configuration', 'amazon-product-importer'),
				__('Importer Configuration', 'amazon-product-importer'),
				'manage_options',
				'nxtal_importer_configuration',
				'nxtal_importer_configuration_callback'
			);
		}
		public function getConfigurationLink( $links) {
			$pluginLinks = array();

			if (function_exists('WC')) {
				$configurationUrl    = admin_url('edit.php?post_type=product&page=nxtal_importer_configuration');
				$pluginLinks[] = '<a href="' . esc_url($configurationUrl) . '">' . esc_html__('Configuration', 'amazon-product-importer') . '</a>';
			}

			return array_merge($pluginLinks, $links);
		}
	}
}
