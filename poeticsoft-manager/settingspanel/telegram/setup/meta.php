<?php

add_action(
  'init', 
  function() {

    register_post_meta(
      'post', 
      'poeticsoft_post_publish_telegram_active', 
      [
        'show_in_rest' => true,
        'single'       => true,
        'type'         => 'boolean',
        'auth_callback' => '__return_true'
      ] 
    );

    register_post_meta(
      'post', 
      'poeticsoft_post_publish_telegram_publishrules', 
      [
        'show_in_rest' => true,
        'single'       => true,
        'type'         => 'string',
        'auth_callback' => '__return_true'
      ] 
    );

    register_post_meta(
      'post', 
      'poeticsoft_post_publish_telegram_publishcalendar', 
      [
        'show_in_rest' => true,
        'single'       => true,
        'type'         => 'string',
        'auth_callback' => '__return_true'
      ] 
    );
  }
);