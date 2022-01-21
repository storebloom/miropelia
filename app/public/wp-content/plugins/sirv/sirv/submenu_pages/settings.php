<div class="sirv-network-wrapper">
  <h2>Deliver WordPress media library from Sirv</h2>

  <p class="sirv-options-desc">
    Existing and future images in the WordPress media library will be copied to Sirv, optimized, resized and rapidly served from the CDN.
  </p>

  <div class="sirv-optiontable-holder">
    <div class="sirv-error"><?php if ($error) echo '<div id="sirv-settings-messages" class="sirv-message error-message">' . $error . '</div>'; ?></div>
    <table class="optiontable form-table">
      <tr>
        <th>
          <label>Serve WordPress media</label>
        </th>
        <td>
          <label><input type="radio" name="SIRV_ENABLE_CDN" value='1' "<?php checked(1, get_option('SIRV_ENABLE_CDN'), true); ?>">Enable</label><br />
          <label><input type="radio" name="SIRV_ENABLE_CDN" value='2' "<?php checked(2, get_option('SIRV_ENABLE_CDN'), true); ?>">Disable</label>
        </td>
        <td>
          <span class="sirv-status <?php echo sirv_getStatus(); ?>"></span>
        </td>
      </tr>
      <!-- <tr>
        <th>
          <label>Network</label>
        </th>
        <td>
          <label><input type="radio" name="SIRV_NETWORK_TYPE" value='1' "<?php checked(1, get_option('SIRV_NETWORK_TYPE'), true); ?>"><b>CDN</b> - deliver images from Sirv's global server network.</label><br />
          <label><input type="radio" name="SIRV_NETWORK_TYPE" value='2' "<?php checked(2, get_option('SIRV_NETWORK_TYPE'), true); ?>"><b>DIRECT</b> - deliver images from Sirv's primary datacentre.</label>
        </td>
      </tr> -->
      <tr>
        <th>
          <label>Parse static images</label>
        </th>
        <td>
          <label><input type="radio" name="SIRV_PARSE_STATIC_IMAGES" value='1' "<?php checked(1, get_option('SIRV_PARSE_STATIC_IMAGES'), true); ?>">Enable</label><br>
          <label><input type="radio" name="SIRV_PARSE_STATIC_IMAGES" value='2' "<?php checked(2, get_option('SIRV_PARSE_STATIC_IMAGES'), true); ?>">Disable</label>
        </td>
        <td>
          <div class="sirv-tooltip">
            <i class="dashicons dashicons-editor-help sirv-tooltip-icon"></i>
            <span class="sirv-tooltip-text sirv-no-select-text">
              Deliver more images from Sirv. This setting looks for images in the HTML page, then serves them from Sirv. It adds some server load, so may be unsuitable for high-traffic websites.
            </span>
          </div>
        </td>
      </tr>
      <?php
      //if ($isMultiCDN && !empty($domains) && !$is_direct) {
      if (count($domains) > 1) {
      ?>
        <tr>
          <th><label>Domain</label></th>
          <td>
            <select id="sirv-choose-domain" name="SIRV_CDN_URL">
              <?php
              foreach ($domains as $domain) {
                $selected = '';
                if ($domain == $sirvCDNurl) {
                  $selected = 'selected';
                }
                echo '<option ' . $selected . ' value="' . $domain . '">' . $domain . '</option>';
              }
              ?>
            </select>
          </td>
        </tr>
      <?php } else { ?>
        <input type="hidden" id="sirv-choose-domain-hidden" name="SIRV_CDN_URL" value="<?php echo $sirvCDNurl; ?>">
      <?php } ?>
      <tr>
        <th>
          <label>Folder name on Sirv</label>
        </th>
        <td colspan="2" style="padding: 0;">
          <?php
          $sirv_folder = get_option('SIRV_FOLDER');
          ?>
          <p class="sirv-viewble-option"><span class="sirv--grey"><?php echo $sirvCDNurl; ?>/</span><?php echo $sirv_folder; ?>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a class="sirv-option-edit" href="#">Change</a></p>
          <p class="sirv-editable-option" style="display: none;">
            <span class="sirv--grey"><?php echo $sirvCDNurl; ?>/</span><input class="regular-text" type="text" name="SIRV_FOLDER" value="<?php echo $sirv_folder; ?>">
          </p>
          <br>
          <div class="sirv-message warning-message sirv-hide sirv-warning-on-folder-change">
            <span style="font-size: 15px;font-weight: 800;">Important!</span><br>Changing folder name will clear the image cache, so images will re-synchronize on first request or use <a class="sirv-show-sync-tab">Sync Images</a> to pre-sync entire library.
          </div>
        </td>
      </tr>
      <tr>
        <th>
        </th>
        <td><input type="submit" name="submit" class="button-primary sirv-save-settings" value="<?php _e('Save Settings') ?>" /></td>
      </tr>
    </table>
  </div>
</div>
<div class="sirv-profiles-wrapper">
  <!-- profiles options-->
  <h2>Image settings</h2>
  <?php
  $useSirvResponsiveOption = get_option('SIRV_USE_SIRV_RESPONSIVE');
  $isShowPlaceholder = $useSirvResponsiveOption == '1' ? true : false;
  $showPlaceholderBlock = $isShowPlaceholder ? 'table-row' : 'none';
  ?>
  <div class="sirv-optiontable-holder">
    <table class="optiontable form-table">
      <tr>
        <th>
          <label style="padding-bottom: 10px;">Lazy loading</label>
        </th>
        <td>
          <label>
            <input type="radio" name="SIRV_USE_SIRV_RESPONSIVE" value='1' "<?php checked('1', $useSirvResponsiveOption);  ?>">Enable
          </label>
          <label>
            <input type="radio" name="SIRV_USE_SIRV_RESPONSIVE" value='2' "<?php checked('2', $useSirvResponsiveOption);  ?>">Disable
          </label>
          <span class="sirv-option-responsive-text">Load images on demand & scale them perfectly.</span>
          <div class="sirv-responsive-msg sirv-message warning-message">
            <div>
              Deactivate any other lazy loading plugins. After saving, check that your images display as expected.
            </div>
          </div>
        </td>
      </tr>
      <tr class="sirv-hide-placeholder" style="display:<?php echo $showPlaceholderBlock; ?>;">
        <th><label>Lazy placeholder</label></th>
        <td>
          <label><input type="radio" name="SIRV_RESPONSIVE_PLACEHOLDER" value='3' "<?php checked(3, get_option('SIRV_RESPONSIVE_PLACEHOLDER'), true); ?>"><b>Image</b> - best experience.</label>
          <label><input type="radio" name="SIRV_RESPONSIVE_PLACEHOLDER" value='2' "<?php checked(2, get_option('SIRV_RESPONSIVE_PLACEHOLDER'), true); ?>"><b>Grey background</b> - most efficient.</label>
          <label><input type="radio" name="SIRV_RESPONSIVE_PLACEHOLDER" value='1' "<?php checked(1, get_option('SIRV_RESPONSIVE_PLACEHOLDER'), true); ?>"><b>Blurred image</b> - popular effect.</label>
          <span class="sirv-option-responsive-text">Display background while image loads.</span>
        </td>
      </tr>
      <tr>
        <th>
          <label>Image profile</label>
        </th>
        <td>
          <!-- <span class="sirv-traffic-loading-ico sirv-shortcodes-profiles"></span> -->
          <select id="sirv-cdn-profiles">
            <?php if (isset($profiles)) echo sirv_renderProfilesOptopns($profiles); ?>
          </select>
          <input type="hidden" id="sirv-cdn-profiles-val" name="SIRV_CDN_PROFILES" value="<?php echo get_option('SIRV_CDN_PROFILES'); ?>">
        </td>
        <td>
          <div class="sirv-tooltip">
            <i class="dashicons dashicons-editor-help sirv-tooltip-icon"></i>
            <span class="sirv-tooltip-text sirv-no-select-text">Style your images with watermarks, text and other customizations using one of <a target="_blank" href="https://my.sirv.com/#/profiles/">your profiles</a>. Learn <a target="_blank" href="https://sirv.com/help/articles/dynamic-imaging/profiles/">about profiles</a></span>
          </div>
        </td>
      </tr>
      <tr>
        <th>
          <label>Sirv shortcode profile</label>
        </th>
        <td>
          <!-- <span class="sirv-traffic-loading-ico sirv-shortcodes-profiles"></span> -->
          <select id="sirv-shortcodes-profiles">
            <?php if (isset($profiles)) echo sirv_renderProfilesOptopns($profiles); ?>
          </select>
          <input type="hidden" id="sirv-shortcodes-profiles-val" name="SIRV_SHORTCODES_PROFILES" value="<?php echo get_option('SIRV_SHORTCODES_PROFILES'); ?>">
        </td>
        <td>
          <div class="sirv-tooltip">
            <i class="dashicons dashicons-editor-help sirv-tooltip-icon"></i>
            <span class="sirv-tooltip-text sirv-no-select-text">Apply one of <a target="_blank" href="https://my.sirv.com/#/profiles/">your profiles</a> for watermarks, text and other image customizations. Learn <a target="_blank" href="https://sirv.com/help/articles/dynamic-imaging/profiles/">about profiles</a>.</span>
          </div>
        </td>
      </tr>
      <tr>
        <th><label>Crop images</label></th>
        <td>
          <a class="sirv-hide-show-a" data-status="false" data-selector=".sirv-crop-wrap" data-show-msg="Show crop options" data-hide-msg="Hide crop options" data-icon-show="dashicons dashicons-arrow-right-alt2" data-icon-hide="dashicons dashicons-arrow-down-alt2"><span class="dashicons dashicons-arrow-right-alt2"></span>Show crop options</a>
        </td>
      </tr>
      <tr>
        <td colspan="2">
          <div class="sirv-crop-wrap" style="display: none;">
            <div class="sirv-crop-wrap__desc">
              <span>Show consistently sized images either via crop or adding background.</span>
              <div class="sirv-crop-wrap__img">
                <img src="https://sirv.sirv.com/website/screenshots/wordpress/crop-example.jpg">
              </div>

            </div>
            <?php
            $crop_data = json_decode(get_option('SIRV_CROP_SIZES'), true);
            if (empty($crop_data)) {
              $encoded_default_crop = sirv_get_default_crop();
              update_option('SIRV_CROP_SIZES', $encoded_default_crop);
              $crop_data = json_decode($encoded_default_crop, true);
            }
            $wp_sizes = sirv_get_image_sizes();
            ksort($wp_sizes);

            foreach ($wp_sizes as $size_name => $size) {
              $size_str = $size_name . "<span>" . $size['width'] . "x" . $size['height'] . "</span>";
              $cropMethod = @$crop_data[$size_name];
              if (empty($cropMethod)) $cropMethod = 'none';
            ?>
              <div class="sirv-crop-row">
                <span class="sirv-crop-row__title"><?php echo $size_str; ?></span>
                <div class="sirv-crop-row__checkboxes">
                  <input type="radio" class="sirv-crop-radio" name="<?php echo $size_name; ?>" id="<?php echo $size_name; ?>1" value="none" <?php checked('none', $cropMethod, true); ?>><label class="fchild" for="<?php echo $size_name; ?>1">No crop</label>
                  <input type="radio" class="sirv-crop-radio" name="<?php echo $size_name; ?>" id="<?php echo $size_name; ?>2" value="wp_crop" <?php checked('wp_crop', $cropMethod, true); ?>><label for="<?php echo $size_name; ?>2">Crop</label>
                  <input type="radio" class="sirv-crop-radio" name="<?php echo $size_name; ?>" id="<?php echo $size_name; ?>3" value="sirv_crop" <?php checked('sirv_crop', $cropMethod, true); ?>><label for="<?php echo $size_name; ?>3">Uniform</label>
                </div>
              </div>
            <?php } ?>
            <input type="hidden" id="sirv-crop-sizes" name="SIRV_CROP_SIZES" value="<?php echo htmlspecialchars(get_option('SIRV_CROP_SIZES')); ?>">
          </div>
        </td>
      </tr>
      <tr>
        <th>
        </th>
        <td><input type="submit" name="submit" class="button-primary sirv-save-settings" value="<?php _e('Save Settings') ?>" /></td>
      </tr>
    </table>
  </div>
</div>


<div class="sirv-miscellaneous-wrapper">
  <h2>Miscellaneous</h2>
  <div class="sirv-optiontable-holder">
    <table class="optiontable form-table">
      <tr>
        <th>
          <label>Include Sirv JS</label>
        </th>
        <td>
          <label><input type="radio" name="SIRV_JS" value="2" <?php checked(2, get_option('SIRV_JS'), true); ?>><b>Detect</b> - add script only to pages that require it.</label>
          <label><input type="radio" name="SIRV_JS" value="1" <?php checked(1, get_option('SIRV_JS'), true); ?>><b>All pages</b> - always add script (select this if images are not loading).</label>
          <label><input type="radio" name="SIRV_JS" value="3" <?php checked(3, get_option('SIRV_JS'), true); ?>><b>No pages</b> - don't add script (may break shortcodes & responsive images).</label>
        </td>
      </tr>
      <tr>
        <th>
          <label>Custom CSS</label>
        </th>
        <td>
          <textarea class="sirv-font-monospace" name="SIRV_CUSTOM_CSS" placeholder="Example:
.here-is-a-style img {
  width: auto !important;
}" value="<?php echo get_option('SIRV_CUSTOM_CSS'); ?>" rows="4"><?php echo get_option('SIRV_CUSTOM_CSS'); ?></textarea>
          <span class="sirv-option-responsive-text">Add styles to fix any rendering conflicts caused by other CSS.</span>
        </td>
      </tr>
      <tr>
        <th>
        </th>
        <td><input type="submit" name="submit" class="button-primary sirv-save-settings" value="<?php _e('Save Settings') ?>" /></td>
      </tr>
    </table>
  </div>
</div>
