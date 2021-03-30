<style>
	.table-wrapper {
		overflow-x:scroll;
		overflow-y:visible;
		width:100%;
	}

	th {
		max-width:100%;
		white-space:nowrap;
	}
</style>
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
													$option['device_id'] = 'Device ID';
													$option['mac_address'] = 'MAC Address';
													$option['lokasi'] = 'Lokasi';
													$option['kassa'] = 'Kassa';
													$option['printer_model'] = 'Printer Model';
													$option['printer_series'] = 'Printer Series';
													//$data_option = $this->m_crud->read_data('Group2', 'Kode, Nama', null, 'Nama asc');
													//foreach($data_option as $row){ $option[$row['Kode']] = $row['Nama']; }
													echo form_dropdown($field, $option, isset($this->session->search[$field])?$this->session->search[$field]:set_value($field), array('class' => 'select2', 'id'=>$field, 'required'=>'required')); 
													?>
												</div>
												<?php $field = 'any'; ?>
												<input style="height: 40px" class="form-control" type="text" id="<?=$field?>" name="<?=$field?>" value="<?=isset($this->session->search[$field])?$this->session->search[$field]:set_value($field)?>" autofocus />
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
													$option['device_id'] = 'Device ID';
													$option['mac_address'] = 'MAC Address';
													$option['lokasi'] = 'Lokasi';
													$option['kassa'] = 'Kassa';
													$option['printer_model'] = 'Printer Model';
													$option['printer_series'] = 'Printer Series';
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
									<?=($this->user == 'netindo')?
									"<div class=\"col-sm-1\" style=\"margin-top:25px;\">
										".anchor($content.'/add', '<i class="fa fa-plus"></i> Add', array('class'=>'pull-right btn btn-primary'))."
									</div>":'';
									?>
								</div>
							<?= form_close(); ?>
						</div>
						<div class="panel-body">
							<div class="row">
								<div class="col-md-12 col-sm-12 col-xs-12">
									<div class="table-wrapper">
										<table class="table table-striped table-bordered">
											<thead>
												<tr>
													<th>No</th><?=($this->user == 'netindo')?'<th>Action</th>':''?><th>Device ID</th><?=($this->user == 'netindo')?'<th>MAC Address</th>':''?><th>Status</th><th>Jenis Device</th><th>Lokasi</th><th>Kassa</th><th>Printer Model</th><th>Printer Series</th><th>Printer Address</th><th>Ukuran Kertas</th>
													<th>Auto Cutter</th><th>Open Drawer</th><th>Scanner</th><th>Fast Pay</th>
												</tr>
											</thead>
											<tbody>
											<?php $no = 0 + (($this->uri->segment(4)!=null)?(($this->uri->segment(4)-1) * 10):0); foreach($master_data as $row){

												$no++; ?>
												<tr>
													<td><?=$no?></td>
													<?=($this->user == 'netindo')?
													"<td>
														<div class=\"btn-group\">
															<button type=\"button\" class=\"btn btn-primary dropdown-toggle\" data-toggle=\"dropdown\">Action <span class=\"caret\"></span><span class=\"sr-only\">Toggle Dropdown</span></button>
															<ul class=\"dropdown-menu\" role=\"menu\">
																<li><div class=\"col-sm-12\">".anchor($content.'/edit/?trx='.base64_encode($row['id']), '<i class="fa fa-edit"></i> Edit', array('class'=>'btn btn-default col-sm-12'))."</div></li>
																<!--<li class=\"divider\"></li>-->
																<!--<li><div class=\"col-sm-12\"><?=anchor($content.'/delete/?trx=".$row['id'].", '<i class=\"fa fa-trash\"></i> Delete', array('class'=>'btn btn-default col-sm-12', 'onclick'=>\"return confirm('Delete Data?');\"))?></div></li>-->
																<li><div class=\"col-sm-12\"><button class=\"btn btn-default col-sm-12\" onclick=\"update_device_id('".$row['device_id']."');hapus('".$table."', 'id', '".$row['id']."')\"><i class=\"fa fa-trash\"></i> Delete</button></div></li>
															</ul>
														</div>
													</td>":'';
													?>
													<td><?=$row['device_id']?></td>
													<?=($this->user == 'netindo')?
														"<td>".$row['mac_address']."</td>"
														:"";
													?>
													<td class="text-center"><img width="25px" src="<?=base_url().'assets/images/status-'.($row['mac_address']!=null?'Y':'T').'.png'?>" /></td>
													<td><?=$row['jenis_device']?></td>
													<td><?=$row['lokasi']?></td>
													<td><?=$row['kassa']?></td>
													<td><?=$row['printer_model']?></td>
													<td><?=$row['printer_name']?></td>
													<td><?=$row['printer_address']?></td>
													<td><?=$row['paper']?></td>
													<td class="text-center"><img width="25px" src="<?=base_url().'assets/images/status-'.($row['auto_cutter']=='true'?'Y':'T').'.png'?>" /></td>
													<td class="text-center"><img width="25px" src="<?=base_url().'assets/images/status-'.($row['open_drawer']=='true'?'Y':'T').'.png'?>" /></td>
													<td class="text-center"><img width="25px" src="<?=base_url().'assets/images/status-'.($row['scanner']=='true'?'Y':'T').'.png'?>" /></td>
													<td class="text-center"><img width="25px" src="<?=base_url().'assets/images/status-'.($row['fast_pay']=='true'?'Y':'T').'.png'?>" /></td>
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

function update_device_id(device_id) {
	$.ajax({
		url:"<?=base_url()?>setting/update_device_id/" + btoa(device_id)
	});
}
</script>

