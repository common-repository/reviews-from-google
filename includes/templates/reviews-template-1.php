<?php 

function greviews_shortcode() {
  global $greviews_options;
  global $plugindir;

  $googleImage = $plugindir . '/images/powered_by_google_on_white.png';

  $place_id = $greviews_options['id'];
  $key = $greviews_options['key'];
  $cache_enabled = $greviews_options['cache_enabled'];

  $greviews = new GReviews($key, $place_id, $cache_enabled);

  $resp = $greviews->get_data();

  // check if there is a response
  if($resp['status'] == 'OK') {

    $wholeStars = $greviews->returnWholeStars($resp['result']['rating']);
    $halfStar = $greviews->returnHalfStars($resp['result']['rating']);
    $stars = '';

    // if a whole number, print a colored star
    for( $i=0; $i<$wholeStars; $i++ ){
      $stars .= "<span class='greview-star-icon full'>".html_entity_decode('&#9734', 0, 'UTF-8')."</span>";
    }
    // if not, print a half colored star.
    if( $halfStar ){
      $stars .= "<span class='greview-star-icon half'>".html_entity_decode('&#x2606', 0, 'UTF-8')."</span>";
    }

    $output = '<div class="greviews-header">
                <div class="greviews-title-container">'.$resp['result']['name'].'</div>
                <div class="greviews-rating-container"><span class="greviews-rating">'.$resp['result']['rating'].'</span>'.$stars.'</div>
              </div>';

    // reset the stars variable
    $stars = '';

    $output .= '<div class="greview-main-container">';

    // if there is a response, check if there is a review.
    if(count($resp['result']['reviews']) > 0) {
      foreach($resp['result']['reviews'] as $reviews) {
        $output .= '<div class="greview-container">';
        
        $output .= '<img class="greview-image" src="'.$reviews['profile_photo_url'].'" alt="'.$reviews['author_name'].'" />';
        
        $output .= '<a class="greview-reviewer" href="'.$reviews['author_url'].'">'.$reviews['author_name'].'</a>';
        $output .= '<div class="greview-content">'.substr($reviews['text'], 0, 140) . '...' .'</div>';
  
        // round down to get number of whole stars needed
        $wholeStars = $greviews->returnWholeStars($reviews['rating']);

        // this will be 1 if you have a half-rating, 0 if not.
        $halfStar = $greviews->returnHalfStars($reviews['rating']);

        $output .= '<div class="greview-rating-container">';
        // if a whole number, print a colored star
        for( $i=0; $i<$wholeStars; $i++ ) {
          $stars .= "<span class='greview-star-icon full'>".html_entity_decode('&#9734', 0, 'UTF-8')."</span>";
        }
        // if not, print a half colored star.
        if( $halfStar ) {
          $stars .= "<span class='greview-star-icon half'>".html_entity_decode('&#x2606', 0, 'UTF-8')."</span>";
        }

        $output .= $stars;

        // reset the stars variable
        $stars = '';
        
        $output .= '</div>';
        $output .= '<div class="greview-relative-time">'.$reviews['relative_time_description'].'</div>';
        $output .= '</div>';
      }
    }
    $output .= '</div>';
    $output .= '
        <div class="greviews-footer">
          <div class="greviews-poweredby">
            <img src="'.$googleImage.'" alt="Powered by Google" />
          </div>
          <div class="greviews-more-link">
            <a href="https://search.google.com/local/reviews?placeid='.$resp['result']['place_id'].'" target="_blank" rel="noreferrer">View all reviews</a>
          </div>
        </div>
        ';
  } else {
    $output = $resp['error_message'];
  }
  return $output;
}

add_shortcode('greviews', 'greviews_shortcode');

?>