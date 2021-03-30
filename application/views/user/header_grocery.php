<head>

	<?php foreach($css_files as $file): ?>
		<link type="text/css" rel="stylesheet" href="<?php echo $file; ?>" />
	<?php endforeach; ?>
	
	<?php foreach($js_files as $file): ?>
		<script src="<?php echo $file; ?>"></script>
	<?php endforeach; ?>	

	<link rel="stylesheet" type = "text/css" href="<?php echo base_url();?>assets/adminLTE/dist/css/AdminLTE.min.css">
	<link rel="stylesheet" type = "text/css" href="<?php echo base_url();?>assets/adminLTE/dist/css/skins/_all-skins.min.css">
	<link rel ="stylesheet" type = "text/css" href="<?php echo base_url();?>assets/Bootstrap/dist/css/bootstrap.min.css">
	<link rel="stylesheet" type = "text/css" href="<?php echo base_url();?>assets/FontAwesome/css/font-awesome.min.css">
    <link rel="stylesheet" type = "text/css" href="<?php echo base_url();?>assets/IonIcons/css/ionicons.min.css">
	<link rel="stylesheet" type = "text/css" href="<?php echo base_url();?>assets/DateRangePicker/daterangepicker.css">
	<link rel ="shortcut icon" type = "icon" href="<?php echo base_url();?>assets/images/site/<?php echo $site->fav_icon;?>">
	
	<script src="<?php echo base_url();?>assets/Bootbox/bootbox.min.js" type="text/javascript"></script>
	<script src="<?php echo base_url();?>assets/adminLTE/dist/js/app.min.js" type="text/javascript"></script>
	<script src="<?php echo base_url();?>assets/DateRangePicker/moment.min.js" type="text/javascript"></script>
	<script src="<?php echo base_url();?>assets/DateRangePicker/daterangepicker.js" type="text/javascript"></script>
	

</head>
