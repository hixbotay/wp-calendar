<?php

class FvnParamOrderStatus extends FvnParam{
    
    const PENDING = ['display'=>'Đang xử lí', 'value'=>'1'];
	const CONFIRMED = ['display'=>'Xác nhận', 'value'=>'2'];
    const WAITTING_APPROVE = ['display'=>'Chờ phê duyệt', 'value'=>'3'];
    const CLOSED = ['display'=>'Đã đóng', 'value'=>'4'];
    const CANCELLED = ['display'=>'Đã hủy', 'value'=>'5'];


}