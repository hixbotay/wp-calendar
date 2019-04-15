<?php include 'header.php';?>
<h3>Please review your visa application details !</h3>
<?php echo FvnHelper::renderLayout('email_order_info',$displayData)?>
<br>
<h3>Passport details</h3>
<?php echo FvnHelper::renderLayout('email_passenger',$displayData)?>
<br>
<h3>Service fees</h3>
<?php echo FvnHelper::renderLayout('email_summary',$displayData)?>

<table style="width:100%">
	<tr>
		<td><?php echo __('Order number')?></td>
		<td><?php echo $displayData->order->order->order_number?></td>
	</tr>
	<tr>
		<td>Full name</td>
		<td><?php echo $displayData->order->firstname.' '.$displayData->order->lastname?></td>
	</tr>
	<tr>
		<td>Mobile</td>
		<td><?php echo $displayData->order->mobile?></td>
	</tr>
	<tr>
		<td>Email</td>
		<td><?php echo $displayData->order->email?></td>
	</tr>
	<tr>
		<td>Pay status</td>
		<td><?php echo $displayData->order->pay_status?></td>
	</tr>
	<tr>
		<td>Total</td>
		<td><?php echo FvnCurrencyHelper::displayPrice($displayData->order->total,$displayData->order->currency)?></td>
	</tr>
</table> 
<?php include 'footer.php';?>