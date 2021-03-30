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
                                <div class="col-sm-3">
                                    <div class="form-group">
                                        <label>Kondisi</label>
                                        <?php $field = 'kondisi';
                                        $kondisi = json_decode($menu_group['status_barang'], true);
                                        $option = null; $option[''] = 'Semua Kondisi';
                                        for ($x = 0; $x < count($kondisi['status_barang_ho']); $x++) {
                                            $option[$kondisi['status_barang_ho'][$x]['status']] = $kondisi['status_barang_ho'][$x]['status'];
                                        }
                                        echo form_dropdown($field, $option, isset($this->session->search[$field])?$this->session->search[$field]:set_value($field), array('class' => 'select2', 'id'=>$field));
                                        ?>
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
										$data_option = $this->m_crud->read_data('Lokasi', 'Kode,nama_toko Nama', $this->where_lokasi, 'Nama asc');
										foreach($data_option as $row){ $option[$row['Kode']] = $row['Nama']; }
										echo form_dropdown($field, $option, isset($this->session->search[$field])?$this->session->search[$field]:set_value($field), array('class' => 'select2', 'id'=>$field));
										?>
									</div>
								</div>
								<div class="col-sm-3">
									<div class="form-group">
										<label>Lokasi Cabang</label>
										<?php $field = 'lokasi_cabang';
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
										<input class="form-control" type="text" id="<?=$field?>" name="<?=$field?>" placeholder="No Retur/No Nota/Supplier/Operator" value="<?=isset($this->session->search[$field])?$this->session->search[$field]:set_value($field)?>" autofocus />
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
                                                <th>Tanggal</th>
                                                <th>No. Retur</th>
                                                <th>No. Pembelian</th>
                                                <th>Nota Supplier</th>
                                                <th>Keterangan</th>
                                                <th>Supplier</th>
                                                <th>Lokasi</th>
                                                <th>Lokasi Cabang</th>
                                                <th>Nilai Retur</th>
                                                <th>Operator</th>
                                            </tr>
                                            </thead>
                                            <tbody>
                                            <?php $no = 0 + (($this->uri->segment(4)!=null)?(($this->uri->segment(4)-1) * 30):0); foreach($report as $row){ $no++; ?>
                                                <tr>
                                                    <td><?=$no?></td>
                                                    <td class="text-center">
                                                        <div class="btn-group">
                                                            <button type="button" class="btn btn-primary dropdown-toggle waves-effect waves-light" data-toggle="dropdown" aria-expanded="false">Pilihan <span class="caret"></span></button>
                                                            <ul class="dropdown-menu" role="menu">
                                                                <li><a href="#" data-toggle="modal" data-target="#<?=$no?>"><i class="md md-visibility"></i> Detail</a></li>
                                                                <!--<li><a href="<?/*=base_url().strtolower($this->control).'/'.$page.'/download/'.base64_encode($row['no_faktur_beli'])*/?>"><i class="md md-get-app"></i> Download</a></li>
															    <li><a href="<?/*=base_url().strtolower($this->control).'/'.$page.'/print/'.base64_encode($row['no_faktur_beli'])*/?>" target="_blank"><i class="md md-print"></i> to PDF</a></li>-->
                                                                <li><a href="#" onclick="re_print('<?=$row['No_Retur']?>', pdf)"><i class="md md-print"></i> to PDF</a></li>
                                                                <li><a href="#" onclick="re_print('<?=$row['No_Retur']?>', cetak, 'print_retur_pembelian')"><i class="md md-print"></i> Print</a></li>
                                                                <?php if ($row['noNota'] == 'Tanpa Nota') {
                                                                    echo '<li><a href = "#" onclick="edit_otorisasi({param:\'href_target_blank\', kode:\''.base64_encode($row['No_Retur']).'\', activity:\'Edit Retur Pembelian\'}, edit_trx)" ><i class="md md-edit" ></i> Edit</a ></li>';
                                                                } ?>
                                                                <li><a href="#" id="delete" onclick="delete_trans('<?=$row['No_Retur']?>')"><i class="md md-close"></i> Delete</a></li>
                                                                <!--<li class="divider"></li>-->
                                                            </ul>
                                                        </div>
                                                    </td>
                                                    <td><?=substr($row['Tgl'],0,10)?></td>
                                                    <td><?=$row['No_Retur']?></td>
                                                    <td><?=$row['no_beli']?></td>
                                                    <td><?=$row['noNota']?></td>
                                                    <td><?=$row['keterangan']?></td>
                                                    <td><?=$row['Nama']?></td>
                                                    <td><?=$row['Lokasi']?></td>
                                                    <td><?=$row['lokasi_cabang']?></td>
                                                    <td class="text-right"><?=number_format($row['Total'],2)?></td>
													<td><?=$row['kd_kasir']?></td>
                                                </tr>
                                            <?php } ?>
                                            </tbody>
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
							<div class="col-sm-4"><b>Tanggal</b></div><div class="col-sm-8"><b> : </b><?=substr($row['Tgl'],0,10)?></div>
							<div class="col-sm-4"><b>No. Transaksi</b></div><div class="col-sm-8"><b> : </b><?=$row['No_Retur']?></div>
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
                                    <th>Kondisi</th>
                                    <th>Ketarangan</th>
									<th>Qty</th>
									<th>Satuan</th>
									<th>Harga Beli</th>
									<th>Sub Total</th>
								</tr>
								</thead>
								<tbody>
								<?php
								$no = 0;
								$sub_total = 0;
								$tqty = 0;
								$total = 0;
								$detail = $this->m_crud->read_data('Det_Retur_Beli drb, barang br, kel_brg kb', 'drb.kd_brg, drb.jml, drb.hrg_beli, drb.kondisi, drb.keterangan, br.barcode, br.nm_brg, kb.nm_kel_brg, br.satuan', "drb.kd_brg = br.kd_brg AND br.kel_brg = kb.kel_brg AND drb.No_Retur = '".$row['No_Retur']."'");
								foreach($detail as $rows){
									$no++;
									$sub_total = $rows['jml'] * $rows['hrg_beli'];
									$total = $total + $sub_total;
									$tqty = $tqty + ($rows['jml']+0);
									?>
									<tr>
										<td><?=$no?></td>
										<td><?=$rows['kd_brg']?></td>
										<td><?=$rows['barcode']?></td>
										<td><?=$rows['nm_brg']?></td>
										<td><?=$rows['nm_kel_brg']?></td>
										<td><?=$rows['kondisi']?></td>
										<td><?=$rows['keterangan']?></td>
										<td><?=($rows['jml']+0)?></td>
										<td><?=$rows['satuan']?></td>
										<td style="text-align:right;"><?=number_format($rows['hrg_beli'],2)?></td>
										<td style="text-align:right;"><?=number_format($sub_total,2)?></td>
									</tr>
								<?php } ?>
								</tbody>
								<tfoot>
								<tr>
									<th colspan="7">TOTAL</th>
									<th><?=$tqty?></th>
									<th colspan="2"></th>
									<th style="text-align:right;"><?=number_format($total,2)?></th>
								</tr>
								</tfoot>
							</table>
						</div>
					</div>
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-default waves-effect" data-dismiss="modal">Close</button>
					<a href="<?=base_url().'cetak/nota_retur_pembelian/'.base64_encode($row['No_Retur'])?>" target="_blank"><button type="button" class="btn btn-primary waves-effect waves-light"><i class="md md-print"></i> Nota</button></a>
				</div>
			</div><!-- /.modal-content -->
		</div><!-- /.modal-dialog -->
	</div><!-- /.modal -->

	<div id="print_retur_pembelian<?=$row['No_Retur']?>" class="hidden">
        <div class="row"><img style="float: right; margin-top: -10px" src="<?=base_url().'barcode.php?size=30&sizefactor=2&text='.$row['No_Retur']?>"></div>
        <img style="height: 1cm; position: absolute" src="<?=base_url().'assets/images/site/'.$this->m_website->site_data()->logo?>">
		<table width="100%" cellspacing="0" cellpadding="1" style="letter-spacing: 5px; font-family: 'Courier New'; margin-bottom: 10px; font-size: 9pt">
			<thead>
			<tr>
				<td style="height: 1.5cm; text-align: center" colspan="8">Nota Retur Pembelian</td>
			</tr>
			</thead>
			<tbody>
			<tr>
				<td width="2%"></td>
				<td width="23%"></td>
				<td width="2%"></td>
				<td width="25%"></td>

				<td width="3%"></td>
				<td width="19%"></td>
				<td width="2%"></td>
				<td width="25%"></td>
			</tr>
			<tr>
				<td></td>
				<td style="font-size: 8pt !important">Tanggal</td>
				<td style="font-size: 8pt !important">:</td>
				<td style="font-size: 10pt !important"><?=substr($row['Tgl'],0,10)?></td>

				<td></td>
				<td style="font-size: 8pt !important">Retur Ke</td>
				<td style="font-size: 8pt !important">:</td>
				<td style="font-size: 10pt !important"><?=$row['Kode']." - ".$row['Nama']?></td>
			</tr>
			<tr>
				<td></td>
				<td style="font-size: 8pt !important">No. Transaksi</td>
				<td style="font-size: 8pt !important">:</td>
				<td style="font-size: 10pt !important"><?=$row['No_Retur']?></td>

                <td></td>
                <td style="font-size: 8pt !important">Nota Supplier</td>
                <td style="font-size: 8pt !important">:</td>
                <td style="font-size: 10pt !important"><?=$row['noNota']?></td>
			</tr>
            <tr>
                <td></td>
                <td style="font-size: 8pt !important">Operator</td>
                <td style="font-size: 8pt !important">:</td>
                <td style="font-size: 10pt !important"><?=$this->m_website->get_nama_user($row['kd_kasir'])?></td>

                <td></td>
                <td style="font-size: 8pt !important">Lokasi Cabang</td>
                <td style="font-size: 8pt !important">:</td>
                <td style="font-size: 10pt !important"><?=$row['lokasi_cabang']?></td>
            </tr>
			</tbody>
		</table>

		<table width="100%" style="letter-spacing: 5px; font-family: 'Courier New'; font-size:9pt;">
			<thead>
			<tr>
				<td style="border-bottom: solid; border-width: thin; padding-left: 5pt; font-size: 10pt !important">No</td>
				<td style="border-bottom: solid; border-width: thin; padding-left: 5pt; font-size: 10pt !important">Kode Barang</td>
				<td style="border-bottom: solid; border-width: thin; padding-left: 5pt; font-size: 10pt !important">Barcode</td>
				<td style="border-bottom: solid; border-width: thin; padding-left: 5pt; font-size: 10pt !important">Nama Barang</td>
				<td style="border-bottom: solid; border-width: thin; padding-left: 5pt; font-size: 10pt !important">Qty</td>
				<td style="border-bottom: solid; border-width: thin; padding-left: 5pt; font-size: 10pt !important">Satuan</td>
				<td style="border-bottom: solid; border-width: thin; padding-left: 5pt; font-size: 10pt !important">Kondisi</td>
				<td style="border-bottom: solid; border-width: thin; padding-left: 5pt; font-size: 10pt !important">Harga Beli</td>
				<td style="border-bottom: solid; border-width: thin; padding-left: 5pt; font-size: 10pt !important">Sub Total</td>
			</tr>
			</thead>
			<tbody>
			<?php
			$no = 0;
			$sub_total = 0;
			$total = 0;
			$tqty = 0;
			foreach($detail as $rows){
				$no++;
				$sub_total = $rows['jml'] * $rows['hrg_beli'];
				$total = $total + $sub_total;
				$tqty = $tqty + ($rows['jml']+0);
				?>
				<tr>
					<td><?=$no?></td>
					<td><?=$rows['kd_brg']?></td>
					<td><?=$rows['barcode']?></td>
					<td><?=$rows['nm_brg']?></td>
					<td><?=($rows['jml']+0)?></td>
					<td><?=$rows['satuan']?></td>
					<td><?=$rows['kondisi']?></td>
					<td class="text-right"><?=number_format($rows['hrg_beli'],2)?></td>
					<td class="text-right"><?=number_format($sub_total,2)?></td>
				</tr>
				<?php
			} ?>
			</tbody>
			<tfoot>
			<tr>
				<td style="border-top: solid; border-width: thin" colspan="4">TOTAL</td>
				<td style="border-top: solid; border-width: thin"><?=$tqty?></td>
				<td style="border-top: solid; border-width: thin" colspan="3"></td>
				<td style="border-top: solid; border-width: thin" class="text-right"><?=number_format($total,2)?></td>
			</tr>
			</tfoot>
		</table>
		<table width="100%" style="letter-spacing: 5px; font-family: 'Courier New';font-size: 9pt">
			<thead>
			<tr>
				<td style="border-top: solid; border-width: thin" width="25%"></td>
				<td style="border-top: solid; border-width: thin" width="25%"></td>
				<td style="border-top: solid; border-width: thin" width="25%"></td>
				<td style="border-top: solid; border-width: thin" width="25%"></td>
			</tr>
			</thead>
			<tbody>
			<tr>
				<td style="text-align:left;" colspan="3">
                    <u><?=number_to_words($total)?></u>
				</td>
				<td style="text-align:center;">
                    <?php echo date("d M Y"); ?>
                </td>
			</tr>
            <tr>
                <td style="text-align:center;">
                    <b><br/><br/><br/><br/>____________<br/>Penerimaan</b>
                </td>
                <td style="text-align:center;">
                    <b><br/><br/><br/><br/>____________<br/>EDP In</b>
                </td>
                <td style="text-align:center;">
                    <b><br/><br/><br/><br/>____________<br/>Gudang Retur</b>
                </td>
                <td style="text-align:center;">
                    <b><br/><br/><br/><br/>____________<br/>Mengetahui</b>
                </td>
            </tr>
			</tbody>
		</table>
        <span style="font-weight: bold; font-style: italic"><?=$this->m_website->reprint($row['No_Retur'], 'reprint')?></span>
        <table width="100%" border="0" style="margin-top: 5mm; letter-spacing: 5px; font-family: 'Courier New';font-size: 9pt">
            <tr>
                <td colspan="3">Ket : <?=$row['keterangan']?></td>
            </tr>
        </table>
	</div>
<?php } ?>

<script>
    function pdf(id, res) {
        if (res == true) {
            window.open("<?=base_url().'cetak/nota_retur_pembelian/'?>" + btoa(id));
        }
    }

	function edit_trx(id, res) {
        if (res == true) {
			//add_activity('Edit Pembelian '+id.kode);
            if (id.param == 'href_target_blank') {
                window.open("<?=base_url().'pembelian/edit_retur_tanpa_nota/'?>" + id.kode);
            } else if(id.param == 'href') {
                window.location = "<?=base_url().'pembelian/edit_retur_tanpa_nota/'?>" + id.kode;
            }
        }
    }
	
	function delete_trans(kode) {
		if (confirm('Akan menghapus data?')) {
			var table_ = ['Master_Retur_Beli', 'Det_Retur_Beli', 'Kartu_stock'];
			var condition_ = ['No_Retur=\''+kode+'\'','No_Retur=\''+kode+'\'','kd_trx=\''+kode+'\''];

			hapus_otorisasi({param:'', kode:btoa(kode), activity:'Hapus Retur Pembelian', table:table_, condition:condition_}, delete_transaksi);
		}
	}
	
	function delete_transaksi(id, res) {
	    if (res == true) {
			$.ajax({
				url: "<?php echo base_url() . 'site/delete_ajax_trx' ?>",
				type: "POST",
				data: {table: id.table, condition: id.condition},
				success: function (res) {
					if (res == true) {
						alert("Data berhasil dihapus!");
					} else {
						alert("Data gagal dihapus!");
					}
					window.location = window.location.href.replace('#','');
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