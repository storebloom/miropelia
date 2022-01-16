<?php
defined('ABSPATH') or die('No script kiddies please!');

include_once "option.generator.class.php";

class Woo_options extends Options_generator{

  protected static function render_sirv_content_cache($option){
    $without_content = 0;
    $with_content = 0;

    if (isset($option['data_provider']) && !empty($option['data_provider'])) {
      $cache_data = call_user_func($option['data_provider']);
      $without_content = (int) $cache_data['empty'] + (int) $cache_data['missing'];
      $with_content = (int) $cache_data['all'] - $without_content;

      $option['values'][0]['label'] = $option['values'][0]['label'] . ' (<span class="' . $option['option_name'] . '-' . $option['values'][0]['attrs']['value'] . '">' . $with_content . '</span>)';
      $option['values'][1]['label'] = $option['values'][1]['label'] . ' (<span class="' . $option['option_name'] . '-' . $option['values'][1]['attrs']['value'] . '">' . $without_content . '</span>)';
    }


    $html = '
    <tr>
      ' . self::render_option_title($option['label']) . '
      <td>
        ' . self::render_radio_component($option) . '
      </td>
    </tr>
    <tr>
      <th></th>
      <td>
        <input type="button" name="' . $option['option_name'] . '" class="button-primary ' . $option['button_class'] . '" value="' . $option['button_val'] . '">&nbsp;
        <span class="sirv-traffic-loading-ico" style="display: none;"></span>
        <span class="sirv-show-empty-view-result" style="display: none;"></span>
      </td>
    </tr>
    <tr>
    <th></th>
      <td style="color: #666666;">
          Content found in your Sirv folders is cached.
          If you see outdated content in a product
          gallery, clear the cache.
      </td>
    </tr>';

    return $html;
  }
}

?>
