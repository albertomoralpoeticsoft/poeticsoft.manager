<?php

function poeticsoft_manager_partner_data(WP_REST_Request $req) {

  $res = new WP_REST_Response();

  try {

    $res->set_data([
      'partnerid' => 'partner_id',
      'partnername' => 'partner_name'
    ]);
    
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
      'poeticsoft/manager',
      'partner/data',
      [
        [
          'methods'  => 'GET',
          'callback' => 'poeticsoft_manager_partner_data',
          'permission_callback' => '__return_true'
        ]
      ]
    );
  }
);