<?php
/*
Plugin Name: Spider Invasion!
Version: 0.5
Plugin URI: http://www.mendoweb.be/blog/wordpress-plugin-spider-invasion/
Description: Spiders invade your oldest posts. The older the post, the more spiders you get.
Author: Mathieu Decaffmeyer
Author URI: http://www.mendoweb.be/
License: GPLv3

This program is free software: you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation, either version 3 of the License, or
(at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program.  If not, see <http://www.gnu.org/licenses/>.
*/

function includeJQuery() {
	if ( !is_admin() ) {
		wp_enqueue_script( 'jquery' );
	}
}
add_action( 'init', 'includeJQuery' );

function get_date_diff($d1, $d2) {
	/*
	// only from PHP 5.3 and later versions
	$datetime_d2 = date_create( $d2 );
	$datetime_d1 = date_create( $d1 );
	$interval = date_diff( $datetime_d2, $datetime_d1 );
	return array(
		'y' => (int) $interval->format( '%y' ), 
		'm' => (int) $interval->format( '%m' ), 
		'd' => (int) $interval->format( '%d' )
	);
	*/
	// compatible with PHP 5.2 and lower
	$diff = abs( strtotime( $d2 ) - strtotime( $d1 ) );
	$years = floor($diff / (365*60*60*24));
	$months = floor(($diff - $years * 365*60*60*24) / (30*60*60*24));
	$days = floor(($diff - $years * 365*60*60*24 - $months*30*60*60*24)/ (60*60*24));
	return array(
		'y' => $years, 
		'm' => $months, 
		'd' => $days
	);
}

function spider_invasion_js() {
	if( 'post' != get_post_type() ) return;
	$date_post = get_the_date( 'Ymd' );
	if( empty( $date_post ) ) return;
	
	$date_diff = get_date_diff( $date_post, date( 'Ymd' ) );
	if( $date_diff['y']*12 + $date_diff['m'] < 8 ) return;
	$nb_spiders = $date_diff['y'];
	$compressJS = TRUE;
	
	wp_enqueue_script( 'spider-invasion', plugins_url( 'js' , __FILE__ ) . '/spider-invasion' . ($compressJS ? '.min' : '') . '.js' );
	$params = array(
		'max_density' => 800*800,
		'min_density' => 1800*1800,
		'invade_content' => FALSE,
		'images_dir' => plugins_url( 'images' , __FILE__ ),
		'nb_spiders' => $nb_spiders,
	);
	wp_localize_script( 'spider-invasion', 'param', $params );
}
add_action('wp_head', 'spider_invasion_js');

?>
