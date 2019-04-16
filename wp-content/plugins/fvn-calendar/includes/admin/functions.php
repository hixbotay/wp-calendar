<?php
//add style sheet
function HB_admin_add_root_stylesheet($screen){
	//FvnImporter::css('hb');
}


//remove auto crop image
//add_filter( 'image_resize_dimensions', 'woafun_image_disable_crop', 10, 6 );
function woafun_image_disable_crop( $enable, $orig_w, $orig_h, $dest_w, $dest_h, $crop )
{
    // Instantly disable this filter after the first run
    remove_filter( current_filter(), __FUNCTION__ );

    return image_resize_dimensions( $orig_w, $orig_h, $dest_w, $dest_h, false );
}