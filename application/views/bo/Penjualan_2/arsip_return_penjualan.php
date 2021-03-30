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
									<div id="daterange" style="cursor: pointer;">
										<input type="text" name="<?=$field?>" id="<?=$field?>" class="form-control" style="height: 40px;" value="<?=isset($this->session->search[$field])?$this->session->search[$field]:(set_value($field)?set_value($field):date("Y/m/d")." - ".date("Y/m/d"))?>">
									</div>
								</div>
							</div>
							<div class="row">
								<div class="col-sm-3">
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
									<label>&nbsp;</label>
									<button type="submit" class="btn btn-primary waves-effect waves-light" name="to_excel">Export</button>
								</div>
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
											<th>Tanggal</th>
											<th>No. Transaksi</th>
											<th>Customer</th>
											<th>Lokasi</th>
											<th>Operator</th>
											<th>Nilai Retur</th>
											<th>Pilihan</th>
										</tr>
										</thead>
										<tbody>
										<?php $no = 0 + (($this->uri->segment(4)!=null)?(($this->uri->segment(4)-1) * 30):0); $nr = 0; foreach($report as $row){ $no++; ?>
											<tr>
												<td><?=$no?></td>
												<td><?=substr($row['tgl'],0,10)?></td>
												<td><?=$row['kd_trx']?></td>
												<td><?=$row['Nama']?></td>
												<td><?=$row['Lokasi']?></td>
												<td><?=$row['kd_kasir']?></td>
												<td class="text-right"><?=number_format($row['nilai_retur']-$row['diskon_item'],2)?></td>
												<td class="text-center">
													<a class="btn btn-primary" href="#" data-toggle="modal" data-target="#<?=$no?>"><i class="md md-visibility"></i> Detail</a>
												</td>
											</tr>
										<?php
										$nr = $nr + ($row['nilai_retur']-$row['diskon_item']);
										} ?>
										</tbody>
										<tfoot>
										<tr>
											<th colspan="6">TOTAL PER PAGE</th>
											<th style="text-align: right"><?=number_format($nr)?></th>
											<th></th>
										</tr>
										<tr>
											<th colspan="6">TOTAL</th>
											<th style="text-align: right"><?=number_format($tnr)?></th>
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

<?php $i = 0 + (($this->uri->segment(4)!=null)?(($this->uri->segment(4)-1) * 30):0); foreach($report as $row){ $i++; ?>
	<div id="<?=$i?>" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true" style="display: none;">
		<div class="modal-dialog modal-lg">
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
					<h4 class="modal-title" id="myLargeModalLabel">Detail <?=$title?> / <?=$row['Nama']?></h4>
				</div>
				<div class="modal-body">
					<div class="row">
						<div class="col-sm-6">
							<div class="col-sm-4"><b>Tanggal</b></div><div class="col-sm-8"><b> : </b><?=substr($row['tgl'],0,10)?></div>
							<div class="col-sm-4"><b>No. Transaksi</b></div><div class="col-sm-8"><b> : </b><?=$row['kd_trx']?></div>
							<div class="col-sm-4"><b>Lokasi</b></div><div class="col-sm-8"><b> : </b><?=$row['Lokasi']?></div>
						</div>
					</div>
					<hr/>
					<div class="row">
						<div class="col-md-12 col-sm-12 col-xs-12">
							<table class="table table-striped table-bordered">
								<thead>
								<tr>
									<th>No</th>
									<th>Kode Barang</th>
									<th>Barcode</th>
									<th>Nama Barang</th>
									<th>Kelompok Barang</th>
									<th>Qty Retur</th>
									<th>Satuan</th>
									<th>Harga Jual</th>
									<th>Diskon Item</th>
									<th>Nilai Retur</th>
								</tr>
								</thead>
								<tbody>
								<?php
								$no = 0;
								$total = 0;
								$detail = $this->m_crud->read_data('Det_Trx dt, barang br, kel_brg kb', 'br.kd_brg, br.barcode, br.nm_brg, kb.nm_kel_brg, br.satuan, dt.qty, dt.hrg_jual, dt.dis_persen', "dt.kd_brg = br.kd_brg AND br.kel_brg = kb.kel_brg AND dt.qty < 0 AND dt.kd_trx = '".$row['kd_trx']."'");
								foreach($detail as $rows){
									$no++;
									$sub_total = ($rows['qty'] * $rows['hrg_jual'] * -1) - $rows['dis_persen'];
									$total = $total + $sub_total;
									?>
									<tr>
										<td><?=$no?></td>
										<td><?=$rows['kd_brg']?></td>
										<td><?=$rows['barcode']?></td>
										<td><?=$rows['nm_brg']?></td>
										<td><?=$rows['nm_kel_brg']?></td>
										<td><?=($rows['qty'] * -1 + 0)?></td>
										<td><?=$rows['satuan']?></td>
										<td style="text-align:right;"><?=number_format($rows['hrg_jual'],2)?></td>
										<td style="text-align:right;"><?=number_format($rows['dis_persen'],2)?></td>
										<td style="text-align:right;"><?=number_format($sub_total,2)?></td>
									</tr>
								<?php } ?>
							</table>
							<table width="100%">
								<tfoot>
								<tr>
									<th class="text-right">TOTAL</th>
									<th class="text-right" style="width: 200px"><?=number_format($total,2)?></th>
								</tr>
								</tfoot>
							</table>
						</div>
					</div>
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-default waves-effect" data-dismiss="modal">Close</button>
					<a class="btn btn-primary" href="<?=base_url().strtolower($this->control).'/'.$page.'/print/'.base64_encode($row['kd_trx'])?>" target="_blank"><i class="md md-print"></i> to PDF</a>
				</div>
			</div><!-- /.modal-content -->
		</div><!-- /.modal-dialog -->
	</div><!-- /.modal -->
<?php } ?>

<script>
	function trx_number(kode) {
		$("#ntf_transaksi"+kode).text("");
		var tgl_return = $("#tgl_return"+kode).val();
		var lokasi = $("#lokasi"+kode).val();

		if (tgl_return != ''){
			$.ajax({
				url: "<?php echo base_url().'site/max_kode/' ?>" + btoa("NB") + "/" + btoa(tgl_return) + "/" + btoa(lokasi),
				type: "GET",
				success: function (data) {
					$("#no_transaksi"+kode).val(data);
				}
			});
		}else {
			$("#no_transaksi"+kode).val("");
		}
	}

	function hitung(id, kode) {
		var qty_return = parseInt($("#qty_return"+id+kode).val());
		var stock = parseInt($("#stock"+id+kode).val());
		var qty_beli = parseInt($("#qty_beli"+id+kode).val());
		var harga_beli = $("#harga_beli"+id+kode).val();

		if (qty_return < 0) {
			$("#ntf_qty_return"+id+kode).text("Qty harus lebih dari 0!");
			$("#simpan"+kode).prop("disabled", true);
			$("#nilai_return"+id+kode).val("");
		} else if (isNaN(qty_return)) {
			$("#nilai_return"+id+kode).val("");
			$("#ntf_qty_return"+id+kode).text("");
			$("#simpan"+kode).prop("disabled", false);
		} else if (qty_return > stock) {
			$("#ntf_qty_return"+id+kode).text("Stock tidak tersedia!");
			$("#simpan"+kode).prop("disabled", true);
			$("#nilai_return"+id+kode).val("");
		} else if(qty_return > qty_beli) {
			$("#ntf_qty_return"+id+kode).text("Stock melebihi pembelian!");
			$("#simpan"+kode).prop("disabled", true);
			$("#nilai_return"+id+kode).val("");
		} else {
			var nilai_return = qty_return * harga_beli;

			$("#nilai_return"+id+kode).val(to_rp(nilai_return));

			$("#ntf_qty_return"+id+kode).text("");
			$("#simpan"+kode).prop("disabled", false);
		}
	}

	function simpan(kode, data) {
		var list_data = [];
		var no_transaksi = $("#no_transaksi"+kode).val();
		var tgl_return = $("#tgl_return"+kode).val();
		var lokasi = $("#lokasi"+kode).val();
		var no_pembelian = $("#no_pembelian"+kode).val();
		var kode_supplier = $("#kode_supplier"+kode).val();

		if (no_transaksi == '') {
			$("#ntf_transaksi"+kode).text("No Transaksi Tidak Boleh Kosong!");
		}

		if (no_transaksi != '') {
			var status = 0;
			for (var i = 1; i <= data; i++) {
				var qty_return = $("#qty_return" + i + kode).val();
				var harga_beli = $("#harga_beli" + i + kode).val();
				var nilai_return = hapuskoma($("#nilai_return" + i + kode).val());
				var kode_barang = $("#kode_barang" + i + kode).val();

				var retur = {
					kode_barang_: kode_barang,
					qty_return_: qty_return,
					harga_beli_: harga_beli,
					nilai_return_: nilai_return
				};

				status = status + qty_return;

				if (qty_return > 0) {
					list_data.push(retur);
				}
			}

			if (status == 0) {
				$("#ntf_qty_return"+1+kode).text("Qty Return Belum Diisi!");
			} else {
				if (confirm("Akan Menyimpan Transaksi?")) {
					$.ajax({
						url: "<?php echo base_url() . 'pembelian/trans_return_pembelian' ?>",
						type: "POST",
						data: {
							no_transaksi_: no_transaksi,
							tgl_return_: tgl_return,
							lokasi_: lokasi,
							no_pembelian_: no_pembelian,
							kode_supplier_: kode_supplier,
							list_data_: list_data
						},
						success: function (data) {
							if (data == true) {
								alert("Transaksi Berhasil!");
							} else {
								alert("Transaksi Gagal!")
							}
							location.reload();
						}
					});
				}
			}
		}
	}

	function after_change(val) {
        $.ajax({
            url: "<?php echo base_url().'site/set_session_date/' ?>" + btoa('field-date') + '/' + btoa(val),
            type: "GET"
        });
    }
</script>