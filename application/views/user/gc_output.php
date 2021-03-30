<body class="hold-transition skin-purple sidebar-collapse sidebar-mini">
    <div class="wrapper">
	<?php include "content-navtoggle.php" ?>
	<?php include "content-nav.php" ?>

	<div class="content-wrapper"  style="background:white;">
	<?php include "content-top.php" ?>
        
		<div style="height:30px;"></div>
		
		<section class="content" >
		
		<?php 
		if (!empty($output_extra)){
			$cnt = 'extra/'.$output_extra['content'].'.php';
			include $cnt;
		}
		?>
		
		<?php echo $output->output ?>
			
		<?php
			if($this->uri->segment(2) == 'user-list'){
			?>
			<center>
			<a href="<?php echo base_url();?>user/reg-user" class="btn btn-md btn-success">
				<i class="fa fa-plus"></i> Add New User
			</a>
			</center>
		<?php } ?>

        </section>
      
	 </div>

      <?php include "content-bot.php" ?>
	  <?php include "content-controltoggle.php" ?>

    </div>
	<script>
	window.onload = function(){
		$('#prd').daterangepicker().val('');
	};
	</script>
	</body>
	