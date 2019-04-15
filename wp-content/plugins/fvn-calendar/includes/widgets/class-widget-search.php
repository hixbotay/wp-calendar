<?php
if (! defined ( 'ABSPATH' )) {
	exit ();
}



/**
 * Display form search route
 */
class HB_Widget_Search extends HB_Widget {
	
	/**
	 * Constructor.
	 */
	public function __construct() {
		$this->form_path = FVN_PATH . 'includes/widgets/form/widget-search.xml';
		parent::__construct ( false, $name = __ ( 'Search', 'hb' ), array (
				'classname' => 'HB_Widget_Search',
				'description' => __ ( 'Display search route form', 'hb' ) 
		) );
	}
	function createAiportSelectBox($field, $selected) {
		$options = array ();
		global $wpdb;
		$options = $wpdb->get_results ( "
				SELECT dest.post_title as text,dest.ID as value
				FROM $wpdb->posts as route
				LEFT JOIN $wpdb->postmeta as dest_from ON (route.ID = dest_from.post_id AND dest_from.meta_key = '_from')
				LEFT JOIN $wpdb->posts as dest ON (dest_from.meta_value = dest.ID)
				WHERE route.post_status = 'publish'
				AND route.post_type = 'HB_route'
				GROUP BY dest.ID
				ORDER BY dest.post_title ASC
				" );
		$option = ( object ) array (
				'value' => 0,
				'text' => __ ( 'From', 'hb' ) 
		);
		array_unshift ( $options, $option );
		$select = FvnHtml::select ( $options, $field, 'style="width:200px;"', 'value', 'text', $selected, 'desfrom' );
		return $select;
	}
	function getArrivalDestination($field, $selected, $from) {
		global $wpdb;
		$options = $wpdb->get_results ( "
				SELECT dest.post_title as text,dest.ID as value
				FROM $wpdb->posts as route
				LEFT JOIN $wpdb->postmeta as dest_from ON (route.ID = dest_from.post_id AND dest_from.meta_key = '_from')
				LEFT JOIN $wpdb->postmeta as dest_to ON (route.ID = dest_to.post_id AND dest_to.meta_key = '_to')
				LEFT JOIN $wpdb->posts as dest ON (dest_to.meta_value = dest.ID)
				WHERE route.post_status = 'publish'
				AND dest_from.meta_value = $from
				AND route.post_type = 'HB_route'
				GROUP BY dest.ID
				ORDER BY dest.post_title ASC
				" );
		$option = ( object ) array (
				'value' => 0,
				'text' => __ ( 'To', 'hb' ) 
		);
		array_unshift ( $options, $option );
		$select = FvnHtml::select ( $options, $field, 'style="width:200px;"', 'value', 'text', $selected, 'desto' );
		return $select;
	}
	
	/**
	 * Output widget.
	 *
	 * @param array $args        	
	 * @param array $instance        	
	 */
	public function output($args, $instance) {
		HBImporter::js ( 'select2', 'widget_HB_search' );
		HBImporter::css ( 'select2' );
		HBImporter::helper ( 'date' );
		wp_enqueue_script ( 'jquery-ui-datepicker' );
		wp_enqueue_style ( 'jquery-style', 'http://ajax.googleapis.com/ajax/libs/jqueryui/1.8.2/themes/smoothness/jquery-ui.css' );
		$js_format = HBDateHelper::getConvertDateFormat ( 'J' );
		$date_format = HBDateHelper::getConvertDateFormat ( 'P' );
		echo '<script type="text/javascript">
				var dateFormat = "' . $js_format . '";
				var msg_depart_warn = "' . __ ( 'Please select depature destination', 'hb' ) . '";
				var msg_arrival_warn = "' . __ ( 'Please select arrival destination', 'hb' ) . '";
				var msg_depart_date_warn = "' . __ ( 'Please select depature date', 'hb' ) . '";
				var msg_return_date_warn = "' . __ ( 'Please select return date', 'hb' ) . '";
				var msg_return_date_invalid_warn = "' . __ ( 'Return date is invalid', 'hb' ) . '";
				var getDestReturnUrl = "' . wp_nonce_url ( site_url ( 'index.php?jbaction=route&task=ajaxgetreturndest' ), 'hb_action', 'hb_meta_nonce' ) . '";
			</script>';
		$cart = HBFactory::getCart ();
		if (! $cart->from)
			$cart->from = 0;
		
		$from_select = $this->createAiportSelectBox ( 'desfrom', $cart->from );
		$desto = $this->getArrivalDestination ( 'desto', $cart->to, $cart->from );
		$adult_limit = ! empty ( $instance ['adult_limit'] ) ? $instance ['adult_limit'] : 10;
		$child_limit = ! empty ( $instance ['child_limit'] ) ? $instance ['child_limit'] : 10;
		$infant_limit = ! empty ( $instance ['infant_limit'] ) ? $instance ['infant_limit'] : 10;
		$today = HBFactory::getDate ( 'now', get_option ( 'timezone_string' ) );
		
		if ($cart->start) {
			$start = HBFactory::getDate ( $cart->start )->format ( $date_format );
		} else {
			$today->add ( new DateInterval ( 'P1D' ) );
			$start = $today->format ( $date_format );
		}
		if ($cart->end) {
			$end = HBFactory::getDate ( $cart->end )->format ( $date_format );
		} else {
			$today->add ( new DateInterval ( 'P2D' ) );
			$end = $today->format ( $date_format );
		}
		?>
<form name="routeSearchForm" class="" method="post"
	action="<?php echo site_url('index.php')?>" id="routeFrm"
	onsubmit="return validateSearch()">
	<div class="container-fluid">
	<div id='search_form' class="row">
		<div class="col-md-12">
			<div class="row">
				<div class="form-inline">
							<?php echo FvnHtml::booleanlist('roundtrip','class="inputbox"',isset($cart->roundtrip) ? $cart->roundtrip : 1,__('Roundtrip','hb'),__('One way','hb'),'roundtrip')?>
						</div>
			</div>
			<div class="row" id="destination_select">
						<?php echo $from_select?>
						&nbsp;
						<?php echo $desto?>
				</div>
			<div class="row">
				<div class="pull-left" style="margin-right: 10px">
					<div class="control-group">
						<div class="control-label">
							<label><?php echo __('Departure date','hb')?></label>
						</div>
						<div class="controls">
							<div class="input-append">
								<input type="text" class="inputbox input-block-level "
									name="start" id="start" value="<?php echo $start ?>" size="13"
									maxlength="10" readonly />
							</div>
						</div>
					</div>
				</div>
				<div class="pull-left" id="returnDate">
					<div class="control-group">
						<div class="control-label">
							<label><?php echo __('Return date','hb')?></label>
						</div>
						<div class="controls">
							<div class="input-append">
								<input type="text" class="inputbox input-block-level" name="end"
									id="end" value="<?php echo $end ?>" size="13" maxlength="10"
									readonly />
							</div>
						</div>
					</div>
				</div>

			</div>

			<div class="row">
				<div>
					<div class="pull-left" style="margin-right: 10px">
						<div class="control-group">
							<div class="control-label">
								<label class="help-inline" for="adult"><?php echo __('Adult','hb')?></label>
							</div>
							<div class="controls">
		            						<?php echo FvnHtml::integerlist(1, $adult_limit, 1, 'adult','class="input-mini"',$cart->adult)?>
		            					</div>
						</div>
					</div>
					<div class="pull-left" style="margin-right: 10px">
		            				<?php if($instance['child_option']){?>
		            					<div class="control-group">
							<div class="control-label">
								<label class="help-inline" for="adult"><?php echo __('Child','hb')?> </label>
							</div>
							<div class="controls">
		            							<?php echo FvnHtml::integerlist(0, $child_limit, 0, 'children','class="input-mini"',$cart->children)?>
		            						</div>
						</div>				
		            				<?php }?>
		            			</div>
					<div class="pull-left" style="margin-right: 10px">
		            				<?php if($instance['infant_option']){?>
		            					<div class="control-group">
							<div class="control-label">
								<label class="help-inline" for="adult"><?php echo __('Infant','hb')?></label>
							</div>
							<div class="controls">
		            							<?php echo FvnHtml::integerlist(0, $infant_limit, 0, 'infant','class="input-mini"',$cart->infant)?>
		            						</div>
						</div>
		            				<?php }?>
		            			</div>
				</div>


			</div>

			<div class="row div_bottonsearch">
				<div class="center" align="center">
					<button type="submit" class="btn btn-warning"><?php echo __('Search','hb')?></button>
				</div>
			</div>
		</div>


	</div>
	</div>
	<input type="hidden" name="jbaction" value="route" /> 
			<?php echo wp_nonce_field('hb_action','hb_meta_nonce')?>
			<input type="hidden" name="task" value="search" />

</form>
<?php
		return;
	}
}
