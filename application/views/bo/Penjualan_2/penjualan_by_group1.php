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
								<div class="col-sm-3">
									<label>Periode</label>
									<?php $field = 'field-date';?>
									<div id="daterange" style="cursor: pointer;">
										<input type="text" name="<?=$field?>" id="<?=$field?>" class="form-control" style="height: 40px;" value="<?=isset($this->session->search[$field])?$this->session->search[$field]:(set_value($field)?set_value($field):date("Y/m/d")." - ".date("Y/m/d"))?>">
									</div>
								</div>
								<div class="col-sm-3">
									<div class="form-group">
										<label>Lokasi</label>
										<?php $field = 'lokasi';
										$option = null; $option['-'] = 'Semua Lokasi';
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
												<th>Net Sales</th>
												<th>Jumlah Beli</th>
												<th>Pilihan</th>
											</tr>
											</thead>
											<tbody>
											<?php $no = 0 + (($this->uri->segment(4)!=null)?(($this->uri->segment(4)-1) * 30):0);
											$qty = 0;
											$gs = 0;
											$di = 0;
											$ns = 0;
											$tb = 0;
											foreach($report as $row){ $no++; ?>
												<tr>
													<td><?=$no?></td>
													<td><?=$row['Kode']?></td>
													<td><?=$row['Nama']?></td>
													<td style="text-align: center;"><?=($row['qty']+0)?></td>
													<td class="text-right"><?=(number_format($row['gross_sales']+0))?></td>
													<td class="text-right"><?=(number_format($row['diskon_item']+0))?></td>
													<td class="text-right"><?=number_format(($row['gross_sales']+0) - ($row['diskon_item']+0))?></td>
													<td class="text-right"><?=(number_format(($row['total_beli']+0)))?></td>
													<td class="text-center">
														<div class="btn-group">
															<button type="button" class="btn btn-primary dropdown-toggle waves-effect waves-light" data-toggle="dropdown" aria-expanded="false">Pilihan <span class="caret"></span></button>
															<ul class="dropdown-menu" role="menu">
																<li><a href="#" onclick="detail_barang('<?=$row['Kode']?>')"><i class="md md-visibility"></i> Detail</a></li>
																<li><a href="<?=base_url().strtolower($this->control).'/'.$page.'/download/'.base64_encode($row['Kode'])?>"><i class="md md-get-app"></i> Download</a></li>
																<li><a href="#" onclick="to_pdf('<?=$row['Kode']?>')"><i class="md md-print"></i> to PDF</a></li>
																<li><a href="<?=base_url().strtolower($this->control).'/'.$page.'/print/'.base64_encode($row['Kode']).'/'.base64_encode('inventory')?>" target="_blank"><i class="md md-print"></i> Inventory</a></li>
																<li><a href="#" onclick="printDiv('print_by_supplier<?=$no?>')"><i class="md md-print"></i> Print</a></li>
																<!--<li class="divider"></li>-->
															</ul>
														</div>
													</td>
												</tr>
											<?php
												$qty = $qty + $row['qty'];
												$gs = $gs + $row['gross_sales'];
												$di = $di + $row['diskon_item'];
												$ns = $ns + ($row['gross_sales'] - $row['diskon_item']);
												$tb = $tb + $row['total_beli'];
											} ?>
											</tbody>
											<tfoot>
											<tr>
												<th colspan="3">TOTAL PER PAGE</th>
												<th style="text-align: center"><?=($qty+0)?></th>
												<th style="text-align: right"><?=number_format($gs, 2)?></th>
												<th style="text-align: right"><?=number_format($di, 2)?></th>
												<th style="text-align: right"><?=number_format($ns, 2)?></th>
												<th class="text-right"><?=number_format($tb, 2)?></th>
												<th></th>
											</tr>
											<tr>
												<th colspan="3">TOTAL</th>
												<th style="text-align: center"><?=($tqty+0)?></th>
												<th style="text-align: right"><?=number_format($tgs, 2)?></th>
												<th style="text-align: right"><?=number_format($tdi, 2)?></th>
												<th style="text-align: right"><?=number_format($tns, 2)?></th>
												<th class="text-right"><?=number_format($ttb, 2)?></th>
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
	<div id="print_by_supplier<?=$i?>" class="hidden">
		<table width="100%" cellspacing="0" cellpadding="1" style="letter-spacing: 5px; font-family: 'Courier New'; margin-bottom: 10px; font-size: 9pt">
			<thead>
			<tr>
				<td colspan="8" class="text-center">Laporan Penjualan By <?=$menu_group['as_group1']?></td>
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
				<td style="border-bottom: solid; border-width: thin; padding-left: 5pt">No</td>
				<td style="border-bottom: solid; border-width: thin; padding-left: 5pt">Kode Barang</td>
				<td style="border-bottom: solid; border-width: thin; padding-left: 5pt">Barcode</td>
				<td style="border-bottom: solid; border-width: thin; padding-left: 5pt"><?=$menu_group['as_deskripsi']?></td>
				<td style="border-bottom: solid; border-width: thin; padding-left: 5pt">Nama Barang</td>
				<td style="border-bottom: solid; border-width: thin; padding-left: 5pt">Stock Awal</td>
				<td style="border-bottom: solid; border-width: thin; padding-left: 5pt">Stock Periode</td>
				<td style="border-bottom: solid; border-width: thin; padding-left: 5pt">Stock Akhir</td>
				<td style="border-bottom: solid; border-width: thin; padding-left: 5pt">Jual</td>
				<td style="border-bottom: solid; border-width: thin; padding-left: 5pt">Retur</td>
				<td style="border-bottom: solid; border-width: thin; padding-left: 5pt">Satuan</td>
				<td style="border-bottom: solid; border-width: thin; padding-left: 5pt">Harga Beli</td>
				<!--<td style="border-bottom: solid; border-width: thin; padding-left: 5pt">Harga Jual</td>-->
				<td style="border-bottom: solid; border-width: thin; padding-left: 5pt">Diskon Item</td>
				<td style="border-bottom: solid; border-width: thin; padding-left: 5pt">Jumlah Beli</td>
				<!--<td style="border-bottom: solid; border-width: thin; padding-left: 5pt">Gross Sales</td>-->
			</tr>
			</thead>
			<tbody>
			<?php
			$no = 0;
			$jumlah = 0; $sa = 0; $sp = 0; $st = 0; $jl = 0; $rt = 0; $di = 0;
			/*$q_tgl = "AND mt.tgl >= '".$tgl_awal." 00:00:00' and mt.tgl <= '".$tgl_akhir." 23:59:59'";
			$where = ""; ($lokasi==null)?"":$where.=" AND mt.Lokasi='".$lokasi."'";
			$where_stock = "kd_brg=br.kd_brg AND lokasi NOT IN ('MUTASI', 'Retur') "; ($lokasi==null)?"":$where_stock.=" AND Lokasi='".$lokasi."'";
			$stock_awal = "isnull((select sum(saldo_awal + stock_in - stock_out) from kartu_stock where ".$where_stock." and tgl < '".$tgl_awal." 00:00:00'),0) as stock_awal";
			$stock_periode = "isnull((select sum(stock_in-stock_out) from kartu_stock where ".$where_stock." and tgl >= '".$tgl_awal." 00:00:00' and tgl <= '".$tgl_akhir." 23:59:59'),0) as stock_periode";
			$stock_periode2 = ", isnull((select sum(stock_in-stock_out) from kartu_stock where ".$where_stock." and tgl > '".$tgl_awal." 00:00:00'), 0) as stock_periode";
			$retur = "isnull((select sum(dtrx.qty) from Det_Trx dtrx, Master_Trx mtrx where dtrx.kd_brg=br.kd_brg and dtrx.qty < 0 and dtrx.kd_trx=mtrx.kd_trx and mtrx.tgl >= '".$tgl_awal." 00:00:00' and mtrx.tgl <= '".$tgl_akhir." 23:59:59'),0) as retur";
			$jual = "isnull((select sum(dtrx.qty) from Det_Trx dtrx, Master_Trx mtrx where dtrx.kd_brg=br.kd_brg and dtrx.qty > 0 and dtrx.kd_trx=mtrx.kd_trx and mtrx.tgl >= '".$tgl_awal." 00:00:00' and mtrx.tgl <= '".$tgl_akhir." 23:59:59'),0) as jual";
			$detail = $this->m_crud->read_data("Det_Trx dt, barang br, Master_Trx mt", "SUM(dt.dis_persen) diskon_item, br.hrg_beli, AVG(dt.hrg_jual) hrg_jual, br.kd_brg, br.Deskripsi, br.nm_brg, br.barcode, br.satuan, ".$stock_awal.",".$stock_periode2.",".$retur.",".$jual."", "mt.HR = 'S' AND dt.qty > 0 AND mt.kd_trx=dt.kd_trx AND dt.kd_brg=br.kd_brg AND br.Group1 = '".$row['Kode']."' ".$q_tgl." ".$where, null, "br.kd_brg, br.nm_brg, br.barcode, br.Deskripsi, br.satuan, br.hrg_beli");*/

            /*ini_set('max_execution_time', 3600);
            ini_set('memory_limit', '1000M');
            $q_tgl = " BETWEEN '".$tgl_awal." 00:00:00' AND '".$tgl_akhir." 23:59:59'";
            $where = "dp.kd_brg=br.kd_brg AND br.Group1 = '".$row['Kode']."' AND dp.tgl".$q_tgl;
            $where_stock = "";
            if($lokasi!=null) {
                $where .= " AND dp.Lokasi='".$lokasi."'";
                $where_stock = " AND stock_transaksi.lokasi='".$lokasi."'";
            }
            $stock_awal = " ,ISNULL((SELECT SUM(stock_in-stock_out) FROM stock_transaksi WHERE kd_brg=dp.kd_brg AND tgl<'".$tgl_awal." 00:00:00'".$where_stock.") ,0) stock_awal";
            $stock_masuk = " ,ISNULL((SELECT SUM(stock_in) FROM stock_transaksi WHERE kd_brg=dp.kd_brg AND tgl ".$q_tgl.$where_stock.") ,0) stock_masuk";
            $jumlah_retur = " ,ISNULL((SELECT SUM(stock_out) FROM stock_transaksi WHERE kd_brg=dp.kd_brg AND keterangan='Retur Pembelian' AND tgl ".$q_tgl.$where_stock."), 0) retur";
            $detail = $this->m_crud->read_data("detail_penjualan dp, barang br", "dp.hrg_beli, dp.kd_brg, br.barcode, br.Deskripsi, br.nm_brg, br.satuan, SUM(qty_jual) jual".$stock_awal.$stock_masuk.$jumlah_retur, $where, null, 'dp.hrg_beli, dp.kd_brg, br.barcode, br.Deskripsi, br.nm_brg, br.satuan');*/
			foreach($detail as $rows){ $no++; ?>
				<tr>
					<td><?=$no?></td>
					<td><?=$rows['kd_brg']?></td>
					<td><?=$rows['barcode']?></td>
					<td><?=$rows['Deskripsi']?></td>
					<td><?=$rows['nm_brg']?></td>
					<td><?=($rows['stock_awal']+0)?></td>
					<td><?=($rows['stock_masuk']+0)?></td>
					<td><?=($rows['stock_awal']+$rows['stock_masuk']-$rows['jual']-$rows['retur']+0)?></td>
					<td><?=($rows['jual']+0)?></td>
					<td><?=($rows['retur']+0)?></td>
					<td><?=$rows['satuan']?></td>
					<td class="text-right"><?=number_format($rows['hrg_beli'])?></td>
					<!--<td class="text-right"><?/*=number_format($rows['hrg_jual'])*/?></td>-->
					<!--<td class="text-right"><?=number_format($rows['diskon_item'])?></td>-->
					<td class="text-right"><?=number_format($rows['hrg_beli'] * $rows['jual'])?></td>
					<!--<td class="text-right"><?/*=number_format($rows['hrg_jual'] * $rows['jual'] - $rows['diskon_item'])*/?></td>-->
				</tr>
				<?php
				$jumlah = $jumlah + ($rows['hrg_beli'] * $rows['jual']);
				$sa = $sa + ($rows['stock_awal']+0); $sp = $sp + ($rows['stock_masuk']+0); $st = $st + ($rows['stock_awal']+$rows['stock_masuk']-$rows['jual']-$rows['retur']+0); $jl = $jl + ($rows['jual']+0); $rt = $rt + ($rows['retur']+0); $di = $di + $rows['diskon_item'];
			} ?>
			</tbody>
			<tfoot>
			<tr>
				<td style="border-top: solid; border-width: thin" colspan="5">TOTAL</td>
				<td style="border-top: solid; border-width: thin"><?=$sa?></td>
				<td style="border-top: solid; border-width: thin"><?=$sp?></td>
				<td style="border-top: solid; border-width: thin"><?=$st?></td>
				<td style="border-top: solid; border-width: thin"><?=$jl?></td>
				<td style="border-top: solid; border-width: thin"><?=$rt?></td>
				<td style="border-top: solid; border-width: thin"></td>
				<td style="border-top: solid; border-width: thin"></td>
				<!--<td style="border-top: solid; border-width: thin" class="text-right"><?/*=number_format($di)*/?></td>-->
				<td style="border-top: solid; border-width: thin" class="text-right"><?=number_format($jumlah)?></td>
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
<?php } ?>

<div id="detail_barang" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true" style="display: none;">
    <div class="modal-dialog modal-full">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                <h4 class="modal-title" id="myLargeModalLabel">Detail <?=$title?></h4>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-sm-8">
                        <div class="col-sm-4"><b>Kode <?=$menu_group['as_group1']?></b></div><div class="col-sm-8"><b> : </b><b id="kd_supp"></b></div>
                        <div class="col-sm-4"><b>Nama <?=$menu_group['as_group1']?></b></div><div class="col-sm-8"><b> : </b><b id="nm_supp"></b></div>
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
                            <select class="form-control" id="filter" onchange="cari(document.getElementById('kd_supp').innerHTML)">
                                <option value="br.Deskripsi"><?=$menu_group['as_deskripsi']?></option>
                                <option value="br.kd_brg">Kode Barang</option>
                                <option value="br.barcode">Barcode</option>
                                <option value="br.nm_brg">Nama Barang</option>
                            </select>
                        </div>
                        <div class="col-md-7">
                            <input type="text" class="form-control" id="cari" onkeyup="cari(document.getElementById('kd_supp').innerHTML)">
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
                                <th>Stock Masuk</th>
                                <th>Stock Akhir</th>
                                <th>Jual</th>
                                <th>Retur</th>
                                <th>Satuan</th>
                                <th>Harga Beli</th>
                                <th>Harga Jual</th>
                                <!--<th>Diskon Item</th>-->
                                <th>Jumlah Beli</th>
                                <th>Jumlah Jual</th>
                            </tr>
                            </thead>
                            <tbody id="det">
                            </tbody>
                            <tfoot id="foot">
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default waves-effect" data-dismiss="modal">Close</button>
                <!--<button class="btn btn-primary waves-effect waves-light" onclick="to_pdf(document.getElementById('kd_supp').innerHTML)"><i class="md md-print"></i> to PDF</button>-->
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->

<script>
	function to_pdf(kode) {
        var tgl = $("#field-date").val();
        var lokasi = $("#lokasi").val();

        window.open("<?=base_url().'penjualan/detil_penjualan_group1/to_pdf/'?>" + btoa(kode) + '/' + btoa(lokasi) + '/' + btoa(tgl));
	}

	function detail_barang(kode) {
        var lokasi = $("#lokasi").val();
        var tgl = $("#field-date").val();

        $.ajax({
            url: "<?php echo base_url() . 'penjualan/detil_penjualan_group1' ?>",
            data: {lokasi_:lokasi, tgl_periode_: tgl, kode_:kode},
            type: "POST",
            dataType: "JSON",
            beforeSend: function () {
                $('#loading').show();
            },
            complete: function () {
                $("#loading").hide();
            },
            success: function (res) {
                $("#det").html(res.list_barang);
                $("#foot").html(res.foot);
                $("#kd_supp").text(res.kd);
                $("#nm_supp").text(res.nm);
                $("#modal-title").text("Detail Barang");
                $("#modal-tanggal").text(tgl);
                $("#modal-lokasi").text(lokasi);
                $("#detail_barang").modal("show");
            }
        });
    }

	function cari(kode) {
        var value = $("#cari").val();
        var filter = $("#filter").val();
        var lokasi = $("#lokasi").val();
        var tgl = $("#field-date").val();

        $.ajax({
            url: "<?php echo base_url() . 'penjualan/detil_penjualan_group1' ?>",
            data: {lokasi_:lokasi, tgl_periode_: tgl, value_:value, filter_:filter, kode_:kode},
            type: "POST",
            dataType: "JSON",
            success: function (res) {
                $("#det").html(res.list_barang);
                $("#foot").html(res.foot);
                $("#kd_supp").text(res.kd);
                $("#nm_supp").text(res.nm);
                $("#modal-title").text("Detail Barang");
                $("#modal-tanggal").text(tgl);
                $("#modal-lokasi").text(lokasi);
                $("#detail_barang").modal("show");
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

