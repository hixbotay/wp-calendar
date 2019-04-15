<?php
/**
 * @package 	FVN-extension
 * @author 		Joombooking
 * @link 		http://http://woafun.com/
 * @copyright 	Copyright (C) 2011 - 2012 Vuong Anh Duong
 * @license 	GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 **/
defined('ABSPATH') or die('Restricted access');

class FvnActionUser extends FvnAction{
	function ajax_quick_view(){
		global $woocommerce;			
		global $post;
		
		$product_id = $_REQUEST['product_id'];
		if(intval($product_id)){
		
			wp( 'p=' . $product_id . '&post_type=product' );
			ob_start();
		
		
			while ( have_posts() ) : the_post(); ?>
			 	    <script>
				 	    var url = <?php echo "'"."$this->wcqv_plugin_dir_url/js/prettyPhoto.init.js'"; ?>;
				 	    jQuery.getScript(url);
				 	    var wc_add_to_cart_variation_params = {"ajax_url":"\/wp-admin\/admin-ajax.php"};     
			            jQuery.getScript("<?php echo $woocommerce->plugin_url(); ?>/assets/js/frontend/add-to-cart-variation.min.js");
			 	    </script>
		 	        <div class="product">  
		
		 	                <div itemscope itemtype="<?php echo woocommerce_get_product_schema(); ?>" id="product-<?php the_ID(); ?>" <?php post_class('product'); ?> >  
		 	                        <?php do_action('wcqv_show_product_sale_flash'); ?> 
		
		 	                           <div class="images">
											<?php 
									
									        if ( has_post_thumbnail() ) {
												$attachment_count = count( $product->get_gallery_attachment_ids() );
												$gallery          = $attachment_count > 0 ? '[product-gallery]' : '';
												$props            = wc_get_product_attachment_props( get_post_thumbnail_id(), $post );
												$image            = get_the_post_thumbnail( $post->ID, apply_filters( 'single_product_large_thumbnail_size', 'shop_single' ), array(
													'title'	 => $props['title'],
													'alt'    => $props['alt'],
												) );
												echo apply_filters( 'woocommerce_single_product_image_html', sprintf( '<a href="%s" itemprop="image" class="woocommerce-main-image zoom" title="%s" data-rel="prettyPhoto' . $gallery . '">%s</a>', $props['url'], $props['caption'], $image ), $post->ID );
											} else {
												echo apply_filters( 'woocommerce_single_product_image_html', sprintf( '<img src="%s" alt="%s" />', wc_placeholder_img_src(), __( 'Placeholder', 'woocommerce' ) ), $post->ID );
											}
									
									
											$attachment_ids = $product->get_gallery_attachment_ids();
											if ( $attachment_ids ) :
												$loop 		= 0;
												$columns 	= apply_filters( 'woocommerce_product_thumbnails_columns', 3 );
												?>
												<div class="thumbnails <?php echo 'columns-' . $columns; ?>"><?php
													foreach ( $attachment_ids as $attachment_id ) {
														$classes = array( 'thumbnail' );
														if ( $loop === 0 || $loop % $columns === 0 )
															$classes[] = 'first';
														if ( ( $loop + 1 ) % $columns === 0 )
															$classes[] = 'last';
														$image_link = wp_get_attachment_url( $attachment_id );
														if ( ! $image_link )
															continue;
														$image_title 	= esc_attr( get_the_title( $attachment_id ) );
														$image_caption 	= esc_attr( get_post_field( 'post_excerpt', $attachment_id ) );
														$image       = wp_get_attachment_image( $attachment_id, apply_filters( 'single_product_small_thumbnail_size', 'shop_thumbnail' ), 0, $attr = array(
															'title'	=> $image_title,
															'alt'	=> $image_title
															) );
														$image_class = esc_attr( implode( ' ', $classes ) );
														echo apply_filters( 'woocommerce_single_product_image_thumbnail_html', sprintf( '<a href="%s" class="%s" title="%s" >%s</a>', $image_link, $image_class, $image_caption, $image ), $attachment_id, $post->ID, $image_class );
														$loop++;
													}
												?>
													
												</div>
											<?php endif; ?>
											</div>
		                               
			 	                        <div class="summary entry-summary scrollable">
			 	                                <div class="summary-content">   
			                                       <?php		
			                                        do_action( 'woocommerce_template_single_title' );
			                                        do_action( 'woocommerce_template_single_rating' );
			                                        do_action( 'woocommerce_template_single_price' );
			                                        do_action( 'woocommerce_template_single_excerpt' );
			                                        do_action( 'woocommerce_template_single_add_to_cart' );
			                                        do_action( 'woocommerce_template_single_meta' );
			                                        ?>
			 	                                </div>
			 	                         </div>
			 	                  <div class="scrollbar_bg"></div>		 
		 	                </div> 
		 	        </div>
		 	       
		 	        <?php endwhile;
		
		            	$post                  = get_post($product_id);
		            	$next_post             = get_next_post();
					    $prev_post             = get_previous_post();
					    $next_post_id          = ($next_post != null)?$next_post->ID:'';
					    $prev_post_id          = ($prev_post != null)?$prev_post->ID:'';
					    $next_post_title       = ($next_post != null)?$next_post->post_title:'';
		 		     	$prev_post_title       = ($prev_post != null)?$prev_post->post_title:'';
					 	$next_thumbnail        = ($next_post != null)?get_the_post_thumbnail( $next_post->ID,
					 		                  'shop_thumbnail',''):'';
		 		     	$prev_thumbnail        = ($prev_post != null)?get_the_post_thumbnail( $prev_post->ID,
		 		     		                   'shop_thumbnail',''):'';
		
		 	        ?> 
		            
		 	        <div class ="wcqv_prev_data" data-wcqv-prev-id = "<?php echo $prev_post_id; ?>">
		 	        <?php echo $prev_post_title; ?>
		 	            <?php echo $prev_thumbnail; ?> 
		 	        </div> 
		 	        <div class ="wcqv_next_data" data-wcqv-next-id = "<?php echo $next_post_id; ?>">
		 	        <?php echo $next_post_title; ?>
		 	             <?php echo $next_thumbnail; ?> 
		 	        </div> 
		
		 	        <?php
		 	                  
		 	        echo  ob_get_clean();
		 	
		 	        exit();
		            
					
		}
	}
}