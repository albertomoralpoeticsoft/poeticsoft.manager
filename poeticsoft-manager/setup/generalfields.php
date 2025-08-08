<?php

add_filter(
  'admin_init', 
  function () {

    $fields = [

      'gemini_url' => [
        'title' => 'Gemini URL',
        'value' => ''
      ],

      'gemini_model' => [
        'title' => 'Gemini Model',
        'value' => ''
      ],

      'gemini_apikey' => [
        'title' => 'Gemini Api Key',
        'value' => ''
      ],

      'openai_url' => [
        'title' => 'Openai Url',
        'value' => ''
      ],

      'datetime_zone' => [
        'title' => 'Zona Horaria',
        'value' => 'Europe/Madrid'
      ],

      'openai_token' => [
        'title' => 'Openai Auth Token',
        'value' => ''
      ],

      'telegram_botid' => [
        'title' => 'Telegram Botid',
        'value' => ''
      ],

      'telegram_token' => [
        'title' => 'Telegram Bot Token',
        'value' => ''
      ],

      'telegram_destinations' => [
        'title' => 'Telegram Destinations (Lista: [dest_name_a: id, dest_name_b: id, ...]), name without spaces!',
        'value' => '',
        'type' => 'textarea'
      ],

      'instagram_appsecret' => [
        'title' => 'Instagram App Secret',
        'value' => 'c61e8ba5775455060c4f9cf0bfa7151c'
      ],

      'instagram_appid' => [
        'title' => 'Instagram App Id',
        'value' => '2246765302448565'
      ],

      'instagram_accesstoken' => [
        'title' => 'Instagram access token',
        'value' => 'EAA2DzOQNZAlgBPCh87OZCZCY6S0n52IV6ZClsY9zgAUpPAt93Ok4JDLqpxDQS2hbjNDpuFj3VaQnUNUw0uVY4yC3PAwU8PHEpcsmHjI90bOZB4FiS4nLhFOGRHASaOek4BZBO0YZCzHhWnIY0rloPEwGPPdIrNk9SGZC6IkmnSZCwfFgRfutZCtBAoZBKAJJqAlv7eCCHI6vkU9tGzgXD1SLpQrnDePiGwS76izNJif'
      ],

      'instagram_verifytoken' => [
        'title' => 'Instagram verify token',
        'value' => 'une promenade dans les nuages'
      ],

      'instagram_userid' => [
        'title' => 'Instagram User Id',
        'value' => '17841407559893061'
      ],

      'instagram_facebook_pageid' => [
        'title' => 'Instagram Facebook Page Id',
        'value' => '377257815962117'
      ],
    ];

    foreach($fields as $key => $field) {

      register_setting(
        'general', 
        'poeticsoft_settings_' . $key
      );

      add_settings_field(
        'poeticsoft_settings_' . $key, 
        '<label for="poeticsoft_settings_' . $key . '">' . 
          __($field['title']) .
        '</label>',
        function () use ($key, $field){

          $value = get_option('poeticsoft_settings_' . $key, $field['value']);

          if(isset($field['type'])) {

            if('checkbox' == $field['type']) {

              echo '<input type="checkbox" 
                           id="poeticsoft_settings_' . $key . '" 
                           name="poeticsoft_settings_' . $key . '" 
                           class="regular-text"
                           ' . ($value ? 'checked="chedked"' : '') . ' />';

            }

            if('number' == $field['type']) {

              echo '<input type="number" 
                           id="poeticsoft_settings_' . $key . '" 
                           name="poeticsoft_settings_' . $key . '" 
                           class="regular-text"
                           value="' . $value . '" />';

            } 

            if('textarea' == $field['type']) {

              echo '<textarea id="poeticsoft_settings_' . $key . '" 
                              name="poeticsoft_settings_' . $key . '" 
                              class="regular-text"
                              rows="4">' .
                              $value . 
                              '</textarea>';

            } 
            
          } else {

            echo '<input type="text" 
                         id="poeticsoft_settings_' . $key . '" 
                         name="poeticsoft_settings_' . $key . '" 
                         class="regular-text"
                         value="' . $value . '" />';
          }
        },
        'general'
      );  
    }  
  }
);

?>