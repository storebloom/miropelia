<?php

defined('ABSPATH') or die('No script kiddies please!');

class Exclude{
  public static function excludeSirvContent($currentPath, $excludeType){
    //$excludeType SIRV_EXCLUDE_FILES SIRV_EXCLUDE_PAGES
    //sirv_debug_msg($currentPath);
    $excludeInput = get_option($excludeType);
    if($excludeType == 'SIRV_EXCLUDE_FILES'){
      $currentPath = self::clearCurrentPath($currentPath);
    }
    if( !isset($excludeInput) || empty($excludeInput) ) return false;

    $excludePaths = self::parseExcludePaths($excludeInput);

    return self::loop($excludePaths, $currentPath);

  }


  public static function parseExcludePaths($excludePaths){
    return preg_split('/\r\n|[\r\n]/', trim($excludePaths));
  }


  protected static function clearCurrentPath($currentPath){
    return preg_replace('/-[0-9]{1,}(?:x|&#215;)[0-9]{1,}/is', '', $currentPath);
  }


  protected static function convertExcludeStrToRegEx($excludeStr){
    return str_replace('\*', '.*', preg_quote($excludeStr, '/'));
  }

  protected static function loop($excludePaths, $currentPath){
    for ($i=0; $i < count($excludePaths); $i++) {
      $expression = self::convertExcludeStrToRegEx($excludePaths[$i]);
      //sirv_debug_msg($expression);
      $result = self::check($currentPath, $expression);
      if($result) return true;
    }

    return false;
  }

  protected static function check($path, $expression){
    return preg_match('/' . $expression . '/', $path) != false;
  }
}

?>
