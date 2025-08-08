<?php

/**
 *
 * Plugin Name: poeticsoft-manager
 * Plugin URI: http://poeticsoft.com/plugins/poeticsoft-manager
 * Description: Poeticsoft Manager Plugin
 * Version: 0.00
 * Author: Poeticsoft Team
 * Author URI: http://poeticsoft.com/team
 */

function plugin_log($display, $withdate = false) { 

  $text = is_string($display) ? 
  $display 
  : 
  json_encode($display, JSON_PRETTY_PRINT);

  $message = $withdate ? 
  date("d-m-y h:i:s") . PHP_EOL
  :
  '';

  $message .= $text . PHP_EOL;

  file_put_contents(
    WP_CONTENT_DIR . '/plugin_log.txt',
    $message,
    FILE_APPEND
  );
}

function plugin_cron($text) { 

  $message = date("d-m-y h:i:s") . PHP_EOL .
  $text . PHP_EOL;

  file_put_contents(
    WP_CONTENT_DIR . '/plugin_cron.txt',
    $message,
    FILE_APPEND
  );
}
  
require_once(dirname(__FILE__) . '/setup/main.php'); 
require_once(dirname(__FILE__) . '/api/main.php');
require_once(dirname(__FILE__) . '/block/main.php'); 
require_once(dirname(__FILE__) . '/settingspanel/main.php'); 

register_activation_hook(__FILE__, 'poeticsoft_assistant_init');