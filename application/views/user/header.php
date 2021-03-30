<!DOCTYPE html>
<html>

<head>
	<meta charset="UTF-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=1">
	
	<title>
		<?php
		echo $title." | ".$site->title;
		?>
	</title>

	
	<?php
	echo meta("description", $site->meta_descr);
	?>
	
	<?php
	echo meta("keywords", $site->meta_key);
	?>
	

	<link rel="stylesheet" type = "text/css" href="<?php echo base_url();?>assets/adminLTE/dist/css/AdminLTE.min.css">
	<link rel="stylesheet" type = "text/css" href="<?php echo base_url();?>assets/adminLTE/dist/css/skins/_all-skins.min.css">
	<link rel ="stylesheet" type = "text/css" href="<?php echo base_url();?>assets/Bootstrap/dist/css/bootstrap.min.css">
	<!--
	<link rel ="stylesheet" type = "text/css" href="<?php echo base_url();?>assets/DataTables/media/css/dataTables.bootstrap.min.css">
	<link rel="stylesheet" type="text/css" href="<?php echo base_url();?>assets/DataTables/extensions/Buttons/css/buttons.bootstrap.min.css">
	-->
	<link rel="stylesheet" href="<?=base_url()?>assets/DataTables/dataTables.bootstrap.css">
	
	<link rel="stylesheet" type = "text/css" href="<?php echo base_url();?>assets/FontAwesome/css/font-awesome.min.css">
    <link rel="stylesheet" type = "text/css" href="<?php echo base_url();?>assets/IonIcons/css/ionicons.min.css">
	<link rel ="shortcut icon" type = "icon" href="<?php echo base_url();?>assets/images/site/<?php echo $site->fav_icon;?>">
	
	<script src="<?php echo base_url();?>assets/Jquery/jquery-2.2.0.min.js" type="text/javascript"></script>
	<script src="<?php echo base_url();?>assets/adminLTE/dist/js/app.min.js" type="text/javascript"></script>
	<script src="<?php echo base_url();?>assets/adminLTE/plugins/chartjs/Chart.js" type="text/javascript"></script>
	<script src="<?php echo base_url();?>assets/adminLTE/plugins/fastclick/fastclick.min.js" type="text/javascript"></script>
	<script src="<?php echo base_url();?>assets/Bootstrap/dist/js/bootstrap.min.js" type="text/javascript"></script>
	<script src="<?php echo base_url();?>assets/Bootbox/bootbox.min.js" type="text/javascript"></script>
	<!--
	<script src="<?php echo base_url();?>asset/DataTables/media/js/jquery.dataTables.min.js" type="text/javascript"></script>
	<script src="<?php echo base_url();?>asset/DataTables/media/js/dataTables.bootstrap.min.js" type="text/javascript"></script>
	<script src="<?php echo base_url();?>asset/DataTables/extensions/Buttons/js/dataTables.buttons.min.js" type="text/javascript"></script>
	-->
	<link rel="stylesheet" type = "text/css" href="<?php echo base_url();?>assets/adminLTE/plugins/datepicker/datepicker3.css">
	<script src="<?php echo base_url();?>assets/adminLTE/plugins/datepicker/bootstrap-datepicker.js" type="text/javascript"></script>
	
	<link rel="stylesheet" href="<?=base_url()?>assets/adminLTE/plugins/bootstrap-wysihtml5/bootstrap3-wysihtml5.min.css">
	
	<link rel="stylesheet" href="<?php echo base_url();?>assets/adminLTE/plugins/select2/select2.min.css">
	
	<link rel="stylesheet" type = "text/css" href="<?php echo base_url();?>assets/jquery.autocomplete.css">
	<script src="<?php echo base_url();?>assets/jquery.autocomplete.js" type="text/javascript"></script>
	
	<noscript>
		 <meta HTTP-EQUIV="REFRESH" content="0; url=<?php echo base_url();?>nojs"> 
	</noscript>
	
</head>
<body>

<?php if($this->session->userdata('isLogin')==false){ redirect('site'); } ?>

