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
								<!--<div class="col-sm-3">
									<div class="form-group">
										<label>Lokasi</label>
										<?php /*$field = 'lokasi';
										$option = null; $option[''] = 'Semua Lokasi';
										//$option['all'] = 'All';
										$data_option = $this->m_crud->read_data('Lokasi', 'Kode, Nama', null, 'Nama asc');
										foreach($data_option as $row){ $option[$row['Kode']] = $row['Nama']; }
										echo form_dropdown($field, $option, isset($this->session->search[$field])?$this->session->search[$field]:set_value($field), array('class' => 'select2', 'id'=>$field));
										*/ ?>
									</div>
								</div>-->
								<div class="col-sm-3">
									<div class="form-group">
										<label>Tipe Transaksi</label>
										<?php $field = 'tipe';
										$option = null; $option[''] = 'Semua Tipe';
										$option['Tunai'] = 'Tunai';
										$option['Transfer'] = 'Transfer';
										$option['Cek/Giro'] = 'Cek/Giro';
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
                                <div class="col-sm-1">
                                    <label>&nbsp;</label>
                                    <button class="btn btn-primary waves-effect waves-light" onclick="return false" id="kartu_hutang" name="kartu_hutang">Kartu Hutang</button>
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
												<th>Tanggal</th>
												<th>No. Transaksi</th>
												<th>Nota Pembelian</th>
												<th>Nota Supplier</th>
												<th>Supplier</th>
												<th>Jenis Pembayaran</th>
												<th>Pembayaran</th>
												<th>Operator</th>
												<th>Tanggal Jatuh Tempo</th>
												<th>Pembulatan</th>
												<th>Nama Bank</th>
												<th>Cek/Giro</th>
												<th>Tanggal Cair Giro</th>
												<th>Keterangan</th>
											</tr>
											</thead>
											<tbody>
											<?php $no = 0 + (($this->uri->segment(4)!=null)?(($this->uri->segment(4)-1) * 30):0); $tp = 0; foreach($report as $row){ $no++; ?>
												<?php $pembayaran = $row['jumlah']; ?>
												<tr>
													<td><?=$no?></td>
													<td class="text-center">
														<div class="btn-group">
															<button type="button" class="btn btn-primary dropdown-toggle waves-effect waves-light" data-toggle="dropdown" aria-expanded="false">Pilihan <span class="caret"></span></button>
															<ul class="dropdown-menu" role="menu">
																<li><a href="#" data-toggle="modal" data-target="#<?=$no?>"><i class="md md-visibility"></i> Detail</a></li>
																<!--<li><a href="<?/*=base_url().strtolower($this->control).'/'.$page.'/download/'.base64_encode($row['no_faktur_beli'])*/?>"><i class="md md-get-app"></i> Download</a></li>
															<li><a href="<?/*=base_url().strtolower($this->control).'/'.$page.'/print/'.base64_encode($row['no_faktur_beli'])*/?>" target="_blank"><i class="md md-print"></i> to PDF</a></li>-->
																<li><a href="#" onclick="edit_otorisasi({param:'href_target_blank', kode:'<?=base64_encode($row['no_nota'])?>'}, edit_trx)"><i class="md md-edit"></i> Edit</a></li>
																<li><a href="#" id="delete" onclick="delete_trans('<?=$row['no_nota']?>')"><i class="md md-close"></i> Delete</a></li>
                                                                <li><a href="#" onclick="re_print('<?=$row['no_nota']?>', download_pdf)"><i class="md md-get-app"></i> Download</a></li>
                                                                <li><a href="#" onclick="re_print('<?=$row['no_nota']?>', cetak_pdf)" target="_blank"><i class="md md-print"></i> to PDF</a></li>
                                                                <li><a href="#" onclick="re_print('<?=$row['no_nota']?>', cetak, 'print_pembelian')"><i class="md md-print"></i> Print</a></li>
																<!--<li class="divider"></li>-->
															</ul>
														</div>
													</td>
													<td><?=substr($row['tgl_byr'],0,10)?></td>
													<td><?=$row['no_nota']?></td>
													<td><?=$row['fak_beli']?></td>
													<td><?=$row['noNota']?></td>
													<td><?=$row['Nama']?></td>
													<td><?=$row['cara_byr']?></td>
													<td class="text-right"><?=number_format($row['jumlah'],2)?></td>
													<td><?=$row['kasir']?></td>
													<td><?=substr($row['tgl_jatuh_tempo'],0,10)?></td>
													<td class="text-right"><?=number_format($row['bulat'],2)?></td>
													<td><?=$row['nm_bank']?></td>
													<td><?=$row['nogiro']?></td>
													<td><?=substr($row['tgl_cair_giro'],0,10)?></td>
													<td><?=$row['ket']?></td>
												</tr>
												<?php
												$tp = $tp + $pembayaran;
											} ?>
											</tbody>
											<tfoot>
											<tr>
												<th colspan="8">TOTAL PER PAGE</th>
												<th class="text-right"><?=number_format($tp, 2)?></th>
												<th colspan="7"></th>
											</tr>
											<tr>
												<th colspan="8">TOTAL</th>
												<th class="text-right"><?=number_format($ttp, 2)?></th>
												<th colspan="7"></th>
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

<!--Detail-->
<?php $i = 0 + (($this->uri->segment(4)!=null)?(($this->uri->segment(4)-1) * 30):0); foreach($report as $row){ $i++; ?>
	<div id="print_pembelian<?=$row['no_nota']?>" class="hidden">
        <img style="float: right; margin-top: -10px" src="<?=base_url().'barcode.php?size=30&sizefactor=2&text='.$row['no_nota']?>">
		<table width="100%" cellspacing="0" cellpadding="1" style="letter-spacing: 5px; font-family: 'Courier New'; margin-bottom: 10px; font-size: 9pt">
			<thead>
			<tr>
				<td colspan="8" class="text-center">Nota Bayar Hutang (<?=$row['no_faktur_beli']?>)</td>
			</tr>
			</thead>
			<tbody>
			<tr>
				<td width="2%"></td>
				<td width="20%"></td>
				<td width="2%"></td>
				<td width="28%"></td>

				<td width="10%"></td>
				<td width="12%"></td>
				<td width="2%"></td>
				<td width="25%"></td>
			</tr>
			
			<tr>
				<td></td>
				<td style="font-size: 10pt !important">Tanggal</td>
				<td style="font-size: 10pt !important">:</td>
				<td style="font-size: 10pt !important"><?=substr($row['tgl_byr'],0,10)?></td>

				<td></td>
				<td style="font-size: 10pt !important">Operator</td>
				<td style="font-size: 10pt !important">:</td>
				<td style="font-size: 10pt !important"><?=$row['kasir']?></td>
			</tr>
			<tr>
				<td></td>
				<td style="font-size: 10pt !important">No. Transaksi</td>
				<td style="font-size: 10pt !important">:</td>
				<td style="font-size: 10pt !important"><?=$row['no_nota']?></td>

				<td></td>
				<td style="font-size: 10pt !important">Jenis Pembayaran</td>
				<td style="font-size: 10pt !important">:</td>
				<td style="font-size: 10pt !important"><?=$row['cara_byr']?></td>
			</tr>
			<tr>
				<td></td>
				<td style="font-size: 10pt !important">Nota Pembelian</td>
				<td style="font-size: 10pt !important">:</td>
				<td style="font-size: 10pt !important"><?=$row['fak_beli']?></td>

				<td></td>
				<td style="font-size: 10pt !important">Pembayaran</td>
				<td style="font-size: 10pt !important">:</td>
				<td style="font-size: 10pt !important"><?=number_format($row['jumlah'],2)?></td>
			</tr>
			<tr>
				<td></td>
				<td style="font-size: 10pt !important">Tanggal Jatuh Tempo</td>
				<td style="font-size: 10pt !important">:</td>
				<td style="font-size: 10pt !important"><?=substr($row['tgl_jatuh_tempo'],0,10)?></td>

				<td></td>
				<td style="font-size: 10pt !important">Pembulatan</td>
				<td style="font-size: 10pt !important">:</td>
				<td style="font-size: 10pt !important"><?=number_format($row['bulat'],2)?></td>
			</tr>
			<tr>
				<td></td>
				<td style="font-size: 10pt !important">Supplier</td>
				<td style="font-size: 10pt !important">:</td>
				<td style="font-size: 10pt !important"><?=$row['Nama']?></td>

				<td></td>
				<td style="font-size: 10pt !important">Cek/Giro</td>
				<td style="font-size: 10pt !important">:</td>
				<td style="font-size: 10pt !important"><?=$row['nogiro']?></td>
			</tr>
			<tr>
				<td></td>
				<td style="font-size: 10pt !important">Bank</td>
				<td style="font-size: 10pt !important">:</td>
				<td style="font-size: 10pt !important"><?=$row['nm_bank']?></td>

				<td></td>
				<td style="font-size: 10pt !important">Keterangan</td>
				<td style="font-size: 10pt !important">:</td>
				<td style="font-size: 10pt !important"><?=$row['ket']?></td>
			</tr>
			<tr>
				<td></td>
				<td style="font-size: 10pt !important">Tanggal Cair Giro</td>
				<td style="font-size: 10pt !important">:</td>
				<td style="font-size: 10pt !important"><?=substr($row['tgl_cair_giro'],0,10)?></td>

				<td></td>
				<td style="font-size: 10pt !important"></td>
				<td style="font-size: 10pt !important"></td>
				<td style="font-size: 10pt !important"></td>
			</tr>
			</tbody>
		</table>
		
		<table width="100%" style="letter-spacing: 5px; font-family: 'Courier New';font-size: 9pt">
			<thead>
			<tr>
				<td style="border-top: solid; border-width: thin" width="33%"></td>
				<td style="border-top: solid; border-width: thin" width="33%"></td>
				<td style="border-top: solid; border-width: thin" width="33%"></td>
			</tr>
			</thead>
			<tbody>
			<tr>
				<td style="text-align:center;">
				</td>
				<td style="text-align:center;">
				</td>
				<td style="text-align:center;">
					<b><br/><br/><br/><br/>_____________</b>
				</td>
			</tr>
			</tbody>
		</table>
        <span style="font-weight: bold; font-style: italic"><?=$this->m_website->reprint($row['no_nota'], 'reprint')?></span>
    </div>


	<div id="<?=$i?>" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true" style="display: none;">
		<div class="modal-dialog modal-full">
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
					<h4 class="modal-title" id="myLargeModalLabel">Detail <?=$title?> / <?=$row['nama']?></h4>
				</div>
				<div class="modal-body">
					<div class="row">
						<div class="col-sm-6">
							<div class="col-sm-4"><b>Tanggal</b></div><div class="col-sm-8"><b> : </b><?=substr($row['tgl_byr'],0,10)?></div>
							<div class="col-sm-4"><b>No. Transaksi</b></div><div class="col-sm-8"><b> : </b><?=$row['no_nota']?></div>
							<div class="col-sm-4"><b>Nota Beli</b></div><div class="col-sm-8"><b> : </b><?=$row['fak_beli']?></div>
							<div class="col-sm-4"><b>Supplier</b></div><div class="col-sm-8"><b> : </b><?=$row['Nama']?></div>
							<div class="col-sm-4"><b>Tanggal Jatuh Tempo</b></div><div class="col-sm-8"><b> : </b><?=substr($row['tgl_jatuh_tempo'],0,10)?></div>
						</div>
						<div class="col-sm-2"></div>
						<div class="col-sm-4">
							<div class="col-sm-4"><b>Jenis Pembayaran</b></div><div class="col-sm-8"><b> : </b><?=$row['cara_byr']?></div>
							<div class="col-sm-4"><b>Pembayaran</b></div><div class="col-sm-8"><b> : </b><?=number_format($row['jumlah'],2)?></div>
							<div class="col-sm-4"><b>Pembulatan</b></div><div class="col-sm-8"><b> : </b><?=number_format($row['bulat'],2)?></div>
							<div class="col-sm-4"><b>Operator</b></div><div class="col-sm-8"><b> : </b><?=$row['kasir']?></div>
						</div>
					</div>
					<hr/>
					<div class="row">
						<div class="col-sm-6">
							<div class="col-sm-4"><b>Bank</b></div><div class="col-sm-8"><b> : </b><?=$row['nm_bank']?></div>
							<div class="col-sm-4"><b>Cek/Giro</b></div><div class="col-sm-8"><b> : </b><?=$row['nogiro']?></div>
						</div>
						<div class="col-sm-2"></div>
						<div class="col-sm-4">
							<div class="col-sm-4"><b>Tanggal Cair Giro</b></div><div class="col-sm-8"><b> : </b><?=substr($row['tgl_cair_giro'],0,10)?></div>
							<div class="col-sm-4"><b>Keterangan</b></div><div class="col-sm-8"><b> : </b><?=$row['ket']?></div>
						</div>
					</div>
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-default waves-effect" data-dismiss="modal">Close</button>
					<a href="<?=base_url().'pembelian/bayar_hutang_report/print/'.base64_encode($row['no_nota'])?>" target="_blank"><button type="button" class="btn btn-primary waves-effect waves-light"><i class="md md-print"></i> Nota</button></a>
				</div>
			</div><!-- /.modal-content -->
		</div><!-- /.modal-dialog -->
	</div><!-- /.modal -->
<?php } ?>

<div id="modal-hutang" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="modal-barang" aria-hidden="true" style="display: none;">
    <div class="modal-dialog modal-full">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                <h4 class="modal-title" id="modal-title"></h4>
            </div>
            <div class="modal-body">
                <div class="row" style="margin-bottom: 10px">
                    <div class="col-sm-3">
                        <div class="form-group">
                            <?php $field = 'search_supplier'; ?>
                            <input class="form-control" placeholder="Search Supplier" type="text" id="<?=$field?>" name="<?=$field?>" onkeyup="filter_supplier($(this).val())" value="<?=isset($this->session->search[$field])?$this->session->search[$field]:set_value($field)?>" autofocus />
                            <?=form_error($field, '<div class="error" style="color:red;">', '</div>')?>
                        </div>
                    </div>
                    <div class="col-sm-9">
                        <h3 class="pull-right" id="total-hutang"></h3>
                    </div>
                </div>
                <div class="row">
                    <div class="panel-group panel-group-joined" id="list-hutang">
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default waves-effect" data-dismiss="modal">Close</button>
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->

<script>
    function filter_supplier(supplier) {
        $.ajax({
            url: "<?php echo base_url() . 'pembelian/get_kartu_hutang' ?>",
            data: {supplier_: supplier},
            type: "POST",
            dataType: "JSON",
            cache: false,
            beforeSend: function () {
                $('#loading').show();
                $("#list-barang").html("");
            },
            complete: function () {
                $("#loading").hide();
            },
            success: function (res) {
                $("#list-hutang").html(res.list_hutang);
                $("#total-hutang").text(res.total_hutang);
            }
        });
    }

    $("#kartu_hutang").click(function () {
        $.ajax({
            url: "<?php echo base_url() . 'pembelian/get_kartu_hutang' ?>",
            type: "POST",
            dataType: "JSON",
            cache: false,
            beforeSend: function () {
                $('#loading').show();
                $("#list-barang").html("");
            },
            complete: function () {
                $("#loading").hide();
            },
            success: function (res) {
                $("#modal-title").text("Kartu Hutang");
                $("#modal-hutang").modal("show");
                $("#list-hutang").html(res.list_hutang);
                $("#total-hutang").text(res.total_hutang);
            }
        });
    });
	
	function edit_trx(id, res) {
        if (res == true) {
			//add_activity('Edit Pembelian '+id.kode);
            if (id.param == 'href_target_blank') {
                window.open("<?=base_url().'pembelian/bayar_hutang/edit/'?>" + id.kode);
            } else if(id.param == 'href') {
                window.location = "<?=base_url().'pembelian/bayar_hutang/edit/'?>" + id.kode;
            }
        }
    }
	
	function delete_trans(kode) {
		if (confirm('Akan menghapus data?')) {
			var table_ = ['bayar_hutang'];
			var condition_ = ['no_nota=\''+kode+'\''];

			$.ajax({
				url: "<?php echo base_url().'site/delete_ajax_trx' ?>",
				type: "POST",
				data: {table:table_, condition:condition_},
				success: function (res) {
					if (res == true) {
						alert("Data berhasil dihapus!");
					} else {
						alert("Data gagal dihapus!");	
					}
					location.reload();
				}
			});
		}
	}

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

	function hitung_total(kode) {
		var banyak_data = parseInt($("#banyak_data"+kode).val());

		var total_retur = 0;
		for (var i = 1; i <= banyak_data; i++) {
			var qty = $("#qty_return" + i + kode).val();
			var harga = $("#harga_beli" + i + kode).val();
			if (qty > 0) {
				total_retur = total_retur + (parseInt(qty) * parseInt(harga));
			}
		}
		$("#total_retur"+kode).text(to_rp(total_retur));
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
			hitung_total(kode);
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
							if (data != false) {
								if (confirm("Transaksi Berhasil! Akan Mencetak Nota?")) {
									window.open('<?=base_url().'cetak/nota_retur_pembelian/'?>' + btoa(data));
								}
								location.reload();
							} else {
								alert("Transaksi Gagal!");
								location.reload();
							}
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