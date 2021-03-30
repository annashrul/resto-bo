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
	
	
	
	<noscript>
		 <meta HTTP-EQUIV="REFRESH" content="0; url=<?php echo base_url();?>nojs"> 
	</noscript>
	
</head>
<body>

