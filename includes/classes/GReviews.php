<?php

class GReviews {
  private $key;
  private $place_id;
  private $cache_enabled;
  
  function __construct($key, $place_id, $cache_enabled) {
    $this->key = $key;
    $this->place_id = $place_id;
    $this->cache_enabled = $cache_enabled;
  }

  public function get_data() {
    $url = "https://maps.googleapis.com/maps/api/place/details/json?place_id=".urlencode($this->place_id)."&key=".urlencode($this->key);
    
    // send out a request for the data
    $response = wp_remote_get($url);
    $response = json_decode($response['body'], true);
    
    if ($response['status'] == 'INVALID_REQUEST') {
      return array(
        'error_message' => 'Google Places ID is incorrect'
      );
    } else if ($response['status'] == 'OK') {
      $this->addToDB($response);
    }
    
    return $response;
  }

  private function addToDB($response) {
    $reviews_db = new Stored_Reviews_WP();
    $reviews_db->process_response($response);
  }
}