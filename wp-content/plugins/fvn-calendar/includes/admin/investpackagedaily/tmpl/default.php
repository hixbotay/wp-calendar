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
?>
<form action="admin.php" action="GET" id="adminForm" name="adminForm">
<div class="wrap">
	<h1><?php echo __("Các gói đầu tư")?>
	<!-- <a href="<?php echo admin_url('admin.php?page=investpackage&layout=edit')?>" class="page-title-action"><?php echo __("Add")?></a> -->
	</h1>

	<div>
		
			<table class="wp-list-table widefat fixed striped posts">
				<thead>
					<tr>
						<th>Tên gói</th>
						<th>Giá nhỏ nhất</th>
						<th>Lãi xuất</th>
						<th>Chi tiết</th>
					</tr>
				</thead>
				<tbody>
					<?php foreach ($this->items as $item){ ?>
						<tr>
							<td><a href="admin.php?page=investpackage&layout=edit&id=<?php echo $item->id?>"><?php echo $item->name;?></a></td>
							<td><?php echo $item->min_price;?></td>
							<td><?php echo reset($item->rate);?>%</td>
							<td><?php echo $item->description;?></td>						
						</tr>
					<?php }?>
				</tbody>
			</table>
			<input type="hidden" name="page" value="<?php echo $this->input->get('page')?>"/>
			<?php echo $this->pagination->getListFooter()?>
			
		
	</div>
</div>
</form>