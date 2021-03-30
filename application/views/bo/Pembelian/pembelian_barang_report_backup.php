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
                                        <label>Sort</label>
                                        <div class="input-group">
                                            <div class="input-group-btn">
                                                <?php $field = 'order_by';
                                                $option = null;$option = null;
                                                $option['pr.no_faktur_beli'] = 'No. Transaksi';
                                                $option['pr.tgl_beli'] = 'Tanggal';
                                                $option['pr.noNota'] = 'Nota Supplier';
                                                $option['sp.nama'] = 'Supplier';
                                                $option['pr.Operator'] = 'Operator';
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
							</div>
							<div class="row">
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
										<label>Tipe Transaksi</label>
										<?php $field = 'tipe';
										$option = null; $option[''] = 'Semua Tipe';
										$option['Tunai'] = 'Tunai';
										$option['Kredit'] = 'Kredit';
										$option['Konsinyasi'] = 'Konsinyasi';
										echo form_dropdown($field, $option, isset($this->session->search[$field])?$this->session->search[$field]:set_value($field), array('class' => 'select2', 'id'=>$field));
										?>
									</div>
								</div>
								<div class="col-sm-3">
									<div class="form-group">
										<label>Search</label>
										<?php $field = 'any'; ?>
										<input class="form-control" type="text" id="<?=$field?>" name="<?=$field?>" placeholder="Pembelian/No Nota/Supplier/Operator" value="<?=isset($this->session->search[$field])?$this->session->search[$field]:set_value($field)?>" autofocus />
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
												<th>No. Transaksi</th>
												<th>Nota Supplier</th>
												<th>Type</th>
												<th>Pelunasan</th>
												<th>Supplier</th>
												<th>Lokasi</th>
												<th>Operator</th>
												<th>Penerima</th>
												<th>Diskon</th>
												<th style="width: 20px !important;">PPN</th>
												<th>Total Pembelian</th>
											</tr>
											</thead>
											<tbody>
											<?php $no = 0 + (($this->uri->segment(4)!=null)?(($this->uri->segment(4)-1) * 15):0); $tp = 0;
											foreach($report as $row){
											    $no++;
											    /*$sub_total = 0;
                                                $get_detail = $this->m_crud->read_data("det_beli", "harga_beli, jumlah_beli, jumlah_retur, diskon disc1, disc2, PPN ppn", "no_faktur_beli='".$row['no_faktur_beli']."'");
                                                foreach ($get_detail as $row_detail) {
                                                    $hitung_netto = ((float)$row_detail['jumlah_beli']) * $row_detail['harga_beli'];
                                                    $disc = $this->m_website->double_diskon($hitung_netto, array($row_detail['disc1'], $row_detail['disc2']));
                                                    $hitung_sub_total = $this->m_website->grand_total_ppn($disc, 0, $row_detail['ppn']);
                                                    $sub_total = $sub_total + $hitung_sub_total;
                                                }
                                                $sub_total = $sub_total-$row['disc']+$row['ppn'];
                                                $tp = $tp + $sub_total;*/
											    ?>
												<tr>
													<td><?=$no?></td>
													<td class="text-center">
														<div class="btn-group">
															<button type="button" class="btn btn-primary dropdown-toggle waves-effect waves-light" data-toggle="dropdown" aria-expanded="false">Pilihan <span class="caret"></span></button>
															<ul class="dropdown-menu" role="menu">
																<li><a href="#" data-toggle="modal" data-target="#<?=$no?>"><i class="md md-visibility"></i> Detail</a></li>
																<!--<li><a href="<?/*=base_url().strtolower($this->control).'/'.$page.'/download/'.base64_encode($row['no_faktur_beli'])*/?>"><i class="md md-get-app"></i> Download</a></li>
																<li><a href="<?/*=base_url().strtolower($this->control).'/'.$page.'/print/'.base64_encode($row['no_faktur_beli'])*/?>" target="_blank"><i class="md md-print"></i> to PDF</a></li>-->
																<li><a href="#" onclick="edit_otorisasi({param:'href_target_blank', kode:'<?=base64_encode($row['no_faktur_beli'])?>', activity:'Edit Pembelian'}, edit_trx)"><i class="md md-edit"></i> Edit</a></li>
																<li><a href="#" id="delete" onclick="delete_trans('<?=$row['no_faktur_beli']?>')"><i class="md md-close"></i> Delete</a></li>
																<li><a href="#" onclick="re_print('<?=$row['no_faktur_beli']?>', pdf)"><i class="md md-print"></i> to PDF</a></li>
																<li><a href="#" onclick="re_print('<?=$row['no_faktur_beli']?>', cetak, 'print_pembelian')"><i class="md md-print"></i> Print</a></li>
																<li><a href="<?=base_url().'cetak/form_alokasi/'.base64_encode($row['no_faktur_beli'])?>" target="_blank"><i class="md md-print"></i> Form Alokasi</a></li>
                                                                <?php if($row['alokasi']>0){ ?><li><a href="<?=base_url().'cetak/list_alokasi/'.base64_encode($row['no_faktur_beli'])?>" target="_blank"><i class="md md-print"></i> List Alokasi</a></li><?php } ?>
																<li><a href="<?=base_url().'cetak/barcode_barang/'.base64_encode($row['no_faktur_beli']).'/'.base64_encode('pembelian')?>" target="_blank"><i class="md md-print"></i> Barcode Barang</a></li>
																<li><a href="#" data-toggle="modal" onclick="trx_number(<?=$no?>, '<?=$row['serial']?>')" data-target="#return<?=$no?>"><i class="md md-cloud-upload"></i> Return</a></li>
																<?php if($row['type']=='Kredit' && $row['Pelunasan'] == 'Belum Lunas'){ ?>
																	<li><a href="<?=base_url().'pembelian/bayar_hutang/bayar_nota_beli/'.base64_encode($row['no_faktur_beli'])?>" target="_blank"><i class="md md-payment"></i> Bayar Hutang fdd</a></li>
																<?php } ?>
																<!--<li class="divider"></li>-->
															</ul>
														</div>
													</td>
													<td><?=substr($row['tgl_beli'],0,10)?></td>
													<td><?=$row['no_faktur_beli']?></td>
													<td><?=$row['noNota']?></td>
													<td><?=$row['type']?></td>
													<td><?=$row['Pelunasan']?></td>
													<td><?=$row['supplier']?></td>
													<td><?=$row['lokasi']?></td>
													<td><?=$row['operator']?></td>
													<td><?=$row['nama_penerima']?></td>
													<td class="text-right"><?=number_format($row['disc'], 2)?></td>
													<td class="text-right"><?=number_format($row['ppn'], 2)?></td>
													<td class="text-right"><?=number_format((float)$row['total_beli'], 2)?></td>
												</tr>
												<?php
                                                $tp = $tp + (float)$row['total_beli'];
											} ?>
											</tbody>
											<tfoot>
											<tr>
												<th colspan="13">TOTAL PER PAGE</th>
												<th class="text-right"><?=number_format($tp, 2)?></th>
											</tr>
											<tr>
												<th colspan="13">TOTAL</th>
												<th class="text-right"><?=number_format((float)$detail['total_beli'], 2)?></th>
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
<?php $i = 0 + (($this->uri->segment(4)!=null)?(($this->uri->segment(4)-1) * 15):0); foreach($report as $row){ $i++; ?>
	<div id="print_pembelian<?=$row['no_faktur_beli']?>" class="hidden">
        <img style="float: right; margin-top: -10px" src="<?=base_url().'barcode.php?size=30&sizefactor=2&text='.$row['no_faktur_beli']?>">
		<table width="100%" cellspacing="0" cellpadding="1" style="letter-spacing: 5px; font-family: 'Courier New'; margin-bottom: 10px; font-size: 9pt">
			<thead>
			<tr>
				<td colspan="8" style="text-align: center">Laporan Arsip Pembelian (<?=$row['no_faktur_beli']?>)</td>
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
				<td style="font-size: 10pt !important"><?=substr($row['tgl_beli'],0,10)?></td>

				<td></td>
				<td style="font-size: 10pt !important">Operator</td>
				<td style="font-size: 10pt !important">:</td>
				<td style="font-size: 10pt !important"><?=$row['operator']?></td>
			</tr>
			<tr>
				<td></td>
				<td style="font-size: 10pt !important">Pelunasan</td>
				<td style="font-size: 10pt !important">:</td>
				<td style="font-size: 10pt !important"><?=$row['Pelunasan']?></td>

				<td></td>
				<td style="font-size: 10pt !important">Penerima</td>
				<td style="font-size: 10pt !important">:</td>
				<td style="font-size: 10pt !important"><?=$row['nama_penerima']?></td>
			</tr>
			<tr>
				<td></td>
				<td style="font-size: 10pt !important">Lokasi</td>
				<td style="font-size: 10pt !important">:</td>
				<td style="font-size: 10pt !important"><?=$row['lokasi']?></td>

				<td></td>
				<td style="font-size: 10pt !important"></td>
				<td style="font-size: 10pt !important"></td>
				<td style="font-size: 10pt !important"></td>
			</tr>
			</tbody>
		</table>

		<table width="100%" style="letter-spacing: 5px; font-family: 'Courier New'; font-size:9pt;">
			<thead>
			<tr>
				<td style="border-bottom: solid; border-width: thin; padding-left: 5pt; font-size: 10pt !important">No</td>
				<td style="border-bottom: solid; border-width: thin; padding-left: 5pt; font-size: 10pt !important">Kode Barang</td>
				<td style="border-bottom: solid; border-width: thin; padding-left: 5pt; font-size: 10pt !important">Nama Barang</td>
				<td style="border-bottom: solid; border-width: thin; padding-left: 5pt; font-size: 10pt !important">Harga Beli</td>
				<td style="border-bottom: solid; border-width: thin; padding-left: 5pt; font-size: 10pt !important">Harga Jual</td>
				<td style="border-bottom: solid; border-width: thin; padding-left: 5pt; font-size: 10pt !important">Margin</td>
				<td style="border-bottom: solid; border-width: thin; padding-left: 5pt; font-size: 10pt !important">Diskon 1 %</td>
				<td style="border-bottom: solid; border-width: thin; padding-left: 5pt; font-size: 10pt !important">Diskon 2 %</td>
				<td style="border-bottom: solid; border-width: thin; padding-left: 5pt; font-size: 10pt !important">Qty Beli</td>
				<!--<td style="border-bottom: solid; border-width: thin; padding-left: 5pt; font-size: 10pt !important">Qty Retur</td>-->
                <td style="border-bottom: solid; border-width: thin; padding-left: 5pt; font-size: 10pt !important">Qty Bonus</td>
				<td style="border-bottom: solid; border-width: thin; padding-left: 5pt; font-size: 10pt !important">Ppn</td>
				<td style="border-bottom: solid; border-width: thin; padding-left: 5pt; font-size: 10pt !important">Sub Total</td>
			</tr>
			</thead>
			<tbody>
			<?php
			$no = 0;
			$sub_total = 0;
			$detail = $this->db->query("SELECT isnull(br.hrg_jual_1 ,0) harga_jual, db.kode_barang, br.nm_brg, db.jumlah_beli, isnull(db.jumlah_bonus, 0) jumlah_bonus, db.harga_beli, db.ppn ppn_item, db.diskon disc1, db.disc2, db.disc3, db.disc4, br.satuan, isnull(dr.jml,0) jumlah_retur
										FROM master_beli mb
										LEFT JOIN det_beli db ON db.no_faktur_beli=mb.no_faktur_beli 
										LEFT JOIN barang br ON db.kode_barang = br.kd_brg
										LEFT JOIN Master_Retur_Beli mr ON db.no_faktur_beli=mr.no_beli
										LEFT JOIN Det_Retur_Beli dr ON dr.No_Retur=mr.No_Retur AND dr.kd_brg=db.kode_barang
										WHERE mb.no_faktur_beli = '".$row['no_faktur_beli']."'")->result_array();
			$qt = 0;
			$qr = 0;
			$qb = 0;
			$st = 0;
			foreach($detail as $rows){
				$no++;
				$hitung_netto = ((float)$rows['jumlah_beli']) * (float)$rows['harga_beli'];
				$diskon = $this->m_website->double_diskon($hitung_netto, array($rows['disc1'], $rows['disc2']));
				$hitung_sub_total = $this->m_website->grand_total_ppn($diskon, 0, $rows['ppn_item']);
				$sub_total = $sub_total + $hitung_sub_total;
                $d1 = $rows['harga_beli']*(1-($rows['disc1']/100));
                $hrg_beli = $d1*(1-($rows['disc2']/100));
				?>
				<tr>
					<td><?=$no?></td>
					<td><?=$rows['kode_barang']?></td>
					<td><?=$rows['nm_brg']?></td>
					<td class="text-right"><?=number_format($rows['harga_beli'], 2)?></td>
					<td class="text-right"><?=number_format($rows['harga_jual'], 2)?></td>
					<td class="text-center"><?=(($rows['harga_jual']!=0)?round((1 - ($hrg_beli/$rows['harga_jual']))*100, 2):'0')?> %</td>
					<td style="text-align: center"><?=($rows['disc1']+0)?></td>
					<td style="text-align: center"><?=($rows['disc2']+0)?></td>
					<td><?=(float)$rows['jumlah_beli'].' '.$rows['satuan']?></td>
					<!--<td><?/*=(int)$rows['jumlah_retur'].' '.$rows['satuan']*/?></td>-->
					<td><?=(int)$rows['jumlah_bonus'].' '.$rows['satuan']?></td>
					<td><?=($rows['ppn_item']+0)?></td>
					<td class="text-right"><?=number_format($hitung_sub_total, 2)?></td>
				</tr>
			<?php
				$qt = $qt + (float)$rows['jumlah_beli'];
				/*$qr = $qr + (float)$rows['jumlah_retur'];*/
				$qb = $qb + (int)$rows['jumlah_bonus'];
				$st = $st + $hitung_sub_total;
			} ?>
			</tbody>
			<tfoot>
			<tr>
				<td style="border-top: solid; border-width: thin" colspan="8">TOTAL</td>
				<td style="border-top: solid; border-width: thin"><?=$qt?></td>
				<!--<td style="border-top: solid; border-width: thin"><?/*=$qr*/?></td>-->
				<td style="border-top: solid; border-width: thin"><?=$qb?></td>
				<td style="border-top: solid; border-width: thin"></td>
				<td style="border-top: solid; border-width: thin" class="text-right"><?=number_format($st, 2)?></td>
			</tr>
			<tr>
				<td style="border-top: solid; border-width: thin" colspan="11">DISKON</td>
				<td style="border-top: solid; border-width: thin" class="text-right"><?=number_format($row['disc'], 2)?></td>
			</tr>
			<tr>
				<td style="border-top: solid; border-width: thin" colspan="11">PPN</td>
				<td style="border-top: solid; border-width: thin" class="text-right"><?=number_format($row['ppn'], 2)?></td>
			</tr>
			<tr>
				<td style="border-top: solid; border-width: thin" colspan="11">GRAND TOTAL</td>
				<td style="border-top: solid; border-width: thin" class="text-right"><?=number_format($st-$row['disc']+$row['ppn'], 2)?></td>
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
        <span style="font-weight: bold; font-style: italic"><?=$this->m_website->reprint($row['no_faktur_beli'], 'reprint')?></span>
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
						<div class="col-sm-6">
							<div class="col-sm-4"><b>Tanggal</b></div><div class="col-sm-8"><b> : </b><?=substr($row['tgl_beli'],0,10)?></div>
							<div class="col-sm-4"><b>No. Transaksi</b></div><div class="col-sm-8"><b> : </b><?=$row['no_faktur_beli']?></div>
							<div class="col-sm-4"><b>Lokasi</b></div><div class="col-sm-8"><b> : </b><?=$row['lokasi']?></div>
						</div>
						<div class="col-sm-2"></div>
						<div class="col-sm-4">
							<div class="col-sm-4"><b>Operator</b></div><div class="col-sm-8"><b> : </b><?=$row['operator']?></div>
							<div class="col-sm-4"><b>Nama Penerima</b></div><div class="col-sm-8"><b> : </b><?=$row['nama_penerima']?></div>
							<div class="col-sm-4"><b>Pelunasan</b></div><div class="col-sm-8"><b> : </b><?=$row['Pelunasan']?></div>
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
									<th>Nama Barang</th>
									<th>Harga Beli</th>
									<th>Harga Jual</th>
									<th>Margin</th>
									<th>Diskon 1 %</th>
									<th>Diskon 2 %</th>
									<th>Qty Beli</th>
									<!--<th>Qty Retur</th>-->
									<th>Qty Bonus</th>
									<th>Ppn</th>
									<th>Sub Total</th>
								</tr>
								</thead>
								<tbody>
								<?php
								$no = 0;
								foreach($detail as $rows){
									$hitung_netto = ((float)$rows['jumlah_beli']) * (float)$rows['harga_beli'];
									$diskon = $this->m_website->double_diskon($hitung_netto, array($rows['disc1'], $rows['disc2'], $rows['disc3'], $rows['disc4']));
									$hitung_sub_total = $this->m_website->grand_total_ppn($diskon, 0, $rows['ppn_item']);
									$sub_total = $sub_total + $hitung_sub_total;
									$no++;
									$d1 = $rows['harga_beli']*(1-($rows['disc1']/100));
									$hrg_beli = $d1*(1-($rows['disc2']/100));

									?>
									<tr>
										<td><?=$no?></td>
										<td><?=$rows['kode_barang']?></td>
										<td><?=$rows['nm_brg']?></td>
										<td style="text-align:right;"><?=number_format($rows['harga_beli'],2)?></td>
										<td class="text-right"><?=number_format($rows['harga_jual'], 2)?></td>
										<td class="text-center"><?=(($rows['harga_jual']!=0)?round((1 - ($hrg_beli/$rows['harga_jual']))*100, 2):'0')?> %</td>
										<td><?=($rows['disc1']+0)?></td>
										<td><?=($rows['disc2']+0)?></td>
										<td><?=(float)$rows['jumlah_beli'].' '.$rows['satuan']?></td>
										<!--<td><?/*=(int)$rows['jumlah_retur'].' '.$rows['satuan']*/?></td>-->
										<td><?=(int)$rows['jumlah_bonus'].' '.$rows['satuan']?></td>
										<td><?=($rows['ppn_item']+0)?></td>
										<td style="text-align:right;"><?=number_format($hitung_sub_total,2)?></td>
									</tr>
								<?php
								} ?>
								</tbody>
                                <tfoot>
                                <tr>
                                    <th colspan="8">TOTAL</th>
                                    <th><?=$qt?></th>
                                    <!--<th><?/*=$qr*/?></th>-->
                                    <th><?=$qb?></th>
                                    <th></th>
                                    <th class="text-right"><?=number_format($st,2)?></th>
                                </tr>
								<tr>
									<th style="border-top: solid; border-width: thin" colspan="11">DISKON</th>
									<th style="border-top: solid; border-width: thin" class="text-right"><?=number_format($row['disc'], 2)?></th>
								</tr>
								<tr>
									<th style="border-top: solid; border-width: thin" colspan="11">PPN</th>
									<th style="border-top: solid; border-width: thin" class="text-right"><?=number_format($row['ppn'], 2)?></th>
								</tr>
								<tr>
									<th style="border-top: solid; border-width: thin" colspan="11">GRAND TOTAL</th>
									<th style="border-top: solid; border-width: thin" class="text-right"><?=number_format($st-$row['disc']+$row['ppn'], 2)?></th>
								</tr>
                                </tfoot>
							</table>
						</div>
					</div>
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-default waves-effect" data-dismiss="modal">Close</button>
					<a href="<?=base_url().'cetak/nota_pembelian/'.base64_encode($row['no_faktur_beli'])?>" target="_blank"><button type="button" class="btn btn-primary waves-effect waves-light"><i class="md md-print"></i> Nota</button></a>
				</div>
			</div><!-- /.modal-content -->
		</div><!-- /.modal-dialog -->
	</div><!-- /.modal -->
<?php } ?>

<!--Retur-->
<?php $i = 0 + (($this->uri->segment(4)!=null)?(($this->uri->segment(4)-1) * 15):0); foreach($report as $row){ $i++; ?>
	<div id="return<?=$i?>" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true" style="display: none;">
		<div class="modal-dialog modal-full">
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
					<h4 class="modal-title" id="myLargeModalLabel">Return Pembelian Kepada <?=$row['nama']?></h4>
				</div>
				<div class="modal-body">
					<div class="row">
						<div class="col-sm-6">
							<input type="hidden" id="kode_supplier<?=$i?>" value="<?=$row['kode_supplier']?>">
							<div class="row" style="margin-bottom: 3px">
								<label class="col-sm-4">No. Transaksi</label>
								<div class="col-sm-6">
									<input type="text" id="no_transaksi<?=$i?>" name="no_transaksi<?=$i?>" class="form-control" readonly value="">
									<b class="error" id="ntf_transaksi<?=$i?>"></b>
								</div>
							</div>
							<div class="row" style="margin-bottom: 3px">
								<label class="col-sm-4">Tgl Return</label>
								<div class="col-sm-6">
									<div class="input-group date">
										<div class="input-group-addon">
											<i class="fa fa-calendar"></i>
										</div>
										<input class="form-control pull-right datepicker_date_from" readonly onchange="trx_number(<?=$i?>)" name="tgl_return<?=$i?>" id="tgl_return<?=$i?>" type="text" value="<?=set_value('tgl_return'.$i)?set_value('tgl_return'.$i):date("Y-m-d")?>">
									</div>
								</div>
							</div>
							<div class="row" style="margin-bottom: 3px">
								<label class="col-sm-4">Lokasi</label>
								<div class="col-sm-6">
									<input type="hidden" id="kd_lokasi<?=$i?>" name="kd_lokasi<?=$i?>" value="<?=$row['kd_lokasi']?>">
									<input type="text" id="lokasi<?=$i?>" name="lokasi<?=$i?>" class="form-control" readonly value="<?=$row['lokasi']?>">
								</div>
							</div>
						</div>
						<div class="col-sm-6">
							<div class="row" style="margin-bottom: 3px">
								<label class="col-sm-4">No. Pembelian</label>
								<div class="col-sm-6">
									<input type="text" id="no_pembelian<?=$i?>" name="no_pembelian<?=$i?>" class="form-control" readonly value="<?=$row['no_faktur_beli']?>">
								</div>
							</div>
                            <div class="row" style="margin-bottom: 3px">
                                <label class="col-sm-4">Keterangan</label>
                                <div class="col-sm-6">
                                    <textarea class="form-control" onkeyup="hide_notif('ntf_keterangan<?=$i?>')" id="keterangan<?=$i?>" name="keterangan<?=$i?>"></textarea>
                                    <b class="error" id="ntf_keterangan<?=$i?>"></b>
                                </div>
                            </div>
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
									<th>Nama Barang</th>
									<th>Satuan</th>
									<th>Stock</th>
									<th>Harga Beli</th>
									<th>Diskon 1 %</th>
									<th>Diskon 2 %</th>
									<th>Qty Beli</th>
									<th>Ppn</th>
                                    <!--<th>Ket</th>-->
									<th>Sub Total</th>
									<th>Qty Retur</th>
									<th>Nilai Retur</th>
								</tr>
								</thead>
								<tbody>
								<?php
								$no = 0;
								$sub_total = 0;
								$detail = $this->m_crud->read_data('det_beli db, barang br', 'db.kode_barang, br.nm_brg, db.jumlah_beli, db.harga_beli, db.ppn ppn_item, db.diskon disc1, db.disc2, db.disc3, db.disc4, br.satuan, (SELECT SUM(stock_in-stock_out) FROM Kartu_stock WHERE kd_brg=db.kode_barang AND lokasi not in (\'MUTASI\',\'Retur\') AND lokasi=\''.$row['kd_lokasi'].'\') stock, (SELECT SUM(jml) FROM Master_Retur_Beli, Det_Retur_Beli WHERE no_beli=\''.$row['no_faktur_beli'].'\' AND kd_brg=db.kode_barang) retur', "db.kode_barang=br.kd_brg AND db.no_faktur_beli = '".$row['no_faktur_beli']."'");
								foreach($detail as $rows){
									$no++;
									$hitung_netto = $rows['jumlah_beli'] * $rows['harga_beli'];
									$diskon = $this->m_website->double_diskon($hitung_netto, array($rows['disc1'], $rows['disc2'], $rows['disc3'], $rows['disc4']));
									$hitung_sub_total = $this->m_website->grand_total_ppn($diskon, 0, $rows['ppn_item']);
									$sub_total = $sub_total + $hitung_sub_total;
									?>
									<input type="hidden" id="kode_barang<?=$no?><?=$i?>" value="<?=$rows['kode_barang']?>">
									<input type="hidden" id="stock<?=$no?><?=$i?>" value="<?=$rows['stock']?>">
									<input type="hidden" id="qty_beli<?=$no?><?=$i?>" value="<?=$rows['jumlah_beli']?>">
									<input type="hidden" id="harga_beli<?=$no?><?=$i?>" value="<?=$rows['harga_beli']?>">
									<input type="hidden" id="disc1<?=$no?><?=$i?>" value="<?=$rows['disc1']?>">
									<input type="hidden" id="disc2<?=$no?><?=$i?>" value="<?=$rows['disc2']?>">
									<input type="hidden" id="ppn<?=$no?><?=$i?>" value="<?=$rows['ppn_item']?>">
									<tr>
										<td><?=$no?></td>
										<td><?=$rows['kode_barang']?></td>
										<td><?=$rows['nm_brg']?></td>
										<td><?=$rows['satuan']?></td>
										<td><?=($rows['stock']+0)?></td>
										<td style="text-align:right;"><?=number_format((float)$rows['harga_beli'],2)?></td>
										<td><?=($rows['disc1']+0)?></td>
										<td><?=($rows['disc2']+0)?></td>
										<td><?=(int)$rows['jumlah_beli']-(int)$rows['retur']?></td>
										<td><?=($rows['ppn_item']+0)?></td>
                                        <input type="hidden" value="-" class="form-control input-sm" style="width: 150px" id="ket<?=$no?><?=$i?>" name="ket<?=$no?><?=$i?>">
										<td style="text-align:right"><?=number_format($hitung_sub_total,2)?></td>
                                        <td style="text-align:right"><input type="number" min="0" onkeyup="hitung('<?=$no?>', '<?=$i?>')" class="form-control input-sm" style="width: 75px" id="qty_return<?=$no?><?=$i?>" name="qty_return<?=$no?><?=$i?>"><b class="error" id="ntf_qty_return<?=$no?><?=$i?>"></b></td>
                                        <td style="text-align:right"><input type="text" class="form-control text-right input-sm" style="width: 130px" id="nilai_return<?=$no?><?=$i?>" name="nilai_return<?=$no?><?=$i?>" readonly></td>
                                    </tr>
								<?php } ?>
								</tbody>
                                <tr>
                                    <th class="text-right" colspan="10">TOTAL</th>
                                    <th class="text-right"><b id="sub_total_retur<?=$i?>"><?=number_format($sub_total, 2)?></b></th>
                                    <th class="text-right"><b id="qty_total_retur<?=$i?>"></b></th>
                                    <th class="text-right"><b id="total_retur<?=$i?>"></b></th>
                                </tr>
							</table>
							<input type="hidden" id="banyak_data<?=$i?>" value="<?=$no?>">
						</div>
					</div>
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-default waves-effect" data-dismiss="modal">Close</button>
					<button id="simpan<?=$i?>" onclick="simpan(<?=$i?>, <?=$no?>)" type="button" class="btn btn-primary waves-effect waves-light">Simpan</button>
				</div>
			</div><!-- /.modal-content -->
		</div><!-- /.modal-dialog -->
	</div><!-- /.modal -->
<?php } ?>

<script>
    function pdf(id, res) {
        if (res == true) {
            window.open("<?=base_url().'cetak/nota_pembelian/'?>" + btoa(id));
        }
    }

	function edit_trx(id, res) {
        if (res == true) {
			if (id.param == 'href_target_blank') {
                window.open("<?=base_url().'pembelian/edit_pembelian_barang/'?>" + id.kode);
            } else if(id.param == 'href') {
                window.location = "<?=base_url().'pembelian/edit_pembelian_barang/'?>" + id.kode;
            }
        }
    }
	
	function delete_trans(kode) {
		if (confirm('Akan menghapus data?')) {
			var table_ = ['master_beli', 'det_beli', 'Kartu_stock'];
			var condition_ = ['no_faktur_beli=\''+kode+'\'','no_faktur_beli=\''+kode+'\'','kd_trx=\''+kode+'\''];
			
			hapus_otorisasi({param:'', kode:btoa(kode), activity:'Hapus Pembelian', table:table_, condition:condition_}, delete_transaksi);
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

	function trx_number(kode, lokasi) {
		$("#ntf_transaksi"+kode).text("");
		var tgl_return = $("#tgl_return"+kode).val();

		if (tgl_return != '') {
			$.ajax({
				url: "<?php echo base_url().'site/max_kode/' ?>" + btoa("NB") + "/" + btoa(tgl_return) + "/" + btoa(lokasi),
				type: "GET",
				success: function (data) {
					$("#no_transaksi"+kode).val(data);
				}
			});
		} else {
			$("#no_transaksi"+kode).val("");
		}
	}

	function hitung_total(kode) {
		var banyak_data = parseInt($("#banyak_data"+kode).val());

		var total_retur = 0;
		var total_qty = 0;
		for (var i = 1; i <= banyak_data; i++) {
			var qty = $("#qty_return" + i + kode).val();
			var harga = $("#harga_beli" + i + kode).val();
			var nilai_return = hapuskoma($("#nilai_return" + i + kode).val());
			if (qty > 0) {
				total_retur = total_retur + parseFloat(nilai_return);
				total_qty = total_qty + parseInt(qty)
			}
		}
		$("#qty_total_retur"+kode).text(total_qty);
		$("#total_retur"+kode).text(to_rp(total_retur.toFixed(2)));
	}

	function hitung(id, kode) {
		var qty_return = parseInt($("#qty_return"+id+kode).val());
		var stock = parseInt($("#stock"+id+kode).val());
		var qty_beli = parseInt($("#qty_beli"+id+kode).val());
		var harga_beli = $("#harga_beli"+id+kode).val();
		var disc1 = $("#disc1"+id+kode).val();
		var disc2 = $("#disc2"+id+kode).val();
		var ppn = $("#ppn"+id+kode).val();

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
		    var nilai_kotor = qty_return * harga_beli;
            var diskon = double_diskon(nilai_kotor, [disc1, disc2]);
            var nilai_return = hitung_ppn(diskon, 0, ppn);

			$("#nilai_return"+id+kode).val(to_rp(nilai_return.toFixed(2)));

			$("#ntf_qty_return"+id+kode).text("");
			$("#simpan"+kode).prop("disabled", false);
			hitung_total(kode);
		}
	}

	function simpan(kode, data) {
		var list_data = [];
		var no_transaksi = $("#no_transaksi"+kode).val();
		var tgl_return = $("#tgl_return"+kode).val();
		var lokasi = $("#kd_lokasi"+kode).val();
		var no_pembelian = $("#no_pembelian"+kode).val();
		var kode_supplier = $("#kode_supplier"+kode).val();
		var keterangan_master = $("#keterangan"+kode).val();

		if (no_transaksi == '') {
			$("#ntf_transaksi"+kode).text("No Transaksi Tidak Boleh Kosong!");
		}

        if (keterangan_master == '') {
            $("#ntf_keterangan"+kode).text("Keterangan Tidak Boleh Kosong!");
        }

		if (no_transaksi != '' && keterangan_master != '') {
			var status = 0;
			for (var i = 1; i <= data; i++) {
                var disc1 = $("#disc1" + i + kode).val();
                var disc2 = $("#disc2" + i + kode).val();
                var ppn = $("#ppn" + i + kode).val();
				var qty_return = $("#qty_return" + i + kode).val();
				var harga_beli_kotor = $("#harga_beli" + i + kode).val();
                var diskon = double_diskon(harga_beli_kotor, [disc1, disc2]);
                var harga_beli = hitung_ppn(diskon, 0, ppn);
				var nilai_return = hapuskoma($("#nilai_return" + i + kode).val());
				var kode_barang = $("#kode_barang" + i + kode).val();
				var keterangan = $("#ket" + i + kode).val();

				var retur = {
					kode_barang_: kode_barang,
					qty_return_: qty_return,
					harga_beli_: harga_beli,
					nilai_return_: nilai_return,
                    keterangan_: keterangan
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
							list_data_: list_data,
                            keterangan_: keterangan_master
						},
						success: function (data) {
							if (data != false) {
								if (confirm("Transaksi Berhasil! Akan Mencetak Nota?")) {
									window.open('<?=base_url().'cetak/nota_retur_3ply/'?>' + btoa(data));
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