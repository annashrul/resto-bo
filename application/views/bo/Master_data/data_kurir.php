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
									<div class="col-sm-3">
										<div class="form-group">
											<label>Search</label>
											<div class="input-group">
												<?php $field = 'any'; ?>
												<input class="form-control" type="text" id="<?=$field?>" name="<?=$field?>" value="<?=isset($this->session->search[$field])?$this->session->search[$field]:set_value($field)?>" autofocus />	
											</div>
											<?=form_error($field, '<div class="error" style="color:red;">', '</div>')?>
										</div>
									</div>
									<div class="col-sm-6" style="margin-top:25px;">
										<button type="submit" class="btn btn-primary waves-effect waves-light" name="search">Search</button>
									</div>
								</div>
							<?= form_close(); ?>
						</div>
						<div class="panel-body">
							<div class="row">
								<div class="col-md-12 col-sm-12 col-xs-12">
									<div class="table-responsive">
										<table id="" class="table table-striped table-bordered">
											<thead>
												<tr>
													<th>No</th><th>Action</th><th>Kurir</th><th>Gambar</th><th>Status</th>
												</tr>
											</thead>
											<tbody>
											<?php $no = 0 + (($this->uri->segment(4)!=null)?(($this->uri->segment(4)-1) * 50):0);
											foreach($master_data as $row){
											    $no++;
                                                if ($row['status'] == '' || $row['status'] == '0') {
                                                    $fav = 'Set Aktif';
                                                    $fav_val = '1';
                                                } else {
                                                    $fav = 'Set Tidak Aktif';
                                                    $fav_val = '0';
                                                }
											    ?>
												<tr>
													<td><?=$no?></td>
													<td>
														<div class="btn-group">
															<button type="button" class="btn btn-primary dropdown-toggle" data-toggle="dropdown">Action <span class="caret"></span><span class="sr-only">Toggle Dropdown</span></button>
															<ul class="dropdown-menu" role="menu">
                                                                <li><div class="col-sm-12"><button class="btn btn-default col-sm-12" onclick="update('<?=base64_encode(json_encode(array('table'=>$table, 'update'=>array(array('col'=>'status', 'data'=>$fav_val)), 'id'=>$row['id_kurir'])))?>')"><i class="fa fa-circle-o"></i> <?=$fav?></button></div></li>
															</ul>
														</div>
													</td>
													<td><?=$row['kurir']?></td>
													<td><img width="100px" src="<?=base_url().$row['gambar']?>"></td>
													<td><img width="25px" src="<?=base_url().'assets/images/status-'.($row['status']==1?'Y':'T').'.png'?>" /></td>
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
			success: function (data) { window.location='<?=site_url().$this->control?>/<?=$page?>'; },
			error: function (jqXHR, textStatus, errorThrown){ alert('Delete Failed'); }
		});
	}
}
</script>

