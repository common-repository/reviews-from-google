<?php

global $stored_reviews_db_version;
$stored_reviews_db_version = '1.0';

function greviews_install() {
	global $wpdb;
  global $stored_reviews_db_version;
  
	$table_name = $wpdb->prefix . 'stored_reviews_wp';
	
	$charset_collate = $wpdb->get_charset_collate();
	$sql = "CREATE TABLE IF NOT EXISTS $table_name (
		id SMALLINT NOT NULL AUTO_INCREMENT PRIMARY KEY,
		title varchar(255) NOT NULL,
		content text NOT NULL,
		rating varchar(255) NOT NULL,
    source varchar(255) NOT NULL,
		author_url varchar(255) DEFAULT NULL,
		author_image_url varchar(255) DEFAULT NULL,
		creation_date datetime DEFAULT CURRENT_TIMESTAMP NOT NULL
	) $charset_collate;";
  
	require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
  
  dbDelta( $sql );
  
  add_option( 'stored_reviews_db_version', $stored_reviews_db_version );
  
	if( !wp_next_scheduled( 'daily_google_reviews_update' ) ) {  
		wp_schedule_event( time(), 'daily', 'daily_google_reviews_update' );  
 	}
}

function greviews_update_db_check() {
  global $stored_reviews_db_version;
  
  if (get_site_option( 'stored_reviews_db_version' ) != $stored_reviews_db_version) {
    greviews_install();
  }
}
add_action( 'plugins_loaded', 'greviews_update_db_check' );

// unschedule event upon plugin deactivation
function greviews_deactivate() {	
	// find out when the last event was scheduled
	$timestamp = wp_next_scheduled ('daily_google_reviews_update');
	// unschedule previous event if any
	wp_unschedule_event ($timestamp, 'daily_google_reviews_update');
}

function update_google_reviews_daily() {
	global $greviews_options;
	
	// do something every day
  $place_id = $greviews_options['id'];
  $key = $greviews_options['key'];
  $cache_enabled = $greviews_options['cache_enabled'];

  $greviews = new GReviews($key, $place_id, $cache_enabled);
  $greviews->get_data();
}
add_action('daily_google_reviews_update', 'update_google_reviews_daily');