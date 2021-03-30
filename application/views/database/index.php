<?= form_open('database') ?>
	<input type="text" name="menu" placeholder="Your Menu" autofocus required /><br/>
<?=	form_close() ?>

<?php if(isset($content)){ $this->load->view('database/'.$content); } ?>
