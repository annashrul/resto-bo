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
													$option['Customer.kd_cust'] = 'Kode';
													$option['Customer.Nama'] = 'Nama';
													$option['Customer.alamat'] = 'Alamat';

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
													$option['Customer.kd_cust'] = 'Kode';
													$option['Customer.Nama'] = 'Nama';
                                                    $option['Customer.alamat'] = 'Alamat';
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
									<div class="table-wrapper">
										<table class="table table-striped table-bordered">
											<thead>
												<tr>
													<th>No</th><th>Action</th><th>Status</th><th>Kode</th><th>Nama</th><th>Tipe</th><th>Alamat</th><th>Disc</th><th class="tgl">Tgl Ultah</th><th>Disc Ultah</th><th>Telp. 1</th>
													<th>Telp. 2</th><th>Telp. 3</th><th>Deposit</th><th>Tgl Deposit</th><th>Special Price</th><th>Tgl Akhir</th>
												</tr>
											</thead>
											<tbody>
											<?php $no = 0 + (($this->uri->segment(4)!=null)?(($this->uri->segment(4)-1) * 10):0); foreach($master_data as $row){
												$lokasi = [];
												$exp_alamat = explode("|", $row['alamat']);

												$get_prov = $this->m_crud->get_data("provinces","name","id='".$exp_alamat[4]."'");
												$get_kota = $this->m_crud->get_data("regencies","name","id='".$exp_alamat[3]."'");
												$get_kec = $this->m_crud->get_data("districts","name","id='".$exp_alamat[2]."'");
												$get_des = $this->m_crud->get_data("villages","name","id='".$exp_alamat[1]."'");

												array_push($lokasi, $get_des['name']);
												array_push($lokasi, $get_kec['name']);
												array_push($lokasi, $get_kota['name']);
												array_push($lokasi, $get_prov['name']);

												$alamat = $exp_alamat[0].", ".implode(", ", array_map("ucfirst", $lokasi));
												$no++; ?>
												<tr>
													<td><?=$no?></td>
													<td>
														<div class="btn-group">
															<button type="button" class="btn btn-primary dropdown-toggle" data-toggle="dropdown">Action <span class="caret"></span><span class="sr-only">Toggle Dropdown</span></button>
															<ul class="dropdown-menu" role="menu">
																<li><div class="col-sm-12"><?=anchor($content.'/edit/?trx='.base64_encode($row['kd_cust']), '<i class="fa fa-edit"></i> Edit', array('class'=>'btn btn-default col-sm-12'))?></div></li>
																<!--<li class="divider"></li>-->
																<!--<li><div class="col-sm-12"><?=anchor($content.'/delete/?trx='.$row['kd_cust'], '<i class="fa fa-trash"></i> Delete', array('class'=>'btn btn-default col-sm-12', 'onclick'=>"return confirm('Delete Data?');"))?></div></li>-->
                                                                <?php
                                                                if ($row['kd_cust'] != '1000001') {
                                                                    echo '
                                                                    <li><div class="col-sm-12"><button class="btn btn-default col-sm-12" onclick="hapus(\''.$table.'\', \'kd_cust\', \''.$row['kd_cust'].'\')"><i class="fa fa-trash"></i> Delete</button></div></li>
                                                                    ';
                                                                }
                                                                ?>
															</ul>
														</div>
													</td>
													<td style="text-align: center"><img width="25px" src="<?=base_url().'assets/images/status-'.($row['status']==1?'Y':'T').'.png'?>" /></td>
													<td><?=$row['kd_cust']?></td>
													<td><?=$row['Nama']?></td>
													<td><?=$row['nama_tipe']?></td>
													<td><?=$exp_alamat[0]?></td>
													<td><?=($row['diskon']+0)?></td>
													<td><?=substr($row['tgl_ultah'], 0, 10)?></td>
													<td><?=($row['diskon_ultah']+0)?></td>
													<td><?=$row['tlp1']?></td>
													<td><?=$row['tlp2']?></td>
													<td><?=$row['tlp3']?></td>
													<td><?=($row['deposit']+0)?></td>
													<td><?=substr($row['tgldeposit'], 0, 10)?></td>
													<td class="text-center"><img width="25px" src="<?=base_url().'assets/images/status-'.($row['SPECIAL_PRICE']==1?'Y':'T').'.png'?>" /></td>
													<td><?=substr($row['TGLAKHIR'], 0, 10)?></td>
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

