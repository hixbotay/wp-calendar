<?php 
	$passenger_numer = count($displayData->passengers);
// 	debug($displayData->order);
	?>

<table width="100%" class="table-summary" border="1" cellpadding="5">
	<tbody>
		<tr>
			<th>Type of service</th>
			<th class="text-center">Quantity</th>
			<th class="text-center">Unit price</th>
			<th class="text-center">Total fee</th>
		</tr>
		<tr>
			<td>Visa on Arrival - <?php echo $displayData->period->name?></td>
			<td class="text-center"><?php echo $passenger_numer?></td>
			<td class="text-center"><?php echo FvnCurrencyHelper::displayPrice($displayData->order->params['price']['single'])?></td>
			<td class="text-center"><?php echo FvnCurrencyHelper::displayPrice($displayData->order->params['price']['main'])?></td>
		</tr>
		<?php if($displayData->order->private_later){?>
		<tr>
			<td>Private letter</td>
			<td class="text-center">-</td>
			<td class="text-center"><?php echo FvnCurrencyHelper::displayPrice($displayData->order->params['price']['private_later'])?></td>
			<td class="text-center"><?php echo FvnCurrencyHelper::displayPrice($displayData->order->params['price']['private_later'])?></td>
		</tr>
		<?php }?>
		<?php if($displayData->order->params['airport_fast_track']){?>
		<tr>
			<td>Airport fast check-in</td>
			<td class="text-center"><?php echo $passenger_numer?></td>
			<td class="text-center"><?php echo FvnCurrencyHelper::displayPrice($displayData->order->params['price']['airport_fast_track']['single'])?></td>
			<td class="text-center"><?php echo FvnCurrencyHelper::displayPrice($displayData->order->params['price']['airport_fast_track']['total'])?></td>
		</tr>
		<?php }?>
		<?php if($displayData->order->params['car_service']){?>
		<tr>
			<td><?php echo __('Car '.$displayData->order->params['car_service'].' seats')?></td>
			<td class="text-center">1</td>
			<td class="text-center"><?php echo FvnCurrencyHelper::displayPrice($displayData->order->params['price']['car_service'])?></td>
			<td class="text-center"><?php echo FvnCurrencyHelper::displayPrice($displayData->order->params['price']['car_service'])?></td>
		</tr>
		<?php }?>
		<tr>
			<td class="total" colspan="3">Total</td>
			<td class="text-center total"><?php echo FvnCurrencyHelper::displayPrice($displayData->order->total)?></td>
		</tr>
	</tbody>
</table>