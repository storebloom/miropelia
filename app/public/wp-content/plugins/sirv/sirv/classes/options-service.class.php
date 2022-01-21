<?php

    defined( 'ABSPATH' ) or die( 'No script kiddies please!' );

    class getValue{

        protected static $jsFile = 'https://scripts.sirv.com/sirvjs/v3/sirv.js';

        public static function getOption($optionName){
            $value = '';
            switch ($optionName) {
                case 'SIRV_AWS_HOST':
                    $value = 'http://' . get_option($optionName);
                    break;
                case 'SIRV_JS_FILE':
                    $value = self::$jsFile;
                    break;
                default:
                    $value = get_option($optionName);
                    break;
            }

            return $value;
        }
    }
?>
