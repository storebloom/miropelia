<?php
$storageInfo = sirv_getStorageInfo();
?>

<h2>Synchronization</h2>
<p class="sirv-options-desc">Copy your WordPress media library to Sirv, for supreme optimization and fast CDN delivery.</p>
<div class="sirv-optiontable-holder">
  <table class="optiontable form-table">
    <?php if (get_option('SIRV_ENABLE_CDN') != 1) { ?>
      <tr>
        <th class="no-padding" colspan="2">
          <div class="sirv-message warning-message">
            <span style="font-size: 15px;font-weight: 800;">Note:</span> <a class="sirv-show-account-tab">network status</a> is currently Disabled.
          </div>
        </th>
      </tr>
    <?php } ?>
    <tr>
      <th class="sirv-sync-messages no-padding" colspan="2">
        <?php if ($error) echo '<div id="sirv-sync-message" class="sirv-message error-message">' . $error . '</div>'; ?>
      </th>
    </tr>
    <tr>
      <td colspan="2">
        <h3>Status</h3>
        <p class="sirv-options-desc">Images are copied to Sirv the first time they are viewed, which can take 1-2 seconds per image. To perform a full synchronization now, click Sync images:</p>
      </td>
    </tr>
    <tr class="small-padding">
      <th colspan="2">
        <div class="sirv-sync-images-progress-block">
          <div class="sirv-progress">
            <div class="sirv-progress__text">
              <div class="sirv-progress__text--percents"><?php echo $cacheInfo['progress'] . '%'; ?></div>
              <div class="sirv-progress__text--complited"><span><?php echo $cacheInfo['q_s'] . ' out of ' . $cacheInfo['total_count_s']; ?></span> images completed</div>
            </div>
            <!-- <div class="sirv-progress__bar <?php if ($isSynced) echo 'sirv-failed-imgs-bar'; ?>"> -->
            <div class="sirv-progress__bar">
              <div class="sirv-progress__bar--line-complited sirv-complited" style="width: <?php echo $cacheInfo['progress_complited'] . '%;'; ?>"></div>
              <div class="sirv-progress__bar--line-queued sirv-queued" style="width: <?php echo $cacheInfo['progress_queued'] . '%;'; ?>"></div>
              <div class="sirv-progress__bar--line-failed sirv-failed" style="width: <?php echo $cacheInfo['progress_failed'] . '%;'; ?>"></div>
            </div>
          </div>
          <?php if (!$isMuted) { ?>
            <div class="sirv-sync-button-container">
              <input type="button" name="sirv-sync-images" class="button-primary sirv-initialize-sync" value="<?php echo $sync_button_text; ?>" <?php echo $is_sync_button_disabled; ?> />
            </div>
          <?php } ?>
        </div>
        <table class="sirv-progress-data">
          <tbody>
            <tr>
              <td>
                <div class="sirv-progress-data__label sirv-complited"></div>
              </td>
              <td>Synced</td>
              <td>
                <div class="sirv-progress-data__complited--text"><?php echo $cacheInfo['q_s']; ?></div>&nbsp;&nbsp;&nbsp;
                <div class="sirv-progress-data__complited--size"><?php echo $cacheInfo['size_s']; ?></div>
              </td>
            </tr>
            <tr>
              <td>
                <div class="sirv-progress-data__label sirv-queued"></div>
              </td>
              <td>Queued</td>
              <td>
                <div class="sirv-progress-data__queued--text"><?php echo $cacheInfo['queued_s']; ?></div>
              </td>
              <td></td>
            </tr>
            <tr>
              <td>
                <div class="sirv-progress-data__label sirv-failed"></div>
              </td>
              <td>Failed</td>
              <td>
                <div class="sirv-progress-data__failed--text"><?php echo $cacheInfo['FAILED']['count_s']; ?></div>&nbsp;&nbsp;&nbsp;
                <div class="failed-images-block" style="<?php echo $is_show_failed_block; ?>">
                  <span class=" sirv-traffic-loading-ico" style="display: none;"></span><a href="#">Show</a>
                </div>
              </td>
            </tr>
          </tbody>
        </table>
      </th>
    </tr>
    <tr class="sync-errors-wrap">
      <th colspan="2">
        <div class="sync-errors">
          <table style="width:830px;" class="optiontable form-table sirv-form-table">
            <thead>
              <tr>
                <td style="width: 65%;"><b>Error message</b></td>
                <td><b>Count</b></td>
                <td></td>
              </tr>
            </thead>
            <tbody class='sirv-fetch-errors'></tbody>
          </table>
        </div>
      </th>
    </tr>
    <?php if (!$isMuted) {
      $fetch_limit = isset($storageInfo['limits']['fetch:file']['limit']) ? $storageInfo['limits']['fetch:file']['limit'] : 2000;
    ?>
      <tr class="sirv-processing-message" style='display: none;'>
        <td colspan="2">
          <span class="sirv-traffic-loading-ico"></span><span class="sirv-queue">Processing (1/3): calculating folders...</span>
          <p style="margin: 10px 0 !important; font-weight: bold; color: #8a6d3b;">
            Keep this page open until synchronisation reaches 100%. Your account can sync <?php echo $fetch_limit; ?> images per hour (<a target="_blank" href="admin.php?page=sirv/sirv/submenu_pages/account.php">check current usage</a>).
            If sync stops, refresh this page and resume the sync.
            </?php>
        </td>
      </tr>
      <tr class='sirv-resync-block' style="<?php echo $is_show_resync_block; ?>">
        <td colspan="2">
          <span>
            <h2>Re-Synchronize</h2>
          </span>
        </td>
      </tr>
      <?php
      $g_disabled = $isGarbage ? '' : 'disabled';
      $g_checked = $isGarbage ? 'checked' : '';
      $g_show = $isGarbage ? '' : 'style="display: none;"';
      $g_dis_class = $isGarbage ? '' : 'sirv-dis-text';
      $f_disabled = $isFailed ? '' : 'disabled';
      $f_dis_class = $isFailed ? '' : 'sirv-dis-text';
      $f_checked = $isFailed ? 'checked' : '';
      $a_checked = !$isFailed ? 'checked' : '';
      ?>
      <tr class="sirv-discontinued-images" <?php echo $g_show; ?>>
        <td class="no-padding" colspan="2">
          <div class="sirv-message warning-message">
            <span style="font-size: 15px;font-weight: 800;">Recommendation:</span> <span class="sirv-old-cache-count"><?php echo $cacheInfo['garbage_count'] ?></span> images in plugin database no longer exist.&nbsp;&nbsp;
            <input type="button" name="optimize_cache" class="button-primary optimize-cache" value="Clean up" />&nbsp;
            <span class="sirv-traffic-loading-ico" style="display: none;"></span>
          </div>
        </td>
      </tr>
      <tr class="sirv-resync-block small-padding" style="<?php echo $is_show_resync_block; ?>">
        <td colspan="2">
          <label class="sirv-ec-failed-item <?php echo $f_dis_class; ?>">
            <input type="radio" name="empty_cache" value="failed" <?php echo $f_disabled . ' ' . $f_checked; ?>>Failed images (<?php echo $cacheInfo['FAILED']['count_s']; ?>)
          </label>
          <br>
          <label class="sirv-ec-all-item">
            <input type="radio" name="empty_cache" value="all" <?php echo $a_checked; ?>>All images (<?php echo ($cacheInfo['total_count'] - $cacheInfo['queued']) ?>)
          </label>
        </td>
      </tr>
      <tr class="sirv-resync-block sirv-resync-button-block" style="<?php echo $is_show_resync_block; ?>">
        <td>
          <input type="button" name="empty_cache" class="button-primary empty-cache" value="Empty cache" />&nbsp;
          <span class="sirv-traffic-loading-ico" style="display: none;"></span>
        </td>
      </tr>
    <?php } ?>
    <!-- <tr>
            <th></th>
            <td>
              <input type="button" name="tst" class="button-primary tst" value="Test" />
            </td>
          </tr> -->
  </table>
</div>
<!-- Diff settings block -->
<div class="sirv-optiontable-holder sirv-sync-diff-settings-wrapper">
  <table class="optiontable form-table">
    <tbody>
      <tr>
        <td colspan="2">
          <h3>Image deletion</h3>
          <!-- <p class="sirv-options-desc">Some description here</p> -->
        </td>
      </tr>
      <tr>
        <th>
          <label>Auto-delete from Sirv</label>
        </th>
        <td>
          <label>
            <input type="radio" name="SIRV_DELETE_FILE_ON_SIRV" value='1' "<?php checked('1', get_option('SIRV_DELETE_FILE_ON_SIRV'), true);  ?>">Enable
          </label><br>
          <label>
            <input type="radio" name="SIRV_DELETE_FILE_ON_SIRV" value='2' "<?php checked('2', get_option('SIRV_DELETE_FILE_ON_SIRV'), true);  ?>">Disable
          </label><br>
          <span class="sirv-option-responsive-text">If image deleted from WordPress Media Library, delete from Sirv.</span>
        </td>
      </tr>
      <tr>
        <th></th>
        <td>
          <input type="submit" name="submit" class="button-primary sirv-save-settings" value="<?php _e('Save settings') ?>" />
        </td>
      </tr>
    </tbody>
  </table>
</div>
<?php
$css_sync_data = json_decode(get_option('SIRV_CSS_BACKGROUND_IMAGES_SYNC_DATA'), true);
$scan_type = isset($css_sync_data['scan_type']) ? $css_sync_data['scan_type'] : 'theme';
$custom_path = isset($css_sync_data['custom_path']) ? $css_sync_data['custom_path'] : '';
$isCustomPathShow = $scan_type == 'custom' ? ' style="display: table-row;"' : '';
$images_info = sirv_show_css_images_info($css_sync_data);
$skip_images_str = '';
$hide_skip_data_block = 'style="display:none;"';
if (!empty($images_info['skip_data'])) {
  $skip_images_str = sirv_skipped_images_to_str($css_sync_data);
  $hide_skip_data_block = '';
}
?>
<div class="sirv-optiontable-holder sirv-sync-css-images-wrapper">
  <table class="optiontable form-table">
    <tbody>
      <tr>
        <td colspan="2">
          <h3>Sync CSS images<sup><span style="color: orange;">beta</span></sup></h3>
          <p class="sirv-options-desc">Use Sirv to deliver any CSS background images located on your domain.</p>
        </td>
      </tr>
      <tr>
        <th>
          <label>CSS location</label>
        </th>
        <td>
          <label class="">
            <input class="sirv-custom-backcss-path-rb" type="radio" name="css_location" value="theme" <?php checked('theme', $scan_type, true) ?>><b>Active theme</b> - your CSS is normally part of your theme.
          </label>
          <br>
          <label class="sirv-ec-all-item">
            <input class="sirv-custom-backcss-path-rb" type="radio" name="css_location" value="custom" <?php checked('custom', $scan_type, true) ?>><b>Folder</b> - enter path to your CSS, if outside your theme.
          </label>
        </td>
      </tr>
      <tr class="sirv-custom-backcss-path-text-tr" <?php echo $isCustomPathShow; ?>>
        <th></th>
        <td colspan="2">
          <div class="sirv-custom-backcss-path-text-wrap">
            <div>
              <input type="text" name="" id="sirv-custom-backcss-path-text" value="<?php echo $custom_path; ?>" placeholder="Enter path to CSS folder">
              <span class="sirv-input-const-text"><?php echo wp_normalize_path(ABSPATH); ?></span>
            </div>
          </div>
        </td>
      </tr>
      <tr>
        <th></th>
        <td colspan="2">
          <input type="button" name="sync_css" class="button-primary sync-css" value="Scan CSS for images" />&nbsp;
          <span class="sirv-traffic-loading-ico" style="display: none;"></span>
          <span class="sirv-show-empty-view-result" style="display: none;"></span>
        </td>
      </tr>
      <tr>
        <td colspan="2">
          <table class="sirv-css-images-sync-data small-padding" style="width: 100%;">
            <tbody>
              <tr>
                <th>
                  <label>Scanned theme/folder</label>
                </th>
                <td>
                  <span class="sirv-css-sync-data-theme"><?php echo $css_sync_data['theme']; ?></span>
                </td>
              </tr>
              <tr>
                <th>
                  <label>Image domain</label>
                </th>
                <td>
                  <span class="sirv-css-sync-data-domain"><?php echo $css_sync_data['img_domain']; ?></span>
                </td>
              </tr>
              <tr>
                <th>
                  <label>Last scan</label>
                </th>
                <td>
                  <span class="sirv-css-sync-data-date"><?php echo $css_sync_data['last_sync_str']; ?></span>
                </td>
              </tr>
              <tr>
                <th>
                  <label>Images found</label>
                </th>
                <td>
                  <!-- <span class="sirv-css-sync-data-img-count"><?php echo $css_sync_data['img_count']; ?></span> -->
                  <span class="sirv-css-sync-data-img-count"><?php echo $images_info['sync_data']; ?></span>
                  <div class="sirv-skipped-images-wrap" <?php echo $hide_skip_data_block; ?>>
                    <span class="sirv-css-sync-data-img-count-skipped">
                      <?php echo $images_info['skip_data']; ?>
                    </span>
                    <div class="sirv-tooltip">
                      <i class="dashicons dashicons-editor-help sirv-tooltip-icon"></i>
                      <span class="sirv-tooltip-text sirv-no-select-text">Images were either:<br>
                        - On another domain<br>
                        - Inaccessible<br>
                        - Link couldn't be followed</span>
                    </div>
                    <a <?php echo $hide_skip_data_block; ?> class="sirv-hide-show-a sirv-show-skip-data-list" data-status="false" data-selector=".sirv-skip-images-list" data-show-msg="Show list" data-hide-msg="Hide list" data-icon-show="dashicons dashicons-arrow-right-alt2" data-icon-hide="dashicons dashicons-arrow-down-alt2">
                      <span class="dashicons dashicons-arrow-right-alt2"></span>
                      Show list
                    </a>
                  </div>
                </td>
              </tr>
              <tr>
                <td colspan="3">
                  <textarea class="sirv-font-monospace sirv-skip-images-list" value="<?php echo $skip_images_str; ?>" rows="5" readonly><?php echo $skip_images_str; ?></textarea>
                </td>
              </tr>
            </tbody>
          </table>
        </td>
      </tr>
      <tr>
        <td colspan="2">
          <p style="margin-bottom: 5px;">All images found will be served by Sirv. <a class="sirv-hide-show-a" data-status="false" data-selector=".sirv-css-sync-bg-img-txtarea-wrap" data-show-msg="Show CSS code" data-hide-msg="Hide CSS code" data-icon-show="dashicons dashicons-arrow-right-alt2" data-icon-hide="dashicons dashicons-arrow-down-alt2"><span class="dashicons dashicons-arrow-right-alt2"></span>Show CSS code</a></p>
          <div class="sirv-css-sync-bg-img-txtarea-wrap">
            <textarea class="sirv-font-monospace" name="SIRV_CSS_BACKGROUND_IMAGES" rows="10" value="<?php echo htmlspecialchars(get_option('SIRV_CSS_BACKGROUND_IMAGES')); ?>"><?php echo get_option('SIRV_CSS_BACKGROUND_IMAGES'); ?></textarea>
            <input type="submit" name="submit" class="sirv-save-css-code button-primary sirv-save-settings" value="<?php _e('Save CSS code') ?>" disabled />
          </div>
        </td>
      </tr>
    </tbody>
  </table>
</div>
<div class="sirv-optiontable-holder sirv-sync-exclude-images-wrapper">
  <table class="optiontable form-table">
    <tbody>
      <tr>
        <td colspan="2">
          <h3>Exclude images from Sirv</h3>
          <p class="sirv-options-desc">If there are images you don't want Sirv to serve, list them below. They could be specific images or entire pages.</p>
        </td>
      </tr>
      <tr>
        <th>
          <label>Exclude files/folders</label>
        </th>
        <td>
          <span>Files that should not served by Sirv:</span>
          <textarea class="sirv-font-monospace" name="SIRV_EXCLUDE_FILES" value="<?php echo get_option('SIRV_EXCLUDE_FILES'); ?>" rows="5" placeholder="e.g.
/wp-content/plugins/a-plugin/*.png
/wp-content/uploads/2021/04/an-image.jpg"><?php echo get_option('SIRV_EXCLUDE_FILES'); ?></textarea>
          <span class="sirv-option-responsive-text">
            You can enter full URLs and the domain will be stripped.<br>
            Use * to specify all files at a certain path.
          </span>
        </td>
      </tr>
      <tr>
        <th>
          <label>Exclude pages</label>
        </th>
        <td>
          <span>Web pages that should not have files served by Sirv:</span>
          <textarea class="sirv-font-monospace" name="SIRV_EXCLUDE_PAGES" value="<?php echo get_option('SIRV_EXCLUDE_PAGES'); ?>" rows="5" placeholder="e.g.
/example/particular-page.html
/a-whole-section/*"><?php echo get_option('SIRV_EXCLUDE_PAGES'); ?></textarea>
          <span class="sirv-option-responsive-text">
            You can enter full URLs and the domain will be stripped.<br>
            Use * to specify all pages at a certain path.
          </span>
        </td>
      </tr>
      <tr>
        <th></th>
        <td>
          <input type="submit" name="submit" class="button-primary sirv-save-settings" value="<?php _e('Save settings') ?>" />
        </td>
      </tr>
    </tbody>
  </table>
</div>
