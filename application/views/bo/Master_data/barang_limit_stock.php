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
											<label>Lokasi</label>
											<?php $field = 'lokasi';
											$option = null; $option[''] = '-- Location --';
											//$option['all'] = 'All';
											$data_option = $lokasi;
											foreach($data_option as $row){ $option[$row['Kode']] = $row['Nama']; }
											echo form_dropdown($field, $option, isset($this->session->search[$field])?$this->session->search[$field]:set_value($field), array('class' => 'select2', 'id'=>$field)); 
											?>
										</div>
									</div>
									<div class="col-sm-3">
										<div class="form-group">
											<label><?=$menu_group['as_group1']?></label>
											<?php $field = 'group1';
											$option = null; $option[''] = '-- '.$menu_group['as_group1'].' --';
											//$option['all'] = 'All';
											$data_option = $group1;
											foreach($data_option as $row){ $option[$row['kode']] = $row['kode'].' | '.$row['nama']; }
											echo form_dropdown($field, $option, isset($this->session->search[$field])?$this->session->search[$field]:set_value($field), array('class' => 'select2', 'id'=>$field)); 
											?>
										</div>
									</div>
									<div class="col-sm-3">
										<div class="form-group">
											<label>Search</label>
											<div class="input-group">
												<div class="input-group-btn">
													<?php $field = 'column';
													$option = null;
													$option['kd_brg'] = 'Kode Barang';
													$option['barcode'] = 'Barcode';
													$option['nm_brg'] = 'Nama Barang';
													$option['Deskripsi'] = $menu_group['as_deskripsi'];
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
									<div class="col-sm-2">
										<div class="form-group">
											<label>Sort</label>
											<div class="input-group">
												<div class="input-group-btn">
													<?php $field = 'order_by';
													$option = null;
													$option['kd_brg'] = 'Kode Barang';
													$option['barcode'] = 'Barcode';
													$option['nm_brg'] = 'Nama Barang';
													$option['Deskripsi'] = $menu_group['as_deskripsi'];
													//$data_option = $this->m_crud->read_data('Group2', 'Kode, Nama', null, 'Nama asc');
													//foreach($data_option as $row){ $option[$row['Kode']] = $row['Nama']; }
													echo form_dropdown($field, $option, isset($this->session->search[$field])?$this->session->search[$field]:set_value($field), array('class' => 'select2', 'id'=>$field, 'required'=>'required')); 
													?>
												</div>
												<?php $field = 'order_sort';
												$option = null;
												$option['asc'] = 'Asc';
												$option['desc'] = 'Desc';
												//$data_option = $this->m_crud->read_data('Group2', 'Kode, Nama', null, 'Nama asc');
												//foreach($data_option as $row){ $option[$row['Kode']] = $row['Nama']; }
												echo form_dropdown($field, $option, isset($this->session->search[$field])?$this->session->search[$field]:set_value($field), array('class' => 'select2', 'id'=>$field, 'required'=>'required')); 
												?>
											</div>
											<?=form_error($field, '<div class="error" style="color:red;">', '</div>')?>
										</div>
									</div>
									<div class="col-sm-2" style="margin-top:25px;">
										<button type="submit" class="btn btn-primary waves-effect waves-light" name="search">Search</button>
										<button type="submit" class="btn btn-primary waves-effect waves-light" name="to_excel">Export</button>
									</div>
									<!--<div class="col-sm-1" style="margin-top:25px;">
										<?=anchor($content.'/add', '<i class="fa fa-plus"></i> Add', array('class'=>'pull-right btn btn-primary'))?>
									</div>-->
								</div>
							<?= form_close(); ?>
						</div>
						<div class="panel-body">
							<div class="row">
								<div class="col-md-12 col-sm-12 col-xs-12">
									<div>
										<?php $caption_harga = $this->m_crud->get_data('harga', 'hrg1, hrg2, hrg3, hrg4', "Kode = '1111'"); ?>
										<table id="" class="table table-striped table-bordered">
											<thead>
												<tr>
													<th>No</th><th>Action</th><th>Lokasi</th><th>Kode Barang</th><th>Barcode</th><th>Nama Barang</th>
													<th><?=$menu_group['as_deskripsi']?></th><th>Harga Jual</th><th>Stock Min</th><th>Stock Max</th><th>Stock</th>
												</tr>
											</thead>
											<tbody>
											<?php $no = 0 + (($this->uri->segment(4)!=null)?(($this->uri->segment(4)-1) * 15):0); foreach($master_data as $row){ $no++; ?>
												<tr>
													<td><?=$no?></td>
													<td>
														<input type="hidden" id="trx<?=$no?>" value="<?=base64_encode($row['id_'.$table])?>" />
														<div class="btn-group">
															<button type="button" id="simpan<?=$no?>" onclick="barang_simpan(<?=$no?>)" class="btn btn-warning" style="display:none;">Simpan</button>
															<button type="button" id="edit<?=$no?>" onclick="barang_edit(<?=$no?>)" class="btn btn-primary">Edit</button>
															<!--<button type="button" class="btn btn-primary dropdown-toggle" data-toggle="dropdown">Action <span class="caret"></span><span class="sr-only">Toggle Dropdown</span></button>-->
															<ul class="dropdown-menu" role="menu">
																<li><div class="col-sm-12"><?=anchor($content.'/edit/?trx='.base64_encode($row['id_'.$table]), '<i class="fa fa-edit"></i> Edit', array('class'=>'btn btn-default col-sm-12'))?></div></li>
																<!--<li class="divider"></li>-->
																<!--<li><div class="col-sm-12"><?=anchor($content.'/delete/?trx='.$row['id_'.$table], '<i class="fa fa-trash"></i> Delete', array('class'=>'btn btn-default col-sm-12', 'onclick'=>"return confirm('Delete Data?');"))?></div></li>-->
																<!--<li><div class="col-sm-12"><button class="btn btn-default col-sm-12" onclick="hapus('<?=$table?>', '<?='id_'.$table?>', '<?=$row['id_'.$table]?>')"><i class="fa fa-trash"></i> Delete</button></div></li>-->
															</ul>
														</div>
													</td>
													<td><?=$row['nm_lokasi']?></td>
													<td><?=$row['barang']?></td>
													<td><?=$row['barcode']?></td>
													<td><?=$row['nm_brg']?></td>
													<td><?=$row['Deskripsi']?></td>
													<td style="text-align:right;"><?=number_format($row['hrg_jual_1'])?></td>
													<td id="smin<?=$no?>" style="text-align:center;"><?=$row['stock_min'] + 0?></td>
													<td id="smax<?=$no?>" style="text-align:center;"><?=$row['stock_max'] + 0?></td>
													<td id="stock<?=$no?>" style="text-align:center; background:<?=($row['stock']<=$row['stock_min'])?'#ff4d4d':(($row['stock']>$row['stock_max'])?'#ffff00':null)?>;"><?=$row['stock'] + 0?></td>
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

function barang_simpan(id){
	if(parseInt($('#stock_max'+id).val())<=parseInt($('#stock_min'+id).val())){
		$('#alr_stock_max'+id).text('Max harus lebih dari Min!');
	} else if(parseInt($('#stock_min'+id).val())<0){
		$('#alr_stock_min'+id).text('Min harus lebih dari 0!');
	} else {
		$.ajax({
			url: "<?= base_url() . 'master_data/update_barang_limit_stock' ?>",
			type: "POST",
			data: {trx:$('#trx'+id).val(), stock_min:$('#stock_min'+id).val(), stock_max:$('#stock_max'+id).val()},
			dataType: "JSON",
			/*beforeSend: function () {
				$('#loading').show();
			},
			complete: function () {
				$("#loading").hide();
			},*/
			success: function (data) {
				if(data.status==1){
					$('#simpan'+id).hide();
					$('#edit'+id).show();

					if(parseInt($('#stock'+id).text())<=parseInt($('#stock_min'+id).val())){
						$('#stock'+id).css('background', '#ff4d4d');
					} else if(parseInt($('#stock'+id).text())>parseInt($('#stock_max'+id).val())){
						$('#stock'+id).css('background', '#ffff00');
					} else {
						$('#stock'+id).css('background', '');
					}
					$("#smin"+id).html($('#stock_min'+id).val());
					$("#smax"+id).html($('#stock_max'+id).val());
				}
			}
		});
	}
}

function enter_to(event, field, id) {
	if (event.ctrlKey && event.keyCode == 13) {
		$("#simpan"+id).click();
	} else if (event.keyCode == 13) {
		$("#"+field+id).focus().select();
	} 
}

function barang_edit(id){
	$('#simpan'+id).show();
	$('#edit'+id).hide();
		
	$("#smin"+id).html('<input class="form-control" onfocus="$(this).select()" onkeydown="return isNumber(event);" onkeyup="enter_to(event, \'stock_max\', '+id+');" style="max-width:70px;" type="text" id="stock_min'+id+'" value="'+($('#smin'+id).text())+'" /><b class="error" id="alr_stock_min'+id+'"></b>');
	$("#smax"+id).html('<input class="form-control" onfocus="$(this).select()" onkeydown="return isNumber(event);" onkeyup="enter_to(event, \'stock_min\', '+id+');" style="max-width:70px;" type="text" id="stock_max'+id+'" value="'+($('#smax'+id).text())+'" /><b class="error" id="alr_stock_max'+id+'"></b>');
	
	$("#stock_min"+id).focus().select();
}
</script>

