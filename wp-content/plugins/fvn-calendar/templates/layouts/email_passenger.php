<table width="100%" class="table-summary" border="1" cellpadding="5">
	<tbody>
		<tr>
			<th width="20" class="text-center">#</th>
			<th>Full name</th>
			<th>Gender</th>
			<th>Date of birth</th>
			<th>Nationality</th>
			<th>Passport number</th>
		</tr>
		<?php foreach($displayData->passengers as $i=>$p){?>
		<tr>
			<td class="text-center"><?php echo ($i+1)?></td>
			<td><?php echo $p->firstname.' '.$p->lastname?></td>
			<td><?php echo $p->gender?></td>
			<td><?php echo FvnDateHelper::display($p->birthday)?></td>
			<td><?php echo $displayData->country->country_name?></td>
			<td><?php echo $p->passport?></td>
		</tr>
		<?php }?>
	</tbody>
</table>