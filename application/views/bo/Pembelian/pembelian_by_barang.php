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
										<label>Lokasi</label>
										<?php $field = 'lokasi';
										$option = null; $option[''] = 'Semua Lokasi';
										//$option['all'] = 'All';
										$data_option = $this->m_crud->read_data('Lokasi', 'Kode, nama_toko Nama', $this->where_lokasi, 'Nama asc');
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
											<th>Kode Barang</th>
											<th>Barcode</th>
											<th>Nama Barang</th>
											<th>Qty Beli</th>
											<th>Satuan</th>
											<th>Nilai Pembelian</th>
											<th>Harga Rata-rata</th>
											<th>Pilihan</th>
										</tr>
										</thead>
										<tbody>
										<?php $no = 0 + (($this->uri->segment(4)!=null)?(($this->uri->segment(4)-1) * 15):0);
                                        $tq = 0;
                                        $tj = 0;
                                        foreach($report as $row){
										    $no++;
                                            /*$get_detail = $this->m_crud->read_data("det_beli", "harga_beli, jumlah_beli, jumlah_retur, diskon disc1, disc2, PPN ppn", "left(CONVERT(varchar, tgl_beli, 120), 10) between '".$tgl_awal."' and '".$tgl_akhir."' and kode_barang='".$row['kd_brg']."'");
                                            foreach ($get_detail as $row_detail) {
                                                $hitung_netto = ((int)$row_detail['jumlah_beli']-(int)$row_detail['jumlah_retur']) * $row_detail['harga_beli'];
                                                $disc = $this->m_website->double_diskon($hitung_netto, array($row_detail['disc1'], $row_detail['disc2']));
                                                $hitung_sub_total = $this->m_website->grand_total_ppn($disc, 0, $row_detail['ppn']);
                                                $sub_total = $sub_total + $hitung_sub_total;
                                            }
                                            $tp = $tp + $sub_total;*/

                                            /*$get_detail = $this->m_crud->read_data("pembelian_report byb", "byb.kd_brg, byb.nm_brg, byb.barcode, byb.satuan, byb.sub_total, byb.disc, byb.ppn, byb.jumlah_beli, (select sum(pbb.sub_total) from pembelian_by_barang pbb where pbb.no_faktur_beli = byb.no_faktur_beli) tot_trx", "byb.kd_brg='".$row['kd_brg']."' AND left(CONVERT(varchar, byb.tgl_beli, 120), 10) between '".$tgl_awal."' and '".$tgl_akhir."'");
                                            foreach ($get_detail as $row_detail) {
                                                $diskon = $row_detail['sub_total']*($row_detail['disc']/$row_detail['tot_trx']);
                                                $ppn = ($row_detail['sub_total']-$diskon)*($row_detail['ppn']/$row_detail['tot_trx']);
                                                $sub_total = $sub_total + ($row_detail['sub_total']-$diskon+$ppn);
                                                $qty_jual = $qty_jual + $row_detail['jumlah_beli'];
                                            }*/

                                            $tq = $tq + (float)$row['jumlah_beli'];
                                            $tj = $tj + (float)$row['total_beli'];
										    ?>
											<tr>
												<td><?=$no?></td>
												<td><?=$row['kd_brg']?></td>
												<td><?=$row['barcode']?></td>
												<td><?=$row['nm_brg']?></td>
												<td><?=(float)$row['jumlah_beli']?></td>
												<td><?=$row['satuan']?></td>
												<td class="text-right"><?=number_format((float)$row['total_beli'], 2)?></td>
												<td class="text-right"><?=number_format((float)$row['total_beli']/(float)$row['jumlah_beli'], 2)?></td>
												<td class="text-center">
													<div class="btn-group">
														<button type="button" class="btn btn-primary dropdown-toggle waves-effect waves-light" data-toggle="dropdown" aria-expanded="false">Pilihan <span class="caret"></span></button>
														<ul class="dropdown-menu" role="menu">
															<li><a href="#" data-toggle="modal" data-target="#<?=$no?>"><i class="md md-visibility"></i> Detail</a></li>
															<li><a href="<?=base_url().strtolower($this->control).'/'.$page.'/download/'.base64_encode($row['kd_brg'])?>"><i class="md md-get-app"></i> Download</a></li>
															<li><a href="<?=base_url().strtolower($this->control).'/'.$page.'/print/'.base64_encode($row['kd_brg'])?>" target="_blank"><i class="md md-print"></i> to PDF</a></li>
															<li><a href="#" onclick="printDiv('print_pembelian_by_barang<?=$no?>')"><i class="md md-print"></i> Print</a></li>
															<!--<li><a href="<?/*=base_url().'cetak/nota_pembelian/'.base64_encode($row['kd_brg'])*/?>" target="_blank"><i class="md md-print"></i> Nota</a></li>-->

															<!--<li class="divider"></li>-->
														</ul>
													</div>
												</td>
											</tr>
										<?php
										$qt = $qt + (float)$row['qty_beli'];
										} ?>
										</tbody>
										<tfoot>
										<tr>
											<th colspan="4">TOTAL PER PAGE</th>
											<th><?=$tq?></th>
											<th></th>
											<th class="text-right"><?=number_format($tj, 2)?></th>
											<th></th>
											<th></th>
										</tr>
                                        <?php
                                        /*$ttq = 0;
                                        $ttj = 0;
                                        foreach ($detail as $row) {
                                            $qty_jual = 0;
                                            $sub_total = 0;
                                            $get_detail = $this->m_crud->read_data("pembelian_by_barang byb", "byb.kd_brg, byb.nm_brg, byb.barcode, byb.satuan, byb.sub_total, byb.disc, byb.ppn, byb.jumlah_beli, (select sum(pbb.sub_total) from pembelian_by_barang pbb where pbb.no_faktur_beli = byb.no_faktur_beli) tot_trx", "byb.kd_brg='".$row['kd_brg']."' AND left(CONVERT(varchar, byb.tgl_beli, 120), 10) between '".$tgl_awal."' and '".$tgl_akhir."'");
                                            foreach ($get_detail as $row_detail) {
                                                $diskon = $row_detail['sub_total']*($row_detail['disc']/$row_detail['tot_trx']);
                                                $ppn = ($row_detail['sub_total']-$diskon)*($row_detail['ppn']/$row_detail['tot_trx']);
                                                $sub_total = $sub_total + ($row_detail['sub_total']-$diskon+$ppn);
                                                $qty_jual = $qty_jual + $row_detail['jumlah_beli'];
                                            }
                                            $ttq = $ttq + $qty_jual;
                                            $ttj = $ttj + $sub_total;
                                        }*/
                                        ?>
										<tr>
											<th colspan="4">TOTAL</th>
											<th><?=(float)$detail['qty_beli']?></th>
											<th></th>
											<th class="text-right"><?=number_format((float)$detail['total_beli'], 2)?></th>
											<th></th>
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

<?php $i = 0 + (($this->uri->segment(4)!=null)?(($this->uri->segment(4)-1) * 15):0); foreach($report as $row){ $i++; ?>
	<div id="<?=$i?>" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true" style="display: none;">
		<div class="modal-dialog modal-full">
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
					<h4 class="modal-title" id="myLargeModalLabel">Detail <?=$title?> / <?=$row['kd_brg']?></h4>
				</div>
				<div class="modal-body">
					<div class="row">
						<div class="col-sm-6">
							<div class="col-sm-4"><b>Kode Barang</b></div><div class="col-sm-8"><b> : </b><?=$row['kd_brg']?></div>
							<div class="col-sm-4"><b>Barcode</b></div><div class="col-sm-8"><b> : </b><?=$row['barcode']?></div>
							<div class="col-sm-4"><b>Nama Barang</b></div><div class="col-sm-8"><b> : </b><?=$row['nm_brg']?></div>
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
									<th>Faktur Beli</th>
									<th>Supplier</th>
									<th>Tanggal</th>
									<th>Qty</th>
									<th>Satuan</th>
									<th>Harga Beli</th>
									<th>Disc 1</th>
									<th>Disc 2</th>
									<th>PPN</th>
                                    <th>Disc Trx</th>
                                    <th>PPN Trx</th>
									<th>Sub Total</th>
								</tr>
								</thead>
								<tbody>
								<?php
								$no = 0; $qt = 0; $d1 = 0; $d2 = 0; $d3 = 0; $d4 = 0; $ppn = 0; $st = 0; $dit= 0; $ppt = 0;
								//And det_beli.tgl_Beli between '".$tgl_awal."' and '".$tgl_ahir."' and master_beli.lokasi='".$lokasi."';
								$condition = " AND LEFT(CONVERT(varchar, pbb.tgl_beli, 120), 10) BETWEEN '".$tgl_awal."' AND '".$tgl_akhir."'";
								$condition2 = ($lokasi=='')?"":" AND pbb.lokasi='".$lokasi."' ";
								$detail = $this->m_crud->read_data("pembelian_by_barang pbb, det_beli db, Supplier sp", "pbb.no_faktur_beli, pbb.tgl_beli, sp.nama, pbb.jumlah_beli qty, pbb.satuan, db.harga_beli, db.diskon disc1, db.disc2, db.ppn, pbb.sub_total, pbb.disc disct, pbb.ppn ppnt", "pbb.kd_brg = '".$row['kd_brg']."' AND pbb.kd_brg=db.kode_barang AND pbb.kode_supplier=sp.kode AND pbb.no_faktur_beli=db.no_faktur_beli".$condition.$condition2, "pbb.no_faktur_beli asc");
								//$detail = $this->db->query("SELECT det_beli.No_Faktur_Beli,supplier.nama,det_beli.Tgl_Beli, det_beli.Kode_Barang, Barang.Nm_brg, (det_beli.Jumlah_Beli-det_beli.Jumlah_Retur) qty, Barang.Satuan, det_beli.Harga_Beli,det_beli.Diskon,det_beli.Disc2,det_beli.Disc3,det_beli.Disc4,det_beli.PPN,(det_beli.Jumlah_Beli-det_beli.Jumlah_Retur)*(det_beli.Harga_beli*(1-isnull(det_beli.Diskon, 0)/100)*(1-isnull(det_beli.Disc2, 0)/100)*(1-isnull(det_beli.Disc3, 0)/100)*(1-isnull(det_beli.Disc4, 0)/100)*(1+isnull(det_beli.PPN, 0)/100)) sub_total  FROM Barang INNER JOIN det_beli ON Barang.Kd_brg = det_beli.Kode_Barang INNER JOIN master_beli on det_beli.no_faktur_beli=master_beli.no_faktur_beli INNER JOIN supplier on master_beli.kode_supplier=supplier.kode where Barang.Kd_brg='".$row['kd_brg']."' ".$condition.$condition2." and left(det_beli.No_Faktur_Beli,2)<>'NK' order By det_beli.Tgl_beli")->result_array();
								foreach($detail as $rows){
									$no++;
									?>
									<tr>
										<td><?=$no?></td>
										<td><?=$rows['no_faktur_beli']?></td>
										<td><?=$rows['nama']?></td>
										<td><?=substr($rows['tgl_beli'], 0, 10)?></td>
										<td><?=($rows['qty']+0)?></td>
										<td><?=$rows['satuan']?></td>
										<td style="text-align:right;"><?=number_format($rows['harga_beli'])?></td>
										<td><?=($rows['disc1']+0)?></td>
										<td><?=($rows['disc2']+0)?></td>
										<td><?=($rows['ppn']+0)?></td>
                                        <td style="text-align:right;"><?=number_format($rows['disct'], 2)?></td>
                                        <td style="text-align:right;"><?=number_format($rows['ppnt'], 2)?></td>
										<td style="text-align:right;"><?=number_format($rows['sub_total']-$rows['disct']+$rows['ppnt'], 2)?></td>
									</tr>
								<?php
								$qt = $qt + (int)$rows['qty'];
								$st = $st + (float)$rows['sub_total']-$rows['disct']+$rows['ppnt'];
								$d1 = $d1 + ($rows['disc1']+0);
								$d2 = $d2 + ($rows['disc2']+0);
								$dit = $dit + ($rows['disct']+0);
								$ppt = $ppt + ($rows['ppnt']+0);
								$ppn = $ppn + ($rows['ppn1']+0);
								} ?>
								</tbody>
								<tfoot>
								<tr>
									<th colspan="4">TOTAL</th>
									<th><?=$qt?></th>
									<th></th>
									<th></th>
									<th><?=$d1?></th>
									<th><?=$d2?></th>
									<th><?=$ppn?></th>
                                    <th class="text-right"><?=number_format($dit, 2)?></th>
                                    <th class="text-right"><?=number_format($ppt, 2)?></th>
									<th class="text-right"><?=number_format($st, 2)?></th>
								</tr>
								</tfoot>
							</table>
						</div>
					</div>
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-default waves-effect" data-dismiss="modal">Close</button>
					<a href="<?=base_url().strtolower($this->control).'/'.$page.'/print/'.base64_encode($row['kd_brg'])?>" target="_blank"><button type="button" class="btn btn-primary waves-effect waves-light"><i class="md md-print"></i> to PDF</button></a>
				</div>
			</div><!-- /.modal-content -->
		</div><!-- /.modal-dialog -->
	</div><!-- /.modal -->

	<div id="print_pembelian_by_barang<?=$i?>" class="hidden">
		<table width="100%" cellspacing="0" cellpadding="1" style="letter-spacing: 5px; font-family: 'Courier New'; margin-bottom: 10px; font-size: 9pt">
			<thead>
			<tr>
				<td colspan="8" class="text-center">Laporan Pembelian By Barang</td>
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
				<td style="font-size: 10pt !important">Kode Barang</td>
				<td style="font-size: 10pt !important">:</td>
				<td style="font-size: 10pt !important"><?=$row['kd_brg']?></td>

				<td></td>
				<td style="font-size: 10pt !important">Nama Barang</td>
				<td style="font-size: 10pt !important">:</td>
				<td style="font-size: 10pt !important"><?=$row['nm_brg']?></td>
			</tr>
			<tr>
				<td></td>
				<td style="font-size: 10pt !important">Barcode</td>
				<td style="font-size: 10pt !important">:</td>
				<td style="font-size: 10pt !important"><?=$row['barcode']?></td>

				<td></td>
				<td></td>
				<td></td>
				<td></td>
			</tr>
			</tbody>
		</table>

		<table width="100%" style="letter-spacing: 5px; font-family: 'Courier New'; font-size:9pt;">
			<thead>
			<tr>
				<td style="border-bottom: solid; border-width: thin; padding-left: 5pt; font-size: 10pt !important">No</td>
				<td style="border-bottom: solid; border-width: thin; padding-left: 5pt; font-size: 10pt !important">Faktur Beli</td>
				<td style="border-bottom: solid; border-width: thin; padding-left: 5pt; font-size: 10pt !important">Supplier</td>
				<td style="border-bottom: solid; border-width: thin; padding-left: 5pt; font-size: 10pt !important">Tanggal</td>
				<td style="border-bottom: solid; border-width: thin; padding-left: 5pt; font-size: 10pt !important">Qty</td>
				<td style="border-bottom: solid; border-width: thin; padding-left: 5pt; font-size: 10pt !important">Satuan</td>
				<td style="border-bottom: solid; border-width: thin; padding-left: 5pt; font-size: 10pt !important">Harga Beli</td>
				<td style="border-bottom: solid; border-width: thin; padding-left: 5pt; font-size: 10pt !important">Disc 1</td>
				<td style="border-bottom: solid; border-width: thin; padding-left: 5pt; font-size: 10pt !important">Disc 2</td>
				<td style="border-bottom: solid; border-width: thin; padding-left: 5pt; font-size: 10pt !important">PPN</td>
                <td style="border-bottom: solid; border-width: thin; padding-left: 5pt; font-size: 10pt !important">Disc Trx</td>
                <td style="border-bottom: solid; border-width: thin; padding-left: 5pt; font-size: 10pt !important">PPN Trx</td>
                <td style="border-bottom: solid; border-width: thin; padding-left: 5pt; font-size: 10pt !important">Sub Total</td>
			</tr>
			</thead>
			<tbody>
			<?php
			$no = 0;
			foreach($detail as $rows) {
				$no++;
				?>
				<tr>
                    <td><?=$no?></td>
                    <td><?=$rows['no_faktur_beli']?></td>
                    <td><?=$rows['nama']?></td>
                    <td><?=substr($rows['tgl_beli'], 0, 10)?></td>
                    <td><?=($rows['qty']+0)?></td>
                    <td><?=$rows['satuan']?></td>
                    <td style="text-align:right;"><?=number_format($rows['harga_beli'])?></td>
                    <td><?=($rows['disc1']+0)?></td>
                    <td><?=($rows['disc2']+0)?></td>
                    <td><?=($rows['ppn']+0)?></td>
                    <td style="text-align:right;"><?=number_format($rows['disct'], 2)?></td>
                    <td style="text-align:right;"><?=number_format($rows['ppnt'], 2)?></td>
                    <td style="text-align:right;"><?=number_format($rows['sub_total']-$rows['disct']+$rows['ppnt'], 2)?></td>
				</tr>
				<?php
			} ?>
			</tbody>
			<tfoot>
			<tr>
				<td style="border-top: solid; border-width: thin" colspan="4">TOTAL</td>
				<td style="border-top: solid; border-width: thin"><?=$qt?></td>
				<td style="border-top: solid; border-width: thin"></td>
				<td style="border-top: solid; border-width: thin"></td>
                <th><?=$d1?></th>
                <th><?=$d2?></th>
                <th><?=$ppn?></th>
                <th class="text-right"><?=number_format($dit, 2)?></th>
                <th class="text-right"><?=number_format($ppt, 2)?></th>
                <th class="text-right"><?=number_format($st, 2)?></th>
			</tr>
			</tfoot>
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
	</div>
<?php } ?>

<script>
	function after_change(val) {
		$.ajax({
			url: "<?php echo base_url().'site/set_session_date/' ?>" + btoa('field-date') + '/' + btoa(val),
			type: "GET"
		});
	}
</script>