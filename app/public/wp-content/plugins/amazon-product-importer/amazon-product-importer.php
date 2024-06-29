<?php
/**
 * Plugin Name: Amazon Product Importer
 * Plugin URI: https://woocommerce.com/products/amazon-product-importer/
 * Description: Import product in your woocommerce shop directly from any Amazon marketplace websites by the extension in just one click and sale the imported product as yours or as an affiliate.
 * Version: 2.3.0
 * Author: Nxtal
 * Author URI: https://woocommerce.com/vendor/nxtal/
 * Copyright: © 2023 Nxtal.
 * License: GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 * Text Domain: amazon-product-importer
 * Woo: 6399556:dfe8e80354b100a41c5c625a259c1659
 * WC tested up to: 7.5.1
 * WC requires at least: 2.6
 */
// don't call the file directly
if (! defined('ABSPATH')) {
	exit; // Exit if accessed directly
}

if (! defined('AMAZON_PRODUCT_IMPORTER_DIR')) {
	define('AMAZON_PRODUCT_IMPORTER_DIR', plugin_dir_path(__FILE__));
}

//Check if WooCommerce is active
if (in_array('woocommerce/woocommerce.php', apply_filters('active_plugins', get_option('active_plugins')))) {
	if (!class_exists('AmazonProductImporter')) {
		class AmazonProductImporter {
		
			public function __construct() {
				/* Include Files */
				include_once AMAZON_PRODUCT_IMPORTER_DIR . '/view/admin/configuration.php';
				include_once AMAZON_PRODUCT_IMPORTER_DIR . '/view/front/form.php';
				
				/* Include Classes */
				include_once AMAZON_PRODUCT_IMPORTER_DIR . '/classes/admin/NxtalImporterConfiguration.php';
				include_once AMAZON_PRODUCT_IMPORTER_DIR . '/classes/admin/NxtalImporterConfigurationSave.php';
				  
				/* Register Hooks */
				register_activation_hook(
					__FILE__,
					array(
						$this,
						'activation'
					)
				);
				
				add_action(
					'admin_menu',
					array(
						new NxtalImporterConfiguration(),
						'getConfigurationPage'
					)
				);
				
				add_filter(
					'plugin_action_links_' . plugin_basename(__FILE__),
					array(
						new NxtalImporterConfiguration(),
						'getConfigurationLink'
					)
				);
				add_action(
					'admin_post_nxtal_importer_save_configuration',
					array(
						new NxtalImporterConfigurationSave(),
						'saveConfiguration'
					)
				);
				
				add_action(
					'init',
					function () {
						if (isset($_REQUEST['clear_log']) && '1' == $_REQUEST['clear_log']) {
							@unlink(AMAZON_PRODUCT_IMPORTER_DIR . '/debug.log');
							wp_redirect(get_admin_url() . 'edit.php?post_type=product&page=nxtal_importer_configuration&clear');
						}
					}
				);
				
				add_action( 'rest_api_init', function () {
					register_rest_route( 'nxtal', 'amazonproductimporter', array(
							'methods' => 'GET, POST', 
							'callback' => array( $this, 'importer' ),
							'permission_callback' => function () {
								return true;
							}
					) );
				});
				
				
				add_filter(
					'manage_edit-product_columns',
					array(
						$this,
						'add_origin_column'
					),
					10,
					1
				);
				
				add_action(
					'manage_product_posts_custom_column',
					array(
						$this,
						'add_origin_column_content'
					),
					10,
					2
				);
				
				if ( isset($_GET['post_type']) && 'product' == $_GET['post_type']) {
					add_action(
						'admin_head',
						array(
							$this,
							'add_origin_column_css'
						)
					);
				}	
			}

			public function add_origin_column( $columns) {
				$columns['origin'] = __( 'Origin', 'amazon-product-importer' );
				return $columns;
			}
			
			public function add_origin_column_content( $column, $postid ) {
				if ('origin' == $column ) {
					$origin = get_post_meta($postid, 'product_origin', true);
		
					if ($origin) {
						echo '<a href="' . esc_url_raw($origin) . '" target="_blank">' . esc_html($this->getHostFromUrl($origin)) . '</a>';
					} else {
						echo '-';
					}
				}
			}
			
			public function getHostFromUrl( $url) {
				return parse_url($url, PHP_URL_HOST);
			}
			
			public function add_origin_column_css() {
				?>
				<style type="text/css" >
					th#origin { width: 12%; }
				</style>
				<?php
			}
			
			public function importer() {
				
				require_once(AMAZON_PRODUCT_IMPORTER_DIR . '/classes/front/Importer.php');
			}
			
			public function activation() {
				if (!get_option('amazon_importer_setting')) {
					add_option(
						'amazon_importer_setting',
						array(
							'secret_key' => md5(rand()),
							'advance_option' => 0,
							'affiliate_id' => '',
							'replace_texts' => '',
							'log' => 0
						)
					);
				}
			}
		   
			public static function getImportHosts() {
				return array(
					array(
						'name' => '<b>Amazon.com</b>, amazon.com.au, amazon.com.be, amazon.com.br, amazon.ca, amazon.cn, amazon.eg, amazon.fr, amazon.de, amazon.in, amazon.it, amazon.co.jp, amazon.com.mx, amazon.nl, amazon.pl, amazon.sa, amazon.sg, amazon.es, amazon.se, amazon.com.tr, amazon.ae, amazon.co.uk',
						'parser' => 'AmazonParser',
						'valid_url' => '/amazon\.(.+)\/(dp|gp)\//'
					),
				);
			}
			
			public static function addLog( $method, $message = null, $filePath = null, $line = null) {
				$log = gmdate('Y-m-d H:i:s') . "\t" . $method . "\t" . $message . "\t" . $filePath . "\t" . $line . PHP_EOL;
				$config = get_option('amazon_importer_setting');
				if ($config['log']) {
					error_log($log, 3, AMAZON_PRODUCT_IMPORTER_DIR . 'debug.log');
				}
			}
			
			public static function getImportHostNames() {
				return implode(', ', array_column(self::getImportHosts(), 'name'));
			}
			
			public static function getImportOptions() {
				return array(
					array(
						'name' => 'name',
						'label' => __('Name', 'amazon-product-importer'),
						'desc' => __('Required', 'amazon-product-importer')
					),
					array(
						'name' => 'sku',
						'label' => __('SKU', 'amazon-product-importer'),
						'desc' => __('Required', 'amazon-product-importer')
					),
					/*array(
						'name' => 'upc',
						'label' => __('UPC', 'amazon-product-importer'),
						'desc' => ''
					),*/
					array(
						'name' => 'weight',
						'label' => __('Weight', 'amazon-product-importer'),
						'desc' => ''
					),
					array(
						'name' => 'description',
						'label' => __('Description', 'amazon-product-importer'),
						'desc' => ''
					),
					array(
						'name' => 'price',
						'label' => __('Price', 'amazon-product-importer'),
						'desc' => __('Required', 'amazon-product-importer')
					),
					array(
						'name' => 'image',
						'label' => __('Image', 'amazon-product-importer'),
						'desc' => ''
					),
					array(
						'name' => 'brand',
						'label' => __('Manufacturer', 'amazon-product-importer'),
						'desc' => __('New Manufacturer will be created if not exist.', 'amazon-product-importer')
					),
					array(
						'name' => 'category',
						'label' => __('Category', 'amazon-product-importer'),
						'desc' => __('New category will be created if not exist.', 'amazon-product-importer')
					),
					array(
						'name' => 'variant',
						'label' => __('Variation', 'amazon-product-importer'),
						'desc' => __('New Variation attribute will be created if not exist.', 'amazon-product-importer')
					),
					array(
						'name' => 'feature',
						'label' => __('Feature', 'amazon-product-importer'),
						'desc' => __('New feature will be created if not exist.', 'amazon-product-importer')
					),
					array(
						'name' => 'review',
						'label' => __('Customer Reviews', 'amazon-product-importer'),
						'desc' => ''
					),
					/*array(
						'name' => 'meta_title',
						'label' => __('Meta title', 'amazon-product-importer'),
						'desc' => ''
					),
					array(
						'name' => 'meta_description',
						'label' => __('Meta description', 'amazon-product-importer'),
						'desc' => ''
					),
					array(
						'name' => 'meta_keyword',
						'label' => __('Meta keyword', 'amazon-product-importer'),
						'desc' => ''
					)*/
				);
			}
			
			public static function getCategories() {
				$catArgs = array(
					'orderby'    => 'name',
					'order'      => 'asc',
					'hide_empty' => false,
				);
				
				$categories = array();
				foreach (get_terms('product_cat', $catArgs) as $cat) {
					$categories[] = array(
						'term_id' => $cat->term_id,
						'name' => htmlspecialchars_decode($cat->name)
					);
				}
				return $categories;
			}
			
			public static function getTaxClasses() {
				return array_map(
					function ( $tax) {
						return array(
							'slug' => str_replace(' ', '-', strtolower($tax)),
							'name' => $tax
						);
					},
					WC_Tax::get_tax_classes()
				);
			}
			
			public static function slugify( $text) {
				// replace non letter or digits by -
				$text = preg_replace('~[^\pL\d]+~u', '-', $text);

				// transliterate
				//$text = iconv('utf-8', 'us-ascii//TRANSLIT', $text);

				// remove unwanted characters
				$text = preg_replace('~[^-\w]+~', '', $text);

				// trim
				$text = trim($text, '-');

				// remove duplicate -
				$text = preg_replace('~-+~', '-', $text);

				// lowercase
				$text = strtolower($text);

				if (empty($text)) {
					return '';
				}
				return $text;
			}
		}
		
		$AmazonProductImporter = new AmazonProductImporter();
	}
}
