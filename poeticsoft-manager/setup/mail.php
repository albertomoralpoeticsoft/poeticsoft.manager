<?php

add_action(
  'phpmailer_init', 
  function($phpmailer) {

    $phpmailer->isSMTP();
    $phpmailer->Host = 'smtp.mail.ovh.net';
    $phpmailer->SMTPAuth = true;
    $phpmailer->Port = 465;
    $phpmailer->Username = 'partners@poeticsoft.com';
    $phpmailer->Password = 'JsAU8)0987654';
    $phpmailer->SMTPSecure = 'ssl';
    $phpmailer->From = 'partners@poeticsoft.com';
    $phpmailer->FromName = 'Poeticsoft Partners';    
    $phpmailer->isHTML(true);
  }
);

add_action(
  'wp_mail_failed',
  function ($wp_error) {

    plugin_log('wp_mail_failed');
    plugin_log(json_encode($wp_error));
  } ,
  10, 
  1 
);