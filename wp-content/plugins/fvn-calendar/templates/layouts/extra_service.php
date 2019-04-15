<?php
$i=1;
if($displayData['params']['airport_fast_track']){?>
	<div>
		<label><?php echo $i?>. <?php echo __('Airport fast check-in')?></label>
		<span class="price">
		<?php echo FvnCurrencyHelper::displayPrice($displayData['price']['airport_fast_track']['single'])?> x 
		<?php echo $displayData['params']['passenger_number']?> <?php echo $displayData['params']['passenger_number']>1?__('applicants'):__('applicant')?> = 
		<?php echo FvnCurrencyHelper::displayPrice($displayData['price']['airport_fast_track']['total'])?></span>
	</div>
<?php $i++;
}?>

<?php if($displayData['params']['car_service']){?>
	<div>
		<label><?php echo $i?>. <?php echo __('Car pick-up')?></label>
		<span class="price">
			<?php echo __('Car '.$displayData['params']['car_service'].' seats')?> = <?php echo FvnCurrencyHelper::displayPrice($displayData['price']['car_service'])?>
		</span>
	</div>
<?php }?>