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
HBImporter::helper('currency');
?>
<form action="admin.php" action="GET" id="adminForm" name="adminForm">
<div class="wrap">
	<h1><?php echo __("Các giao dịch")?>	</h1>
	<a href="<?php echo admin_url('admin.php?page=transaction&layout=edit')?>" class="page-title-action"><?php echo __("Add")?></a>
	<div class="tablenav top">
		
		<div class="alignleft actions">
			<?php echo FvnHtml::text('filter_title', '',$this->getState('filter_title'))?>
			<input name="filter_action" id="post-query-submit"
				class="button" value="Lọc" type="submit">
		</div>

		<br class="clear">
	</div>


	<div>
		
			<table class="wp-list-table widefat fixed striped posts">
				<thead>
					<tr>
						<th><?php echo __('User')?></th>
						<th><?php echo __('Số tiền')?></th>
						<th><?php echo __('Nội dung')?></th>
						<th><?php echo __('Ngày tạo')?></th>
					</tr>
				</thead>
				<tbody>
					<?php foreach ($this->items as $item){?>
						<tr>
						<td><?php echo $item->user_display_name.'('.$item->user_email.')';?></td>
						<td><?php echo FvnCurrencyHelper::displayPrice($item->total);?></td>
						<td><?php echo $item->content;?></td>
						<td><?php echo $item->created?></td>
					</tr>
					<?php }?>
				</tbody>
			</table>
			<input type="hidden" name="page" value="<?php echo $this->getState('page')?>"/>
			<?php echo $this->pagination->getListFooter()?>
			
		
	</div>
</div>
</form>