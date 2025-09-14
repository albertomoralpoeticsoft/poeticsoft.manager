<?php

trait Poeticsoft_Manager_Trait_Admin_GeneralFields { 

  public function register_admin_generalfields() {

    // 17841477384731714
    // IGAASbQAplbwpBZAFA3dnZAVdXNRNi04bDJkclRISktxcEFLR3V5YURRazFRM1dxbmJ3ekRxSFp5a1NHNkZAZAVzJYM2RrcDZAxRVBhQ2RxcUsyeDRVUWtCT3JpZA1UyX3hoM3pvQnFRLWxTWnJnNTlQR1lmT1ZAkZAEhlOFVDaTFPRmFNVQZDZD
    
    add_filter(
      'admin_init', 
      function () {

        $fields = [ 
          // 'devfacebook_app_id' => [
          //   'title' => 'Dev Facebook APP Id',
          //   'value' => '17841477384731714'
          // ],
        ];

        foreach($fields as $key => $field) {

          register_setting(
            'general', 
            'poeticsoft_manager_settings_' . $key
          );

          add_settings_field(
            'poeticsoft_manager_settings_' . $key, 
            '<label for="poeticsoft_manager_settings_' . $key . '">' . 
              __($field['title']) .
            '</label>',
            function () use ($key, $field){

              $value = get_option('poeticsoft_manager_settings_' . $key, $field['value']);

              if(isset($field['type'])) {

                if('checkbox' == $field['type']) {

                  echo '<input type="checkbox" 
                              id="poeticsoft_manager_settings_' . $key . '" 
                              name="poeticsoft_manager_settings_' . $key . '" 
                              class="regular-text"
                              ' . ($value ? 'checked="chedked"' : '') . ' />';

                }

                if('number' == $field['type']) {

                  echo '<input type="number" 
                               id="poeticsoft_manager_settings_' . $key . '" 
                               name="poeticsoft_manager_settings_' . $key . '" 
                               class="regular-text"
                               value="' . $value . '" />';

                } 

                if('textarea' == $field['type']) {

                  echo '<textarea id="poeticsoft_manager_settings_' . $key . '" 
                                  name="poeticsoft_manager_settings_' . $key . '" 
                                  class="regular-text"
                                  rows="4">' .
                                  $value . 
                                  '</textarea>';

                } 
                
              } else {

                echo '<input type="text" 
                             id="poeticsoft_manager_settings_' . $key . '" 
                             name="poeticsoft_manager_settings_' . $key . '" 
                             class="regular-text"
                             value="' . $value . '" />';
              }
            },
            'general'
          );  
        }  
      }
    );
  }
}