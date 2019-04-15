<div class="panel-fees">
<ul>
	<li class="clearfix">
		<label>Passport holder:</label>
		<span class="passport_holder_t"><?php echo $displayData['params']['country']->country_name?></span>
	</li>
	<li class="clearfix">
		<label>Number of visa:</label>
		<span class="group_size_t"><?php echo $displayData['params']['passenger_number'] ==1 ? '1 '.__('Applicant') : $displayData['params']['passenger_number'].' '.__('Applicants')?></span>
	</li>
	<li class="clearfix">
		<label>Type of visa:</label>
		<span class="visa_type_t"><?php echo $displayData['params']['period']->name?></span>
	</li>
	<li class="clearfix">
		<label>Purpose of visit:</label>
		<span class="visit_purpose_t"><?php echo __('For '.$displayData['params']['purpose_of_visit']);?></span>
	</li>
	<li class="clearfix">
		<label>Arrival airport:</label>
		<span class="arrival_port_t"><?php echo $displayData['params']['airport']->name?></span>
	</li>
	<li class="clearfix">
		<label>Arrival date:</label>
		<span class="arrival_date_t"><?php echo HBDateHelper::display($displayData['params']['start'])?></span>
	</li>
	<li class="clearfix">
		<label>Visa service fee:</label>
		<span class="total_visa_price price"><?php echo FvnCurrencyHelper::displayPrice($displayData['price']['single']).
			' X '.$displayData['params']['passenger_number'].' '.
			($displayData['params']['passenger_number']>1?__('applicants'):__('applicant')).
			' = '.FvnCurrencyHelper::displayPrice($displayData['price']['main']);?></span>
	</li>
	<li class="clearfix" id="processing_time_li" style="display: block">
	<div class="clearfix">
		<label>Processing time:</label>
		<span class="processing_note_t"><?php echo $displayData['params']['processing_time']->name?></span>
	</div>
	<span class="processing_t price"><?php echo FvnCurrencyHelper::displayPrice($displayData['price']['processing_time']['single']).
			' X '.$displayData['params']['passenger_number'].' '.($displayData['params']['passenger_number']>1?__('persons'):__('person')).
			' = '.FvnCurrencyHelper::displayPrice($displayData['price']['processing_time']['total']);?></span>
</li>
<li class="clearfix" id="private_visa_li" style="display: block">
	<label>Private letter:</label>
	<span class="private_visa_t price"><?php echo isset($displayData['price']['private_later']) ? FvnCurrencyHelper::displayPrice($displayData['price']['private_later']) : ''?></span>
</li>

<li class="clearfix" id="extra_service_li" style="display: block">
	<label>Extra services:</label>
	<div class="extra_services">
		<?php echo FvnHelper::renderLayout('extra_service',array('params'=>$displayData['params'],'price'=>$displayData['price']));?>
	</div>
</li>

<li class="clearfix" id="promotion_li" style="background-color: #F8F8F8; display: none">
	<div class="clearfix" id="promotion_box_succed" style="display: none">
		<label class="left">Promotion discount:</label>
		<span class="promotion_t price">- 0 USD</span>
	</div>
		</li>
		<li class="total clearfix">
			<div class="clearfix">
				<label>TOTAL FEE:</label>
				<span class="total_price"><?php echo FvnCurrencyHelper::displayPrice($displayData['price']['total'])?></span>
			</div>
		</li>
	</ul>
	
</div>