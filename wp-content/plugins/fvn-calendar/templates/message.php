<?php get_header();
?>
<section id="site-main">
<div class="container">
<?php foreach($_SESSION['hb']['message'] as $message){?>
	<center><h2 class="text-<?php echo $message['type']?>"><?php echo $message['content']?></h2></center>
<?php }?>
</div>
</section>
<?php get_footer() ?>