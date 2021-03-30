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
													$option['kode'] = 'Kode';
													$option['dariTgl'] = 'Tanggal Mulai';
													$option['sampaiTgl'] = 'Tanggal Selesai';
													$option['lokasi'] = 'Lokasi';
													$option['pildiskon'] = 'Jenis Diskon';
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
													$option = null;$option = null;
													$option['kode'] = 'Kode';
													$option['dariTgl'] = 'Tanggal Mulai';
													$option['sampaiTgl'] = 'Tanggal Selesai';
													$option['lokasi'] = 'Lokasi';
													$option['pildiskon'] = 'Jenis Diskon';
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
													<th>No</th><th>Action</th><th>Kategori</th><th>Kode</th><th>Customer</th><th>Tanggal Mulai</th><th>Tanggal Selesai</th><th style="width: 300px">Lokasi</th><th>Jenis Diskon</th><th>Diskon</th><!--<th>Diskon&nbsp;2</th>-->
												</tr>
											</thead>
											<tbody>
											<?php
											$no = 0 + (($this->uri->segment(4)!=null)?(($this->uri->segment(4)-1) * 50):0);
                                            foreach($master_data as $row){
                                                $no++;
                                                $array_lokasi = array();
                                                $data_lokasi = json_decode($row['lokasi'], true);
												for ($i = 0; $i < count($data_lokasi['lokasi_list']); $i++) {
													array_push($array_lokasi, $data_lokasi['lokasi_list'][$i]['kode']);
												}
												sort($array_lokasi);
												?>
												<tr>
													<td><?=$no?></td>
													<td>
														<div class="btn-group">
															<button type="button" class="btn btn-primary dropdown-toggle" data-toggle="dropdown">Action <span class="caret"></span><span class="sr-only">Toggle Dropdown</span></button>
															<ul class="dropdown-menu" role="menu">
																<li><div class="col-sm-12"><?=anchor($content.'/edit/?trx='.base64_encode($row['id_promo']).'&lokasi='.base64_encode($row['lokasi']), '<i class="fa fa-edit"></i> Edit', array('class'=>'btn btn-default col-sm-12'))?></div></li>
																<!--<li class="divider"></li>-->
																<!--<li><div class="col-sm-12"><?=anchor($content.'/delete/?trx='.$row['id_promo'], '<i class="fa fa-trash"></i> Delete', array('class'=>'btn btn-default col-sm-12', 'onclick'=>"return confirm('Delete Data?');"))?></div></li>-->
																<li><div class="col-sm-12"><button class="btn btn-default col-sm-12" onclick="hapus('<?=$row['id_promo']?>')"><i class="fa fa-trash"></i> Delete</button></div></li>
															</ul>
														</div>
													</td>
													<td><?=($row['cat_promo']=='brg')?'Barang':(($row['cat_promo']=='kel_brg')?'Kelompok Barang':'Supplier')?></td>
													<td><?=$row['kode']?></td>
													<td><?=($row['member']==''?'Semua Customer':'Hanya Member')?></td>
													<td><?=substr($row['dariTgl'],0,10)?></td>
													<td><?=substr($row['sampaiTgl'],0,10)?></td>
													<td><?=implode(', ', $array_lokasi)?></td>
													<td><?=($row['pildiskon']=='money')?'Rp':'%'?></td>
													<td class="text-right"><?=($row['diskon']+0)?></td>
													<!--<td class="text-right"><?/*=($row['diskon2']+0)*/?></td>-->
												</tr>
											<?php
											$array_lokasi = array();
											} ?>
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
$(document).ready(function () {
    if ('<?=$this->uri->segment(3)?>' != '') {
        send_message('promo=<?=base64_decode($this->uri->segment(3))?>');
    }
});

function hapus(id) {
	if(confirm('Delete Data?')){
	    var table_ = ['master_promo', 'Promo'];
	    var condition_ = ["id_promo = '"+id+"'", "id_promo = '"+id+"'"];
		$.ajax({
			type:'POST',
			url:'<?=site_url()?>site/delete_ajax_trx',
			data: {table: table_, condition: condition_},
			success: function (data) { window.location='<?=site_url().$this->control?>/<?=$page?>'; },
			error: function (jqXHR, textStatus, errorThrown){ alert('Delete Failed'); }
		});
	}
}
</script>

