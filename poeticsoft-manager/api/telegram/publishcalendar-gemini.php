<?php

function poeticsoft_telegram_calendar(WP_REST_Request $req) {

  $res = new WP_REST_Response();

  try {

    global $wpdb;
    $sql = "
      SELECT
        posts.ID
      FROM
        {$wpdb->prefix}posts AS posts
      INNER JOIN
        {$wpdb->prefix}postmeta AS postmeta ON posts.ID = postmeta.post_id
      WHERE
        posts.post_type = 'post'
        AND posts.post_status = 'publish'
        AND postmeta.meta_key = 'poeticsoft_post_publish_telegram_usepublishrules'
        AND postmeta.meta_value = '1';
    ";
    $result = $wpdb->get_results($sql);

    $postsrules = array_map(
      function($post) {

        $postid = $post->ID;
        $postmeta = get_post_meta($postid);
        $rules = $postmeta['poeticsoft_post_publish_telegram_publishrules'][0];

        return [
          'id' => $postid,
          'rules' => json_decode($rules)
        ];
      },
      $result
    );

    $rules = [];
    foreach($postsrules as $postrule) {

      foreach($postrule['rules'] as $rule) {

        $rules[] = $rule->instructions;
      }
    } 
    $rules = implode(', ', $rules);

    $data = poeticsoft_api_data();

    $url = $data['gemini_url'];
    $model = $data['gemini_model'];
    $apikey = $data['gemini_apikey'];
    
    $geminiurl = $url . '/' . $model . ':generateContent?key=' . $apikey;    
      
    $promp = '
    Calcula las fechas a partir de las siguientes reglas: '.
    $rules . 
    ' detecta si hay fechas o rangos de fecha erróneos 
    y en ese caso notificalo en campo explicación  de la respuesta.
    Si no entiendes las reglas pregunta tus dudas, necesito fechas exactas
    ';

    $schema = [
      'type' => 'object',
      'properties' => [
        'publicaciones' => [
          'type' => 'array',
          'items' => [
            'type' => 'object',
            'properties' => [
              'destino' => ['type' => 'string', 'enum' => ['grupo', 'canal']],
              'fecha' => ['type' => 'string', 'format' => 'date-time'],
            ],
            'required' => ['destino', 'fecha'],
          ],
        ],
        'explicaciones' => [
          'type' => 'string',
        ],
      ],
      'required' => ['publicaciones', 'explicaciones'],
    ];

    $requestBody = [
      'contents' => [
        [
          'role' => 'user',
          'parts' => [
            [
              'text' => $promp
            ]
          ]
        ]
      ],
      'generationConfig' => [
        'responseMimeType' => 'application/json',
        'responseJsonSchema' => $schema,
        "temperature" => 0.1,
      ]
    ]; 
    
    $args = array(
      'method'      => 'POST',
      'headers'     => [
        'Content-Type' => 'application/json',
      ],
      'body'        => json_encode($requestBody),
      'timeout'     => 45
    );

    $response = wp_remote_post($geminiurl, $args);

    if (is_wp_error($response)) {

      throw new Exception(
        $response->get_error_message(), 
        500
      ); 
    }
    
    $data = json_decode($response['body']);
    
    if (
      json_last_error() === JSON_ERROR_NONE 
      && 
      isset($data->candidates[0]->content->parts[0]->text)
    ) {
      
      $jsonResponseText = $data->candidates[0]->content->parts[0]->text;
      $structuredData = json_decode($jsonResponseText, true);

      if (json_last_error() === JSON_ERROR_NONE) {
        
        $publicaciones = $structuredData['publicaciones'];
        $explicaciones = $structuredData['explicaciones'];

      } else {
          
        throw new Exception(
          'Error al decodificar el JSON de la respuesta del modelo: ' . json_last_error_msg(), 
          500
        ); 
      }
        
    } else {

      throw new Exception(
        'Error: Respuesta inválida de la API de Gemini.', 
        500
      ); 
    }   

    $res->set_data([
      "prompt" => $promp,
      "json" => $publicaciones,
      "explain" => $explicaciones
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
      'poeticsoft/telegram',
      'calendar',
      array(
        array(
          'methods'  => 'GET',
          'callback' => 'poeticsoft_telegram_calendar',
          'permission_callback' => '__return_true'
        )
      )
    );
  }
);