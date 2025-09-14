<?php

// https://developer.wordpress.org/rest-api/extending-the-rest-api/adding-custom-endpoints/

trait Poeticsoft_Manager_Trait_Meta_API {  

  public function register_meta_api() {

    add_action(
      'rest_api_init',
      function () {

        register_rest_route(
          'poeticsoft/manager',
          'meta/verify',
          array(
            array(
              'methods'  => 'GET',
              'callback' => [$this, 'api_meta_verify'],
              'permission_callback' => [$this, 'auth']
            )
          )
        );
      }
    );
  }  
    
  public function api_meta_verify(WP_REST_Request $req) {

    $hub_mode = $req->get_param('hub_mode');
    $hub_challenge = $req->get_param('hub_challenge');
    $hub_verify_token = $req->get_param('hub_verify_token');

    $verify_token = 'poeticsoft-meta-verified';

    if ($hub_verify_token === $verify_token) {

      return new WP_REST_Response($hub_challenge, 200);

    } else {

      return new WP_REST_Response('Error: token inválido', 403);
    }
  }
}