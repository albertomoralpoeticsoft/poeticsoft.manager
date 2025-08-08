<?php

require_once(WP_PLUGIN_DIR . '/poeticsoft-manager/tools/htmltomarkdown/vendor/autoload.php');
require_once(WP_PLUGIN_DIR . '/poeticsoft-manager/api/telegram/functions.php');

use League\HTMLToMarkdown\HtmlConverter;

function poeticsoft_telegram_htm2markdown($html) {

  $converter = new HtmlConverter([
    'strip_tags' => true
  ]);
  $converter->getConfig()->setOption('hard_break', true);
  return $converter->convert($html);
}

function poeticsoft_telegram_checkdatepublish($rule) {

  $timezone = get_option('poeticsoft_settings_datetime_zone', '');
  $now = new DateTime('now', new DateTimeZone($timezone));

  try {

    $from = new DateTime($rule->whenfrom);
    $to   = new DateTime($rule->whento);

  } catch (Exception $e) {

    return [
      'ok' => false,
      'reason' => 'error de formato en fechas'
    ];
  }

  if ($now < $from || $now > $to) {

    return [
      'ok' => false,
      'reason' => 'fuera de rango de fechas'
    ];
  }

  $weekday = (int) $now->format('N'); // 1 (lunes) a 7 (domingo)
  $weekday = $weekday - 1; // 0 (lunes) a 6 (domingo)
  if (!in_array($weekday, $rule->whenweekday)) {    

    return [
      'ok' => false,
      'reason' => $weekday . ' no es dia de la semana'
    ];
  }

  $scheduled = clone $now;
  $scheduled->setTime((int)$rule->whenhour, (int)$rule->whenmin, 0);
  $window_end = clone $scheduled;
  $window_end->modify('+5 minutes');

  if ($now < $scheduled || $now >= $window_end) {   

    return [
      'ok' => false,
      'reason' => 'no es hora',
      'now' => $now->format('H:i'),
      'scheduled' => $scheduled->format('H:i'),
      'window_end' => $window_end->format('H:i'),
    ];
  }
  
  return ['ok' => true];
}

/* -----------------------------------------------------------------------
  Dialog 
*/

function poeticsoft_telegram_sendmessage(
  $channel = 'telegram_ps_channelid',
  $message = 'Message'
) {

  $data = poeticsoft_api_data();
  $telegramtoken = $data['telegram_ps_token'];
  $apiurl = 'https://api.telegram.org/bot' . $telegramtoken . '/';
  $channelid = $data[$channel];
  $params = [
    'chat_id' => $channelid,
    'text' => $message,
    'parse_mode' => 'HTML'
  ];
  $url = $apiurl . 'sendMessage?' . http_build_query($params);

  $response = wp_remote_get($url);

  if (
    !is_array($response) 
    || 
    is_wp_error($response) 
  ) {      
    
    throw new Exception(
      $response->get_error_message(), 
      500
    );
  }

  return json_decode($response['body']);
}

function poeticsoft_telegram_messagedata($update) { 

  $data = [
    'destination' => '',
    'name' => '',
    'text' => ''
  ];

  if(isset($update['message'])) {

    if($update['message']['chat']['type'] == 'supergroup') {        

      $data['destination'] = 'telegram_ps_groupid';

    } else {

      $data['destination'] = 'telegram_ps_botid';
    }

    $data['name'] = $update['message']['from']['first_name'] . 
                    ' ' . 
                    $update['message']['from']['last_name'];
    $data['text'] = $update['message']['text'];
  }
  
  if(isset($update['channel_post'])) {

    $data['destination'] = 'telegram_ps_channelid';
    $data['name'] = $update['channel_post']['sender_chat']['title'];
    $data['text'] = $update['channel_post']['text'];
  }

  return $data;
}

/* -----------------------------------------------------------------------
  Media 
*/

function poeticsoft_telegram_sendphoto(
  $channel,
  $mediaurl,
  $message
) {

  $data = poeticsoft_api_data();
  $telegramtoken = $data['telegram_ps_token'];
  $apiurl = 'https://api.telegram.org/bot' . $telegramtoken . '/';
  $channelid = $data[$channel];
  $params = [
    'chat_id' => $channelid,
    'photo' => $mediaurl,
    'caption' => $message,
    'parse_mode' => 'Markdown'
  ];
  $url = $apiurl . 'sendPhoto?' . http_build_query($params);

  $response = wp_remote_get($url);

  if (
    !is_array($response) 
    || 
    is_wp_error($response) 
  ) {      
    
    throw new Exception(
      $response->get_error_message(), 
      500
    );
  }

  return json_decode($response['body']);
}

function poeticsoft_telegram_sendvideo(
  $channel,
  $mediaurl,
  $message
) {

  $data = poeticsoft_api_data();
  $telegramtoken = $data['telegram_ps_token'];
  $apiurl = 'https://api.telegram.org/bot' . $telegramtoken . '/';
  $channelid = $data[$channel];
  $params = [
    'chat_id' => $channelid,
    'video' => $mediaurl,
    'caption' => $message,
    'parse_mode' => 'Markdown'
  ];
  $url = $apiurl . 'sendVideo?' . http_build_query($params);

  $response = wp_remote_get($url);

  if (
    !is_array($response) 
    || 
    is_wp_error($response) 
  ) {      
    
    throw new Exception(
      $response->get_error_message(), 
      500
    );
  }

  return json_decode($response['body']);
}

function poeticsoft_telegram_publishmedia($destination, $postid) {

  $post = get_post($postid);
  $legend = $post->post_excerpt;
  $description = $post->post_content;
  $content = '';
  
  if(trim($legend) != '') {

    $content .= '<div>' . trim($legend) . '</div>';
  }

  if(trim($description) != '') {

    $content .= '<div>' . trim($description) . '</div>';
  }

  $type = $post->post_mime_type;
  switch ($type) {

    case 'image/png':
    case 'image/jpeg':

      $image = wp_get_attachment_image_src($postid, 'large');
      $url = $image[0];
                 
      // TO DO NO REPEAT

      $message = '<div>👉 <a href="' . $url . '">' . strtoupper($post->post_title) . '</a></div>' .
                 $content;
      $converter = new HtmlConverter([
        'strip_tags' => true
      ]);
      $converter->getConfig()->setOption('hard_break', true);
      $message = $converter->convert($message);

      $sent = poeticsoft_telegram_sendphoto(
        $destination,
        $url,
        $message
      );

      break;

    case 'video/mp4':

      $url = $post->guid;
                 
      // TO DO NO REPEAT (PARSER FUNCTION FOR TEXTS)

      $message = '<div>👉 <a href="' . $url . '">' . strtoupper($post->post_title) . '</a></div>' .
                 $content;
      $converter = new HtmlConverter([
        'strip_tags' => true
      ]);
      $converter->getConfig()->setOption('hard_break', true);
      $message = $converter->convert($message);
    
      $sent = poeticsoft_telegram_sendvideo(
        $destination,
        $url,
        $message
      ); 
      
      break;
  }

  return $sent;
}

/* -----------------------------------------------------------------------
  Post 
*/

function poeticsoft_telegram_publishpost(
  $what,
  $where, 
  $postid
) {

  $telegramtoken = get_option('poeticsoft_settings_telegram_token', '');

  $apiurl = 'https://api.telegram.org/bot' . $telegramtoken . '/';
  $params = [
    'parse_mode' => 'Markdown'
  ];
  
  $post = get_post($postid);
  $endpoint = '';
  $content = '';
  $posturl = get_permalink($postid);

  switch($what) {

    case 'content':

      $endpoint = 'sendMessage';
      $text = '<div>' . 
        '👉 <a href="' . 
          $posturl . 
        '">' . 
        strtoupper($post->post_title) . 
        '</a>' . 
      '</div>
      <div>' . $post->post_content . '</div>
      <div>
        <a href="' . 
          $posturl . 
        '">
          Ver en la web
        </a>
      </div>';
      $params['text'] = poeticsoft_telegram_htm2markdown($text);
      
      break;

    case 'imageexcerpt':

      $endpoint = 'sendPhoto';      
      $content = $post->post_excerpt;
      $caption = '<div>' . 
        '👉 <a href="' . 
          $posturl . 
        '">' . 
        strtoupper($post->post_title) . 
        '</a>' . 
      '</div>
      <div>' . $content . '</div>
      <div>
        <a href="' . 
          $posturl . 
        '">s
          Ver en la web
        </a>
      </div>';
      $params['caption'] = poeticsoft_telegram_htm2markdown($caption);

      $thumbnail_id = get_post_thumbnail_id($postid);
      $params['photo'] = wp_get_attachment_image_src( $thumbnail_id, 'large')[0];

      break;
  }

  $wheres = is_array($where) ? $where : explode(',', $where);

  $results = [];

  foreach($wheres as $where) {

    $params['chat_id'] = $where;
  
    $url = $apiurl . $endpoint . '?' . http_build_query($params);

    $response = wp_remote_get($url);

    if (
      !is_array($response) 
      || 
      is_wp_error($response) 
    ) {      
      
      throw new Exception(
        $response->get_error_message(), 
        500
      );
    }

    $results[] = json_decode($response['body']);
  }

  return $results;
}

/* -----------------------------------------------------------------------
  Cron 
*/

function poeticsoft_telegram_publishcron() {

  global $wpdb;
  $sql = "
    SELECT DISTINCT 
      post_id, meta_value
    FROM
      {$wpdb->prefix}postmeta as postmeta
    WHERE
      postmeta.meta_key = 'poeticsoft_post_publish_telegram_publishrules'
      AND postmeta.meta_value IS NOT NULL
      AND postmeta.meta_value != ''
  ";
  
  $result = $wpdb->get_results($sql);
  $results = [];
  foreach($result as $postidpublishrules) {

    $postid = $postidpublishrules->post_id;
    $publishrules = json_decode($postidpublishrules->meta_value);
    foreach($publishrules as $publishrule) {

      if($publishrule->active) {

        $intime = poeticsoft_telegram_checkdatepublish($publishrule);

        if($intime['ok']) {
          
          $results[] = poeticsoft_telegram_publishpost(
            $publishrule->what,
            $publishrule->where, 
            $postid
          );
        }
      }
    }
  }

  return $results;
}