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
											<?php $field = 'any'; ?>
											<div class="input-group">
												<div class="input-group-btn">
													<?php $field = 'column';
													$option = null;
													$option['kode'] = 'Kode';
													$option['Nama'] = 'Nama';
													$option['Jns_Kas'] = 'Jenis Kas';
													//$data_option = $this->m_crud->read_data('Group2', 'Kode, Nama', null, 'Nama asc');
													//foreach($data_option as $row){ $option[$row['Kode']] = $row['Nama']; }
													echo form_dropdown($field, $option, isset($this->session->search[$field])?$this->session->search[$field]:set_value($field), array('class' => 'select2', 'id'=>$field, 'required'=>'required'));
													?>
												</div>
												<?php $field = 'any'; ?>
												<input class="form-control" style="height: 40px" type="text" id="<?=$field?>" name="<?=$field?>" value="<?=isset($this->session->search[$field])?$this->session->search[$field]:set_value($field)?>" autofocus />
												<?=form_error($field, '<div class="error" style="color:red;">', '</div>')?>
											</div>
										</div>
									</div>
									<div class="col-sm-3">
										<div class="form-group">
											<label>Sort</label>
											<div class="input-group">
												<div class="input-group-btn">
													<?php $field = 'order_by';
													$option = null;$option = null;
													$option['tipe_kas'] = 'Tipe Kas';
													$option['kode'] = 'Kode';
													$option['Nama'] = 'Nama';
													$option['Jns_Kas'] = 'Jenis Kas';
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
									<div class="">
										<table id="" class="table table-striped table-bordered">
											<thead>
												<tr>
													<th>No</th><th style="width: 80px">Action</th><th>Tipe Kas</th><th>Kode</th><th>Nama</th><th>Jenis Kas</th>
												</tr>
											</thead>
											<tbody>
											<?php $no = 0 + (($this->uri->segment(4)!=null)?(($this->uri->segment(4)-1) * 50):0); foreach($master_data as $row){ $no++; ?>
												<tr>
													<td><?=$no?></td>
													<td>
														<div class="btn-group">
															<button type="button" class="btn btn-primary dropdown-toggle" data-toggle="dropdown">Action <span class="caret"></span><span class="sr-only">Toggle Dropdown</span></button>
															<ul class="dropdown-menu" role="menu">
																<li><div class="col-sm-12"><?=anchor($content.'/edit/?trx='.base64_encode($row['kode']).'&tabel='.base64_encode($row['tabel']), '<i class="fa fa-edit"></i> Edit', array('class'=>'btn btn-default col-sm-12'))?></div></li>
																<!--<li class="divider"></li>-->
																<!--<li><div class="col-sm-12"><?=anchor($content.'/delete/?trx='.$row['kode'].'|'.$row['tipe_kas'], '<i class="fa fa-trash"></i> Delete', array('class'=>'btn btn-default col-sm-12', 'onclick'=>"return confirm('Delete Data?');"))?></div></li>-->
																<li><div class="col-sm-12"><button class="btn btn-default col-sm-12" onclick="hapus('<?=$row['tabel']?>', '<?='kode'?>', '<?=$row['kode']?>')"><i class="fa fa-trash"></i> Delete</button></div></li>
															</ul>
														</div>
													</td>
													<td><?=$row['tipe_kas']?></td>
													<td><?=$row['kode']?></td>
													<td><?=$row['Nama']?></td>
													<td><?=$row['Jns_Kas']?></td>
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
			success: function (data) {
				window.location='<?=site_url().$this->control?>/<?=$page?>';
			},
			error: function (jqXHR, textStatus, errorThrown){ alert('Delete Failed'); }
		});
	}
}

</script>

