<?php

require_once(WP_PLUGIN_DIR . '/poeticsoft-manager/api/telegram/functions.php');

add_action(
  'post_updated',
  function ($post_id, $post_after, $post_before) {

    $post = get_post($post_id);
    $postmeta = get_post_meta($post_id);
  },
  10,
  3
);