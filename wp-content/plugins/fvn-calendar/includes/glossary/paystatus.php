<?php

class FvnParamPayStatus extends FvnParam{
    const SUCCESS = ['display'=>'Đã thanh toán', 'value'=>'1'];
    const PENDING = ['display'=>'Chưa thanh toán', 'value'=>'2'];
    const WAITTING_APPROVE = ['display'=>'Chờ phê duyệt', 'value'=>'3'];
    const CLOSED = ['display'=>'Đã trả lại', 'value'=>'4'];


}