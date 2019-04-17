<?php
/**
 * @package 	FVN-extension
 * @author 		Vuong Anh Duong
 * @link 		http://freelancerviet.net
 * @copyright 	Copyright (C) 2011 - 2012 Vuong Anh Duong
 * @license 	GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 * @version 	$Id$
 **/
// die('ddfdf');
defined ( 'ABSPATH' ) or die ( 'Restricted access' );
FvnImporter::helper('currency','invest');
?>
<form action="admin.php" action="GET" id="adminForm" name="adminForm">
<div class="wrap">
	<h1><?php echo __("Lịch hẹn")?>
	
	</h1>

	<div class="tablenav top">
		
		<div class="alignleft actions">
			<?php echo FvnHtml::text('filter_title', '',$this->input->get('filter_title'))?>
			<input name="filter_action" id="post-query-submit"
				class="button" value="Lọc" type="submit">
		</div>

		<br class="clear">
	</div>


	<div>
		
			<table class="wp-list-table widefat fixed striped posts">
				<thead>
					<tr>
						<th>Ngày</th>
						<th>Thời gian</th>
						<th>Tên</th>
						<th>Điện thoại</th>
						<th>Liên hệ</th>
						<th>Giới tính</th>
						<th>Chú ý</th>
					</tr>
				</thead>
				<tbody>
					<?php foreach ($this->items as $item){?>
						<tr>
						<td><?php echo $item->start;?></td> 
						<td><?php echo $item->start_time.'-'.$item->end_time?></td>
						<td><?php echo $item->name;?></td>
						<td><?php echo $item->phone;?></td>
						<td><?php echo FvnParamVideoCallType::getDisplay($item->type).' '.$item->type_desc;?></td>
						<td><?php FvnParamGender::getDisplay($item->gender);?></td>
						<td><?php echo $item->notes;?></td>
						
					</tr>
					<?php }?>
				</tbody>
			</table>
			<input type="hidden" name="page" value="<?php echo $this->input->get('page')?>"/>
			<?php echo $this->pagination->getListFooter()?>
			
		
	</div>
</div>
</form>