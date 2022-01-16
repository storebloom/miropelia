<?php
  require_once(dirname(__FILE__) . '/../classes/options/woo.options.class.php');
  $options = include(dirname(__FILE__) . '/../data/options/woo.options.data.php');
  echo Woo_options::render_options($options);
?>
