<?php

// https://developer.wordpress.org/rest-api/extending-the-rest-api/adding-custom-endpoints/
// https://developers.facebook.com/docs/instagram-platform/instagram-graph-api/reference/ig-user/media

trait Poeticsoft_Manager_Trait_Partner_API {  

  public function register_partner_api() {

    add_action(
      'rest_api_init',
      function () {

        register_rest_route(
          'poeticsoft/manager',
          'partner/list',
          array(
            array(
              'methods'  => 'GET',
              'callback' => [$this, 'api_partner_list'],
              'permission_callback' => [$this, 'auth']
            )
          )
        );

        register_rest_route(
          'poeticsoft/manager',
          'partner/(?P<id>\d+)',
          array(
            array(
              'methods'  => 'GET',
              'callback' => [$this, 'api_partner_byid'],
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

      $args = array(
        'role'   => 'subscriber',
        'meta_query' => array(
          array(
            'key'     => 'poeticsoft_manager_partner_active',
            'value'   => 'on',
            'compare' => '='
          )
        ),
        'fields' => array(
          'ID', 
          'user_login', 
          'user_email', 
          'display_name'
        )
      );

      $users = get_users($args);
      
      $data = array_map(function($user) {

        $all_meta = get_user_meta($user->ID);
        $filtered_meta = array();

        foreach($all_meta as $key => $value) {

          if(
            strpos($key, 'poeticsoft_manager_partner_') === 0
            &&
            'poeticsoft_manager_partner_active' != $key
          ) {

            $filtered_meta[$key] = maybe_unserialize($value[0]);
          }
        }

        return array(
            'id'           => $user->ID,
            'login'        => $user->user_login,
            'email'        => $user->user_email,
            'display_name' => $user->display_name,
            'data'         => $filtered_meta
        );

      }, $users);
      
      $res->set_data($data);

    } catch (Exception $e) {
      
      $res->set_status($e->getCode());
      $res->set_data($e->getMessage());
    }

    return $res;
  }
    
  public function api_partner_byid(WP_REST_Request $req) {
  
    $res = new WP_REST_Response();

    try { 

      $user_id = (int) $req->get_param('id');

      if (!$user_id) {
          
        throw new Exception('ID de usuario no válido', 400);
      }
      
      $user = get_user_by('ID', $user_id);

      if (!$user) {
          
        throw new Exception('Usuario no encontrado', 404);
      }

      if (!in_array('subscriber', (array) $user->roles)) {
          
        throw new Exception('Usuario no es suscriptor', 403);
      }

      
      $active = get_user_meta($user_id, 'poeticsoft_manager_partner_active', true);

      if ($active !== 'on') {
          
        throw new Exception('Usuario no está activo', 403);
      }

      $all_meta = get_user_meta($user_id);
      $filtered_meta = array();

      foreach($all_meta as $key => $value) {
        if(strpos($key, 'poeticsoft_manager_partner_') === 0) {

          $filtered_meta[$key] = maybe_unserialize($value[0]);
        }
      }

      $data = array(
        'id'           => $user->ID,
        'login'        => $user->user_login,
        'email'        => $user->user_email,
        'display_name' => $user->display_name,
        'meta'         => $filtered_meta
      );

      $res->set_data($data);

    } catch (Exception $e) {
      
      $res->set_status($e->getCode());
      $res->set_data($e->getMessage());
    }

    return $res;
  }
}