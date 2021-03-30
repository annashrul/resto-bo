
<!-- Main content -->
<section class="content">

    <!-- Default box -->
    <div class="box">
        <div class="box-header with-border">
            <h3 class="box-title"><?=$title?></h3>
            <div class="box-tools pull-right">
                <button class="btn btn-box-tool" data-widget="collapse" data-toggle="tooltip" title="Collapse"><i class="fa fa-minus"></i></button>
                <!--<button class="btn btn-box-tool" data-widget="remove" data-toggle="tooltip" title="Remove"><i class="fa fa-times"></i></button>-->
            </div>
        </div>
        <div class="box-body">
            <?= form_open($content); ?>
			<div class="row" style="margin-bottom: 10px;">
				<div class="col-sm-12">
					<?php //anchor($content.'/to_excel', 'Export', array('class' => 'btn btn-primary col-sm-12'))?>
					<!--<input class="btn btn-primary col-sm-12" type="submit" name="to_excel" value="Export" />-->
					<?=anchor($content.'/add', '<i class="fa fa-plus"></i> Add', array('class'=>'pull-right btn btn-primary'))?>
				</div>
			</div>
			<?= form_close(); ?>
			<div class="box">
				<div class="row" style="margin-top: 5px;">
					<div class="col-xs-12">
						<div class="box-body table-responsive">
							<table id="example1" class="table table-bordered table-striped dataTable">
								<thead>
									<tr>
										<th>No</th><th>Action</th><th>Name</th>
									</tr>
								</thead>
								<tbody>
								<?php $no = 0; foreach($user_level as $row){ $no++; ?>
									<tr>
										<td><?=$no?></td>
										<td>
											<div class="btn-group">
												<button type="button" class="btn btn-primary dropdown-toggle" data-toggle="dropdown">Action <span class="caret"></span><span class="sr-only">Toggle Dropdown</span></button>
												<ul class="dropdown-menu" role="menu">
													<li><div class="col-sm-12"><?=anchor($content.'/access/?trx='.$row['id'], '<i class="fa fa-unlock"></i> Access', array('class'=>'btn btn-default col-sm-12'))?></div></li>
													<!--<li class="divider"></li>-->
													<li><div class="col-sm-12"><?=anchor($content.'/edit/?trx='.$row['id'], '<i class="fa fa-edit"></i> Edit', array('class'=>'btn btn-default col-sm-12'))?></div></li>
													<!--<li class="divider"></li>-->
													<li><div class="col-sm-12"><?=anchor($content.'/delete/?trx='.$row['id'], '<i class="fa fa-trash"></i> Delete', array('class'=>'btn btn-default col-sm-12', 'onclick'=>"return confirm('Delete Data?');"))?></div></li>
													<!--<button class="btn btn-danger" onclick="hapus('coa', 'coa_id', '<?=$row['coa_id']?>')"><i class="fa fa-trash"></i> Delete</button>-->
												</ul>
											</div>
										</td>
										<td><?=$row['lvl']?></td>
									</tr>
								<?php } ?>
								</tbody>
							</table>
						</div>
					</div>
				</div>
			</div>
        </div>
    </div><!-- /.box -->

</section><!-- /.content -->

<script>
function hapus(table, column, id){
	if(confirm('Delete Data?')){
		$.ajax({
			//type:'POST',
			url:'<?=site_url().$this->control?>/delete_ajax/' + table + '/' + column + '/' + id,
			//data: {delete_id : id},
			success: function (data) { alert('Delete Success'); window.location='<?=site_url().$this->control?>/<?=$page?>'; },
			error: function (jqXHR, textStatus, errorThrown){ alert('Delete Failed'); }
		});
	}
}
</script>

