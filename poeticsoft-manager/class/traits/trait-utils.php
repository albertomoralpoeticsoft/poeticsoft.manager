<?php

trait Poeticsoft_Manager_Trait_Utils { 

  public function log($display, $withdate = false) { 

    $text = is_string($display) ? 
    $display 
    : 
    json_encode($display, JSON_PRETTY_PRINT);

    $message = $withdate ? 
    date("d-m-y h:i:s") . PHP_EOL
    :
    '';

    $message .= $text . PHP_EOL;

    file_put_contents(
      self::$dir . '/log.txt',
      $message,
      FILE_APPEND
    );
  }

  public function slugify($text) {
  
    $text = strtolower($text);
    $text = preg_replace('~[^\\pL\\d]+~u', '-', $text);
    $text = iconv('utf-8', 'us-ascii//TRANSLIT//IGNORE', $text);
    $text = preg_replace('~-+~', '-', $text);
    if (empty($text)) {
        
      return 'n-a';
    }
    
    return $text;
  }

  public function auth(WP_REST_Request $req) {

    return true;

    return new WP_Error(
      'rest_forbidden',
      __( 'Acceso prohibido.'),
      array( 'status' => 401 )
    );
  }
}