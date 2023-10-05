<?php
function simple_slideshow_shortcode($attr){

    if( !$attr['id'] ){
    	return;
    }

	$height      = get_post_meta( $attr['id'], 'slideshow_height', true );
	$heightunits = get_post_meta( $attr['id'], 'height_units', true );
	$speed       = get_post_meta( $attr['id'], 'slideshow_speed', true );

    if ( get_post_meta( $attr['id'], 'slideshow_arrow', true ) == 1 ){
    	$arrow = 'true';
    } else {
    	$arrow = 'false';
    }

    if ( get_post_meta( $attr['id'], 'slideshow_bullet', true ) == 1 ){
    	$bullet = 'true';
    } else {
    	$bullet = 'false';
    }

 
    $output = '<div id="flexslideshow-' . esc_attr($attr['id']) . '" class="flexslideshow" data-height-units="' . esc_attr($heightunits) . '" data-slideshow-height="' . esc_attr($height) . '">';
		$output .= '<div class="slides">';
			$image_ids = get_post_meta( $attr['id'], 'simple_slideshow_img' );
     	
	    if ( ! empty( $image_ids ) ) :
	        $image_ids = explode( ',', $image_ids[0] );
	        foreach( $image_ids as $image_id ) {
	            $image_url = wp_get_attachment_url($image_id);
	            if( $heightunits == 'px' ){
	            	$output .= '<div class="slideshow-item"><div style="background-image:url(' . esc_url($image_url) .'); height:' . esc_attr($height) .'px;"></div></div>';	
	            } else {
	            	$output .= '<div class="slideshow-item"><div style="background-image:url(' . esc_url($image_url) .');"></div></div>';
	            }
	            
	        }
	    endif;
	$output .= '</div>';
	$output .= '</div>';
	$output .= '<script>';
	$output .= 'jQuery(document).ready(function(jQuery) {';
	$output .= 'jQuery("#flexslideshow-' . esc_attr($attr['id']) .' .slides").slick({';
	$output .= 'autoplay: true,';
	$output .= 'animation: "slide",';
	$output .= 'autoplaySpeed: '. esc_attr($speed) . ',';
	$output .= 'speed: 300,';
	$output .= 'slidesToShow: 1,';
	$output .= 'slidesToScroll: 1,';
	$output .= 'dots: '. esc_attr($bullet) .',';
	$output .= 'arrows: '. esc_attr($arrow) .',';
	$output .= '});';
	$output .= '});';
	$output .= '</script>';
    return $output;
}
 

add_shortcode( 'simple_slideshow' , 'simple_slideshow_shortcode' );
