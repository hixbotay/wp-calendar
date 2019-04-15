<?php
$desc = isset($this->params->description) ? $this->params->description : '';
echo !empty($desc) ? $desc : __('Cash on delivery','hb');