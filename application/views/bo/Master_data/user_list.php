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
												<div class="input-group-btn">
													<?php $field = 'column';
													$option = null;
													$option['nama'] = 'Nama';
													$option['username'] = 'Username';
													$option['lvl'] = 'User Level';
													//$data_option = $this->m_crud->read_data('Group2', 'Kode, Nama', null, 'Nama asc');
													//foreach($data_option as $row){ $option[$row['Kode']] = $row['Nama']; }
													echo form_dropdown($field, $option, isset($this->session->search[$field])?$this->session->search[$field]:set_value($field), array('class' => 'select2', 'id'=>$field, 'required'=>'required')); 
													?>
												</div>
												<?php $field = 'any'; ?>
												<input class="form-control" style="height: 40px" type="text" id="<?=$field?>" name="<?=$field?>" value="<?=isset($this->session->search[$field])?$this->session->search[$field]:set_value($field)?>" autofocus />
											</div>
											<?=form_error($field, '<div class="error" style="color:red;">', '</div>')?>
										</div>
									</div>
									<div class="col-sm-3">
										<div class="form-group">
											<label>Sort</label>
											<div class="input-group">
												<div class="input-group-btn">
													<?php $field = 'order_by';
													$option = null;
                                                    $option['nama'] = 'Nama';
                                                    $option['username'] = 'Username';
                                                    $option['lvl'] = 'User Level';
													//$data_option = $this->m_crud->read_data('Group2', 'Kode, Nama', null, 'Nama asc');
													//foreach($data_option as $row){ $option[$row['Kode']] = $row['Nama']; }
													echo form_dropdown($field, $option, isset($this->session->search[$field])?$this->session->search[$field]:set_value($field), array('class' => 'select2', 'id'=>$field, 'required'=>'required')); 
													?>
												</div>
												<?php $field = 'order_sort';
												$option = null;
												$option['asc'] = 'Ascending';
												$option['desc'] = 'Descending';
												//$data_option = $this->m_crud->read_data('Group2', 'Kode, Nama', null, 'Nama asc');
												//foreach($data_option as $row){ $option[$row['Kode']] = $row['Nama']; }
												echo form_dropdown($field, $option, isset($this->session->search[$field])?$this->session->search[$field]:set_value($field), array('class' => 'select2', 'id'=>$field, 'required'=>'required')); 
												?>
											</div>
											<?=form_error($field, '<div class="error" style="color:red;">', '</div>')?>
										</div>
									</div>
									<div class="col-sm-6" style="margin-top:25px;">
										<button type="submit" class="btn btn-primary waves-effect waves-light" name="search">Search</button>
                                        <?=anchor($content.'/add', '<i class="fa fa-plus"></i> Add', array('class'=>'btn btn-primary'))?>
                                        <button type="submit" class="btn btn-primary waves-effect waves-light" name="export"><i class="fa fa-upload"></i> Export</button>
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
													<th>No</th><th>Action</th><th>Nama</th><th>Username</th><th>User Level</th><th>Lokasi</th><th>Status</th><th>Otorisasi</th>
												</tr>
											</thead>
											<tbody>
											<?php $no = 0 + (($this->uri->segment(4)!=null)?(($this->uri->segment(4)-1) * 10):0);
                                            foreach($master_data as $row){
												$no++;
                                                $array_lokasi = array();
                                                $data_lokasi = json_decode($row['lokasi'], true);
                                                for ($i = 0; $i < count((array)$data_lokasi['lokasi_list']); $i++) {
                                                    array_push($array_lokasi, $data_lokasi['lokasi_list'][$i]['kode']);
                                                }
                                                sort($array_lokasi);
                                                if ($row['password_otorisasi'] != '-') {
                                                    $this->m_export_file->create_barcode(array('file' => $row['nama'], 'code' => $row['password_otorisasi']));
                                                }
												?>
												<tr>
													<td><?=$no?></td>
													<td>
														<div class="btn-group">
															<button type="button" class="btn btn-primary dropdown-toggle" data-toggle="dropdown">Action <span class="caret"></span><span class="sr-only">Toggle Dropdown</span></button>
															<ul class="dropdown-menu" role="menu">
																<li><div class="col-sm-12"><?=anchor($content.'/edit/?trx='.base64_encode($row['user_id']), '<i class="fa fa-edit"></i> Edit', array('class'=>'btn btn-default col-sm-12'))?></div></li>
                                                                <?php if ($row['password_otorisasi'] != '-') { ?>
																<li><div class="col-sm-12"><?=anchor(base_url().'assets/images/barcode/'.$row['nama'].'.png', '<i class="fa fa-barcode"></i> Barcode', array('class'=>'btn btn-default col-sm-12', 'download'=>$row['nama']))?></div></li>
                                                                <?php } ?>
																<!--<li class="divider"></li>-->
																<!--<li><div class="col-sm-12"><?=anchor($content.'/delete/?trx='.$row['user_id'], '<i class="fa fa-trash"></i> Delete', array('class'=>'btn btn-default col-sm-12', 'onclick'=>"return confirm('Delete Data?');"))?></div></li>-->
																<li><div class="col-sm-12"><button class="btn btn-default col-sm-12" onclick="hapus('<?=$table?>', '<?='user_id'?>', '<?=$row['user_id']?>')"><i class="fa fa-trash"></i> Delete</button></div></li>
															</ul>
														</div>
													</td>
													<td><?=$row['nama']?></td>
													<td><?=$row['username']?></td>
													<td><?=$row['lvl']?></td>
                                                    <td><?=implode(', ', $array_lokasi)?></td>
                                                    <td style="text-align: center"><img width="25px" src="<?=base_url().'assets/images/status-'.($row['status']==1?'Y':'T').'.png'?>" /></td>
                                                    <td style="text-align: center"><img width="25px" src="<?=base_url().'assets/images/status-'.($row['password_otorisasi']=='-'?'T':'Y').'.png'?>" /></td>
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

