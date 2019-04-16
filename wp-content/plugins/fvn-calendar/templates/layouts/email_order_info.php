
<table width="100%" class="table-summary" border="1" cellpadding="5">
	<tbody><tr>
		<th>Type of visa</th>
		<th>Purpose of visit</th>
		<th>Arrival airport</th>
		<th>Processing time</th>
		<th>Arrival date</th>
		<th>Flight number</th>
	</tr>
	<tr>
		<td><?php echo $displayData->period->name?></td>
		<td><?php echo __('For '.$displayData->order->purpose_of_visit);?></td>
		<td><?php echo $displayData->airport->name?></td>
		<td><?php echo $displayData->processing_time->name?></td>
		<td><?php echo FvnDateHelper::display($displayData->order->start).' '.$displayData->order->start_time?></td>
		<td><?php echo $displayData->order->params['flight']['number']?></td>
	</tr>
</tbody></table>