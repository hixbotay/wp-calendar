<?php

class FvnParamOrderStatus extend FvnParam{
    
    const PENDING = ['display'=>'Đang xử lí', 'value'=>'PENDING'];
	const CONFIRMED = ['display'=>'Xác nhận', 'value'=>'CONFIRMED'];
    const WAITTING_APPROVE = ['display'=>'Chờ phê duyệt', 'value'=>'WAITTING_APPROVE'];
    const CLOSED = ['display'=>'Đã đóng', 'value'=>'CLOSED'];
    const CANCELLED = ['display'=>'Đã hủy', 'value'=>'CANCELLED'];


}