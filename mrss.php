<?php
/*
Plugin Name: MediaRSS
Plugin URI: http://wordpress.org/extend/plugins/mrss/
Description: Adds &lt;media&gt; tags to your feeds.
Version: 1.0
Author: Andy Skelton
Author URI: http://andy.wordpress.com/
*/

add_action('template_redirect', 'mrss_init');

function mrss_init() {
	if ( ! is_feed() )
		return;

	if ( isset( $_GET['mrss'] ) && $_GET['mrss'] == 'off' )
		return;

	add_action('rss2_ns', 'mrss_ns');

	add_action('rss2_item', 'mrss_item', 10, 0);
}

function mrss_ns() {
	?>xmlns:media="http://search.yahoo.com/mrss"
	<?php
}

function mrss_item($content = null) {
	$media = array();

	if ( !isset( $content ) )
		$content = get_the_content();

	// img tags
	if ( preg_match_all('/<img (.+?)>/', $content, $matches) ) {
		foreach ( $matches[1] as $attrs ) {
			$item = $img = array();
			foreach ( wp_kses_hair($attrs, array('http')) as $attr )
				$img[$attr['name']] = $attr['value'];
			if ( !isset($img['src']) )
				continue;
			$item['content']['attr']['url'] = $img['src'];
			$item['content']['attr']['medium'] = 'image';
			if ( !empty($img['title']) ) {
				$item['content']['children']['title']['attr']['type'] = 'html';
				$item['content']['children']['title']['children'][] = $img['title'];
			} elseif ( !empty($img['alt']) ) {
				$item['content']['children']['title']['attr']['type'] = 'html';
				$item['content']['children']['title']['children'][] = $img['alt'];
			}
			$media[] = $item;
		}
	}

	$media = apply_filters('mrss_media', $media);

	if ( !empty($media) )
		foreach( $media as $item )
			mrss_print($item);
}

function mrss_print($element, $indent = 2) {
	echo "\n";
	foreach ( $element as $name => $data ) {
		echo str_repeat("\t", $indent) . "<media:$name";
		if ( !empty($data['attr']) ) {
			foreach ( $data['attr'] as $attr => $value )
				echo " $attr=\"$value\"";
		}
		if ( !empty($data['children']) ) {
			$nl = false;
			echo ">";
			foreach ( $data['children'] as $_name => $_data ) {
				if ( is_int($_name) ) {
					echo $_data;
				} else {
					$nl = true;
					mrss_print( array( $_name => $_data ), $indent + 1 );
				}
			}
			if ( $nl )
				echo str_repeat("\t", $indent);
			echo "</media:$name>\n";
		} else {
			echo " />\n";
		}
	}
}

/*
	SAMPLE CODE
	The following examples are intented to show you how you can develop your own MediaRSS filters.
*/

/*
This function will result in code like this:
		<media:content url="http://localhost/admin.gif" medium="image">
			<media:title type="html">admin</media:title>
		</media:content>
*/
/*
function mrss_add_author_image($media) {
	$name = get_the_author();

	foreach ( array('jpg', 'gif', 'png') as $ext ) {
		if ( file_exists( ABSPATH . "/$name.$ext") ) {
			$item['content']['attr']['url'] = get_option('siteurl') . "/$name.$ext";
			$item['content']['attr']['medium'] = 'image';
			$item['content']['children']['title']['attr']['type'] = 'html';
			$item['content']['children']['title']['children'][] = "$name";
			array_unshift($media, $item);
			break;
		}
	}
	return $media;
}
add_filter('mrss_media', 'mrss_add_author_image');
*/

/*
This function will search post_content and if it finds "[audio http://example.com/song.mp3]" it adds this to the feed:
		<media:content url="http://example.com/song.mp3" medium="audio">
			<media:player url="http://localhost/wp-content/plugins/audio-player/player.swf?soundFile=http://example.com/song.mp3" />
		</media:content>
*/
/*
function mrss_audio_macro($media) {
	$content = get_the_content();

	if ( preg_match_all('/\[audio (.+)]/', $content, $matches) ) {
		foreach ( $matches[1] as $url ) {
			$item = array();
			$url = html_entity_decode($url);
			$url = preg_replace('/[<>"\']/', '', $url);
			$item['content']['attr']['url'] = $url;
			$item['content']['attr']['medium'] = 'audio';
			$item['content']['children']['player']['attr']['url'] = get_option( 'siteurl' ). "/wp-content/plugins/audio-player/player.swf?soundFile=" . $url;
			$media[] = $item;
		}
	}
	
	return $media;
}
add_filter('mrss_media', 'mrss_audio_macro');
*/

?>
