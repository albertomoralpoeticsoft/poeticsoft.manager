<?php

trait Poeticsoft_Manager_Trait_Partner_Data {  

  public function register_partner_data() {  
    
    register_meta( 
      'user', 
      'poeticsoft_manager_partner_active', 
      [
        'show_in_rest' => true,
        'type' => 'string',
        'single' => true,
        'auth_callback' => [$this, 'auth'],
    ]);

    register_meta( 
      'user', 
      'poeticsoft_manager_partner_telefono', 
      [
        'show_in_rest' => true,
        'type' => 'string',
        'single' => true,
        'auth_callback' => [$this, 'auth'],
    ]);

    add_action('admin_init', function() {

      add_action('show_user_profile', [$this, 'poeticsoft_manager_partner_customfields']);
      add_action('edit_user_profile', [$this, 'poeticsoft_manager_partner_customfields']);
      add_action('personal_options_update', [$this, 'poeticsoft_manager_partner_customfields_save']);
      add_action('edit_user_profile_update', [$this, 'poeticsoft_manager_partner_customfields_save']);

    }); 
  }

  public function poeticsoft_manager_partner_customfields($user) {

    if (user_can($user, 'administrator')) { return; }

    ?>
      <h2>Información adicional</h2>
      <table class="form-table">
    <?php  

    /* Active partner */

    $poeticsoft_manager_partner_active = get_user_meta(
      $user->ID, 
      'poeticsoft_manager_partner_active', 
      true
    );

    ?>
    <tr>
      <th>
        <label for="poeticsoft_manager_partner_active">
          Partner active
        </label>
      </th>
      <td>
        <input 
          type="checkbox" 
          name="poeticsoft_manager_partner_active" 
          id="poeticsoft_manager_partner_active"
          <?php echo $poeticsoft_manager_partner_active == 'on' ? 'checked' : '' ?>
        />
      </td>
    </tr>
    <?php

    /* Teléfono */
    
    $poeticsoft_manager_partner_telefono = esc_attr(
      get_user_meta(
        $user->ID, 
        'poeticsoft_manager_partner_telefono', 
        true
      )
    );
    
    ?>
      
    <tr>
      <th>
        <label for="poeticsoft_manager_partner_telefono">
          Teléfono de contacto
        </label>
      </th>
      <td>
        <input 
          type="text" 
          name="poeticsoft_manager_partner_telefono" 
          id="telefono_contacto" 
          value="<?php echo $poeticsoft_manager_partner_telefono ?>" 
          class="regular-text" 
        />
        <br />
        <span class="description">
          Introduce tu número de contacto.
        </span>
      </td>
    </tr>
    <?php

    ?>
    </table>
    <?php
  }

  public function poeticsoft_manager_partner_customfields_save($user_id) {
    
    if (!current_user_can('edit_user', $user_id)) return false;

    /* Active partner */

    $activepartner = 'off';
    if (
      isset($_POST['poeticsoft_manager_partner_active'])
      &&
      $_POST['poeticsoft_manager_partner_active'] == 'on'
    ) {

      $activepartner = $_POST['poeticsoft_manager_partner_active'];
    }

    update_user_meta(
      $user_id, 
      'poeticsoft_manager_partner_active', 
      $activepartner
    );

    if (isset($_POST['poeticsoft_manager_partner_telefono'])) {

      update_user_meta(
        $user_id, 
        'poeticsoft_manager_partner_telefono', 
        sanitize_text_field($_POST['poeticsoft_manager_partner_telefono'])
      );
    }
  } 
}