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


  protected static function render_pin_gallery($option){
    $values = array(
      array(
        'label' => 'Unpinned',
        'check_data_type' => 'checked',
        'attrs' => array(
          'type' => 'radio',
          'value' => 'no',
        ),
      ),
      array(
        'label' => 'Left',
        'check_data_type' => 'checked',
        'attrs' => array(
          'type' => 'radio',
          'value' => 'left',
        ),
      ),
      array(
        'label' => 'Right',
        'check_data_type' => 'checked',
        'attrs' => array(
          'type' => 'radio',
          'value' => 'right',
        ),
      ),
    );

    $radio_data = array(
      'Pin video(s)' => array(
        'option_name' => 'sirv-woo-pin-video',
        'value' => '',
        'check_value' => 'video',
        'values' => $values,
    ),
      'Pin spin(s)' => array(
        'option_name' => 'sirv-woo-pin-spin',
        'value' => '',
        'check_value' => 'spin',
        'values' => $values,
      ),
      'Pin images by file mask' => array(
        'option_name' => 'sirv-woo-pin-image',
        'value' => '',
        'check_value' => 'image',
        'values' => $values,
      ),
    );

    $option_data = json_decode($option['value'], true);
    $option['attrs']['value'] = esc_attr($option['value']);

    $input_data = array(
      'attrs' => array(
        'type' => 'text',
        'placeholder' => 'e.g. *-hero.jpg ',
        'value' => $option_data['image_template'],
        'id' => 'sirv-woo-pin-input-template',
      ),
    );

    $radio_html = '<table class="sirv-woo-pin-table-radio"><tbody>';
    foreach ($radio_data as $radio_name => $radio_item) {
      foreach ($radio_item['values'] as $index => $sub_option) {
        //cheking if option checked, readonly, disabled etc for multiple options like radio and added param to attrs.
        $radio_item['values'][$index] = self::check_option($sub_option, $option_data[$radio_item['check_value']]);

        if (!isset($sub_option['attrs']['name'])) {
          $radio_item['values'][$index]['attrs']['name'] = $radio_item['option_name'];
        }
      }

      $radio_html .= "<tr><th>$radio_name</th><td>" . self::render_radio_component($radio_item) . '</td></tr>' . PHP_EOL;
    }
    $radio_html .= '</tbody></table>';

    $above_text = (isset($option['above_text']) && $option['above_text']) ? self::render_above_text($option['above_text']) : '';
    $is_img_input_hide = $option_data['image'] == 'no' ? 'sirv-block-hide ' : '';

    $html = '
      <tr>
        ' . self::render_option_title($option['label']) .'
        <td>
        ' . $above_text . '<br>
        '. $radio_html . '
        <div class="'. $is_img_input_hide .'sirv-woo-pin-input-wrapper">
          '. self::render_text_component($input_data) .'
          '. self::render_below_text('Filenames matching this pattern will be pinned. Use * as a wildcard.') .'
        </div>
        '. self::render_hidden_component($option) .'
        </td>
      </tr>';

    return $html;
  }


  protected static function render_sirv_smv_order_content($option){
    $above_text = (isset($option['above_text']) && $option['above_text']) ? self::render_above_text($option['above_text']) : '';
    $below_text = (isset($option['below_text']) && $option['below_text']) ? self::render_below_text($option['below_text']) : '';
    $option_data = json_decode($option['value']);
    //$option['attrs']['value'] = json_encode($option_data, JSON_HEX_APOS | JSON_HEX_QUOT);
    $option['attrs']['value'] = htmlspecialchars(json_encode($option_data), ENT_QUOTES, 'UTF-8');
    $select_items = array('spin' => 'Spin', 'video' => 'Video', 'zoom' => 'Zoom', 'image' => 'Image');
    $order_html = '';

    if(!empty($option_data)){
      foreach ($option_data as $index => $item) {
        $order_html .= '
          <li class="sirv-smv-order-item sirv-smv-order-item-changeble sirv-no-select-text" data-order="'. ($index + 1) .'">
            <div>
              <div class="sirv-smv-order-item-select">
                <select>
                  '. self::render_sirv_smv_order_content_select_options($select_items, $item) . '
                </select>
              </div>
              <div class="sirv-smv-order-item-order">'. ($index + 1) .'</div>
            </div>
          </li>
        ';
      }
    }

    $html =
    '<tr>
    ' . self::render_option_title($option['label']) . '
      <td>
      '. $above_text .'
        <div class="sirv-smv-order-content-wrapper">
          <ul id="sirv-smv-order-items">
            <li class="sirv-smv-order-item sirv-smv-order-add-item sirv-no-select-text">
              <div>
                <div class="sirv-smv-order-item__title">Add item</div>
              </div>
            </li>
            '. $order_html .'
          </ul>
        </div>
        ' . self::render_hidden_component($option) . '
        ' . $below_text . '
      </td>
    </tr>';

    return $html;
  }


  protected static function render_sirv_smv_order_content_select_options($options, $selected_val){
    $html = '';

    foreach ($options as $value => $name) {
      $selected = $value == $selected_val ? ' selected="selected" ' : '';
      $html .= '<option value="'. $value .'"'. $selected .'>'. $name .'</option>' . PHP_EOL;
    }

    return $html;

  }
}

?>
