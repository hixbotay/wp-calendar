<?php
$desc = isset($this->params->description) ? $this->params->description : '';
echo !empty($desc) ? $desc : __('Thanh toán bằng internet banking','hb');