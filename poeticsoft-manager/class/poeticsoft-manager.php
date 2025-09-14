<?php

require_once __DIR__ . '/traits/trait-utils.php';
require_once __DIR__ . '/traits/trait-admin.php';
require_once __DIR__ . '/traits/trait-admin-generalfields.php';
require_once __DIR__ . '/traits/trait-partner.php';
require_once __DIR__ . '/traits/trait-partner-data.php';
require_once __DIR__ . '/traits/trait-partner-avatar.php';
require_once __DIR__ . '/traits/trait-partner-api.php';
require_once __DIR__ . '/traits/trait-meta.php';
require_once __DIR__ . '/traits/trait-meta-api.php';

class Poeticsoft_Manager {  

  use Poeticsoft_Manager_Trait_Utils;
  use Poeticsoft_Manager_Trait_Admin;
  use Poeticsoft_Manager_Trait_Admin_GeneralFields;
  use Poeticsoft_Manager_Trait_Partner;
  use Poeticsoft_Manager_Trait_Partner_Data;
  use Poeticsoft_Manager_Trait_Partner_Avatar;
  use Poeticsoft_Manager_Trait_Partner_API;
  use Poeticsoft_Manager_Trait_Meta;
  use Poeticsoft_Manager_Trait_Meta_API;

  private static $instance = null;
  public static $dir;
  public static $url;

  public static function get_instance() {

    if (self::$instance === null) {

      self::$instance = new self();
    }

    return self::$instance;
  }

  private function __construct() {

    $this->set_vars();

    $this->register_admin();
    $this->register_admin_generalfields();
    $this->register_partner();
    $this->register_partner_avatar();
    $this->register_partner_data();
    $this->register_partner_api();
    $this->register_meta();
    $this->register_meta_api();
  }    

  private function set_vars() {

    self::$dir = WP_PLUGIN_DIR . '/poeticsoft-manager/';
    self::$url = WP_PLUGIN_URL . '/poeticsoft-manager/';
  }
}
