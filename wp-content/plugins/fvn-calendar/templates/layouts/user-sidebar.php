<ul class="nav nav-tabs" id="myTab" role="tablist">
  <li class="nav-item">
    <a class="nav-link <?php echo $displayData['layout'] == 'profile'? 'active' : ''?>"  href="<?php echo site_url('/profile')?>">Thông tin cá nhân</a>
  </li>
  <li class="nav-item">
  <a class="nav-link <?php echo $displayData['layout'] == 'myorders'? 'active' : ''?>"  href="<?php echo site_url('/myorders')?>">Các gói đang đầu tư</a>
  </li>
  <li class="nav-item">
  <a class="nav-link <?php echo $displayData['layout'] == 'mydrawrequest'? 'active' : ''?>"  href="<?php echo site_url('/mydrawrequest')?>">Yêu cầu rút tiền</a>
  </li>
  <li class="nav-item">
  <a class="nav-link <?php echo $displayData['layout'] == 'mytransaction'? 'active' : ''?>"   href="<?php echo site_url('/mytransaction')?>">Giao dịch</a>
  </li>
</ul>
<div class="mb-2"></div>