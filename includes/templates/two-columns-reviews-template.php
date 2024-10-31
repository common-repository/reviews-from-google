<?php 

function greviews_two_columns_shortcode() {
  global $greviews_options;
  
  $reviewsDB = new Stored_Reviews_WP();
  $reviews = $reviewsDB->get_reviews(5);
  
  if(count($reviews) > 0) {
    $output = $reviewsDB->shortcodeHeader();
    
    $output .= $reviewsDB->reviewsHTML(5);
    $output .= $reviewsDB->shortcodeFooter();
  } else {
    if($greviews_options['key'] == '' || $greviews_options['id'] == '') {
      $output = "<p>You need to configure the google reviews plugin, you can do that <a href='/wp-admin/options-general.php?page=greviews-options'>here</a>.</p>";
    } else {
      $output = "<p>There are no reviews to display.</p>";
    }
  }
  
  wp_enqueue_script('greviews_read_more_less');

  return $output;
}

add_shortcode('greviews-two-columns', 'greviews_two_columns_shortcode');

?>