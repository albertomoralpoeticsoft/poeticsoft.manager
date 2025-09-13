<?php

// https://developer.wordpress.org/rest-api/extending-the-rest-api/adding-custom-endpoints/

trait Poeticsoft_Manager_Trait_Partner_API {  

  public function register_partner_api() {

    add_action(
      'rest_api_init',
      function () {

        register_rest_route(
          'poeticsoft/manager',
          'partner',
          array(
            array(
              'methods'  => 'GET',
              'callback' => [$this, 'api_partner_list'],
              'permission_callback' => [$this, 'auth']
            )
          )
        );
      }
    );
  }  
    
  public function api_partner_list(WP_REST_Request $req) {
  
    $res = new WP_REST_Response();

    try { 
      
      $res->set_data('api_partner_list');

    } catch (Exception $e) {
      
      $res->set_status($e->getCode());
      $res->set_data($e->getMessage());
    }

    return $res;
  }
}