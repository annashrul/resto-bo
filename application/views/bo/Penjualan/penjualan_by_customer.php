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
								<div class="col-md-12 col-sm-12 col-xs-12 table-responsive">
									<table class="table table-striped table-bordered">
										<thead>
										<tr>
											<th>No</th>
											<th>Kode Customer</th>
											<th>Nama Customer</th>
											<th>Qty Terjual</th>
											<th>Sub Total</th>
											<th>Diskon Item</th>
											<th>Diskon Transaksi</th>
											<th>Net Sales</th>
											<th>Tax</th>
											<th>Service</th>
											<th>Gross Sales</th>
											<th>Pilihan</th>
										</tr>
										</thead>
										<tbody>
										<?php $no = 0 + (($this->uri->segment(4)!=null)?(($this->uri->segment(4)-1) * 10):0);
										$qt = 0; $di = 0; $dt = 0; $gs = 0; $tax = 0; $srv = 0;
										foreach($report as $row){
										    $no++;
										    $ns = ($row['gross_sales']+0) - ($row['diskon_item']+$row['diskon_trx']+0);
										    ?>
											<tr>
												<td><?=$no?></td>
												<td><?=$row['kd_cust']?></td>
												<td><?=$row['Nama']?></td>
												<td><?=($row['qty']+0)?></td>
												<td class="text-right"><?=(number_format($row['gross_sales']+0))?></td>
												<td class="text-right"><?=(number_format($row['diskon_item']+0))?></td>
												<td class="text-right"><?=(number_format($row['diskon_trx']+0))?></td>
												<td class="text-right"><?=number_format($ns)?></td>
                                                <td class="text-right"><?=(number_format($row['tax']+0))?></td>
                                                <td class="text-right"><?=(number_format($row['service']+0))?></td>
                                                <td class="text-right"><?=(number_format($ns+$row['tax']+$row['service']+0))?></td>
												<td class="text-center">
													<div class="btn-group">
														<button type="button" class="btn btn-primary dropdown-toggle waves-effect waves-light" data-toggle="dropdown" aria-expanded="false">Pilihan <span class="caret"></span></button>
														<ul class="dropdown-menu" role="menu">
															<li><a href="#" onclick="get_detail('<?=$row['kd_cust']?>')"><i class="md md-visibility"></i> Detail</a></li>
															<!--<li><a href="<?/*=base_url().strtolower($this->control).'/'.$page.'/download/'.base64_encode($row['kd_cust'])*/?>"><i class="md md-get-app"></i> Download</a></li>
															<li><a href="<?/*=base_url().strtolower($this->control).'/'.$page.'/print/'.base64_encode($row['kd_cust'])*/?>" target="_blank"><i class="md md-print"></i> to PDF</a></li>-->

															<!--<li class="divider"></li>-->
														</ul>
													</div>
												</td>
											</tr>
										<?php
										$qt = $qt + (int)$row['qty'];
										$gs = $gs + (float)$row['gross_sales'];
										$di = $di + (float)$row['diskon_item'];
										$dt = $dt + (float)$row['diskon_trx'];
										$tax = $tax + (float)$row['tax'];
										$srv = $srv + (float)$row['service'];
										} ?>
										</tbody>
										<tfoot>
										<tr>
											<th colspan="3">TOTAL PER PAGE</th>
											<th><?=($qt+0)?></th>
											<th class="text-right"><?=number_format($gs)?></th>
											<th class="text-right"><?=number_format($di)?></th>
											<th class="text-right"><?=number_format($dt)?></th>
											<th class="text-right"><?=number_format($gs-$di-$dt)?></th>
                                            <th class="text-right"><?=number_format($tax)?></th>
                                            <th class="text-right"><?=number_format($srv)?></th>
											<th class="text-right"><?=number_format($gs-$di-$dt+$tax+$srv)?></th>
											<th></th>
										</tr>
										<tr>
											<th colspan="3">TOTAL</th>
											<th><?=($tqt+0)?></th>
											<th class="text-right"><?=number_format($tgs)?></th>
											<th class="text-right"><?=number_format($tdi)?></th>
											<th class="text-right"><?=number_format($tdt)?></th>
											<th class="text-right"><?=number_format($tns)?></th>
											<th class="text-right"><?=number_format($ttax)?></th>
											<th class="text-right"><?=number_format($tsrv)?></th>
											<th class="text-right"><?=number_format($tns+$ttax+$tsrv)?></th>
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

<div id="detail_customer" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true" style="display: none;">
    <div class="modal-dialog modal-full">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                <h4 class="modal-title" id="myLargeModalLabel">Detail <?=$title?></h4>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-sm-8">
                        <div class="col-sm-4"><b>Kode Customer</b></div><div class="col-sm-8"><b> : </b><b id="det_kode"></b></div>
                        <div class="col-sm-4"><b>Nama Customer</b></div><div class="col-sm-8"><b> : </b><b id="det_nama"></b></div>
                    </div>
                    <div class="col-sm-4">
                        <div class="row">
                            <b>Periode : <b><b id="det_periode"></b>
                        </div>
                        <div class="row">
                            <b>Lokasi : <b><b id="det_lokasi"></b>
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
                                <th>No. Faktur</th>
                                <th>Tanggal</th>
                                <th>Kode Barang</th>
                                <th>Barcode</th>
                                <th>Nama Barang</th>
                                <th><?=$menu_group['as_deskripsi']?></th>
                                <th>Qty Kirim</th>
                                <th>Qty Retur</th>
                                <th>Qty Laku</th>
                                <th>Harga Jual</th>
                                <th>Diskon</th>
                                <th>Sub Total</th>
                            </tr>
                            </thead>
                            <tbody id="list_by_customer">
                        </table>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default waves-effect" data-dismiss="modal">Close</button>
                <!--<button class="btn btn-primary waves-effect waves-light" onclick="to_pdf(<?/*=$i*/?>)"><i class="md md-print"></i> to PDF</button>-->
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->

<script>
    function get_detail(kode) {
        var tgl_periode = $("#field-date").val();
        var lokasi = $("#lokasi").val();
        $.ajax({
            url: "<?php echo base_url().'penjualan/detail_by_customer/' ?>" + btoa(kode) + '/' + btoa(tgl_periode) + '/' + btoa(lokasi),
            type: "GET",
            dataType: "JSON",
            beforeSend: function () {
                $('#loading').show();
            },
            complete: function () {
                $('#loading').hide();
            },
            success: function (res) {
                $("#list_by_customer").html(res.list);
                $("#detail_customer").modal('show');
                $("#det_kode").text(res.det.kode);
                $("#det_nama").text(res.det.nama);
                $("#det_lokasi").text(res.det.lokasi);
                $("#det_periode").text(res.det.periode);
            }
        });
    }

	function to_pdf(id) {
		var filter = $("#filter"+id).val();
		var kode = document.getElementById('kd_supp'+id).innerHTML;

		window.open("<?=base_url().'penjualan/penjualan_by_supplier/print/'?>" + btoa(kode));
	}
	function after_change(val) {
		$.ajax({
			url: "<?php echo base_url().'site/set_session_date/' ?>" + btoa('field-date') + '/' + btoa(val),
			type: "GET"
		});
	}
</script>

