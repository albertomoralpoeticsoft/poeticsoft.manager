<?php

trait Poeticsoft_Manager_Trait_Partner_Avatar {  

  public function register_partner_avatar() {

    add_action(
      'user_edit_form_tag', 
      function() {

        echo ' enctype="multipart/form-data"';
      }
    );

    add_filter(
      'get_avatar' , 
      function (
        $avatar, 
        $id_or_email, 
        $size, 
        $default, 
        $alt 
      ) {
        
        $user = false;

        if (is_numeric($id_or_email)) {

          $id = (int) $id_or_email;
          $user = get_user_by('id', $id);
        } 
        
        elseif (is_object($id_or_email)) {

          if (!empty($id_or_email->user_id)) {

            $id = (int) $id_or_email->user_id;
            $user = get_user_by('id' , $id);
          }
        } 
        
        else {
            
          $user = get_user_by('email', $id_or_email);	
        }

        if ($user && is_object($user)) {   

          $avatar_id = get_user_meta($user->ID, 'poeticsoft_manager_partner_avatar_id', true);
          $avatar_url = $avatar_id ? wp_get_attachment_url($avatar_id) : '';
          $avatar_url = esc_url($avatar_url ? $avatar_url : 'https://www.gravatar.com/avatar/?d=mp&s=96');
          $avatar = '<img 
            alt="' . $alt . '" 
            src="' . $avatar_url . '" 
            class="avatar avatar-' . $size . ' photo psm_custom_avatar_image" 
            height="' . $size . '" 
            width="' . $size . '" 
            style="cursor: pointer"
          />';
        } 

        return $avatar;
      },
      1, 
      5 
    );

    add_filter(
      'user_profile_picture_description', 
      function($description, $user) {

        return '<input 
          type="file"  
          id="psm_custom_avatar_selector"
          name="psm_custom_avatar_selector"
          style="display: none"
        />
        <script>

          document.addEventListener(
            "DOMContentLoaded", 
            () => {

              const customAvatarSelector = document.getElementById("psm_custom_avatar_selector")
              const customAvatarImages = document.getElementsByClassName("psm_custom_avatar_image")

              customAvatarSelector
              .addEventListener(
                "change", 
                function(event) {

                  const file = event.target.files[0];
                  if(file) {

                    const reader = new FileReader()
                    reader.onload = function(e) {

                      for (const image of customAvatarImages) {

                        image.src = e.target.result
                      }
                    }
                    reader.readAsDataURL(file)
                  }
                }
            )

              for (const image of customAvatarImages) {

                image
                .addEventListener(
                  "click", 
                  function(event) {

                    customAvatarSelector && customAvatarSelector.click()
                  }
              )
              }
            }
        )

        </script>';
      },
      10,
      2
    );

    function poeticsoft_manager_partner_avatar_save($user_id) {

      if (!current_user_can('upload_files')) return;

      if (!empty($_FILES['psm_custom_avatar_selector']['name'])) {

        require_once(ABSPATH . 'wp-admin/includes/file.php');
        require_once(ABSPATH . 'wp-admin/includes/image.php');
        
        $uploaded = media_handle_upload('psm_custom_avatar_selector', 0);
        
        if (!is_wp_error($uploaded)) {
            
          update_user_meta($user_id, 'poeticsoft_manager_partner_avatar_id', $uploaded);
        }
      }
    }

    add_action('personal_options_update', 'poeticsoft_manager_partner_avatar_save');
    add_action('edit_user_profile_update', 'poeticsoft_manager_partner_avatar_save');
        
  }
}