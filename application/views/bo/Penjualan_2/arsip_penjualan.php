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
							    	<label>Lokasi</label>
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
                                        <label>Tipe Transaksi</label>
                                        <?php $field = 'tipe';
                                        $option = null; $option[''] = 'Semua Tipe';
                                        $option['Tunai'] = 'Tunai';
                                        $option['Non_Tunai'] = 'Non Tunai';
                                        $option['Voucher'] = 'Voucher';
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
									<div class="table-responsive" data-pattern="priority-columns">
										<table id="" class="table table-small-font table-striped table-bordered">
											<thead>
											<tr>
												<th>No</th>
												<th>Pilihan</th>
												<th>Tanggal</th>
												<th>No. Nota</th>
												<th data-priority="1">Jam</th>
												<th data-priority="1">Customer</th>
												<th data-priority="1">Kasir</th>
												<th data-priority="1">Sub Total</th>
												<th data-priority="1">Diskon Item</th>
												<th data-priority="1">Omset</th>
												<th data-priority="1">Disc Total(%)</th>
												<th data-priority="1">Disc Total(Rp)</th>
												<th data-priority="1">Reg. Member</th>
												<th data-priority="1">Trx. Lain</th>
												<th data-priority="1">Keterangan</th>
												<th data-priority="1">Grand Total</th>
												<th data-priority="1">Tunai</th>
												<th data-priority="1">Non Tunai</th>
												<th data-priority="1">Charge</th>
                                                <th data-priority="1">Voucher</th>
                                                <th data-priority="1">Nama Voucher</th>
                                                <th data-priority="1">Nama Kartu</th>
												<th data-priority="1">Mesin EDC</th>
												<th data-priority="1">Status</th>
												<th data-priority="1">Lokasi</th>
												<th data-priority="1">Jenis Trx.</th>
												<!--<th>Profit</th>-->
											</tr>
											</thead>
											<tbody>
											<?php $no = 0 + (($this->uri->segment(4)!=null)?(($this->uri->segment(4)-1) * 30):0);
											$omset = 0;
											$dis_item = 0;
											$sub_total = 0;
											$dis_persen = 0;
											$dis_rp = 0;
											$kas_lain = 0;
											$gt = 0;
											$bayar = 0;
											$jml_kartu = 0;
											$charge = 0;
											$voucher = 0;
											foreach($report as $row){ $no++; ?>
												<tr>
													<td><?=$no?></td>
													<td class="text-center">
														<div class="btn-group">
															<button type="button" class="btn btn-primary dropdown-toggle waves-effect waves-light" data-toggle="dropdown" aria-expanded="false">Pilihan <span class="caret"></span></button>
															<ul class="dropdown-menu" role="menu">
																<li><a href="#" data-toggle="modal" data-target="#<?=$no?>"><i class="md md-visibility"></i> Detail</a></li>
																<li><a href="<?=base_url().strtolower($this->control).'/'.$page.'/download/'.base64_encode($row['kd_trx'])?>"><i class="md md-get-app"></i> Download</a></li>
																<li><a href="<?=base_url().strtolower($this->control).'/'.$page.'/print/'.base64_encode($row['kd_trx'])?>" target="_blank"><i class="md md-print"></i> to PDF</a></li>

																<!--<li class="divider"></li>-->
															</ul>
														</div>
													</td>
													<td><?=substr($row['tgl'],0,10)?></td>
													<td><?=$row['kd_trx']?></td>
													<td><?=substr($row['jam'],10,9)?></td>
													<td><?=$row['Nama']?></td>
													<td><?=$row['kd_kasir']?></td>
													<td class="text-right"><?=number_format($row['omset'])?></td>
													<td class="text-right"><?=number_format($row['diskon_item'])?></td>
													<td class="text-right"><?=number_format($row['omset']-$row['diskon_item'])?></td>
													<td><?=$row['dis_persen']+0?></td>
													<td class="text-right"><?=number_format($row['dis_rp'])?></td>
													<td><?=$row['RegMember']+0?></td>
													<td><?=$row['kas_lain']+0?></td>
													<td><?=$row['ket_kas_lain']?></td>
													<td class="text-right"><?=number_format($row['omset']-$row['diskon_item']-$row['dis_rp']-$row['kas_lain'])?></td>
													<td class="text-right"><?=number_format($row['bayar'])?></td>
													<td class="text-right"><?=number_format($row['jml_kartu'])?></td>
                                                    <td class="text-right"><?=number_format($row['charge'])?></td>
                                                    <td class="text-right"><?=number_format($row['voucher'])?></td>
                                                    <td><?=$row['nm_voucher']?></td>
                                                    <td><?=$row['kartu']?></td>
													<td><?=$row['pemilik_kartu']?></td>
													<td><?=$row['status']?></td>
													<td><?=$row['Lokasi']?></td>
													<td><?=$row['Jenis_trx']?></td>
													<!--<td class="text-right"><?/*=number_format($row['profit'])*/?></td>-->
												</tr>
											<?php
												$omset = $omset + $row['omset'];
												$dis_item = $dis_item + $row['diskon_item'];
												$sub_total = $sub_total + $row['omset'] - $row['diskon_item'];
												$dis_persen = $dis_persen + $row['dis_persen'];
												$dis_rp = $dis_rp + $row['dis_rp'];
												$kas_lain = $kas_lain + $row['kas_lain'];
												$gt = $gt + $row['omset'] - $row['diskon_item'] - $row['dis_rp'] - $row['kas_lain'];
												$bayar = $bayar + $row['bayar'];
												$jml_kartu = $jml_kartu + $row['jml_kartu'];
												$charge = $charge + $row['charge'];
												$voucher = $voucher + $row['voucher'];
											} ?>
											</tbody>
											<tfoot>
											<tr>
												<th colspan="7">TOTAL PER PAGE</th>
												<th class="text-right"><?=number_format($omset)?></th>
												<th class="text-right"><?=number_format($dis_item)?></th>
												<th class="text-right"><?=number_format($sub_total)?></th>
												<th class="text-right"><?=($dis_persen+0)?></th>
												<th class="text-right"><?=number_format($dis_rp)?></th>
												<th></th>
												<th class="text-right"><?=number_format($kas_lain)?></th>
												<th></th>
												<th class="text-right"><?=number_format($gt)?></th>
												<th class="text-right"><?=number_format($bayar)?></th>
												<th class="text-right"><?=number_format($jml_kartu)?></th>
												<th class="text-right"><?=number_format($charge)?></th>
												<th class="text-right"><?=number_format($voucher)?></th>
												<th></th>
												<th></th>
												<th></th>
												<th></th>
												<th></th>
												<th></th>
											</tr>
											<tr>
												<th colspan="7">TOTAL</th>
												<th class="text-right"><?=number_format($tomset)?></th>
												<th class="text-right"><?=number_format($tdis_item)?></th>
												<th class="text-right"><?=number_format($tsub_total)?></th>
												<th class="text-right"><?=($tdis_persen+0)?></th>
												<th class="text-right"><?=number_format($tdis_rp)?></th>
												<th></th>
												<th class="text-right"><?=number_format($tkas_lain)?></th>
												<th></th>
												<th class="text-right"><?=number_format($tgt)?></th>
												<th class="text-right"><?=number_format($tbayar)?></th>
												<th class="text-right"><?=number_format($tjml_kartu)?></th>
												<th class="text-right"><?=number_format($tcharge)?></th>
												<th class="text-right"><?=number_format($tvoucher)?></th>
												<th></th>
												<th></th>
												<th></th>
												<th></th>
												<th></th>
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

<?php $i =  0 + (($this->uri->segment(4)!=null)?(($this->uri->segment(4)-1) * 30):0); foreach($report as $row){ $i++; ?>
	<div id="<?=$i?>" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true" style="display: none;">
		<div class="modal-dialog modal-lg">
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" aria-hidden="true">??</button>
					<h4 class="modal-title" id="myLargeModalLabel">Detail <?=$title?></h4>
				</div>
				<div class="modal-body">
					<div class="row">
						<div class="col-sm-6">
							<div class="col-sm-3"><b>Tanggal</b></div><div class="col-sm-9"><b> : </b><?=substr($row['tgl'],0,10)?></div>
							<div class="col-sm-3"><b>No. Nota</b></div><div class="col-sm-9"><b> : </b><?=$row['kd_trx']?></div>
							<div class="col-sm-3"><b>Lokasi</b></div><div class="col-sm-9"><b> : </b><?=$row['Lokasi']?></div>
						</div>
						<div class="col-sm-2"></div>
						<div class="col-sm-4">
							<div class="col-sm-4"><b>Operator</b></div><div class="col-sm-8"><b> : </b><?=$row['kd_kasir']?></div>
						</div>
					</div>
					<hr/>
					<div class="row">
						<div class="col-md-12 col-sm-12 col-xs-12">
							<table id="datatable" class="table table-striped table-bordered">
								<thead>
								<tr>
									<th>No</th>
									<th>Kode Barang</th>
									<th>Nama Barang</th>
									<th>Open Price</th>
									<th>Harga Jual</th>
									<th>Qty</th>
									<th>Diskon</th>
									<th>Sub Total</th>
								</tr>
								</thead>
								<tbody>
								<?php $no = 0;
								$detail = $this->m_crud->read_data("Det_Trx dt, barang br", "dt.*, br.nm_brg, br.satuan", "dt.kd_brg=br.kd_brg AND dt.kd_trx = '".$row['kd_trx']."'");
								foreach($detail as $rows){ $no++; ?>
									<tr>
										<td><?=$no?></td>
										<td><?=$rows['kd_brg']?></td>
										<td><?=$rows['nm_brg']?></td>
										<td class="text-center"><img width="25px" src="<?=base_url().'assets/images/status-'.($rows['open_price']=='1'?'Y':'T').'.png'?>" /></td>
										<td class="text-right"><?=number_format($rows['hrg_jual'])?></td>
										<td class="text-right"><?=number_format($rows['qty']).' '.$rows['satuan']?></td>
										<td class="text-right"><?=number_format($rows['dis_persen'])?></td>
										<td class="text-right"><?=number_format($rows['hrg_jual']*$rows['qty']-$rows['dis_persen'])?></td>
									</tr>
								<?php } ?>
								</tbody>
							</table>
						</div>
					</div>
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-default waves-effect" data-dismiss="modal">Close</button>
					<a href="<?=base_url().strtolower($this->control).'/'.$page.'/print/'.base64_encode($row['kd_trx'])?>" target="_blank"><button type="button" class="btn btn-primary waves-effect waves-light"><i class="md md-print"></i> to PDF</button></a>
				</div>
			</div><!-- /.modal-content -->
		</div><!-- /.modal-dialog -->
	</div><!-- /.modal -->
<?php } ?>
<script>
	function after_change(val) {
        $.ajax({
            url: "<?php echo base_url().'site/set_session_date/' ?>" + btoa('field-date') + '/' + btoa(val),
            type: "GET"
        });
    }
</script>