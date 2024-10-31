<?php
// Create settings page -- admin side only
if(is_admin()) {
  function greviews_options_menu_link() {
    // add it under the Tools menu
    add_options_page(
      'Google Review Settings',
      'Google Reviews',
      'manage_options',
      'greviews-options',
      'greviews_options_content'
    );
  }
  
  // Create options page content
  function greviews_options_content() {
    // Init Options Global
    global $greviews_options;

    if($_SERVER['REQUEST_METHOD'] === 'POST') {
      if(isset($_POST['action'])) {
        if($_POST['action'] === 'deleteData') {
          $storedReviews = new Stored_Reviews_WP();
          $storedReviews->deleteData();
          echo '<div id="message" class="updated notice"><p>You can now delete the plugin</p></div>';
        }
      
        if($_POST['action'] === 'import') {      
          get_reviews_from_google(
            $greviews_options['key'], 
            $greviews_options['id'],
            $greviews_options['cache_enabled']
          );
        }
      }
    }
    
    ob_start() ?>
      <div class="wrap">
        <h2><?php _e('Google Places Reviews Settings') ?></h2>
        <div class="greviews-menu">
          <form method="POST" action="options.php">
            <?php settings_fields('greviews_settings_group'); ?>
            <table class="form-table">
              <tbody>
                <tr>
                  <th class="row"><label for="greviews_settings[id]"><?php _e('Enter Google Places ID', 'greviews'); ?></label></th>
                  <td><input type="text" name="greviews_settings[id]" id="greviews_settings[id]" value="<?php echo $greviews_options['id']?>" class="regular-text">
                  <p class="description"><?php _e("Can't find your Google Places ID? Check this out.", "greviews")?></p></td>
                </tr>
                <tr>
                  <th class="row"><label for="greviews_settings[key]"><?php _e('Enter Google API Key', 'greviews'); ?></label></th>
                  <td><input type="password" name="greviews_settings[key]" id="greviews_settings[key]" value="<?php echo $greviews_options['key']?>" class="regular-text">
                  <p class="description"><?php _e("Find out how to get a Google API key <a href='https://www.youtube.com/watch?v=wG7y928gz-c' target='_blank' rel='noreferrer'>here.</a>", "greviews")?></p></td>
                </tr>
                <tr>
                  <th class="row"><label for="greviews_settings[lowest_allowed_rating]"><?php _e('Lowest Allowed Rating', 'greviews'); ?></label></th>
                  <td><input type="text" name="greviews_settings[lowest_allowed_rating]" id="greviews_settings[lowest_allowed_rating]" 
                            value="<?php echo $greviews_options['lowest_allowed_rating']?>" class="regular-text"></td>
                </tr>
                <tr>
                  <th class="row"><label for="greviews_settings[title_color]"><?php _e('Company Name Color', 'greviews'); ?></label></th>
                  <td><input type="text" class="color-field" name="greviews_settings[title_color]" id="greviews_settings[title_color]"
                            value="<?php echo $greviews_options['title_color'] ?>"></td>
                </tr>
                <tr>
                  <th class="row"><label for="greviews_settings[link_color]"><?php _e('Link Color', 'greviews'); ?></label></th>
                  <td><input type="text" class="color-field" name="greviews_settings[link_color]" id="greviews_settings[link_color]"
                            value="<?php echo $greviews_options['link_color'] ?>"></td>
                </tr>
                <tr>
                  <th class="row"><label for="greviews_settings[viewall_link_color]"><?php _e('View All Reviews Color', 'greviews'); ?></label></th>
                  <td><input type="text" class="color-field" name="greviews_settings[viewall_link_color]" id="greviews_settings[viewall_link_color]"
                            value="<?php echo $greviews_options['viewall_link_color'] ?>"></td>
                </tr>
                <tr>
                  <th class="row"><label for="greviews_settings[text_color]"><?php _e('Text Color', 'greviews'); ?></label></th>
                  <td><input type="text" class="color-field" name="greviews_settings[text_color]" id="greviews_settings[text_color]"
                            value="<?php echo $greviews_options['text_color'] ?>"></td>
                </tr>
                <tr>
                  <th class="row"><label for="greviews_settings[star_color]"><?php _e('Star Color', 'greviews'); ?></label></th>
                  <td><input type="text" class="color-field" name="greviews_settings[star_color]" id="greviews_settings[star_color]"
                            value="<?php echo $greviews_options['star_color'] ?>"></td>
                </tr>
                <tr>
                  <th class="row"><label for="greviews_settings[rating_text_color]"><?php _e('Text Color for Rating Number', 'greviews'); ?></label></th>
                  <td><input type="text" class="color-field" name="greviews_settings[rating_text_color]" id="greviews_settings[rating_text_color]"
                            value="<?php echo $greviews_options['rating_text_color'] ?>"></td>
                </tr>
                <tr>
                  <th class="row"><label for="greviews_settings[container_bg]"><?php _e('Review Container Background Color', 'greviews'); ?></label></th>
                  <td><input type="text" class="color-field" name="greviews_settings[container_bg]" id="greviews_settings[container_bg]"
                            value="<?php echo $greviews_options['container_bg'] ?>"></td>
                </tr>
                <tr>
                <th class="row"><label for="greviews_settings[hide_images]"><?php _e('Hide Images?', 'greviews'); ?></label></th>
                  <td>
                    <select name="greviews_settings[hide_images]" id="greviews_settings[hide_images]">
                      <option value="1" <?php selected($greviews_options['hide_images'], '1') ?>>Yes</option>
                      <option value="0" <?php selected($greviews_options['hide_images'], '0' )?>>No</option>
                    </select>
                  </td>
                </tr>
                <tr>
                <th class="row"><label for="greviews_settings[cache_enabled]"><?php _e('Enable Cache?', 'greviews'); ?></label></th>
                  <td>
                    <select name="greviews_settings[cache_enabled]" id="greviews_settings[cache_enabled]">
                      <option value="yes" <?php selected($greviews_options['cache_enabled'], 'yes') ?>>Yes</option>
                      <option value="no" <?php selected($greviews_options['cache_enabled'], 'no' )?>>No</option>
                    </select>
                    <p class="description"><?php _e("The cache expires every 12 hours.", "greviews")?></p>
                  </td>
                </tr>
                </tr>
              </tbody>
            </table>
          
            <input type="hidden" name="greviews_settings[name]" id="greviews_settings[name]" value="<?php echo $greviews_options['name']; ?>">
        
            <?php if(empty($greviews_options['total_rating'])) { $greviews_options['total_rating'] = 0; } ?>
        
            <input type="hidden" name="greviews_settings[total_rating]" id="greviews_settings[total_rating]" value="<?php echo $greviews_options['total_rating']; ?>"> 

            <p class="submit"><input type="submit" name="submit" id="submit" class="button-primary" value="<?php _e('Save', 'greviews'); ?>"></p>
          </form>
        </div>

        <div class="greviews-menu">
          <form method="post">
            <input type="hidden" name="action" value="import">
            <button name="submit" class="button-primary"><?php _e('Import Latest Reviews', 'greviews'); ?></button>
          </form>

          <form method="post" onsubmit="return confirm('<?php _e('Are you sure?', 'greviews'); ?>');">
            <p class="greviews-alert"><?php _e('Please be careful, pressing the button below will delete all your reviews and plugin settings.', 'greviews'); ?></p>

            <input type="hidden" name="action" value="deleteData">
            <button name="submit" class="button-primary"><?php _e('Delete Plugin Data', 'greviews'); ?></button>
          </form>
        </div>
      </div>

    <?php
    echo ob_get_clean();
  }
  
  add_action('admin_menu', 'greviews_options_menu_link');
  
  // Register settings
  function greviews_register_settings() {
    register_setting('greviews_settings_group', 'greviews_settings');
  }
  
  add_action('admin_init', 'greviews_register_settings');

  function get_reviews_from_google($key, $place_id, $cache_enabled) {
    $greviews = new GReviews($key, $place_id, $cache_enabled);
    $response = $greviews->get_data();

    if($response['error_message']) {
      echo '<div class="notice notice-error"><p>'.$response['error_message'].'</p></div>';
    } else {
      echo '<div id="message" class="updated notice"><p>Reviews successfully imported</p></div>';
    }
  }
}