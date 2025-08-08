<?php

function poeticsoft_instagram_app_webhook_get(WP_REST_Request $req) {

  $res = new WP_REST_Response();

  try {    

    $verifytoken = 'une promenade dans les nuages';    
    
    $params = $req->get_params();

    plugin_log('Instagram webhook callback get');
    plugin_log($params, false);
    
    $mode = $params['hub_mode'];
    $token = $params['hub_verify_token'];
    $challenge = $params['hub_challenge'];

    return new WP_REST_Response(
      $challenge, 
      200, 
      ['Content-Type' => 'text/plain']
    );

  } catch (Exception $e) {
    
    $res->set_status($e->getCode());
    $res->set_data($e->getMessage());
  }

  return $res;
}


function poeticsoft_instagram_app_webhook_post(WP_REST_Request $req) {

  $res = new WP_REST_Response();

  try {    
    
    $params = $req->get_params();

    plugin_log('Instagram webhook callback post');
    plugin_log($params, false);

    $res->set_data('une promenade dans les nuages');

  } catch (Exception $e) {
    
    $res->set_status($e->getCode());
    $res->set_data($e->getMessage());
  }

  return $res;
}

add_action(
  'rest_api_init',
  function () {

    register_rest_route(
      'poeticsoft/instagram',
      'app/webhook',
      array(
        array(
          'methods'  => 'GET',
          'callback' => 'poeticsoft_instagram_app_webhook_get',
          'permission_callback' => '__return_true'
        )
      )
    );

    register_rest_route(
      'poeticsoft/instagram',
      'app/webhook',
      array(
        array(
          'methods'  => 'POST',
          'callback' => 'poeticsoft_instagram_app_webhook_post',
          'permission_callback' => '__return_true'
        )
      )
    );
  }
);