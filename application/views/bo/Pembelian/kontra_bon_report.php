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
							<?=form_open(strtolower($this->control) . '/' . $page, array('role'=>"form", 'class'=>""))?>
							<div class="row">
								<div class="col-sm-3" style="margin-bottom:10px">
									<label>Periode</label>
									<?php $field = 'field-date';?>
									<div id="daterange_all" style="cursor: pointer;">
										<input type="text" name="<?=$field?>" id="<?=$field?>" class="form-control" style="height: 40px;" value="<?=isset($this->session->search[$field])?$this->session->search[$field]:(set_value($field)?set_value($field):date("Y/m/d")." - ".date("Y/m/d"))?>">
									</div>
								</div>
								<div class="col-sm-2">
									<div class="form-group">
										<label>Lokasi</label>
										<?php $field = 'lokasi';
										$option = null; $option[''] = 'Semua Lokasi';
										//$option['all'] = 'All';
										$data_option = $this->m_crud->read_data('Lokasi', 'Kode, Nama', null, 'Nama asc');
										foreach($data_option as $row){ $option[$row['Kode']] = $row['Nama']; }
										echo form_dropdown($field, $option, isset($this->session->search[$field])?$this->session->search[$field]:set_value($field), array('class' => 'select2', 'id'=>$field));
										?>
									</div>
								</div>
								<div class="col-sm-3">
									<div class="form-group">
										<label>Supplier</label>
										<?php $field = 'supplier';
										$option = null; $option[''] = 'Semua Supplier';
										//$option['all'] = 'All';
										$data_option = $this->m_crud->read_data('supplier', 'kode, Nama', null, 'Nama asc');
										foreach($data_option as $row){ $option[$row['kode']] = $row['kode'].' | '.$row['Nama']; }
										echo form_dropdown($field, $option, isset($this->session->search[$field])?$this->session->search[$field]:set_value($field), array('class' => 'select2', 'id'=>$field));
										?>
									</div>
								</div>
								<div class="col-sm-2">
									<div class="form-group">
										<label>Search</label>
										<?php $field = 'any'; ?>
										<input class="form-control" type="text" id="<?=$field?>" name="<?=$field?>" value="<?=isset($this->session->search[$field])?$this->session->search[$field]:set_value($field)?>" autofocus />
										<?=form_error($field, '<div class="error" style="color:red;">', '</div>')?>
									</div>
								</div>
								<div class="col-sm-1">
									<label>&nbsp;</label>
									<button type="submit" class="btn btn-primary waves-effect waves-light" name="search">Search</button>
								</div>
								<!--
								<div class="col-sm-1">
									<label>&nbsp;</label>
									<button type="submit" class="btn btn-primary waves-effect waves-light" name="to_excel">Export</button>
								</div>
								-->
							</div>
							<?=form_close()?>
						</div>
						<div class="panel-body">
							<div class="row">
								<div class="col-md-12 col-sm-12 col-xs-12">
									<table class="table table-striped table-bordered">
										<thead>
										<tr>
											<th style="width: 10px">No</th>
											<th>Pilihan</th>
											<th>No. Kontra Bon</th>
											<th>Nama Supplier</th>
											<th>Jenis</th>
											<th>Tanggal Kontra Bon</th>
											<th>Tanggal Jatuh Tempo</th>
											<th>Nilai Kontra Bon</th>
											<th>Retur</th>
											<th>Biaya Adm</th>
											<th>Total Pembayaran</th>
											<th>Pembulatan</th>
										</tr>
										</thead>
										<tbody>
										<?php $no = 0 + (($this->uri->segment(4)!=null)?(($this->uri->segment(4)-1) * 15):0);
										$tp = 0;
										foreach($report as $row){
										    $no++;
										    ?>
											<tr>
												<td><?=$no?></td>
												<td class="text-center">
													<div class="btn-group">
														<button type="button" class="btn btn-primary dropdown-toggle waves-effect waves-light" data-toggle="dropdown" aria-expanded="false">Pilihan <span class="caret"></span></button>
														<ul class="dropdown-menu" role="menu">
															<li><a href="#" onclick="modal_detail('<?=$row['id_master_kontra']?>','<?=$row['jenis']?>');"><i class="md md-visibility"></i> Detail</a></li>
                                                            <li><a href="#" onclick="re_print('<?=$row['id_master_kontra']?>', download_pdf)"><i class="md md-get-app"></i> Download</a></li>
                                                            <li><a href="#" onclick="re_print('<?=$row['id_master_kontra']?>', cetak_pdf)" target="_blank"><i class="md md-print"></i> to PDF</a></li>
														</ul>
													</div>
												</td>
												<td id="master_kontra<?=$row['id_master_kontra']?>"><?=$row['id_master_kontra']?></td>
												<td id="nama_supplier<?=$row['id_master_kontra']?>"><?=$row['nama_supplier']?></td>
												<td id="jenis<?=$row['id_master_kontra']?>"><?=$row['jenis']?></td>
												<td id="tgl_kontra<?=$row['id_master_kontra']?>"><?=substr($row['tgl_kontra'],0,10)?></td>
												<td id="tgl_bayar<?=$row['id_master_kontra']?>"><?=substr($row['tgl_bayar'],0,10)?></td>
												<td id="nilai_kontrabon<?=$row['id_master_kontra']?>" class="text-right"><?=number_format($row['nilai_kontrabon'], 2)?></td>
												<td id="retur<?=$row['id_master_kontra']?>" class="text-right"><?=number_format($row['retur'], 2)?></td>
												<td id="biaya_adm<?=$row['id_master_kontra']?>" class="text-right"><?=number_format($row['biaya_adm'], 2)?></td>
												<td id="pembayaran<?=$row['id_master_kontra']?>" class="text-right"><?=number_format($row['pembayaran'], 2)?></td>
												<td id="pembulatan<?=$row['id_master_kontra']?>" class="text-right"><?=number_format($row['pembulatan'], 2)?></td>
											</tr>
										<?php
										$tp = $tp + (float)$row['pembayaran'];
										} ?>
										</tbody>
										<tfoot>
										<tr>
											<th colspan="10">TOTAL PER PAGE</th>
											<th class="text-right"><?=number_format($tp, 2)?></th>
											<th></th>
										</tr>
										<tr>
											<th colspan="10">TOTAL</th>
											<th class="text-right"><?=number_format($detail['total_pembayaran'], 2)?></th>
											<th></th>
										</tr>
										</tfoot>
									</table>
									<div class="pull-right">
										<?php
										echo $this->pagination->create_links();
										?>
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

<div id="modal-detail" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true" style="display: none;">
	<div class="modal-dialog modal-full">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
				<h4 class="modal-title" id="myLargeModalLabel">Detail <?=$title?></h4>
			</div>
			<div class="modal-body">
				<div class="row">
					<div class="col-sm-6">
						<div class="col-sm-4"><b>No. Kontra Bon</b></div><div class="col-sm-8"><b> : </b><b id="kontrabon"></b></div>
						<div class="col-sm-4"><b>Supplier</b></div><div class="col-sm-8"><b> : </b><b id="supplier_"></b></div>
						<div class="col-sm-4"><b>Jenis</b></div><div class="col-sm-8"><b> : </b><b id="jenis"></b></div>
					</div>
					<div class="col-sm-6">
						<div class="col-sm-4"><b>Tanggal Kontra Bon</b></div><div class="col-sm-8"><b> : </b><b id="tgl-kontra"></b></div>
						<div class="col-sm-4"><b>Tanggal Jatuh Tempo</b></div><div class="col-sm-8"><b> : </b><b id="tgl-bayar"></b></div>
					</div>
					<!--<div class="col-sm-2"></div>
					<div class="col-sm-4">
						<div class="col-sm-4"><b>Operator</b></div><div class="col-sm-8"><b> : </b><?/*=$row['Operator']*/?></div>
						<div class="col-sm-4"><b>Pelunasan</b></div><div class="col-sm-8"><b> : </b><?/*=$row['Pelunasan']*/?></div>
					</div>-->
				</div>
				<hr/>
				<div class="row">
					<div class="col-md-12 col-sm-12 col-xs-12">
						<table id="datatable" class="table table-striped table-bordered">
							<thead>
							<tr>
								<th>No</th>
								<th id="det_nota_pembelian">Nota Pembelian</th>
								<th>Nota Supplier</th>
								<th id="det_nilai_pembelian">Nilai Pembelian</th>
								<th>Nilai Kontra Bon</th>
							</tr>
							</thead>
							<tbody id="list_detail">
							</tbody>
							<tfoot>
							<tr>
								<th colspan="4"><b>Nilai Kontra Bon</b></th>
								<th class="text-right"><b id="total"></b></th>
							</tr>
							<tr>
								<th colspan="4"><b>Retur</b></th>
								<th class="text-right"><b id="retur"></b></th>
							</tr>
							<tr>
								<th colspan="4"><b>Biaya Adm</b></th>
								<th class="text-right"><b id="biaya_adm"></b></th>
							</tr>
							<tr>
								<th colspan="4"><b>TOTAL Pembayaran</b></th>
								<th class="text-right"><b id="pembayaran"></b></th>
							</tr>
							<tr>
								<th colspan="4"><b>Pembulatan</b></th>
								<th class="text-right"><b id="pembulatan"></b></th>
							</tr>
							</tfoot>
						</table>
					</div>
				</div>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-default waves-effect" data-dismiss="modal">Close</button>
				<!--
				<a href="<?=base_url().strtolower($this->control).'/'.$page.'/print/'.base64_encode($row[''])?>" target="_blank"><button type="button" class="btn btn-primary waves-effect waves-light"><i class="md md-print"></i> to PDF</button></a>
				-->
			</div>
		</div><!-- /.modal-content -->
	</div><!-- /.modal-dialog -->
</div><!-- /.modal -->

<script>
	function after_change(val) {
		$.ajax({
			url: "<?php echo base_url().'site/set_session_date/' ?>" + btoa('field-date') + '/' + btoa(val),
			type: "GET"
		});
	}
	
	function modal_detail(nota='',jenis=''){
		$("#list_detail").html('');
		$("#total").text('');
		$.ajax({
            url: "<?= base_url() . $this->control . '/read_detail_kontra/' ?>"+btoa(nota)+"/"+btoa(jenis),
            type: "GET",
            dataType: "JSON",
            success: function (data) {
				$("#list_detail").html(data.list_detail);
                $("#total").text(to_rp(data.total));
			}
        });
		$('#kontrabon').text($('#master_kontra'+nota).text());
		$('#supplier_').text($('#nama_supplier'+nota).text());
		$('#jenis').text($('#jenis'+nota).text());
		$('#tgl-kontra').text($('#tgl_kontra'+nota).text());
		$('#tgl-bayar').text($('#tgl_bayar'+nota).text());
		$('#retur').text($('#retur'+nota).text());
		$('#biaya_adm').text($('#biaya_adm'+nota).text());
		$('#pembayaran').text($('#pembayaran'+nota).text());
		$('#pembulatan').text($('#pembulatan'+nota).text());
		if(jenis=='Konsinyasi'){	
			$('#det_nota_pembelian').text('Periode Konsinyasi');
			$('#det_nilai_pembelian').text('Nilai Konsinyasi');
		} else {
			$('#det_nota_pembelian').text('Nota Pembelian');
			$('#det_nilai_pembelian').text('Nilai Pembelian');
		}
		$('#modal-detail').modal('show'); 
	}
</script>
