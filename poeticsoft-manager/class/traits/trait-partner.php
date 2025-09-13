<?php

trait Poeticsoft_Manager_Trait_Partner {  

  public function register_partner() {    

    add_action('admin_init', function () {

      remove_all_actions('show_user_profile');
      remove_all_actions('edit_user_profile');
    });

    add_action( 
      'admin_head', 
      function () {

        remove_action(
          'admin_color_scheme_picker', 
          'admin_color_scheme_picker' 
      );
      }
    );    
  }
}