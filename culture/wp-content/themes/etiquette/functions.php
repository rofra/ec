<?php 
if ( function_exists('register_sidebars') )
   register_sidebars(1);

if ( function_exists( 'add_theme_support' ) ) { // Added in 2.9
	    add_theme_support( 'post-thumbnails' );
	    set_post_thumbnail_size( 515, 335, true ); // Normal post thumbnails
	    add_image_size( 'small-post-thumb', 241, 157, true  ); // Small post thumbnails
		add_image_size( 'special-edition', 575, 425, true  ); // Special edition gallery
}

function new_excerpt_length($length) {
	return 50;
}
add_filter('excerpt_length', 'new_excerpt_length');


function new_excerpt_more($more) {
	global $post;
	return ' <a href="'. get_permalink($post->ID) . '">Read more...</a>';

}
add_filter('excerpt_more', 'new_excerpt_more');

add_action ('init', 'register_custom_post_types');

function register_custom_post_types() {

    register_post_type('specialedition', array(
      'label' => __('Special Edition'),
      'singular_label' => __('Special Edition'),
      'labels' => array(
        'add_new_item' => __('Add New Special Edition'),
        'edit_item' => __('Edit Special Edition'),
        'new_item' => __('New Special Edition'),
        'view_item' => __('View Special Edition')),
      'public' => true,
      'show_ui' => true,
      'capability_type' => 'post',
      'hierarchical' => false,
      'query_var' => false,
      //'register_meta_box_cb' => 'article_add_box',
      'supports' => array('title', 'editor', 'author', 'comments', 'excerpt','thumbnail','custom-fields'),
      'menu_position' => 4,
     //'taxonomies' => array('category', 'post_tag')
  ));

}


// add_filter("the_excerpt", "plugin_myContentFilter");

//  function plugin_myContentFilter($excerpt)
//  {
    // Take the existing content and return a subset of it
//    return substr($excerpt, 0, 250);
 // }



   add_shortcode('gallery', 'sators_gallery_shortcode');

    /**
    * The Gallery shortcode - modified for link="none".
    */
    function sators_gallery_shortcode($attr) {
    global $post, $wp_locale;

    static $instance = 0;
    $instance++;

    // Allow plugins/themes to override the default gallery template.
    $output = apply_filters('post_gallery', '', $attr);
    if ( $output != '' )
    return $output;

    // We're trusting author input, so let's at least make sure it looks like a valid orderby statement
    if ( isset( $attr['orderby'] ) ) {
    $attr['orderby'] = sanitize_sql_orderby( $attr['orderby'] );
    if ( !$attr['orderby'] )
    unset( $attr['orderby'] );
    }

    extract(shortcode_atts(array(
    'order'      => 'ASC',
    'orderby'    => 'menu_order ID',
    'id'         => $post->ID,
    'itemtag'    => 'div',
    'icontag'    => 'div',
    'captiontag' => 'div',
    'columns'    => 100,
    'size'       => 'special-edition',
    'include'    => '',
    'exclude'    => ''
    ), $attr));

    $id = intval($id);
    if ( 'RAND' == $order )
    $orderby = 'none';

    if ( !empty($include) ) {
    $include = preg_replace( '/[^0-9,]+/', '', $include );
    $_attachments = get_posts( array('include' => $include, 'post_status' => 'inherit', 'post_type' => 'attachment', 'post_mime_type' => 'image', 'order' => $order, 'orderby' => $orderby) );

    $attachments = array();
    foreach ( $_attachments as $key => $val ) {
    $attachments[$val->ID] = $_attachments[$key];
    }
    } elseif ( !empty($exclude) ) {
    $exclude = preg_replace( '/[^0-9,]+/', '', $exclude );
    $attachments = get_children( array('post_parent' => $id, 'exclude' => $exclude, 'post_status' => 'inherit', 'post_type' => 'attachment', 'post_mime_type' => 'image', 'order' => $order, 'orderby' => $orderby) );
    } else {
    $attachments = get_children( array('post_parent' => $id, 'post_status' => 'inherit', 'post_type' => 'attachment', 'post_mime_type' => 'image', 'order' => $order, 'orderby' => $orderby) );
    }

    if ( empty($attachments) )
    return '';

    if ( is_feed() ) {
    $output = "\n";
    foreach ( $attachments as $att_id => $attachment )
    $output .= wp_get_attachment_link($att_id, $size, true) . "\n";
    return $output;
    }

    $itemtag = tag_escape($itemtag);
    $captiontag = tag_escape($captiontag);
    $columns = intval($columns);
    $itemwidth = $columns > 0 ? floor(100/$columns) : 100;
    $float = is_rtl() ? 'right' : 'left';

    $selector = "gallery-{$instance}";

    $output = apply_filters('gallery_style', "
    <!-- style type='text/css'>
    #{$selector} {
    margin: auto;
    }
    #{$selector} .gallery-item {
    float: {$float};
    margin-top: 10px;
    text-align: center;
    width: {$itemwidth}%;            }
    #{$selector} img {
    border: 2px solid #cfcfcf;
    }
    #{$selector} .gallery-caption {
    margin-left: 0;
    }
    </style -->
    <!-- see gallery_shortcode() in wp-includes/media.php -->
    <div class='gallery-wrap'><div id='$selector' class='gallery galleryid-{$id}'>");

    $i = 0;
    foreach ( $attachments as $id => $attachment ) {
	//	print_r( $attachment );

    if(isset($attr['link']) && 'file' == $attr['link']){ $link = wp_get_attachment_link($id, $size, false, false); } elseif (isset($attr['link']) && 'none' == $attr['link']){$link = wp_get_attachment_image($id, $size, false);} else {  $link = wp_get_attachment_link($id, $size, true, false); }

    $output .= "<{$itemtag} class='gallery-item'>";
    $output .= "
    <{$icontag} class='gallery-icon'>
    $link
    </{$icontag}>";
    if ( $captiontag && trim($attachment->post_excerpt) ) {
    $output .= "
    <{$captiontag} class='gallery-caption'>
    " . wptexturize($attachment->post_excerpt) . "
    </{$captiontag}>";
    }
    $output .= "</{$itemtag}>";
//      if ( $columns > 0 && ++$i % $columns == 0 )
//      $output .= '<br style="clear: both;" />';
      }

    $output .= " 

	</div>
	<div id='{$post->ID}-nav'></div>
	<script type='text/javascript'>
	jQuery(document).ready(function(){
		jQuery('.galleryid-{$post->ID}').cycle({
			pager:		'#{$post->ID}-nav',
			timeout:	0,
			speed:		300
		});
	});
	</script>
	<div class='clear'></div></div>\n";

    return $output;
    }
?>
