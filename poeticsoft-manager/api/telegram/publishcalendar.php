<?php

function poeticsoft_telegram_calendar(WP_REST_Request $req) {

  $res = new WP_REST_Response();

  try {
    
    /* --------------------------------------------------------------------
      SYSTEM 
    */

    $system = '
Eres un asistente que convierte reglas de programación de publicaciones en una estructura JSON interpretable por una aplicación.

Convierte las reglas descritas en lenguaje natural en un JSON con esta estructura:

{
  "reglas": [
    {
      "destino": "Canal o Grupo",
      "desde": "YYYY-MM-DD",
      "hasta": "YYYY-MM-DD",
      "horas_utc": ["HH:MM"],
      "incluir_dias_semana": ["Monday"],
      "excluir_dias_semana": ["Tuesday"],
      "incluir_fechas": ["YYYY-MM-DD"],
      "excluir_fechas": ["YYYY-MM-DD"]
    }
  ]
}

- Usa días de la semana en inglés (ej. "Monday", "Tuesday", ...).
- No incluyas nada fuera del JSON. No expliques.
- Si no se especifican exclusiones o inclusiones, omite esos campos.
- Importante: NO uses bloques de código como ```json ni escapes de caracteres. Devuelve el JSON puro.';

    /* --------------------------------------------------------------------
      RULES IN WP 
    */

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

      foreach($postrule['rules'] as $index => $rule) {

        $rules[] = ($index + 1) . '. ' . $rule->instructions . PHP_EOL;
      }
    } 
    $rules = implode(', ', $rules); 

    /* --------------------------------------------------------------------
      PROMPT 
    */
      
    $prompt = '
Estas son las reglas de publicación:

REGLAS:

' . $rules; 

    /* --------------------------------------------------------------------
      OPEN AI REQUEST
    */
    
    $data = poeticsoft_api_data();
    $apikey = $data['openai_apikey'];  

    $body = [
      'model' => "gpt-4-1106-preview",
      'messages' => [
        [ 
          'role' => 'system', 
          'content' => $system
        ],
        [ 
          'role' => "user", 
          'content' => $prompt 
        ]
      ],
      'temperature' => 0.2,
      'n' => 1
    ];

    $args = [
      'method' =>'POST',
      'headers' => [
        'Content-Type' => 'application/json',
        'Authorization' => 'Bearer ' . $apikey
      ],
      'body' => json_encode($body),
      'timeout' => 20
    ];

    $url = 'https://api.openai.com/v1/chat/completions';

    $response = wp_remote_post($url, $args);

    if (is_wp_error($response)) {
    
      throw new Exception(
        $response->get_error_message(), 
        500
      );
    }

    $responsebody = wp_remote_retrieve_body($response);
    $responsedata = json_decode($responsebody, true);
    $message = $responsedata['choices'][0]['message']['content'];
    $data = json_decode($message);

    $res->set_data([
      'system' => $system,
      'prompt' => $prompt,
      'responsedata' => $responsedata,
      'message' => $message,
      'data' => $data
    ]);

    // $res->set_data([
    //   'system' => $system,
    //   'prompt' => $prompt
    // ]);
    
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