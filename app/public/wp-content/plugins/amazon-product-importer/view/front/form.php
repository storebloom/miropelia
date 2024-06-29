<?php
/**
 * The generated form will be used in Chrome extension to import the product accordinglly.
 *
 * @package: amazon-product-importer
 *
 */
 
if (! defined('ABSPATH')) {
	exit; // Exit if accessed directly
}

if (!function_exists('Nxtal_Import_form')) {
	function Nxtal_Import_form( $data) {
		$html = null;
		if ($data['options']) {
			$html = '<h5 class="card-title text-center">' . __('Import Options', 'amazon-product-importer') . '</h5>
			<h6 class="text-center">' . __('Select the options you want to import.', 'amazon-product-importer') . '</h6>
			<div class="form-group">
				<input type="text" name="affiliate_link" value="' . $data['affiliateLink'] . '" placeholder="' . __('Affiliate link', 'amazon-product-importer') . '" class="input">
				<p class="help-block">' . __('Enter product affiliate link.', 'amazon-product-importer') . '</p>
			</div>
			<div class="form-group">
				<input type="text" name="id_product" placeholder="' . __('Existing product ID', 'amazon-product-importer') . '" class="input">
				<p class="help-block">' . __('If you want to update any existing product with the data of these options then enter the product ID otherwise a new product will be created in your shop.', 'amazon-product-importer') . '</p>
			</div>';
			foreach ($data['options'] as $option) {
				$html .= '<div class="form-group">
					<label>
						<input name="' . $option['name'] . '" type="checkbox" checked="checked" value="1"> ' . $option['label'];
				if ('' != $option['desc']) {
					$html .= '<span class="help-block"> (' . $option['desc'] . ')</span>';
				}
				$html .= '</label>
				</div>';
			}
			if ($data['isAdvaceEnabled']) {
				$html .= '<div class="form-group text-right">
					<a href="#" class="toggle-more">' . __('Advanced options', 'amazon-product-importer') . '</a>
				</div>
				<div class="toggle-content row">
					<div class="form-group row">
						<label for="association_sku" class="col-4 col-form-label">' . __('SKU', 'amazon-product-importer') . '</label>
						<div class="col-8">
							<input type="text" class="form-control" id="association_sku" name="association[sku]" value="">
							<span class="help-block">' . __('Set product SKU.', 'amazon-product-importer') . '</span>		
						</div>
					</div>				
					<div class="form-group row">
						<label for="association_categories" class="col-4 col-form-label">' . __('Category association', 'amazon-product-importer') . '</label>
						<div class="col-8">
							<select name="association[categories][]" id="association_categories" class="form-control chosen" multiple="true">';
				foreach ($data['categories'] as $category) {
					$html .= '<option value="' . __($category['term_id']) . '">' . $category['name'] . '</option>';
				}
				$html .= '</select>		
							<span class="help-block">' . __('Choose categories to assign to the product.', 'amazon-product-importer') . '</span>
						</div>		
					</div>
					<div class="form-group row">
						<label for="association_tax_class" class="col-4 col-form-label">' . __('Tax class', 'amazon-product-importer') . '</label>
						<div class="col-8">
							<select class="form-control" id="association_tax_class" name="association[tax_class]">
							<option value="0">' . __('Standard', 'amazon-product-importer') . '</option>';
				foreach ($data['taxClasses'] as $taxClass) {
					$html .= '<option value="' . $taxClass['slug'] . '">' . $taxClass['name'] . '</option>';
				}
				$html .= '</select>
							<span class="help-block">' . __('Set product tax class.', 'amazon-product-importer') . '</span>	
						</div>		
					</div>
					<div class="form-group row">
						<label for="association_quantity" class="col-4 col-form-label">' . __('Quantity', 'amazon-product-importer') . '</label>
						<div class="col-8">
							<input type="text" class="form-control" id="association_quantity" name="association[quantity]" value="">
							<span class="help-block">' . __('Set product stock quantity.', 'amazon-product-importer') . '</span>		
						</div>
					</div>
					<div class="form-group row">
						<label for="association_price" class="col-4 col-form-label">' . __('Price', 'amazon-product-importer') . '</label>
						<div class="col-8">
							<input type="text" class="form-control" id="association_price" name="association[price]" value="">
							<span class="help-block">' . __('Set product price. You can either set a price prefix with (+,-,*,/) to calculate with the current price or set a fixed price value to override the current one.', 'amazon-product-importer') . '</span>		
						</div>
					</div>					
					<div class="form-group row">
						<label for="association_visibility" class="col-4 col-form-label">' . __('Visibility', 'amazon-product-importer') . '</label>
						<div class="col-8">
							<select class="form-control" id="association_visibility" name="association[visibility]">
								<option value="visible">' . __('Shop and search results', 'amazon-product-importer') . '</option>
								<option value="catalog">' . __('Shop only', 'amazon-product-importer') . '</option>
								<option value="search">' . __('Search results only', 'amazon-product-importer') . '</option>
								<option value="hidden">' . __('Hidden', 'amazon-product-importer') . '</option>
							</select>
							<span class="help-block">' . __('Set catalog visibility.', 'amazon-product-importer') . '</span>	
						</div>		
					</div>
					<div class="form-group row">
						<label for="association_update_existing" class="col-4 col-form-label">' . __('Update existing', 'amazon-product-importer') . '</label>
						<div class="col-8">
							<select class="form-control" id="association_update_existing" name="association[update_existing]">
								<option value="0">' . __('Do not update', 'amazon-product-importer') . '</option>
								<option value="1">' . __('Update with this data', 'amazon-product-importer') . '</option>
							</select>		
							<span class="help-block">' . __('Applicable only if the product id is empty.', 'amazon-product-importer') . '</span>	
						</div>		
					</div>
					<div class="form-group row">
						<label for="association_review" class="col-4 col-form-label">' . __('Max Reviews', 'amazon-product-importer') . '</label>
						<div class="col-8">
							<input type="text" class="form-control" id="association_review" name="association[review]" value="10">
							<span class="help-block">' . __('Set maximum product reviews to import. Set 0 for all reviews, this may increase the import execution time. Applicable only if Customer Reviews option is checked.', 'amazon-product-importer') . '</span>		
						</div>
					</div>
					<div class="form-group row">
						<label for="association_post_status" class="col-4 col-form-label">' . __('Status', 'amazon-product-importer') . '</label>
						<div class="col-8">
							<select class="form-control" id="association_post_status" name="association[post_status]">
								<option value="draft">' . __('Draft', 'amazon-product-importer') . '</option>
								<option value="pending">' . __('Pending Review', 'amazon-product-importer') . '</option>
								<option value="publish">' . __('Published', 'amazon-product-importer') . '</option>
							</select>		
							<span class="help-block">' . __('Set product status.', 'amazon-product-importer') . '</span>	
						</div>		
					</div>
				</div>';
			}
		}
		return $html;
	}
}
