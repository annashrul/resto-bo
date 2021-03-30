<?= form_open('database/create_query') ?>
	<textarea style="width:100%;" rows="5" name="query" placeholder="Your Query" autofocus required ></textarea><br/>
	<button type="submit" name="execute">Execute</button>
<?=	form_close() ?>
