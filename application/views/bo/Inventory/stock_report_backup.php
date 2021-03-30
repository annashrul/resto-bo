<style>
	.modal-body {
		max-height: calc(100vh - 212px);
		overflow-y: auto;
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
							<?=form_open(strtolower($this->control) . '/' . $page , array('role'=>"form", 'class'=>""))?>
							<div class="row">
								<div class="col-sm-3" style="margin-bottom:10px">
									<label>Periode</label>
									<?php $field = 'field-date';?>
									<div id="daterange" style="cursor: pointer;">
										<input type="text" name="<?=$field?>" id="<?=$field?>" class="form-control" style="height: 40px;" value="<?=isset($this->session->search[$field])?$this->session->search[$field]:(set_value($field)?set_value($field):date("Y/m/d")." - ".date("Y/m/d"))?>">
									</div>
								</div>
								<div class="col-sm-2">
									<div class="form-group">
										<label>Filter Stok</label>
										<?php $field = 'filter';
										$option = null;
										$option['-'] = 'Semua Stock';
										$option['>'] = 'Stock +';
										$option['='] = 'Stock 0';
										$option['<'] = 'Stock -';
										$option['<<'] = 'Stock Barang Minimum';
										echo form_dropdown($field, $option, isset($this->session->search[$field])?$this->session->search[$field]:set_value($field), array('class' => 'select2', 'id'=>$field));?>
									</div>
								</div>
								<div class="col-sm-3">
									<div class="form-group">
										<label>Lokasi</label>
										<?php $field = 'lokasi';
										$option = null; $option[''] = 'Semua Lokasi (tanpa HO)';
										//$option['all'] = 'All';
										$data_option = $this->m_crud->read_data('Lokasi', 'Kode, nama_toko Nama', $this->where_lokasi, 'Nama asc');
										foreach($data_option as $row){ $option[$row['Kode']] = $row['Nama']; }
										echo form_dropdown($field, $option, isset($this->session->search[$field])?$this->session->search[$field]:set_value($field), array('class' => 'select2', 'id'=>$field));
										echo form_error($field, '<div class="error" style="color:red;">', '</div>');
										?>
									</div>
								</div>
								<div class="col-sm-3">
									<div class="form-group">
										<label><?=$menu_group['as_group1']?></label>
										<?php $field = 'supplier';
										$option = null; $option[''] = 'Semua '.$menu_group['as_group1'];
										//$option['all'] = 'All';
										$data_option = $this->m_crud->read_data('Group1', 'Kode, Nama', null, 'Nama asc');
										foreach($data_option as $row){ $option[$row['Kode']] = $row['Kode'].' - '.$row['Nama']; }
										echo form_dropdown($field, $option, isset($this->session->search[$field])?$this->session->search[$field]:set_value($field), array('class' => 'select2', 'id'=>$field));
										echo form_error($field, '<div class="error" style="color:red;">', '</div>');
										?>
									</div>
								</div>
							</div>
							<div class="row">
								<div class="col-sm-2">
									<div class="form-group">
										<label>Filter Search</label>
										<?php $field = 'filter2';
										$option = null;
										$option['br.kd_brg'] = 'Kode Barang';
										$option['br.barcode'] = 'Barcode';
										$option['br.nm_brg'] = 'Nama Barang';
										echo form_dropdown($field, $option, isset($this->session->search[$field])?$this->session->search[$field]:set_value($field), array('class' => 'select2', 'id'=>$field));?>
									</div>
								</div>
								<div class="col-sm-3">
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
								<div class="col-sm-1">
									&nbsp;<label>&nbsp;</label>
									<button type="submit" class="btn btn-primary waves-effect waves-light" name="to_excel">Export</button>
								</div>
							</div>
							<?=form_close()?>
						</div>
						<div class="panel-body">
							<div class="row">
								<div class="col-md-12 col-sm-12 col-xs-12">
                                    <div class="table-responsive">
                                        <table class="table table-striped table-bordered">
										<thead>
										<tr>
											<th style="width: 10px">No</th>
											<th>Pilihan</th>
											<th>Kode Barang</th>
											<th>Barcode</th>
											<th>Nama Barang</th>
											<th>Satuan</th>
											<th>Supplier</th>
											<th>Stock Awal</th>
											<th>Stock In</th>
											<th>Stock Out</th>
											<th>Stock Akhir</th>
											<th>Harga Beli</th>
											<th>Harga Jual</th>
											<th>Jumlah by Harga Beli</th>
											<th>Jumlah by Harga Jual</th>
										</tr>
										</thead>
										<tbody>
										<?php $no = 0 + (($this->uri->segment(4)!=null)?(($this->uri->segment(4)-1) * 20):0); $staw = 0; $stma = 0; $stke = 0; $stak = 0; $tbeli = 0; $tjual = 0; foreach($report as $row){
											$no++;
											$stok_akhir = ($row['stock_awal']+$row['stock_masuk']-$row['stock_keluar']+0);
											?>
											<tr>
												<td><?=$no?></td>
												<td>
													<div class="btn-group">
														<button type="button" class="btn btn-primary dropdown-toggle waves-effect waves-light" data-toggle="dropdown" aria-expanded="false">Pilihan <span class="caret"></span></button>
														<ul class="dropdown-menu" style="position: relative" role="menu">
															<li><a href="#" onclick="detail_by_lokasi(['<?=$row['kd_brg']?>', '<?=$row['barcode']?>', '<?=$row['nm_brg']?>'], <?=$no?>)"><i class="md md-visibility"></i> Detail</a></li>
															<li><a href="#" onclick="export_file('<?=$row['kd_brg']?>','excel');"><i class="md md-print"></i> Export</a></li>
														</ul>
													</div>
													<!--
													<a class="btn btn-primary" data-toggle="modal" onclick="detail_by_lokasi(['<?=$row['kd_brg']?>', '<?=$row['barcode']?>', '<?=$row['nm_brg']?>'], <?=$no?>)"><i class="md md-visibility"></i> Detail</a>
													-->
												</td>
												<td><?=$row['kd_brg']?></td>
												<td><?=$row['barcode']?></td>
												<td><?=$row['nm_brg']?></td>
												<td><?=$row['satuan']?></td>
												<td><?=$row['Nama']?></td>
												<td><?=($row['stock_awal']+0)?></td>
												<td><?=($row['stock_masuk']+0)?></td>
												<td><?=($row['stock_keluar']+0)?></td>
												<td><?=$stok_akhir?></td>
												<td><?=number_format($row['hrg_beli'])?></td>
												<td><?=number_format($row['hrg_jual_1'])?></td>
												<td><?=number_format($row['hrg_beli'] * $stok_akhir)?></td>
												<td><?=number_format($row['hrg_jual_1'] * $stok_akhir)?></td>
											</tr>
										<?php
											$staw = $staw + (float)$row['stock_awal'];
											$stma = $stma + (float)$row['stock_masuk'];
											$stke = $stke + (float)$row['stock_keluar'];
											$stak = $stak + $stok_akhir;
											$tbeli = $tbeli + ($row['hrg_beli'] * $stok_akhir);
											$tjual = $tjual + ($row['hrg_jual_1'] * $stok_akhir);
										} ?>
										</tbody>
										<tfoot>
										<tr>
											<th colspan="7">TOTAL PER PAGE</th>
											<th><?=$staw?></th>
											<th><?=$stma?></th>
											<th><?=$stke?></th>
											<th><?=$stak?></th>
											<th></th>
											<th></th>
											<th><?=number_format($tbeli)?></th>
											<th><?=number_format($tjual)?></th>
											<th></th>
										</tr>
										<tr>
											<th colspan="7">TOTAL</th>
											<th><?=$tstaw?></th>
											<th><?=$tstma?></th>
											<th><?=$tstke?></th>
											<th><?=$tstak?></th>
                                            <th></th>
                                            <th></th>
                                            <th><?=number_format($ttbeli)?></th>
                                            <th><?=number_format($ttjual)?></th>
											<th></th>
										</tr>
										</tfoot>
									</table>
                                    </div>
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

<?php $i = 0 + (($this->uri->segment(4)!=null)?(($this->uri->segment(4)-1) * 20):0); foreach($report as $row){ $i++; ?>
<div id="<?=$i?>" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true" style="display: none;">
	<div class="modal-dialog modal-lg">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
				<h4 class="modal-title" id="modal_title">Detail Stock Barang</h4>
			</div>
			<div class="modal-body">
				<div class="row">
					<div class="col-sm-6">
						<div class="col-sm-4"><b>Lokasi</b></div><div class="col-sm-8"><?=$nama_lokasi?></div>
						<div class="col-sm-4"><b>Kode Barang</b></div><div class="col-sm-8"><?=$row['kd_brg']?></div>
						<div class="col-sm-4"><b>Barcode</b></div><div class="col-sm-8"><?=$row['barcode']?></div>
						<div class="col-sm-4"><b>Nama Barang</b></div><div class="col-sm-8"><?=$row['nm_brg']?></div>
					</div>
				</div>
				<hr/>
				<div class="row">
					<div class="col-md-12 col-sm-12 col-xs-12">
						<table class="table table-striped table-bordered">
							<thead>
							<tr>
								<th>No</th>
								<th>No. Transaksi</th>
								<th>Tgl. Transaksi</th>
								<th>Stock In</th>
								<th>Stock Out</th>
								<th>Keterangan</th>
							</tr>
							</thead>
							<tbody>
							<?php $no = 0;
							$detail = $this->m_crud->read_data("Kartu_stock", "kd_trx, tgl, keterangan, stock_in, stock_out", "kd_brg='".$row['kd_brg']."' AND tgl >= '".$tgl_awal." 00:00:00' and tgl <= '".$tgl_akhir." 23:59:59' ".$where_lokasi."", "tgl asc");
							foreach($detail as $rows){ $no++; ?>
								<tr>
									<td><?=$no?></td>
									<td><?=$rows['kd_trx']?></td>
									<td><?=substr($rows['tgl'], 0, 10)?></td>
									<td><?=(float)$rows['stock_in']?></td>
									<td><?=(float)$rows['stock_out']?></td>
									<td><?=$rows['keterangan']?></td>
								</tr>
							<?php } ?>
							</tbody>
						</table>
					</div>
				</div>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-default waves-effect" data-dismiss="modal">Close</button>
				<a href="<?=base_url().strtolower($this->control).'/pdf_detail_stock/'.base64_encode($tgl_awal).'/'.base64_encode($tgl_akhir).'/'.base64_encode($row['kd_brg']).'/'.base64_encode($lokasi)?>" target="_blank" class="btn btn-primary waves-effect"><i class="md md-print"></i> To PDF</a>
			</div>
		</div><!-- /.modal-content -->
	</div><!-- /.modal-dialog -->
</div><!-- /.modal -->
<?php } ?>

<div id="detail_by_supplier" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true" style="display: none;">
	<div class="modal-dialog modal-lg">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
				<h4 class="modal-title" id="modal_title">Detail Stock Barang</h4>
			</div>
			<div class="modal-body">
				<div class="row">
					<div class="col-sm-6">
						<div class="col-sm-4"><b>Kode Barang</b></div><div class="col-sm-8"><span id="kode_barang"></span></div>
						<div class="col-sm-4"><b>Barcode</b></div><div class="col-sm-8"><span id="barcode"></span></div>
						<div class="col-sm-4"><b>Nama Barang</b></div><div class="col-sm-8"><span id="nama_barang"></span></div>
					</div>
				</div>
				<hr/>
				<div class="row">
					<div class="col-md-12 col-sm-12 col-xs-12">
						<table class="table table-striped table-bordered">
							<thead>
							<tr>
								<th>No</th>
								<th>Lokasi</th>
								<th>Stock Awal</th>
								<th>Stock In</th>
								<th>Stock Out</th>
								<th>Stock Akhir</th>
								<th>Pilihan</th>
							</tr>
							</thead>
							<tbody id="detail_list">
							</tbody>
						</table>
					</div>
				</div>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-default waves-effect" data-dismiss="modal">Close</button>
			</div>
		</div><!-- /.modal-content -->
	</div><!-- /.modal-dialog -->
</div><!-- /.modal -->

<div id="detail_by_transaksi" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true" style="display: none;">
	<div class="modal-dialog modal-lg">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
				<h4 class="modal-title" id="modal_title">Detail Stock Barang</h4>
			</div>
			<div class="modal-body">
				<div class="row">
					<div class="col-sm-6">
						<div class="col-sm-4"><b>Lokasi</b></div><div class="col-sm-8"><span id="lokasi2"></span></div>
						<div class="col-sm-4"><b>Kode Barang</b></div><div class="col-sm-8"><span id="kode_barang2"></span></div>
						<div class="col-sm-4"><b>Barcode</b></div><div class="col-sm-8"><span id="barcode2"></span></div>
						<div class="col-sm-4"><b>Nama Barang</b></div><div class="col-sm-8"><span id="nama_barang2"></span></div>
					</div>
				</div>
				<hr/>
				<div class="row">
					<div class="col-md-12 col-sm-12 col-xs-12">
						<table class="table table-striped table-bordered">
							<thead>
							<tr>
								<th>No</th>
								<th>No. Transaksi</th>
								<th>Tgl. Transaksi</th>
								<th>Stock In</th>
								<th>Stock Out</th>
								<th>Keterangan</th>
							</tr>
							</thead>
							<tbody id="detail_list2">
							</tbody>
						</table>
					</div>
				</div>
			</div>
			<div class="modal-footer" id="list">
				<button type="button" class="btn btn-default waves-effect" data-dismiss="modal">Close</button>
			</div>
		</div><!-- /.modal-content -->
	</div><!-- /.modal-dialog -->
</div><!-- /.modal -->

<script>
	function detail_by_lokasi(kode, id) {
		var tgl_awal = $("#tgl_awal").val();
		var tgl_akhir = $("#tgl_akhir").val();
		var tgl = $("#field-date").val();
		var lokasi = $("#lokasi").val();

		var detail = [tgl, kode[0], lokasi];

		if (lokasi == '') {
			$.ajax({
				url: "<?php echo base_url().'inventory/detail_by_lokasi/' ?>" + btoa(JSON.stringify(detail)),
				type: "GET",
                beforeSend: function () {
                    $('#loading').show();
                },
                complete: function () {
                    $("#loading").hide();
                },
				success: function (data) {
					$("#kode_barang").text(kode[0]);
					$("#barcode").text(kode[1]);
					$("#nama_barang").text(kode[2]);
					$('#detail_by_supplier').modal('show');
					$("#detail_list").html(data);
				}
			});
		} else {
			$('#'+id).modal('show');
		}
	}

	function detail_by_transaksi(kode, lokasi) {
		var tgl_awal = $("#tgl_awal").val();
		var tgl_akhir = $("#tgl_akhir").val();
		var tgl = $("#field-date").val();

		var detail = [tgl, kode, lokasi];

		$.ajax({
			url: "<?php echo base_url().'inventory/detail_by_transaksi/' ?>" + btoa(JSON.stringify(detail)),
			type: "GET",
			dataType: "JSON",
			success: function (data) {
				$("#lokasi2").text(data.title[0]);
				$("#kode_barang2").text(data.title[1]);
				$("#barcode2").text(data.title[2]);
				$("#nama_barang2").text(data.title[3]);
				$("#detail_by_supplier").modal('hide');
				$("#detail_by_transaksi").modal('show');
				var content = document.getElementById("list");
				content.innerHTML = content.innerHTML+data.pdf;
				$("#detail_list2").html(data.list);
			}
		});
	}
	
	function export_file(kode, file) {
        var lokasi = $("#lokasi").val();  if(lokasi==''){ lokasi='semua'; }
        var tanggal = $("#field-date").val();
        window.open("<?php echo base_url() . 'inventory/export_stock/' ?>"+btoa(lokasi)+"/"+btoa(tanggal)+"/"+btoa(kode)+"/"+file,'_blank');
    }
	
	$('#detail_by_transaksi').on('hidden.bs.modal', function () {
		$("#detail_by_supplier").modal('show');
		$("#list").html('<button type="button" class="btn btn-default waves-effect" data-dismiss="modal">Close</button>');
	});

	function after_change(val) {
		$.ajax({
			url: "<?php echo base_url().'site/set_session_date/' ?>" + btoa('field-date') + '/' + btoa(val),
			type: "GET"
		});
	}
</script>
