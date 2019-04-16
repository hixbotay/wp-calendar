<?php


/**
 * Support for generating html code
 *
 * @package 	FVN-extension
 * @author 		Vuong Anh Duong
 * @link 		http://http://woafun.com/
 * @copyright 	Copyright (C) 2011 - 2012 Vuong Anh Duong
 * @license 	GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 * @version 	$Id: html.php 82 2012-08-16 15:07:10Z quannv $
 **/
defined('ABSPATH') or die('Restricted access');

class FvnHtml
{
	/**
	 * Return a select options html
	 * @param mixed $list list of option format array(object,object,etc)
	 * @param string $name name of select list box
	 * @param string $attr attributes of select
	 * @param unknown $select_value key of object in list is used as option of select list
	 * @param unknown $label key of object in list is used as label of option of select list
	 * @param unknown $selected selected value
	 * @param unknown $id id of select list box
	 */
	static function select($options, $name, $attr, $key, $label, $selected = null, $id=null,$blank_select = ''){
		if($blank_select){
			array_unshift($options, (object)array($label=>$blank_select,$key=>''));
		}
		$ids = $id ? 'id="'.$id.'"' : '';
		$html = '<select name="'.$name.'" '.$attr.' '.$ids.'>';
		foreach ($options as $i=>$object){
			$object = (object)$object;
			$html .= '<option value="'.$object->$key.'" '.selected($object->$key, $selected,false).' >'.$object->$label.'</option>';
		}
		$html .= '</select>';
		return $html;
	}
	/**
	 * Creating radio box same list box
	 * @param unknown $list
	 * @param unknown $name
	 * @param unknown $attr
	 * @param unknown $key
	 * @param unknown $label
	 * @param unknown $selected
	 * @param unknown $id
	 */
	static function radio($list, $name, $attr, $key, $label, $selected = null, $id=null){
		$ids = $id ? 'id="'.$id.'"' : '';
		$html = '';
		foreach ($list as $i=>$object){
			$html .= '<input type="radio" name="'.$name.'" value="'.$object->$key.'" '.checked($object->$key, $selected,false).' />'.$object->$label;
		}
		return $html;
	}
	
	//create a text box
	static function text($name, $attr, $selected = null, $id=null){
		$ids = $id ? 'id="'.$id.'"' : '';
		$html = '<input type="text" name="'.$name.'" '.$attr.' '.$ids.' value="'.$selected.'"/>';
		return $html;
	}
	static function mail($name, $attr, $selected = null, $id=null){
		$ids = $id ? 'id="'.$id.'"' : '';
		$html = '<input type="mail" name="'.$name.'" '.$attr.' '.$ids.' value="'.$selected.'"/>';
		return $html;
	}
	//create a text area box
	static function textarea($name, $attr, $selected = null, $id=null){
		$ids = $id ? 'id="'.$id.'"' : '';
		$html = '<textarea name="'.$name.'" '.$attr.' '.$ids.'>'.$selected.'</textarea>';
		return $html;
	}
	//create a number box
	static function number($name, $attr, $selected = null, $id=null){
		$ids = $id ? 'id="'.$id.'"' : '';
		$html = '<input type="number" name="'.$name.'" '.$attr.' '.$ids.' value="'.$selected.'"/>';
		return $html;
	}
	
	//create complete html form with control-group class
	static function combo_input($name, $label, $attr, $selected = null, $id=null){
		$ids = $id ? 'id="'.$id.'"' : '';
		$html = '<div class="control-group">';
		$html .= '<div class="control-label">'.$label.'</div>';
		$html .= '<div class="controls">';
		$html .= '<input type="text" name="'.$name.'" '.$attr.' '.$ids.' value="'.$selected.'"/>';
		$html .= '</div>
				</div>';
		return $html;
	}
	
	//show count timmer with PHP time zone at view flight
	static function getTimmer(){
		echo '<script>
			var d = new Date("'.the_date('M d, Y H:i:s',null,null,false ).'");
			var myVar = setInterval(static function(){ myTimer(d) }, 1000);
			function myTimer(d) {
				d.setSeconds(d.getSeconds() + 1);
				var t = "'.the_date('M d, Y ',null,null,false ).'"+d.toLocaleTimeString();
				document.getElementById("bookpro_timmer").innerHTML = t;
			}
			
		</script>
		<div id="bookpro_timmer"></div>';
	}
	
	static function booleanlist($name, $attribs = array(), $selected = null, $yes = 'Yes', $no = 'No', $id = false){
		$html = '<input type="radio" value="0" '.checked($selected,0,0).' name="'.$name.'"/>'.$no;
		$html .= '<input type="radio" value="1" '.checked($selected,1,0).' name="'.$name.'"/>'.$yes;
		return $html;
	}
	static function add_datepicker_lib(){
		wp_enqueue_script( 'jquery-ui-datepicker' );
		wp_register_style( 'jquery-ui', 'http://code.jquery.com/ui/1.11.2/themes/smoothness/jquery-ui.css' );
		wp_enqueue_style( 'jquery-ui' );		
	}
	static function add_timepicker_lib(){
		wp_register_style( 'time-picker', FVN_URL.'assets/css/bootstrap-timepicker.min.css' );
		wp_enqueue_script( 'time-picker', FVN_URL.'assets/js/bootstrap-timepicker.min.js' );
	}
	
	/*
	 * Create calendar html box
	 */
	static function calendar($value = null, $name,$id = null,$format,$attr = '',$options=null){
		
		$id = $id ? 'id="'.$id.'"' : '';
		echo '<input type="text" name="'.$name.'" '.$id.' '.$attr.' value="'.$value.'"/>';
		$option_str = 'dateFormat: "'.$format.'"';
		if(is_array($options)){
			$array = array();
			foreach($options as $k=>$v){
				if(is_string($v)){
					$v = '"'.$v.'"';
				}
				$array[] = $k.':'.$v;				
			}
			$option_str .= ','.implode(',',$array);
		}
		echo self::enqueueScript('jQuery(document).ready(function($){
			$("input[name='."'".$name."'".']" ).datepicker({'.$option_str.'});
		});');
	}
	
	static function enqueueScript($script){
		return "<script type='text/javascript'>$script</script>";
	}
	
	/**
	 * Generates a HTML check box or boxes
	 * @param array An array of objects
	 * @param string The value of the HTML name attribute
	 * @param string Additional HTML attributes for the <select> tag
	 * @param mixed The key that is selected. Can be array of keys or just one key
	 * @param string The name of the object variable for the option value
	 * @param string The name of the object variable for the option text
	 * @returns string HTML for the select list
	 */
	static function checkBoxList( $arr, $tag_name, $tag_attribs,  $key='value', $text='text',$selected=array(),$id=null ) {
		$html='';
		foreach ($arr as $i=>$obj){
			$checked=self::checked($obj->$key,$selected);
			$ids = $id ? "id='{$id}_{$i}'" : '';
			$html .= "<div class='HB-checkbox-item'><input type='checkbox' $ids name='$tag_name' $tag_attribs $checked value='{$obj->$key}'/>{$obj->$text}</div>";
		}
		return $html;
	}
	
	static function integerlist($start, $limit, $default, $name,$attr=null,$selected){
		$html = '<select name="'.$name.'" '.$attr.'>';
		for($start; $start <= $limit; $start++){
			$html .= '<option value="'.$start.'" '.selected($start, $selected,false).' >'.$start.'</option>';
		}
		$html .= '</select>';
		return $html;
	}
	
	static function link($url,$text,$atributes = null){
		
		if(is_array($atributes)){
			$attr = '';
			foreach ($atributes as $key=>$val){
				$attr.= $key.'="'.$val.'"';
			}
		}else{
			$attr = $atributes;
		}
		
		return "<a href='$url' $attr>$text</a>";
	}
	
	/**
	 * Fill in selected for select list
	 * @param unknown $values value of the option
	 * @param array or string $selected value of the selected
	 * @return string
	 */
	static function selected($value, $selected = null){
		$html = '';
		if(is_array($selected)){
			$html = in_array($value,$selected) ? 'selected="selected"' : '';
		}else{
			$html = $value == $selected ? 'selected="selected"' : '';
		}
		return $html;
	}
	
	/**
	 * Fill in selected for checkbox or radio
	 * @param unknown $values value of the option
	 * @param array or string $selected value of the selected
	 * @return string
	 */
	static function checked($value, $selected = null){
		$html = '';
		if(is_array($selected)){
			$html = in_array($value,$selected) ? 'checked="checked"' : '';
		}else{
			$html = $value == $selected ? 'checked="checked"' : '';
		}
		return $html;
	}
	
	static function media_select($name,$id,$selected='',$multi=false) {
// 		debug($selected);
		// Save attachment ID
		wp_enqueue_media();
		$src = '';
		$image ='';//for multi select
		?>	
			
			
			<?php if($multi){
				foreach ($selected as $i=>$s){
					$src = wp_get_attachment_image_src($s,'thumbnail',true)[0];
				?>
					<div class="upload-image-section mb-1">
						<input type="button" class="button upload_image_button" value="<?php _e( 'Upload image' ); ?>" />
						<div class='image-preview-wrapper'>
							<img class='image-preview' src='<?php echo $src?>' style='max-height: 100px; width: 100px;'>
						</div>
						<input type='hidden' class="upload-image-value" name='<?php echo $name?>[]' value='<?php echo $s?>'>
						<a href="javascript:void(0)" onclick="jQuery(this).parent().remove()"><?php echo __('Delete')?></a>
					</div>
				<?php }
				}else{?>
					<div class="upload-image-section">
						<input id="upload_image_button" type="button" class="button" value="<?php _e( 'Upload image' ); ?>" />
						<div class='image-preview-wrapper'>
							<img class='image-preview' src='<?php echo $src?>' style='max-height: 100px; width: 100px;'>
						</div>
						<input type='hidden' class="upload-image-value" name='<?php echo $name?>' value='<?php echo $selected?>'>
					</div>
				<?php }?>
				
			
		<div id="new-image-section"></div>
		<?php if($multi){?>
		<button id="add-new-image" type="button" class="btn-primary btn add-new-image"><?php echo __('Add new image')?></button>
		<?php }?>
		<script type='text/javascript'>
		jQuery( document ).ready( function( $ ) {
			$('.add-new-image').click(function(){
				var html = '<div class="upload-image-section mb-1">'+
						'<input type="button" class="button upload_image_button" value="<?php _e( 'Upload image' ); ?>" />'+
						'<div class="image-preview-wrapper">'+
							'<img class="image-preview" src="" style="max-height: 100px; width: 100px;">'+
						'</div>'+
						'<input type="hidden" class="upload-image-value" name="<?php echo $name?>[]" />'+
						'<a href="javascript:void(0)" onclick="jQuery(this).parent().remove()"><?php echo __('Delete')?></a>'+
					'</div>';				
				$('#new-image-section').append(html);
				
			});
			// Uploading files
			
			var wp_media_post_id = wp.media.model.settings.post.id; // Store the old id
			
			
			jQuery('.upload_image_button').live('click', function( event ){
				var file_frame;
				event.preventDefault();
				var parent = $(this).parents('.upload-image-section');
				
				var set_to_post_id = parent.find('.upload-image-value').val();
				// If the media frame already exists, reopen it.
				if ( file_frame ) {
					// Set the post ID to what we want
					file_frame.uploader.uploader.param( 'post_id', set_to_post_id );
					// Open frame
					file_frame.open();
					return;
				} else {
					// Set the wp.media post id so the uploader grabs the ID we want when initialised
					wp.media.model.settings.post.id = set_to_post_id;
				}
				// Create the media frame.
				file_frame = wp.media.frames.file_frame = wp.media({
					title: 'Select a image to upload',
					button: {
						text: 'Use this image',
					},
					multiple: false	// Set to true to allow multiple files to be selected
				});
				// When an image is selected, run a callback.
				file_frame.on( 'select', function() {
					// We set multiple to false so only get one image from the uploader
					attachment = file_frame.state().get('selection').first().toJSON();
					// Do something with attachment.id and/or attachment.url here
					parent.find('.image-preview').attr( 'src', attachment.url ).css( 'width', 'auto' );
					parent.find('.upload-image-value').val( attachment.id );
					parent=null;
					// Restore the main post ID
					wp.media.model.settings.post.id = attachment.id;
				});
					// Finally, open the modal
					file_frame.open();
					//parent=null;
			});
			// Restore the main ID when the add media button is pressed
			jQuery( 'a.add_media' ).on( 'click', function() {
				console.log(wp_media_post_id);
				wp.media.model.settings.post.id = wp_media_post_id;
			});
		});
	</script>
		<?php
	}
	
	static function editor($name,array $attr,$value,$id){	
		$attr['textarea_name'] = $name;
		return wp_editor( $value, $id, $attr );
	}

	static function time_picker($name, $value,$id,$attr=null,$options=null){
		self::add_timepicker_lib();
		if(!isset($options['showMeridian']))//format 24 gio
			$options['showMeridian'] = false;
		
		$script = '<script>
					jQuery(document).ready(function($){
						$("#'.$id.'").timepicker('.json_encode($options).');
					});</script>';
		$html = '<div class="input-append bootstrap-timepicker">
							<input type="text" name="'.$name.'" id="'.$id.'" '.$attr.' value="'.$value.'"/>
							<span class="add-on"><i class="icon-clock icon-black"></i></span>
						</div>	';
		return $script.$html;
	}
		
	
			

}

?>