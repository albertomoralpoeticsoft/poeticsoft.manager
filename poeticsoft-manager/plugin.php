<?php

/**
 *
 * Plugin Name: Poetic Soft Manager
 * Plugin URI: http://poeticsoft.com/plugins/poeticsoft-manager
 * Description: Manager for Poeticsoft Partners
 * Version: 0.00
 * Author: Poeticsoft Team
 * Author URI: http://poeticsoft.com/team
 */

require_once __DIR__ . '/class/poeticsoft-manager.php';

function poeticsoft_manager_init() {
  
  return Poeticsoft_Manager::get_instance();
}
poeticsoft_manager_init();