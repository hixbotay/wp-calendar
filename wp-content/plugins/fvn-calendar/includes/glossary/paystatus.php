<?php

class FvnParamPayStatus extend FvnParam{
    const SUCCESS = ['display'=>'Đã thanh toán', 'value'=>'SUCCESS'];
    const PENDING = ['display'=>'Chưa thanh toán', 'value'=>'PENDING'];
    const WAITTING_APPROVE = ['display'=>'Chờ phê duyệt', 'value'=>'WAITTING_APPROVE'];
    const CLOSED = ['display'=>'Đã trả lại', 'value'=>'REFUNED'];


}