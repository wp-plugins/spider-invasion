<?php
/*
Plugin Name: Spider Invasion!
Version: 0.3
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

function date_diff_year($d1, $d2) {
	/*
	// only from PHP 5.3 and later versions
	$datetime_d2 = date_create( $d2 );
	$datetime_d1 = date_create( $d1 );
	$interval = date_diff( $datetime_d2, $datetime_d1 );
	return (int) $interval->format( '%y' );
	*/
	// compatible with PHP 5.2 and lower
	$diff = abs( strtotime( $d2 ) - strtotime( $d1 ) );
	return floor( $diff / (365*60*60*24) );
}

function spider_invasion_js() {
	if( !is_single() && !is_archive() ) return;
	$date_post = get_the_date( 'Ymd' );
	if( empty( $date_post ) ) return;
	
	$nb_spiders = date_diff_year( $date_post, date( 'Ymd' ) );
	if( $nb_spiders == 0 ) return;
	
	$invade_content = FALSE;
	$max_density = 800*800;
	$min_density = 1800*1800;
	$plugin_images_dir = plugins_url( 'images' , __FILE__ );
	$compressJS = TRUE;
	
	echo '<!-- start spider invasion -->';
	if($compressJS) {
		$str = <<<JS
<script>
function getRandomPosition(e){var t=document.body.offsetHeight-e.clientHeight;var n=document.body.offsetWidth-e.clientWidth;var r=Math.floor(Math.random()*t);var i=Math.floor(Math.random()*n);return[r,i]}
function spawnSpider(){var e="$plugin_images_dir/cute-spider-"+(Math.floor(Math.random()*3)+1)+".png";var t=document.createElement("img");t.setAttribute("style","opacity:0.5;position:absolute;");t.setAttribute("src",e);document.body.appendChild(t);var n=getRandomPosition(t);var r="$invade_content";if(r===""){var i=document.getElementById("content");if(i!=null&&n[1]>i.offsetLeft-t.clientHeight&&n[1]<i.offsetLeft+i.offsetWidth&&n[0]>i.offsetTop&&n[0]<i.offsetTop+i.offsetHeight){t.setAttribute("style","display:none;")}}t.style.top=n[0]+"px";t.style.left=n[1]+"px"}
window.onload=function(){var e=document.body.offsetHeight*document.body.offsetWidth;var t=Math.floor(e/$max_density);var n=Math.floor(e/$min_density);var r=$nb_spiders>t?t<1?1:t:Math.floor($nb_spiders);r=$nb_spiders<n?n:$nb_spiders;for(i=0;i<r;++i)spawnSpider()}
</script>
JS;
	}
	else {
		$str = <<<JS
<script>
function getRandomPosition(element) {
	var x = document.body.offsetHeight-element.clientHeight;
	var y = document.body.offsetWidth-element.clientWidth;
	var randomX = Math.floor(Math.random()*x);
	var randomY = Math.floor(Math.random()*y);
	return [randomX,randomY];
}
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
	var densityMax = Math.floor(surface/$max_density);
	var densityMin = Math.floor(surface/$min_density);
	var nbSpiders = $nb_spiders > densityMax ? (densityMax < 1 ? 1 : densityMax) : Math.floor($nb_spiders);
	nbSpiders = $nb_spiders < densityMin ? densityMin : $nb_spiders;
	for(i=0;i<nbSpiders;++i) spawnSpider();
}
</script>
JS;
	}
	echo $str;
    echo '<!-- end spider invasion -->
';
}
add_action('wp_head', 'spider_invasion_js');

?>
