<!-- ============================================================== -->
<!-- Start right Content here -->
<!-- ============================================================== -->
<style>
	th, td {
		font-size: 10pt;
	}
</style>
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
                                    <div class="col-sm-2">
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
                                        <input type="hidden" id="tmp_lokasi" value="<?=isset($this->session->search[$field])?$this->session->search[$field]:set_value($field)?>">
                                        <b class="error" id="ntf_lokasi"></b>
                                    </div>
                                    <div class="col-sm-4">
                                        <div class="form-group">
                                            <label>Status</label>
                                            <div class="row">
                                                <div class="col-sm-7">
                                                    <?php $field = 'posting';
                                                    $option = null;
                                                    $option[''] = 'Semua Status';
                                                    $option['1'] = 'Posted';
                                                    $option['0'] = 'Un Posting';
                                                    echo form_dropdown($field, $option, isset($this->session->search[$field])?$this->session->search[$field]:set_value($field), array('class' => 'select2', 'id'=>$field));
                                                    ?>
                                                </div>
                                                <div class="col-sm-5">
                                                    <?php $field = 'selisih';
                                                    $option = null;
                                                    $option['-'] = 'Semua Stock';
                                                    $option['>'] = 'Stock +';
                                                    $option['='] = 'Stock 0';
                                                    $option['<'] = 'Stock -';
                                                    echo form_dropdown($field, $option, isset($this->session->search[$field])?$this->session->search[$field]:set_value($field), array('class' => 'select2', 'id'=>$field));?>
                                                    <!--<div class="checkbox checkbox-primary">
														<input id="<?/*=$field*/?>" name="<?/*=$field*/?>" value="1" type="checkbox" <?/*=(isset($this->session->search[$field]) && $this->session->search[$field]=='1')?'checked':''*/?>>
														<label for="<?/*=$field*/?>" title="Stock Selisih" style="font-size: 10pt">
															Stock Selisih
														</label>
													</div>-->
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-sm-3">
                                        <div class="form-group">
                                            <label><?=$menu_group['as_group1']?></label>
                                            <?php $field = 'group1';
                                            $option = null; $option[''] = 'Semua '.$menu_group['as_group1'];
                                            //$option['all'] = 'All';
                                            $data_option = $this->m_crud->read_data('Group1', 'Kode, Nama', null, 'Nama asc');
                                            foreach($data_option as $row){ $option[$row['Kode']] = $row['Nama']; }
                                            echo form_dropdown($field, $option, isset($this->session->search[$field])?$this->session->search[$field]:set_value($field), array('class' => 'select2', 'id'=>$field));
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
											$option['op.kd_brg'] = 'Kode Barang';
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
									<div class="col-sm-2">
										<div class="form-group">
											<label>Order Search</label>
											<?php $field = 'order';
											$option = null;
											$option['ASC'] = 'A-Z';
											$option['DESC'] = 'Z-A';
											echo form_dropdown($field, $option, isset($this->session->search[$field])?$this->session->search[$field]:set_value($field), array('class' => 'select2', 'id'=>$field));?>
										</div>
									</div>
									<div class="col-sm-1" style="margin-left: -10px">
										<label>&nbsp;</label>
										<button type="submit" class="btn btn-primary waves-effect waves-light" name="search">Search</button>
									</div>
									<div class="col-sm-1" style="margin-left: -10px">
										&nbsp;
										<label>&nbsp;</label>
										<button type="submit" class="btn btn-primary waves-effect waves-light" name="to_excel">Export</button>
									</div>
									<div class="col-sm-1" style="margin-left: -10px">
										&nbsp;
										<label>&nbsp;</label>
										<button type="button" data-toggle="modal" data-target="#import_data" class="btn btn-primary">Import</button>
									</div>
                                    <div class="col-sm-1" style="margin-left: -10px">
										<input type="hidden" id="tmp_status" value="<?=$status?>">
                                        <label>&nbsp;</label>
                                        <button type="button" class="btn btn-primary" id="posting_all" <?=$status?>><span class="md md-send"></span> Posting All</button>
                                    </div>
                                </div>
                            <?=form_close()?>
                        </div>
						<div class="panel-body">
							<div class="row">
								<div class="col-md-12 col-sm-12 col-xs-12">
									<button style="margin-bottom: 18px" class="btn btn-primary" id="barang_opname">Barang Belum Di Opname</button>
									<div class="table-responsive">
										<table class="table table-striped table-bordered">
											<thead>
											<tr>
												<th>No</th>
												<th>No. Transaksi</th>
												<th>Tanggal</th>
												<th>Kode Barang</th>
												<th>Barcode</th>
												<th>Nama Barang</th>
												<th>Kelompok Barang</th>
												<th>Lokasi</th>
												<th>Stock Terakhir</th>
												<th>Stock Fisik</th>
												<th>Stock Selisih</th>
												<th>Value</th>
												<th>Value Selisih</th>
												<th>Operator</th>
												<th>Status</th>
												<th>Pilihan</th>
											</tr>
											</thead>
											<tbody>
											<?php $no = 0 + (($this->uri->segment(4)!=null)?(($this->uri->segment(4)-1) * 20):0); $sf=0; $st=0; $sv=0; foreach($report as $row){ $no++; ?>
												<?php $value = $row['hrg_beli']*((float)$row['qty_fisik']-(float)$row['stock_terakhir']); ?>
												<tr>
													<td><?=$no?></td>
													<td><?=$row['kd_trx']?></td>
													<td><?=substr($row['tgl'],0,10)?></td>
													<td><?=$row['kd_brg']?></td>
													<td><?=$row['barcode']?></td>
													<td><?=$row['nm_brg']?></td>
													<td><?=$row['nm_kel_brg']?></td>
													<td><?=$row['lokasi']?></td>
													<td><?=(float)$row['stock_terakhir']?></td>
													<td><?=(float)$row['qty_fisik']?></td>
													<td><?=(float)$row['qty_fisik']-(float)$row['stock_terakhir']?></td>
													<td><?=number_format($row['hrg_beli'])?></td>
													<td><?=number_format($value)?></td>
													<td><?=$row['kd_kasir']?></td>
													<td><?php if ($row['status'] == 0){
															echo '<div class="panel panel-primary" style="margin-bottom: -1px"><div class="panel-heading text-center">Posting</div></div>';
														}else{
															echo '<div class="panel panel-success" style="margin-bottom: -1px"><div class="panel-heading text-center" style="color: white">Posted</div></div>';
														}?>
													</td>
													<td class="text-center">
														<button type="button" class="btn btn-primary" onclick="posting('<?=$row['kd_trx']?>')" <?=($row['status']==0)?null:'disabled'?>><span class="md md-send"></span> Posting</button>
													</td>
												</tr>
												<?php
												$st = $st + (float)$row['stock_terakhir'];
												$sf = $sf + (float)$row['qty_fisik'];
												$sv = $sv + $value;
											} ?>
											</tbody>
											<tfoot>
											<tr>
												<th colspan="8">TOTAL PER PAGE</th>
												<th><?=$st?></th>
												<th><?=$sf?></th>
												<th><?=$sf-$st?></th>
												<th></th>
												<th><?=number_format($sv)?></th>
												<th></th>
												<th></th>
												<th></th>
											</tr>
											<tr>
												<th colspan="8">TOTAL</th>
												<th><?=$tst?></th>
												<th><?=$tsf?></th>
												<th><?=$tsf-$tst?></th>
												<th></th>
												<th><?=number_format($tsv)?></th>
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

<div class="modal fade" id="import_data" role="dialog">
    <div class="modal-dialog modal-lg">
		<!-- Modal content-->
		<div class="modal-content">
			<?=form_open_multipart($content)?>
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal">&times;</button>
				<h4 class="modal-title">Import Data</h4>
			</div>
			<div class="modal-body">
				<div class="row">
					<div class="col-sm-12">
						<font style="color:red; font-size:14px;">Nama Sheet Excel harus Import Opname.</font><br/>
						<font style="color:red; font-size:14px;">Pastikan kode lokasi di excel sama dengan kode lokasi di system.</font><br/>
						<font style="color:red; font-size:14px;">Pastikan format cell tanggal di excel type text, tanggal opname diinput sampai dengan detiknya dengan format (yyyy-mm-dd H:i:s).</font><br/>
						<br/>
						<p><a href="<?=base_url().'assets/files/import_opname.xlsx'?>" download >Download Format Excel</a></p>
					</div>
				</div>
				<div class="row">
					<div class="col-sm-5">
						<label>Upload File excel</label>
						<?php $field="file"; ?><input class="form-control" style="padding:0;" type="file" name="<?=$field?>" required />
						File type : xlsx|xls - Max size : 5 MB
					</div>
				</div>
			</div>
			<div class="modal-footer">
				<button class="btn btn-primary" type="submit" name="import">Import</button>
				<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
			</div>
			<?=form_close()?>
		</div>
	</div>
</div>


<div id="modal-barang" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="modal-barang" aria-hidden="true" style="display: none;">
	<div class="modal-dialog modal-full">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
				<h4 class="modal-title" id="modal-title"></h4>
			</div>
			<div class="modal-body">
				<div class="row">
					<div class="col-sm-6">
						<!--<div class="col-sm-4"><b>Tanggal</b></div><div class="col-sm-8"><b> : </b><b id="modal-tanggal"></b></div>-->
						<div class="col-sm-4"><b>Lokasi</b></div><div class="col-sm-8"><b> : </b><b id="modal-lokasi"></b></div>
					</div>
				</div>
				<hr/>
				<div class="row">
					<div class="col-sm-2">
						<?php $field = 'selisih_barang';
						$option = null;
						$option['-'] = 'Semua Stock';
						$option['>'] = 'Stock +';
						$option['='] = 'Stock 0';
						$option['<'] = 'Stock -';
						echo form_dropdown($field, $option, isset($this->session->search[$field])?$this->session->search[$field]:set_value($field), array('class' => 'form-control', 'id'=>$field, 'onchange'=>"cari_barang()"));?>
					</div>
					<div class="col-sm-3">
						<div class="form-group">
							<?php $field = 'search_barang'; ?>
							<input class="form-control" placeholder="Search" type="text" id="<?=$field?>" name="<?=$field?>" onkeyup="cari_barang()" value="<?=isset($this->session->search[$field])?$this->session->search[$field]:set_value($field)?>" autofocus />
							<?=form_error($field, '<div class="error" style="color:red;">', '</div>')?>
						</div>
					</div>
					<div class="col-sm-1">
						<label>&nbsp;</label>
						<button type="submit" class="btn btn-primary waves-effect waves-light" onclick="to_excel()">Export</button>
					</div>
                    <div class="col-sm-2">
                        <button type="submit" class="btn btn-primary waves-effect waves-light" onclick="penyesuaian('minus')">Penyesuaian Minus</button>
                    </div>
					<div class="col-sm-2">
                        <button type="submit" class="btn btn-primary waves-effect waves-light" onclick="penyesuaian('plus')">Penyesuaian Plus</button>
                    </div>
				</div>
				<div class="row">
					<div class="col-md-12 col-sm-12 col-xs-12">
						<table class="table table-striped table-bordered">
							<thead>
							<tr>
								<th style="width: 50px">No</th>
								<th>Kode Barang</th>
								<th>Barcode</th>
								<th>Nama Barang</th>
								<th>Kelompok Barang</th>
								<th>Stock Sistem</th>
							</tr>
							</thead>
							<tbody id="list-barang">
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

<script>
	/*var menu = $('.wraper');
	var menuTimeout;

	$(window).on('mousemove', mouseMoveHandler);

	function mouseMoveHandler(e) {
		if (e.pageX < 10 || menu.is(':hover')) {
			// Show the menu if mouse is within 20 pixels from the left or we are hovering over it
			clearTimeout(menuTimeout);
			menuTimeout = null;
			//showMenu();
			alert("left side")
		}
	}*/

	function penyesuaian(jenis='') {
	    var param = $("#param_opname").val();
        var tgl = $("#field-date").val();
        var lokasi = $("#lokasi").val();
        if (param == '1') {
			if(jenis=='minus'){
				if (confirm("Akan menyesuaikan stock minus?")) {
					$.ajax({
						url: "<?php echo base_url() . 'inventory/penyesuaian_opname_minus' ?>",
						type: "POST",
						data: {lokasi_: lokasi, tgl_periode_: tgl},
						beforeSend: function () {
							$("#modal-barang").modal("hide");
							$('#loading').show();
						},
						complete: function () {
							$('#loading').hide();
							$("#modal-barang").modal("show");
						},
						success: function (res) {
							if (res == 'success') {
								alert("Penyesuaian Berhasil");
							}
							//location.reload();
							window.location = "<?=base_url().'inventory/stock_opname_report'?>";
						}
					})
				}
			} else if(jenis=='plus'){
				if (confirm("Akan menyesuaikan stock plus?")) {
					$.ajax({
						url: "<?php echo base_url() . 'inventory/penyesuaian_opname_plus' ?>",
						type: "POST",
						data: {lokasi_: lokasi, tgl_periode_: tgl},
						beforeSend: function () {
							$("#modal-barang").modal("hide");
							$('#loading').show();
						},
						complete: function () {
							$('#loading').hide();
							$("#modal-barang").modal("show");
						},
						success: function (res) {
							if (res == 'success') {
								alert("Penyesuaian Berhasil");
							}
							//location.reload();
							window.location = "<?=base_url().'inventory/stock_opname_report'?>";
						}
					})
				}
			}
        }
    }

	function to_excel() {
        var tgl = $("#field-date").val();
		var selisih_barang = $("#selisih_barang").val();
		var search_barang = $("#search_barang").val();

		var lokasi = $("#lokasi").val();

		window.open("<?=base_url()?>/inventory/list_barang_opname/excel/" + btoa(lokasi) + "/" + btoa(selisih_barang) + "/" + btoa(tgl) + "/" + btoa(search_barang));
	}

	function cari_barang() {
        var tgl = $("#field-date").val();
		var lokasi = $("#lokasi").val();
		var selisih_barang = $("#selisih_barang").val();
		var search_barang = $("#search_barang").val();

		$.ajax({
			url: "<?php echo base_url() . 'inventory/list_barang_opname/search/' ?>" + btoa(lokasi) + "/" + btoa(selisih_barang) + "/" + btoa(tgl) + "/" + btoa(search_barang),
			type: "GET",
			success: function (res) {
				$("#list-barang").html(res);
				$("#modal-datatable").DataTable();
			}
		});
	}

	function to_page() {
		var tgl_awal = $("#tgl_awal").val();
		var tgl_akhir = $("#tgl_akhir").val();
        var tgl = $("#field-date").val();
		var lokasi = $("#lokasi").val();

		$.ajax({
			url: "<?php echo base_url() . 'inventory/list_barang_opname' ?>",
			data: {lokasi_:lokasi, tgl_periode_: tgl},
			type: "POST",
			cache: false,
			beforeSend: function () {
				$('#loading').show();
				$("#list-barang").html("");
			},
			complete: function () {
				$("#loading").hide();
			},
			success: function (res) {
				$("#list-barang").html(res);
				$("#modal-datatable").DataTable();
			}
		});
	}

	$("#barang_opname").click(function () {
		var tgl_awal = $("#tgl_awal").val();
		var tgl_akhir = $("#tgl_akhir").val();
        var tgl = $("#field-date").val();
		var tgl_periode = tgl_awal+' - '+tgl_akhir;
		var lokasi = $("#lokasi").val();

		if (lokasi == '') {
			$("#ntf_lokasi").text("Lokasi Wajib Dipilih!");
		} else {
            $.ajax({
                url: "<?php echo base_url() . 'inventory/list_barang_opname' ?>",
                data: {lokasi_:lokasi, tgl_periode_: tgl},
                type: "POST",
                beforeSend: function () {
                    $('#loading').show();
                    $("#list-barang").html("");
                },
                complete: function () {
                    $("#loading").hide();
                },
                success: function (res) {
                    $("#list-barang").html(res);
                    $("#modal-datatable").DataTable();
                    $("#modal-title").text("List Barang Belum di Opname");
                    $("#modal-tanggal").text(tgl_periode);
                    $("#modal-lokasi").text(lokasi);
                    $("#modal-barang").modal("show");
                }
            });
		}

	});

    function posting(kode) {
    	if (confirm("Akan Memposting Opname?")) {
			$.ajax({
				url: "<?php echo base_url() . 'inventory/posting_opname/item' ?>",
				data: {kode_: kode},
				type: "POST",
				success: function (res) {
					if (res.trim() == 'success') {
						alert("Posting Berhasil!");
						location.reload();
					} else {
						alert("Posting Gagal!");
					}
				}
			});
		}
    }

    $("#posting_all").click(function () {
    	var tgl_awal = $("#tgl_awal").val();
		var tgl_akhir = $("#tgl_akhir").val();
        var tgl = $("#field-date").val();
        var lokasi = $("#lokasi").val();

		if (lokasi == '') {
			$("#ntf_lokasi").text("Lokasi Wajib Dipilih!");
		} else {
			if (confirm("Akan Memposting Opname?")) {
				$.ajax({
					url: "<?php echo base_url() . 'inventory/posting_opname/all' ?>",
					data: {tgl_periode_: tgl, lokasi_: lokasi},
					type: "POST",
                    beforeSend: function () {
                        $('#loading').show();
                    },
                    complete: function () {
                        $('#loading').hide();
                    },
					success: function (res) {
						if (res.trim() == 'success') {
							alert("Posting Berhasil!");
							location.reload();
						} else {
							alert("Posting Gagal!");
						}
					}
				});
			}
		}
    });

	$("#lokasi").change(function () {
		var lokasi = $(this).val();
		var tmp_lokasi = $("#tmp_lokasi").val();
		var tmp_status = $("#tmp_status").val();

		if (lokasi == tmp_lokasi) {
			if (tmp_status == 'enabled') {
				$("#posting_all").prop("disabled", false);
			} else {
				$("#posting_all").prop("disabled", true);
			}
		} else {
			$("#posting_all").prop("disabled", true);
		}

		$("#ntf_lokasi").text("");
	});

	function after_change(val) {
		$.ajax({
			url: "<?php echo base_url().'site/set_session_date/' ?>" + btoa('field-date') + '/' + btoa(val),
			type: "GET"
		});
	}
</script>

