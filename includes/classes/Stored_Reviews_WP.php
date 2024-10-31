<?php

class Stored_Reviews_WP {
  public $title;
  public $content;
  public $rating;
  public $source;
  public $creation_date;
  public $author_url;
  public $author_image_url;
  private $dbname;
  
  public function __construct() {
    global $wpdb; 
    $this->dbname = $wpdb->prefix . 'stored_reviews_wp';
  }
  
  public function process_response($response) {
    global $wpdb;
    global $greviews_options;
  
    if($response['status'] == 'OK') {
      $greviews_options['total_rating'] = $response['result']['rating'];
      $greviews_options['name'] = $response['result']['name'];
  
      update_option('greviews_settings', $greviews_options);
  
      foreach($response['result']['reviews'] as $reviews) {
        $exists = $wpdb->get_var( $wpdb-> prepare(
          "SELECT * FROM $this->dbname WHERE title = %s AND source = %s", $reviews['author_name'], 'Google'
        ));
  
        if( !$exists ) {
          $this->add_review(
            $reviews['author_name'], 
            $reviews['text'], 
            $reviews['rating'], 
            $reviews['author_url'],
            $reviews['profile_photo_url'],
            'Google', 
            $reviews['time']
          );
        }
      }
    }    
  }
  private function add_review($title, $content, $rating, $author_url, $author_image_url, $source, $creation_date) {
    $this->title = $title;
    $this->content = $content;
    $this->rating = $rating;
    $this->source = $source;
    $this->author_url = $author_url;
    $this->author_image_url = $author_image_url;
    $this->creation_date = date('Y-m-d H:i:s', $creation_date);
   
    global $wpdb;
  
    $data = array(
      'title' => $this->title,
      'content' => $this->content,
      'rating' => $this->rating,
      'source' => $this->source,
      'author_url' => $this->author_url,
      'author_image_url' => $this->author_image_url,
      'creation_date' => $this->creation_date
    );
  
    $format = array(
      '%s', '%s', '%s', '%s', '%s', '%s', '%s'
    );
  
    $wpdb->insert($this->dbname, $data, $format);
    $id = $wpdb->insert_id;
  
    return $id;
  }
  
  public function get_reviews($limit = 0, $offset = 0) {
    global $wpdb;
  
    $reviews = $wpdb->get_results( "SELECT * FROM $this->dbname" );

    return $reviews;
  }
  
  public function returnWholeStars($rating) {
    return floor($rating);
  }
  
  public function returnHalfStars($rating) {
    return round($rating * 2) % 2;
  }
  
  // Used this script from the following resource:
  // https://stackoverflow.com/questions/1416697/converting-timestamp-to-time-ago-in-php-e-g-1-day-ago-2-days-ago
  public function time_elapsed_string($datetime, $full = false) {
    $now = new DateTime;
    $ago = new DateTime($datetime);
    $diff = $now->diff($ago);
    $diff->w = floor($diff->d / 7);
    $diff->d -= $diff->w * 7;
    $string = array(
        'y' => 'year',
        'm' => 'month',
        'w' => 'week',
        'd' => 'day',
        'h' => 'hour',
        'i' => 'minute',
        's' => 'second',
    );
  
    foreach ($string as $k => &$v) {
        if ($diff->$k) {
            $v = $diff->$k . ' ' . $v . ($diff->$k > 1 ? 's' : '');
        } else {
            unset($string[$k]);
        }
    }
  
    if (!$full) $string = array_slice($string, 0, 1);
    return $string ? implode(', ', $string) . ' ago' : 'just now';
  }
  
  public function deleteData() {
    global $wpdb;
  
    $wpdb->query( "DROP TABLE IF EXISTS $this->dbname" );
    delete_option('greviews_settings');
  }

  public function shortcodeHeader() {
    global $greviews_options;
    $stars = $this->generateStars($greviews_options['total_rating']);
  
    $output = '<div class="greviews-container">
               <div class="greviews-header">
                <div class="greviews-title-container">'.$greviews_options['name'].'</div>
                <div class="greviews-rating-container"><span class="greviews-rating">'.$greviews_options['total_rating'].'</span>'.$stars.'</div>
               </div>';
    return $output;
  }

  public function widgetHeader() {
    global $greviews_options;
    $stars = $this->generateStars($greviews_options['total_rating']);
    $output = '<div class="greviews-widget-container">';
    $output .= '<div class="greviews-widget-header">
                  <div class="greviews-title-container">'.$greviews_options['name'].'</div>
                  <div class="greviews-rating-container"><span class="greviews-rating">'.$greviews_options['total_rating'].'</span>'.$stars.'</div>';
    $output .= $this->poweredByGoogleImage('widget');
    $output .= '</div>';
    return $output;
  }

  public function generateStars($rating) {
    global $greviews_options;
    
    $wholeStars = $this->returnWholeStars($rating);
    $halfStar = $this->returnHalfStars($rating);
    
    $stars = '';
  
    // if a whole number, print a colored star
    for( $i=0; $i<$wholeStars; $i++ ){
      $stars .= "<span class='greview-star-icon full'>".html_entity_decode('&#9734', 0, 'UTF-8')."</span>";
    }
    // if not, print a half colored star.
    if( $halfStar ){
      $stars .= "<span class='greview-star-icon half'>".html_entity_decode('&#x2606', 0, 'UTF-8')."</span>";
    }
    return $stars;
  }

  public function poweredByGoogleImage($templateType = 'shortcode') {
    global $plugindir;
    $googleImage = $plugindir . '/images/powered_by_google_on_white.png';
    
    if($templateType == 'widget') {
      $output = '<div class="greviews-poweredby-widget">';
    } else {
      $output = '<div class="greviews-poweredby">';
    }
    $output .= '<img src="'.$googleImage.'" alt="Powered by Google" />';
    
    $output .= '</div>';
    
    return $output;
  }

  public function reviewsHTML($limit, $offset = 0, $templateType = 'shortcode', $instance = NULL) {
    global $greviews_options;

    $lowest_allowed_rating = $greviews_options['lowest_allowed_rating'];
    $hide_images = $greviews_options['hide_images'];

    if($hide_images == 0) {
      $hide_images = 'No';
    }

    if($templateType == 'widget') {
      $hide_images = $instance['hide_images'];
      $lowest_allowed_rating = $instance['lowest_rating'];
    }

    $reviews = $this->get_reviews($limit, $offset);
    
    // if there are no reviews, try to fetch reviews from the Google API and add them to the database.
    if(count($reviews) == 0) {
      $place_id = $greviews_options['id'];
      $key = $greviews_options['key'];
      $cache_enabled = $greviews_options['cache_enabled'];
  
      $gapi = new GReviews($key, $place_id, $cache_enabled);
      $gapi->get_data();
  
      // try to get the reviews from the database again.
      $reviews = $this->get_reviews($limit, $offset);
    }
  
    if($templateType == 'shortcode') {
      $output = '<div class="greview-columns-container">';
    } else if ($templateType == 'fullwidth') {
      $output = '<div class="greview-fullwidth-container">';
    } else { 
      $output = '';
    }

    foreach($reviews as $review) {
      // make sure the review is equal or above the lowest allowed rating
      if($review->rating >= $lowest_allowed_rating) {
        $output .= '<div class="greview-container">';
		  
        // if hide image is No, display images.
        if($hide_images == 'No') {
          $output .= '<img class="greview-image" src="'.$review->author_image_url.'" alt="'.$review->title.'" />';
        }

        $output .= '<a class="greview-reviewer" href="'.$review->author_url.'">'.$review->title.'</a>';
        $output .= '<div class="greview-content">'.$review->content.'</div>';
        // round down to get number of whole stars needed
        $wholeStars = $this->returnWholeStars($review->rating);
        // this will be 1 if you have a half-rating, 0 if not.
        $halfStar = $this->returnHalfStars($review->rating);
        $output .= '<div class="greview-rating-container">';
        
        // reset the stars variable
        $stars = '';
        // if a whole number, print a colored star
        for( $i=0; $i<$wholeStars; $i++ ) {
          $stars .= "<span class='greview-star-icon full'>".html_entity_decode('&#9734', 0, 'UTF-8')."</span>";
        }
        // if not, print a half colored star.
        if( $halfStar ) {
          $stars .= "<span class='greview-star-icon half'>".html_entity_decode('&#x2606', 0, 'UTF-8')."</span>";
        }
        $output .= $stars;
          
        $output .= '</div>';
        $output .= '<div class="greview-relative-time">'.$this->time_elapsed_string($review->creation_date).'</div>';
        $output .= '</div>';
      }
    }
    $output .= '</div>';  
    return $output;
  }

  public function shortcodeFooter() {
    global $greviews_options;
    global $plugindir;
    $output = '<div class="greviews-footer">';
    
    $output .= $this->poweredByGoogleImage();
    $output .= $this->generateMoreLink();
          
    $output .= '</div>';
    // closing tag for greviews-container
    $output .= '</div>';
    return $output;
  }

  public function generateMoreLink($templateType = 'shortcode') {
    global $greviews_options;
    if($templateType == 'widget') {
      $output = '<div class="greviews-more-link-widget">';
    } else {
      $output = '<div class="greviews-more-link">';
    }
    
    $output .= '<a href="https://search.google.com/local/reviews?placeid='.$greviews_options['id'].'" target="_blank" rel="noreferrer">
                  View all reviews
                </a>';      
    
    $output .= '</div>';
    return $output;
  }
}