<?php

require_once(WP_PLUGIN_DIR . '/poeticsoft-manager/api/telegram/functions.php');

function poeticsoft_telegram_destinations(WP_REST_Request $req) {

  $res = new WP_REST_Response();

  try { 

    $telegramdestinationsoption = get_option('poeticsoft_settings_telegram_destinations', '');
    $telegramdestinationstext = preg_replace('/[\r\n\s]+/', '', $telegramdestinationsoption);

    if('' == $telegramdestinationstext) {
      
      $res->set_data([]);

    } else {

      $destinations = explode(',', $telegramdestinationstext);
      $destinationslist = [];

      foreach($destinations as $index => $value) {

        $keyvalue = explode(':', $value);

        $destination = [
          'label' => $keyvalue[0],
          'value' => $keyvalue[1]
        ];

        if($index == 0) {

          $destination['default'] = true;
        }

        $destinationslist[] = $destination;
      } 

      $res->set_data($destinationslist);
    }
    
  } catch (Exception $e) {
    
    $res->set_status($e->getCode());
    $res->set_data($e->getMessage());
  }

  return $res;
}

function poeticsoft_telegram_publish(WP_REST_Request $req) {

  $res = new WP_REST_Response();

  try { 

    $postid = $req->get_param('postid');
    $what = $req->get_param('what');
    $where = $req->get_param('where');
    
    $sent = poeticsoft_telegram_publishpost(
      $what,
      $where, 
      $postid
    );

    $res->set_data($sent);
    
  } catch (Exception $e) {
    
    $res->set_status($e->getCode());
    $res->set_data($e->getMessage());
  }

  return $res;
}

function poeticsoft_telegram_cron(WP_REST_Request $req) {

  // https://console.cron-job.org/

  $res = new WP_REST_Response();

  try { 

    $result = poeticsoft_telegram_publishcron();

    $res->set_data($result);
    
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
      'poeticsoft/telegram',
      'destinations',
      array(
        array(
          'methods'  => 'GET',
          'callback' => 'poeticsoft_telegram_destinations',
          'permission_callback' => '__return_true'
        )
      )
    );

    register_rest_route(
      'poeticsoft/telegram',
      'publishwp',
      array(
        array(
          'methods'  => 'GET',
          'callback' => 'poeticsoft_telegram_publish',
          'permission_callback' => '__return_true'
        )
      )
    );

    register_rest_route(
      'poeticsoft/telegram',
      'cron',
      array(
        array(
          'methods'  => 'GET',
          'callback' => 'poeticsoft_telegram_cron',
          'permission_callback' => '__return_true'
        )
      )
    );
  }
);