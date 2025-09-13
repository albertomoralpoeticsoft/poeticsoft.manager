<?php

trait Poeticsoft_Manager_Trait_Admin { 

  public function register_admin() {

    add_action(
      'admin_enqueue_scripts',
      function () {

        wp_enqueue_script(
          'poeticsoft-manager-admin',
          self::$url . 'admin/main.js',
          [        
            'jquery'
          ],
          filemtime(self::$dir . 'admin/main.js'),
          true
        );

        wp_enqueue_style(
          'poeticsoft-manager-admin',
          self::$url . 'admin/main.css',
          [
            
          ],
          filemtime(self::$dir . 'admin/main.css')
        );
      }
    );
  }
}