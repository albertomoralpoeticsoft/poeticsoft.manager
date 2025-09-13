<?php

trait Poeticsoft_Manager_Trait_Admin_GeneralFields { 

  public function register_admin_generalfields() {

    add_filter(
      'admin_init', 
      function () {

        $fields = [          

          // 'key' => [
          //   'title' => 'Key',
          //   'value' => '',
          //   'type' => ''
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