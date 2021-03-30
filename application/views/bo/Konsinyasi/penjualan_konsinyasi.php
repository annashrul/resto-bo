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
									<div data-pattern="priority-columns">
										<table class="table table-striped table-bordered">
											<thead>
											<tr>
												<th>No</th>
												<th>Kode <?=$menu_group['as_group1']?></th>
												<th>Nama <?=$menu_group['as_group1']?></th>
												<th>Qty Terjual</th>
												<th>Gross Sales</th>
												<th>Diskon Item</th>
												<th>Jumlah Beli</th>
                                                <th>Jumlah Jual</th>
                                                <th>Pilihan</th>
											</tr>
											</thead>
											<tbody>
											<?php $no = 0 + (($this->uri->segment(4)!=null)?(($this->uri->segment(4)-1) * 30):0); $qt = 0; $gs = 0; $di = 0; $ns = 0; $tb = 0; foreach($report as $row){ $no++; ?>
												<tr>
													<td><?=$no?></td>
													<td><?=$row['Kode']?></td>
													<td><?=$row['Nama']?></td>
													<td><?=($row['qty']+0)?></td>
													<td class="text-right"><?=(number_format($row['gross_sales']+0))?></td>
													<td class="text-right"><?=(number_format($row['diskon_item']+0))?></td>
													<td class="text-right"><?=(number_format($row['total_beli']+0))?></td>
                                                    <td class="text-right"><?=number_format(($row['gross_sales']+0) - ($row['diskon_item']+0))?></td>
                                                    <td class="text-center">
														<div class="btn-group">
															<button type="button" class="btn btn-primary dropdown-toggle waves-effect waves-light" data-toggle="dropdown" aria-expanded="false">Pilihan <span class="caret"></span></button>
															<ul class="dropdown-menu" role="menu">
																<li><a href="#" data-toggle="modal" data-target="#<?=$no?>"><i class="md md-visibility"></i> Detail</a></li>
																<li><a href="<?=base_url().strtolower($this->control).'/'.$page.'/download/'.base64_encode($row['Kode'])?>"><i class="md md-get-app"></i> Download</a></li>
																<li><a href="<?=base_url().strtolower($this->control).'/'.$page.'/print/'.base64_encode($row['Kode'])?>" target="_blank"><i class="md md-print"></i> to PDF</a></li>
																<li><a href="#" onclick="printDiv('print_konsinyasi<?=$no?>')"><i class="md md-print"></i> Print</a></li>

																<!--<li class="divider"></li>-->
															</ul>
														</div>
													</td>
												</tr>
											<?php
											$qt = $qt + (int)$row['qty'];
											$gs = $gs + (float)$row['gross_sales'];
											$di = $di + (float)$row['diskon_item'];
											$ns = $ns + (float)$row['gross_sales']-(float)$row['diskon_item'];
											$tb = $tb + $row['total_beli'];
											} ?>
											</tbody>
											<tfoot>
											<tr>
												<th colspan="3">TOTAL PER PAGE</th>
												<th><?=$qt?></th>
												<th class="text-right"><?=number_format($gs, 2)?></th>
												<th class="text-right"><?=number_format($di, 2)?></th>
												<th class="text-right"><?=number_format($tb, 2)?></th>
                                                <th class="text-right"><?=number_format($ns, 2)?></th>
                                                <th></th>
											</tr>
											<tr>
												<th colspan="3">TOTAL</th>
												<th><?=$tqt?></th>
												<th class="text-right"><?=number_format($tgs, 2)?></th>
												<th class="text-right"><?=number_format($tdi, 2)?></th>
												<th class="text-right"><?=number_format($ttb, 2)?></th>
                                                <th class="text-right"><?=number_format($tns, 2)?></th>
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
				</div>

			</div> <!-- End Row -->

		</div> <!-- container -->

	</div> <!-- content -->

</div>
<!-- ============================================================== -->
<!-- End Right content here -->
<!-- ============================================================== -->

<?php $i = 0 + (($this->uri->segment(4)!=null)?(($this->uri->segment(4)-1) * 30):0); foreach($report as $row){ $i++; ?>
	<div id="print_konsinyasi<?=$i?>" class="hidden">
		<table width="100%" cellspacing="0" cellpadding="1" style="letter-spacing: 5px; font-family: 'Courier New'; margin-bottom: 10px; font-size: 9pt">
			<thead>
			<tr>
				<td colspan="8" class="text-center">Laporan Penjualan Konsinyasi</td>
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
				<td>Kode <?=$menu_group['as_group1']?></td>
				<td>:</th>
				<td><?=$row['Kode']?></td>

				<td></td>
				<td colspan="3"><?=$periode?></td>
			</tr>
			<tr>
				<td></td>
				<td>Nama <?=$menu_group['as_group1']?></td>
				<td>:</td>
				<td><?=$row['Nama']?></td>

				<td></td>
				<td colspan="3"><?=$q_lokasi?></td>
			</tr>
			</tbody>
		</table>

		<table width="100%" style="letter-spacing: 5px; font-family: 'Courier New'; font-size:9pt;">
			<thead>
			<tr>
				<td style="border: solid; border-width: thin; padding-left: 5pt">No</td>
				<td style="border: solid; border-width: thin; padding-left: 5pt">Kode Barang</td>
				<td style="border: solid; border-width: thin; padding-left: 5pt">Barcode</td>
				<td style="border: solid; border-width: thin; padding-left: 5pt"><?=$menu_group['as_deskripsi']?></td>
				<td style="border: solid; border-width: thin; padding-left: 5pt">Nama Barang</td>
				<td style="border: solid; border-width: thin; padding-left: 5pt">Stock Awal</td>
				<td style="border: solid; border-width: thin; padding-left: 5pt">Stock Masuk</td>
				<td style="border: solid; border-width: thin; padding-left: 5pt">Stock Akhir</td>
				<td style="border: solid; border-width: thin; padding-left: 5pt">Jual</td>
				<td style="border: solid; border-width: thin; padding-left: 5pt">Retur</td>
				<td style="border: solid; border-width: thin; padding-left: 5pt">Satuan</td>
				<td style="border: solid; border-width: thin; padding-left: 5pt">Harga Beli</td>
				<!--<td style="border: solid; border-width: thin; padding-left: 5pt">Harga Jual</td>-->
				<td style="border: solid; border-width: thin; padding-left: 5pt">Diskon Item</td>
				<td style="border: solid; border-width: thin; padding-left: 5pt">Jumlah Beli</td>
				<!--<td style="border: solid; border-width: thin; padding-left: 5pt">Gross Sales</td>-->
			</tr>
			</thead>
			<tbody>
			<?php
			$no = 0;
			$jumlah = 0; $sa = 0; $sp = 0; $st = 0; $jl = 0; $rt = 0; $di = 0; $j_jual = 0;
			$q_tgl = "AND mt.tgl >= '".$tgl_awal." 00:00:00' and mt.tgl <= '".$tgl_akhir." 23:59:59'";
			$where = ""; ($lokasi==null)?"":$where.=" AND mt.Lokasi='".$lokasi."'";
			$lokasi_trx = ""; ($lokasi==null)?"":$lokasi_trx=" AND mtrx.lokasi='".$lokasi."'";
			$where_stock = "kd_brg=br.kd_brg "; ($lokasi==null)?$where_stock.=" AND Lokasi NOT IN ('MUTASI', 'Retur')":$where_stock.=" AND Lokasi='".$lokasi."'";
			$stock_awal = "isnull((select sum(stock_in - stock_out) from kartu_stock where ".$where_stock." and tgl < '".$tgl_awal." 00:00:00'),0) as stock_awal";
			$stock_periode = "isnull((select sum(stock_in) from kartu_stock where ".$where_stock." and tgl >= '".$tgl_awal." 00:00:00' and tgl <= '".$tgl_akhir." 23:59:59'),0) as stock_periode";
			$stock_periode2 = ", isnull((select sum(stock_in-stock_out) from kartu_stock where kd_brg=br.kd_brg and tgl > '".$tgl_awal." 00:00:00'), 0) as stock_periode";
			$retur = "isnull((select sum(dtrx.qty) from Det_Trx dtrx, Master_Trx mtrx where dtrx.kd_brg=br.kd_brg and dtrx.qty < 0 and dtrx.kd_trx=mtrx.kd_trx and mtrx.tgl >= '".$tgl_awal." 00:00:00' and mtrx.tgl <= '".$tgl_akhir." 23:59:59'".$lokasi_trx."),0) as retur";
			$jual = "isnull((select sum(dtrx.qty) from Det_Trx dtrx, Master_Trx mtrx where dtrx.kd_brg=br.kd_brg and dtrx.qty > 0 and dtrx.kd_trx=mtrx.kd_trx and mtrx.tgl >= '".$tgl_awal." 00:00:00' and mtrx.tgl <= '".$tgl_akhir." 23:59:59'".$lokasi_trx."),0) as jual";
			$detail = $this->m_crud->read_data("Det_Trx dt, barang br, Master_Trx mt", "SUM(dt.dis_persen) diskon_item, AVG(dt.hrg_beli) hrg_beli, AVG(dt.hrg_jual) hrg_jual, br.kd_brg, br.Deskripsi, br.nm_brg, br.barcode, br.satuan, ".$stock_awal.",".$stock_periode.",".$retur.",".$jual."", "mt.HR = 'S' AND dt.qty > 0 AND mt.kd_trx=dt.kd_trx AND br.Group2='KS' AND dt.kd_brg=br.kd_brg AND br.Group1 = '".$row['Kode']."' ".$q_tgl." ".$where, null, "br.kd_brg, br.nm_brg, br.barcode, br.Deskripsi, br.satuan");
			foreach($detail as $rows){ $no++; ?>
				<tr>
					<td style="border: solid; border-width: thin; padding-left: 2pt" class="text-center"><?=$no?></td>
					<td style="border: solid; border-width: thin; padding-left: 2pt"><?=$rows['kd_brg']?></td>
					<td style="border: solid; border-width: thin; padding-left: 2pt"><?=$rows['barcode']?></td>
					<td style="border: solid; border-width: thin; padding-left: 2pt"><?=$rows['Deskripsi']?></td>
					<td style="border: solid; border-width: thin; padding-left: 2pt"><?=$rows['nm_brg']?></td>
					<td style="border: solid; border-width: thin; padding-left: 2pt" class="text-center"><?=($rows['stock_awal']+0)?></td>
					<td style="border: solid; border-width: thin; padding-left: 2pt" class="text-center"><?=($rows['stock_periode']+0)?></td>
					<td style="border: solid; border-width: thin; padding-left: 2pt" class="text-center"><?=($rows['stock_awal']+$rows['stock_periode']-$rows['jual']-$rows['retur']+0)?></td>
					<td style="border: solid; border-width: thin; padding-left: 2pt" class="text-center"><?=($rows['jual']+0)?></td>
					<td style="border: solid; border-width: thin; padding-left: 2pt" class="text-center"><?=($rows['retur']+0)?></td>
					<td style="border: solid; border-width: thin; padding-left: 2pt"><?=$rows['satuan']?></td>
					<td style="border: solid; border-width: thin; padding-right: 2pt" class="text-right"><?=number_format($rows['hrg_beli'])?></td>
					<td style="border: solid; border-width: thin; padding-right: 2pt" class="text-right"><?=number_format($rows['hrg_jual'])?></td>
					<td style="border: solid; border-width: thin; padding-right: 2pt" class="text-right"><?=number_format($rows['diskon_item'])?></td>
					<td style="border: solid; border-width: thin; padding-right: 2pt" class="text-right"><?=number_format($rows['hrg_beli'] * $rows['jual'])?></td>
					<td style="border: solid; border-width: thin; padding-right: 2pt" class="text-right"><?=number_format($rows['hrg_jual'] * $rows['jual'] - $rows['diskon_item'])?></td>
				</tr>
			<?php
                $j_jual = $j_jual + $rows['hrg_jual'] * $rows['jual'] - $rows['diskon_item'];
				$jumlah = $jumlah + ($rows['hrg_beli'] * $rows['jual']);
				$sa = $sa + ($rows['stock_awal']+0); $sp = $sp + ($rows['stock_periode']+0); $st = $st + ($rows['stock_awal']+$rows['stock_periode']-$rows['jual']-$rows['retur']+0); $jl = $jl + ($rows['jual']+0); $rt = $rt + ($rows['retur']+0); $di = $di + $rows['diskon_item'];
			} ?>
			</tbody>
			<tfoot>
			<tr>
				<td style="border-top: solid; border-width: thin" colspan="5">TOTAL</td>
				<td style="border-top: solid; border-width: thin" class="text-center"><?=$sa?></td>
				<td style="border-top: solid; border-width: thin" class="text-center"><?=$sp?></td>
				<td style="border-top: solid; border-width: thin" class="text-center"><?=$st?></td>
				<td style="border-top: solid; border-width: thin" class="text-center"><?=$jl?></td>
				<td style="border-top: solid; border-width: thin" class="text-center"><?=$rt?></td>
				<td style="border-top: solid; border-width: thin"></td>
				<td style="border-top: solid; border-width: thin"></td>
				<td style="border-top: solid; border-width: thin"></td>
				<td style="border-top: solid; border-width: thin" class="text-right"><?=number_format($di)?></td>
				<td style="border-top: solid; border-width: thin" class="text-right"><?=number_format($jumlah)?></td>
				<td style="border-top: solid; border-width: thin" class="text-right"><?=number_format($j_jual)?></td>
			</tr>
			</tfoot>
		</table>

		<table width="100%" style="letter-spacing: 5px; font-family: 'Courier New';font-size: 9pt">
			<thead>
			<tr>
				<td style="border-top: solid; border-width: thin;" width="33%"></td>
				<td style="border-top: solid; border-width: thin;" width="33%"></td>
				<td style="border-top: solid; border-width: thin;" width="33%"></td>
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
	</div>
	<div id="<?=$i?>" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true" style="display: none;">
		<div class="modal-dialog modal-full">
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
					<h4 class="modal-title" id="myLargeModalLabel">Detail <?=$title?></h4>
				</div>
				<div class="modal-body">
					<div class="row">
						<div class="col-sm-8">
							<div class="col-sm-4"><b>Kode <?=$menu_group['as_group1']?></b></div><div class="col-sm-8"><b> : </b><b id="kd_supp<?=$i?>"><?=$row['Kode']?></b></div>
							<div class="col-sm-4"><b>Nama <?=$menu_group['as_group1']?></b></div><div class="col-sm-8"><b> : </b><b><?=$row['Nama']?></b></div>
						</div>
						<div class="col-sm-4">
							<div class="row">
								<b><?=$periode?><b>
							</div>
							<div class="row">
								<b><?=$q_lokasi?><b>
							</div>
						</div>
					</div>
					<hr/>
					<div class="pull-right" style="margin-bottom: 10px">
						<div class="row">
							<div class="col-md-5">
								<select class="form-control" id="filter<?=$i?>" onchange="cari(<?=$i?>, <?=$tgl?>)">
									<option value="br.Deskripsi"><?=$menu_group['as_deskripsi']?></option>
									<option value="br.kd_brg">Kode Barang</option>
									<option value="br.barcode">Barcode</option>
									<option value="br.nm_brg">Nama Barang</option>
								</select>
							</div>
							<div class="col-md-7">
								<input type="text" class="form-control" id="cari<?=$i?>" onkeyup="cari(<?=$i?>, '<?=$tgl?>')">
							</div>
						</div>
					</div>
					<div class="row">
						<div class="col-md-12 col-sm-12 col-xs-12">
							<table class="table table-striped table-bordered">
								<thead>
								<tr>
									<th>No</th>
									<th>Kode Barang</th>
									<th>Barcode</th>
									<th><?=$menu_group['as_deskripsi']?></th>
									<th>Nama Barang</th>
									<th>Stock Awal</th>
									<th>Stock Periode</th>
									<th>Stock Akhir</th>
									<th>Jual</th>
									<th>Retur</th>
									<th>Satuan</th>
									<th>Harga Beli</th>
									<th>Harga Jual</th>
									<th>Diskon Item</th>
									<th>Jumlah Beli</th>
									<th>Jumlah Jual</th>
								</tr>
								</thead>
								<tbody id="det<?=$i?>">
								<?php
								$no=0;
								foreach($detail as $rows){ $no++; ?>
									<tr>
										<td><?=$no?></td>
										<td><?=$rows['kd_brg']?></td>
										<td><?=$rows['barcode']?></td>
										<td><?=$rows['Deskripsi']?></td>
										<td><?=$rows['nm_brg']?></td>
										<td><?=($rows['stock_awal']+0)?></td>
										<td><?=($rows['stock_periode']+0)?></td>
										<td><?=($rows['stock_awal']+$rows['stock_periode']-$rows['jual']-$rows['retur']+0)?></td>
										<td><?=($rows['jual']+0)?></td>
										<td><?=($rows['retur']+0)?></td>
										<td><?=$rows['satuan']?></td>
										<td class="text-right"><?=number_format($rows['hrg_beli'])?></td>
										<td class="text-right"><?=number_format($rows['hrg_jual'])?></td>
										<td class="text-right"><?=number_format($rows['diskon_item'])?></td>
										<td class="text-right"><?=number_format($rows['hrg_beli'] * $rows['jual'])?></td>
										<td class="text-right"><?=number_format($rows['hrg_jual'] * $rows['jual'] - $rows['diskon_item'])?></td>
									</tr>
								<?php
								} ?>
								</tbody>
								<tfoot>
								<tr>
									<th colspan="5">TOTAL</th>
									<th><?=$sa?></th>
									<th><?=$sp?></th>
									<th><?=$st?></th>
									<th><?=$jl?></th>
									<th><?=$rt?></th>
									<th></th>
									<th></th>
									<th></th>
									<th class="text-right"><?=number_format($di)?></th>
									<th class="text-right"><?=number_format($jumlah)?></th>
									<th class="text-right"><?=number_format($j_jual)?></th>
								</tr>
								</tfoot>
							</table>
						</div>
					</div>
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-default waves-effect" data-dismiss="modal">Close</button>
					<button class="btn btn-primary waves-effect waves-light" onclick="to_pdf(<?=$i?>)"><i class="md md-print"></i> to PDF</button>
				</div>
			</div><!-- /.modal-content -->
		</div><!-- /.modal-dialog -->
	</div><!-- /.modal -->
<?php } ?>

<script>
	function to_pdf(id) {
		var value = $("#cari"+id).val();
		var filter = $("#filter"+id).val();
		var kode = document.getElementById('kd_supp'+id).innerHTML;

		var data_filter = [filter, value];

		window.open("<?=base_url().'konsinyasi/penjualan_konsinyasi/print/'?>" + btoa(kode) + '/' + btoa(JSON.stringify(data_filter)));
	}

	function cari(id, q_tgl) {
		var value = $("#cari"+id).val();
		var filter = $("#filter"+id).val();
		var kode = document.getElementById('kd_supp'+id).innerHTML;
		var lokasi = $("#lokasi").val();
        var tgl_awal = $("#tgl_awal").val();
        var tgl_akhir = $("#tgl_akhir").val();

		$.ajax({
			url: "<?php echo base_url().'konsinyasi/filter_barang' ?>",
			type: "POST",
			data: {value_:value, filter_:filter, kode_:kode, q_tgl_:q_tgl, lokasi_:lokasi, tgl_awal_:tgl_awal, tgl_akhir_:tgl_akhir},
			dataType: "JSON",
			success: function (res) {
				$("#det"+id).html(res.table);
				$("#jum"+id).text(res.jumlah)
			}
		});
	}

	function after_change(val) {
		$.ajax({
			url: "<?php echo base_url().'site/set_session_date/' ?>" + btoa('field-date') + '/' + btoa(val),
			type: "GET"
		});
	}
</script>

