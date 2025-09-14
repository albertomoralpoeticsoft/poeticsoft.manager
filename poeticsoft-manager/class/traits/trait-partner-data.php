<?php

trait Poeticsoft_Manager_Trait_Partner_Data {  

  public function register_partner_data() { 
    
    $this->metadatas = [
      [
        'name' => 'Teléfono',
        'meta_key' => 'poeticsoft_manager_partner_telefono',
        'description' => 'Teléfono de contacto.'
      ],
      [
        'name' => 'Instagram',
        'meta_key' => 'poeticsoft_manager_partner_instagram',
        'description' => 'Instagram'
      ]
    ];
    
    register_meta( 
      'user', 
      'poeticsoft_manager_partner_active', 
      [
        'show_in_rest' => true,
        'type' => 'string',
        'single' => true,
        'auth_callback' => [$this, 'auth'],
    ]);

    foreach($this->metadatas as $metadata) {

      register_meta( 
        'user', 
        $metadata['meta_key'], 
        [
          'show_in_rest' => true,
          'type' => 'string',
          'single' => true,
          'auth_callback' => [$this, 'auth'],
      ]);
    }

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
          Partner activo
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

    foreach($this->metadatas as $metadata) {
    
      $value = esc_attr(
        get_user_meta(
          $user->ID, 
          $metadata['meta_key'], 
          true
        )
      );
      
      ?>      
      <tr>
        <th>
          <label for="<?php echo $metadata['meta_key'] ?>">
            <?php echo $metadata['name'] ?>
          </label>
        </th>
        <td>
          <input 
            type="text" 
            name="<?php echo $metadata['meta_key'] ?>" 
            id="<?php echo $metadata['meta_key'] ?>" 
            value="<?php echo $value ?>" 
            class="regular-text" 
          />
          <br />
          <span class="description">
            <?php echo $metadata['description'] ?>
          </span>
        </td>
      </tr>
      <?php
    }

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

    /* Data */

    foreach($this->metadatas as $metadata) {

      if (isset($_POST[$metadata['meta_key']])) {

        update_user_meta(
          $user_id, 
          $metadata['meta_key'], 
          sanitize_text_field($_POST[$metadata['meta_key']])
        );
      }
    }    
  } 
}