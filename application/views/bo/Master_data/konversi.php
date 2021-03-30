<!-- ============================================================== -->
<!-- Start right Content here -->
<!-- ============================================================== -->                      
<div class="content-page">
	<!-- Start content -->
	<div class="content">
		<div class="container">

			<!-- Page-Title -->
			<div class="row">
				<div class="col-sm-12">
					<h4 class="pull-left page-title"><?=$title?></h4>
					<ol class="breadcrumb pull-right">
						<li><a href="<?=base_url()?>"><?=$site->title?></a></li>
						<li class="active"><?=$title?></li>
					</ol>
				</div>
			</div>
			
			<div class="row">
				<div class="col-md-12">
					<div class="panel panel-default">
						<div class="panel-heading">
							<!--<h3 class="panel-title">Header</h3>-->
							<?= form_open($content); ?>
								<div class="row">
									<div class="col-sm-2">
										<div class="form-group">
											<label>Search</label>
											<?php $field = 'any'; ?>
											<input class="form-control" type="text" id="<?=$field?>" name="<?=$field?>" value="<?=isset($this->session->any)?$this->session->any:set_value($field)?>" autofocus />	
											<?=form_error($field, '<div class="error" style="color:red;">', '</div>')?>
										</div>
									</div>
									<div class="col-sm-1" style="margin-top:25px;">
										<button type="submit" class="btn btn-primary waves-effect waves-light" name="search">Search</button>
									</div>
									<!--<div class="col-sm-1">
										<label>&nbsp;</label>
										<button type="submit" class="btn btn-primary waves-effect waves-light" name="to_excel">Export</button>
									</div>-->
									<div class="col-sm-1" style="margin-top:25px;">
										<?=anchor($content.'/add', '<i class="fa fa-plus"></i> Add', array('class'=>'pull-right btn btn-primary'))?>
									</div>
								</div>
							<?= form_close(); ?>
						</div>
						<div class="panel-body">
							<div class="row">
								<div class="col-md-12 col-sm-12 col-xs-12">
									<div>
										<table id="" class="table table-striped table-bordered">
											<thead>
												<tr>
													<th>No</th><th>Action</th><th>Kode Barang 1</th><th>Nama Barang 1</th><th>Kode Barang 2</th><th>Nama Barang 2</th><th>Nilai Konversi</th>
												</tr>
											</thead>
											<tbody>
											<?php $no=1; foreach($master_data as $row){?>
												<tr>
													<td><?=$no++?></td>
													<td>
														<div class="btn-group">
															<button type="button" class="btn btn-primary dropdown-toggle" data-toggle="dropdown">Action <span class="caret"></span><span class="sr-only">Toggle Dropdown</span></button>
															<ul class="dropdown-menu" role="menu">
																<li><div class="col-sm-12"><?=anchor($content.'/edit/?trx='.base64_encode($row['kd_brg1']), '<i class="fa fa-edit"></i> Edit', array('class'=>'btn btn-default col-sm-12'))?></div></li>
																<!--<li class="divider"></li>-->
																<!--<li><div class="col-sm-12"><?=anchor($content.'/delete/?trx='.$row['Kode'], '<i class="fa fa-trash"></i> Delete', array('class'=>'btn btn-default col-sm-12', 'onclick'=>"return confirm('Delete Data?');"))?></div></li>-->
																<li><div class="col-sm-12"><button class="btn btn-default col-sm-12" onclick="hapus('<?=$table?>', '<?='kd_brg1'?>', '<?=$row['kd_brg1']?>')"><i class="fa fa-trash"></i> Delete</button></div></li>
																<!--<li><div class="col-sm-12"><button class="btn btn-default col-sm-12" onclick="hapus('<?=$row['konversi']?>', '<?='kd_brg1'?>', '<?=$row['kd_brg1']?>')"><i class="fa fa-trash"></i> Delete</button></div></li>-->
															</ul>
														</div>
													</td>
													<td><?=$row['kd_brg1']?></td>
													<td><?=$row['nama_barang1']?></td>
													<td><?=$row['kd_brg2']?></td>
													<td><?=$row['nama_barang2']?></td>
													<td><?=$row['nilai_konversi']+0?></td>
												</tr>
											<?php } ?>
											</tbody>
										</table>
									</div>
								</div>
							</div>
							<div class="row">
								<div class="col-md-12 col-sm-12 col-xs-12">
									<div class="pull-right">
										<?= $this->pagination->create_links() ?>
										<!--<ul class="pagination pagination-sm"> 
                                            <li> <a href="#"> <i class="fa fa-angle-left"></i> </a> </li> 
                                            <li> <a href="#">1</a> </li> 
                                            <li class="active"> <a href="#">2</a> </li> 
                                            <li> <a href="#">3</a> </li> 
                                            <li class="disabled"> <a href="#">4</a> </li> 
                                            <li> <a href="#">5</a> </li> 
                                            <li> <a href="#">6</a> </li> 
                                            <li> <a href="#"> <i class="fa fa-angle-right"></i> </a> </li> 
                                        </ul>-->
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
				
			</div> <!-- End Row -->
			
		</div> <!-- container -->
				   
	</div> <!-- content -->

</div>
<!-- ============================================================== -->
<!-- End Right content here -->
<!-- ============================================================== -->



<script>
function hapus(table, column, id){
	if(confirm('Delete Data?')){
		$.ajax({
			//type:'POST',
			url:'<?=site_url()?>site/delete_ajax/' + table + '/' + column + '/' + id,
			//data: {delete_id : id},
			success: function (data){ 
				window.location='<?=site_url().$this->control?>/<?=$page?>'; 
			},
			error: function (jqXHR, textStatus, errorThrown){
				alert('Delete Failed'); 
			}
		});
	}
}
</script>

