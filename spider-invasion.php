<?php
/*
Plugin Name: Spider Invasion!
Version: 0.1
Plugin URI: http://www.mendoweb.be/blog/wordpress-plugin-spider-invasion
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

function spider_invasion_js() {
	if( !is_single() ) return;
	$date_post = get_the_date( 'Ymd' );
	if( empty( $date_post ) ) return;
	
	$datetime_current = date_create( date( 'Ymd' ) );
	$datetime_post = date_create( $date_post );
	$interval = date_diff( $datetime_current, $datetime_post );
	$nb_spiders = $interval->format( '%y' );
	if( $nb_spiders == 0 ) return;
	
	$invade_content = FALSE;
	$max_density = 800*800;
	$plugin_images_dir = plugins_url( 'images' , __FILE__ );
	
	$str = <<<JS
<script>
function getRandomPosition(element) {
	var x = document.body.offsetHeight-element.clientHeight;
	var y = document.body.offsetWidth-element.clientWidth;
	var randomX = Math.floor(Math.random()*x);
	var randomY = Math.floor(Math.random()*y);
	return [randomX,randomY];
}
function spawnSpider() {
	var src = '$plugin_images_dir/cute-spider-' + (Math.floor(Math.random() * 3) + 1) + '.png';
	var img = document.createElement('img');
	img.setAttribute("style", "opacity:0.5;position:absolute;");
	img.setAttribute("src", src);
	document.body.appendChild(img);
	var xy = getRandomPosition(img);
	var invadeContent = '$invade_content';
	if(invadeContent === '') {
		var content = document.getElementById('content');
		if(
			content != null && 
			xy[1] > (content.offsetLeft - img.clientHeight) && xy[1] < (content.offsetLeft + content.offsetWidth) && 
			xy[0] > content.offsetTop && xy[0] < (content.offsetTop + content.offsetHeight)
		) {
			img.setAttribute("style", "display:none;");
		}
	}
	img.style.top = xy[0] + 'px';
	img.style.left = xy[1] + 'px';
}
window.onload = function() {
	var surface = document.body.offsetHeight*document.body.offsetWidth;
	var density = Math.floor(surface/$max_density);
	var nbSpiders = $nb_spiders > density ? (density < 1 ? 1 : density) : Math.floor($nb_spiders);
	for(i=0;i<nbSpiders;++i) spawnSpider();
}
</script>
JS;
	echo $str;
}
add_action('wp_head', 'spider_invasion_js');

?>
