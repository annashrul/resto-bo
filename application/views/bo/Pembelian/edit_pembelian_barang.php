<style>
	th, td {
		font-size: 9pt;
	}

	.form-control {
		font-size: 9pt;
	}

    .width-diskon {
        width: 60px;
    }
</style>
<!-- Page-Title -->
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

			<!-- Main Content -->
			<div class="row">
				<div class="col-md-12">
					<div class="panel panel-default">
						<div class="panel-body">
							<div class="row">
								<div class="col-md-12">
									<div class="panel-body">
										<form id="form_head" class="form_head">
										<div class="row">
											<input type="hidden" name="param" value="edit">
											<div class="col-sm-6">
												<div class="row" style="margin-bottom: 3px">
													<label class="col-sm-4">Nota System</label>
													<div class="col-sm-6">
														<input type="text" id="nota_sistem" name="nota_sistem" class="form-control" readonly value="">
													</div>
												</div>
												<div class="row" style="margin-bottom: 3px">
													<label class="col-sm-4">Tgl Pembelian</label>
													<div class="col-sm-6">
														<div class="input-group date">
															<div class="input-group-addon">
																<i class="fa fa-calendar"></i>
															</div>
															<input class="form-control pull-right datepicker_date_from" readonly onchange="trx_number();add_tmp_master()" name="tgl_pembelian" id="tgl_pembelian" type="text" value="<?=set_value('tgl_pembelian')?set_value('tgl_pembelian'):date("Y-m-d")?>">
															<!--custom_front_date('pembelian', $(this).val())-->
														</div>
													</div>
												</div>
												<div class="row" style="margin-bottom: 3px">
													<label class="col-sm-4">Lokasi Beli</label>
													<div class="col-sm-6">
														<select name="lokasi_beli" id="lokasi_beli" onchange="trx_number(); add_tmp_master(); hide_notif('alr_lokasi_beli')" class="select2">
															<option value="">Pilih</option>
															<?php
															foreach ($data_lokasi as $row) {
																echo "<option value=\"".$row['Kode']."|".$row['serial']."\">".$row['Nama']."</option>";
															}
															?>
														</select>
														<b class="error" id="alr_lokasi_beli"></b>
													</div>
												</div>
												<div class="row" style="margin-bottom: 3px">
													<label class="col-sm-4">Jenis Transaksi</label>
													<div class="col-sm-6">
														<select name="jenis_transaksi" id="jenis_transaksi" onchange="add_tmp_master()" class="form-control">
															<!--<option value="">Pilih</option>-->
															<option value="Tunai">Tunai</option>
															<option value="Kredit">Kredit</option>
															<option value="Konsinyasi">Konsinyasi</option>
														</select>
														<b class="error" id="alr_jenis_transaksi"></b>
													</div>
												</div>
											</div>
											<div class="col-sm-6">
												<div class="row" style="margin-bottom: 3px">
													<label class="col-sm-4">Supplier</label>
													<div class="col-sm-6">
														<select type="text" name="supplier" id="supplier" onchange="add_tmp_master(); hide_notif('alr_supplier'); get_no_po($(this).val())" class="select2">
															<option value="">Pilih</option>
															<?php
															foreach ($data_supplier as $row) {
																echo "<option value=\"".$row['Kode']."\">".$row['Kode']." | ".$row['Nama']."</option>";
															}
															?>
														</select>
														<b class="error" id="alr_supplier"></b>
													</div>
												</div>
												<div class="row" style="margin-bottom: 3px">
													<label class="col-sm-4">Nota Supplier</label>
													<div class="col-sm-6">
														<input type="text" id="nota_supplier" name="nota_supplier" onkeyup="hide_notif('alr_nota_supplier')" onblur="add_tmp_master(); cek_nota($(this).val())" class="form-control">
                                                        <b class="error" id="alr_nota_supplier"></b>
													</div>
												</div>
												<div class="row" style="margin-bottom: 3px">
													<label class="col-sm-4">No. PO</label>
													<div class="col-sm-7">
														<div class="input-group">
															<select id="no_po" name="no_po" class="select2" disabled>
																<option value="-">Pilih</option>
																<?php
																foreach ($data_po as $row) {
																	echo "<option value=\"".$row['no_po']."\">".$row['no_po']."</option>";
																}
																?>
															</select>
															<div class="input-group-btn">
																<button onclick="add_po()" type="button" id="cari_no_po" name="cari_no_po" class="btn btn-primary"><i class="md md-search"></i></button>
															</div>
														</div>
													</div>
												</div>
												<div id="container_tgl_jatuh_tempo" class="row" style="margin-bottom: 3px">
                                                    <label class="col-sm-4">Tgl Jatuh Tempo</label>
                                                    <div class="col-sm-6">
                                                        <div class="input-group date">
                                                            <div class="input-group-addon">
                                                                <i class="fa fa-calendar"></i>
                                                            </div>
                                                            <input class="form-control pull-right datepicker_date" readonly name="tgl_jatuh_tempo" id="tgl_jatuh_tempo" onchange="add_tmp_master()" type="text" value="<?=set_value('tgl_jatuh_tempo')?set_value('tgl_jatuh_tempo'):date("Y-m-d")?>">
                                                        </div>
                                                    </div>
												</div>
											</div>
										</div>
										</form>
									</div>
								</div>
							</div>
							<div class="row">
								<div class="col-md-12">
									<div class="panel-heading">
										<div class="row">
											<div class="col-md-1">
												<label>Set Focus</label>
											</div>
											<div class="col-md-3">
												<div class="form-inline">
													<?php $field = 'set_focus'; ?>
													<div class="radio radio-primary">
														<input class="form-control" type="radio" onclick="update_tmp_master('m14', $(this).val())" id="<?=$field?>_barcode" name="<?=$field?>" value="1" checked required />
														<label for="<?=$field?>_barcode"> Barcode </label>
													</div>
													<div class="radio radio-primary">
														<input class="form-control" type="radio" onclick="update_tmp_master('m14', $(this).val())" id="<?=$field?>_qty" name="<?=$field?>" value="2" required />
														<label for="<?=$field?>_qty"> Qty </label>
													</div>
													<?=form_error($field, '<div class="error" style="color:red;">', '</div>')?>
												</div>
											</div>
										</div>
										<div class="row">
											<div class="col-md-2">
												<select class="form-control input-sm" id="cat_cari" name="cat_cari">
													<option value="1">Kode Barang</option>
													<option value="2">Barcode</option>
													<option value="3"><?=$menu_group['as_deskripsi']?></option>
													<option value="4">Kode Packing</option>
												</select>
											</div>
											<div class="col-md-3">
												<div class="input-group">
													<input type="text" class="form-control input-sm" id="barcode" name="barcode">
													<div class="input-group-btn">
														<button onclick="cari_barang()" type="button" id="cari_barcode" name="cari_barcode" class="btn btn-primary btn-sm"><i class="md md-search"></i></button>
													</div>
												</div>
												<b class="error" id="alr_barang"></b>
											</div>
										</div>
									</div>
								</div>
							</div>
							<div class="row">
								<div class="col-md-12 col-sm-12 col-xs-12">
									<div class="table-responsive">
										<table class="table table-striped table-bordered">
											<thead>
											<tr>
                                                <th style="width: 10px">No</th>
                                                <th width="60px">Aksi</th>
                                                <th>Kode Barang</th>
                                                <th>Barcode</th>
                                                <th>Nama Barang</th>
                                                <th><?=$menu_group['as_deskripsi']?></th>
                                                <!--<th data-priority="0">Satuan</th>-->
                                                <th>Harga Beli</th>
                                                <th>Margin</th>
                                                <th>Harga Jual</th>
                                                <!--<th>Harga Jual 2</th>
                                                <th>Harga Jual 3</th>
                                                <th>Harga Jual 4</th>-->
                                                <th>Disc 1</th>
                                                <th>Disc 2</th>
                                                <!--<th>Disc 3</th>
                                                <th>Disc 4</th>-->
                                                <th>GST/PPN</th>
                                                <th>Jumlah</th>
                                                <th>Bonus</th>
                                                <th>Sub Total</th>
                                                <th>Netto</th>
                                                <!--<th data-priority="0">Konversi</th>-->
											</tr>
											</thead>
											<tbody id="list_barang">
											</tbody>
										</table>
									</div>
								</div>
							</div>
							<div class="row">
								<div class="col-md-7">
									<button class="btn btn-primary" onclick="if (confirm('Akan menyimpan transaksi?')){simpan_transaksi()}" id="simpan" type="submit">Simpan</button>
									<button class="btn btn-primary" onclick="if (confirm('Akan membatalkan transaksi?')){batal_transaksi()}" id="batal" type="submit">Batal</button>
									<button class="btn btn-primary" onclick="if (confirm('Akan menutup transaksi?')){tutup_transaksi()}" id="keluar" type="submit">Keluar</button>
									<!--<button class="btn btn-primary" id="tambah_return" type="submit">Tambah Return</button>
									<button class="btn btn-primary" id="rincian_return" type="submit">Rincian Return</button>-->
								</div>
								<div class="col-md-5">
									<div class="pull-right">
                                        <form class="form_head">
										<div class="row" style="margin-bottom: 3px">
											<label class="col-sm-4">Sub Total</label>
											<div class="col-sm-8">
												<input type="text" id="sub_total" name="sub_total" class="form-control text-right" readonly>
											</div>
										</div>
                                        <div class="row" style="margin-bottom: 3px">
                                            <label class="col-sm-4">Discount</label>
                                            <div class="col-sm-3">
                                                <input onblur="update_tmp_master('m10', $('#discount_harga').val())" onkeyup="hitung_total(1); konversi_diskon('persen')" onclick="this.select()" type="number" id="discount_persen" name="discount_persen" class="form-control" placeholder="%">
                                            </div>
                                            <div class="col-sm-5">
                                                <input onblur="update_tmp_master('m10', $(this).val())" onkeydown="isNumber(event)" onkeyup="isMoney('discount_harga', '+'); hitung_total(); konversi_diskon('harga')" onclick="this.select()" type="text" id="discount_harga" name="discount_harga" class="form-control text-right" placeholder="Rp">
                                            </div>
                                        </div>
										<div class="row" style="margin-bottom: 3px">
											<label class="col-sm-4">Pajak %</label>
											<div class="col-sm-3">
												<input onblur="update_tmp_master('m11', $(this).val())" onkeyup="hitung_total()" type="number" id="pajak" name="pajak" class="form-control" placeholder="%">
											</div>
										</div>
										<div class="row" style="margin-bottom: 3px">
											<!--<label class="col-sm-4">Total</label>-->
											<div class="col-sm-8">
												<input type="hidden" id="total" name="total" class="form-control text-right" readonly>
											</div>
										</div>
										<div class="row" style="margin-bottom: 3px">
											<!--<label class="col-sm-4">Total Return</label>-->
											<div class="col-sm-8">
												<input type="hidden" id="total_return" name="total_return" class="form-control text-right" readonly>
											</div>
										</div>
										<div class="row" style="margin-bottom: 3px">
											<label class="col-sm-4">Grand Total</label>
											<div class="col-sm-8">
												<input type="text" id="grand_total" name="grand_total" class="form-control text-right" readonly>
											</div>
										</div>
                                        </form>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>

<div id="modal-container" class="modal fade" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                <h4 class="modal-title" id="modal-label"></h4>
            </div>
			<div class="modal-footer">
				<input onclick="add_barang()" type="button" class="btn btn-primary pull-right" value="Pilih">
			</div>
			<div class="modal-body">
				<div class="row">
                    <div class="col-md-12">
                        <table class="table table-bordered table_check" width="100%">
                            <thead>
                            <tr>
                                <th>Aksi</th>
                                <th>Kode Barang</th>
                                <th>Barcode</th>
                                <th>Nama Barang</th>
                                <th><?=$menu_group['as_deskripsi']?></th>
                            </tr>
                            </thead>
                            <tbody id="list_barang_modal"></tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->

<script>
	$(document).ready(function () {
		$("#container_tgl_jatuh_tempo").hide();

		/*get master*/
		get_tmp_master();

		/*get detail*/
		get_tmp_detail(1);
	}).keydown(function (event) {
        if (event.keyCode == '107' || event.keyCode == '9') {
            $("#barcode").focus();
            $('html, body').animate({ scrollTop: 0 }, 'fast');
            return false;
        }
    });

	function cek_nota(no_nota) {
		if (no_nota != '') {
			$.ajax({
				url: "<?php echo base_url() . 'pembelian/check_nota/' ?>" + btoa(no_nota) + '/' + btoa('edit'),
				type: "GET",
				success: function (res) {
					if (res == '1') {
						$("#alr_nota_supplier").text("Nota Sudah Digunakan!");
						$("#simpan").prop("disabled", true);
					} else {
						$("#alr_nota_supplier").text("");
						$("#simpan").prop("disabled", false);
					}
				}
			});
		}
	}

	function get_tmp_master(param='edit') {
		$.ajax({
			url: "<?php echo base_url() . 'pembelian/get_tr_temp_m/' ?>" + btoa(param),
			type: "GET",
			dataType: "JSON",
			success: function (data) {
				$("#nota_sistem").val(data['m1']);
				$('#tgl_pembelian').val(data['m2']);
				$("#lokasi_beli").select2("val", data['m3']);
				$("#jenis_transaksi").val(data['m4']);
				$("#supplier").select2("val", data['m5']);
				$("#nota_supplier").val(data['m6']);
				$("#no_po").select2("val", data['m7']);
				$("#tgl_jatuh_tempo").val(data['m8']);

				if (data['m4'] == 'Kredit') {
					$("#container_tgl_jatuh_tempo").show();
				}

				if (data['m7'] != '-') {
					$("#no_po").prop("disabled", true);
					$("#cari_no_po").prop("disabled", true);
				}
				
				if (data['m14'] == 1 || data['m14'] == '' || data['m14'] == null) {
					document.getElementById('set_focus_barcode').checked = true;
				} else if(data['m14'] == 2) {
					document.getElementById('set_focus_qty').checked = true;
				}
			}
		});
	}

    function add_tmp_master(param = 'edit') {
        var data = new FormData($('#form_head')[0]);
        var nota_sistem = $("#nota_sistem").val();

        if (nota_sistem != '') {
            $.ajax({
                url: "<?php echo base_url() . 'pembelian/add_tr_temp_m/' ?>" + btoa(param),
                data: data,
                type: "POST",
                mimeType: "multipart/form-data",
                contentType: false,
                cache: false,
                processData: false,
                dataType: "JSON"
            });
        }
    }

    function update_tmp_master(column, data, param = 'edit') {
        if (column == 'm10') {
            data = hapuskoma(data);
        }
        $.ajax({
            url: "<?php echo base_url() . 'pembelian/update_tr_temp_m/' ?>" + btoa(column) + "/" + btoa(data) + "/" + btoa(param),
            type: "GET"
        });
    }

    function to_barcode(event) {
        if (event.keyCode == 13) {
            $("#barcode").focus();
            $('html, body').animate({ scrollTop: 0 }, 'fast');
        }

        if (event.ctrlKey && event.keyCode == 13) {
            $("#barcode").focus();
            $('html, body').animate({ scrollTop: 0 }, 'fast');
        }
    }

    function to_col(event, id, col) {
        if (event.keyCode == 13) {
            $("#"+col+id).focus().select();
        }

        if (event.ctrlKey && event.keyCode == 13) {
            $("#barcode").focus();
            $('html, body').animate({ scrollTop: 0 }, 'fast');
        }
    }

    function to_qty(event, id) {
        if (event.keyCode == 13) {
            $("#d15"+id).focus().select();
        }

        if (event.ctrlKey && event.keyCode == 13) {
            $("#barcode").focus();
            $('html, body').animate({ scrollTop: 0 }, 'fast');
        }
    }

	function get_tmp_detail(param = 0, col = 0) {
		$.ajax({
			url: "<?php echo base_url() . 'pembelian/get_tr_temp_d/' ?>" + btoa('edit'),
			type: "GET",
			dataType: "JSON",
			success: function (data) {
                $("#list_barang").html(data.list_barang);
                $("#sub_total").val(to_rp(data.jumlah['sub_total'].toFixed(2)));
				$("#discount_persen").val(data.jumlah['discount_persen'].toFixed(2));
                $("#discount_harga").val(to_rp(data.jumlah['discount_harga'].toFixed(2)));
				$("#pajak").val(((data.jumlah['pajak']/(data.jumlah['sub_total']-data.jumlah['discount_harga']))*100).toFixed(2));
				$("#total").val(data.jumlah['total'].toFixed(2));
				$("#total_return").val(0);
				$("#grand_total").val(to_rp(data.jumlah['grand_total'].toFixed(2)));
                konversi_diskon('harga');
					
				if(document.getElementById("set_focus_barcode").checked == true){
					$("#barcode").focus();
				} else if(document.getElementById("set_focus_qty").checked == true){
					if(param != 1) {
						if (param == 2) {
							$("#d15" + col).focus().select();
						} else {
							$("#d15" + $("#col").val()).focus().select();
						}
					} else {
						$("#barcode").focus();
					}
				}
			},
            complete: function () {
                var col = $("#col").val();
                for (var i=1; i<=col; i++) {
                    hitung_barang('d15', i, $("#d15"+i).val(), $("#col").val());
                }
            }
		});
	}

    function update_tmp_detail(barcode, column, value, param = 'edit') {
        $.ajax({
            url: "<?php echo base_url() . 'pembelian/update_tr_temp_d/' ?>" + btoa(barcode) + "/" + btoa(column) + "/" + btoa(hapuskoma(value)) + "/" + btoa(param),
            type: "GET"
        });
    }

	function hapus_barang(barcode, param = 'edit') {
		$.ajax({
			url: "<?php echo base_url() . 'pembelian/delete_tr_temp_d/' ?>" + btoa(barcode) + '/' + btoa(param),
			type: "GET",
			success: function (data) {
				if (data) {
					get_tmp_detail();
				}
			}
		});
	}

	function get_no_po(supplier, param = 'edit') {
		$.ajax({
			url: "<?php echo base_url() . 'pembelian/get_no_po/' ?>" + btoa(supplier) + '/' + btoa(param),
			type: "GET",
			success: function (data) {
				$("#no_po").html(data).prop("disabled", false);
			}
		});
	}

	function add_po(param = 'edit') {
		var no_po = $("#no_po").val();
		var nota_sistem = $("#nota_sistem").val();

		if ($("#lokasi_beli").val() == '') {
			$("#alr_lokasi_beli").text("Lokasi wajib dipilih!")
		}

		if ($("#supplier").val() == '') {
			$("#alr_supplier").text("Supplier wajib dipilih!")
		}

		if (nota_sistem != '') {
			update_tmp_master('m7', no_po);
			$.ajax({
				url: "<?php echo base_url() . 'pembelian/add_po_list_barang/' ?>" + btoa(nota_sistem) + '/' + btoa(no_po) + '/' + btoa(param),
				type: "GET",
				success: function (data) {
					if (data) {
						get_tmp_detail();
						if (no_po != '-') {
							$("#no_po").prop("disabled", true);
							$("#cari_no_po").prop("disabled", true);
						}
					}
				}
			});
		}
	}

	function cari_barang() {
        var lokasi_beli = $("#lokasi_beli").val();
        var supplier = $("#supplier").val();
        hide_notif("alr_barang");

        if (lokasi_beli == '') {
            $("#alr_lokasi_beli").text("Lokasi wajib dipilih!");
        }

        if (supplier == '') {
            $("#alr_supplier").text("Supplier wajib dipilih!");
        }

        if (lokasi_beli != '' && supplier != '') {
            $.ajax({
                url: "<?php echo base_url() . 'pembelian/get_list_barang' ?>",
                data: {supplier_: supplier},
                type: "POST",
                dataType: "JSON",
                success: function (data) {
                    $("#modal-container").modal('show');
                    $("#modal-label").text('Daftar Barang dari Supplier ' + data.supplier);
                    $("#list_barang_modal").html(data.list_barang);
                }
            });
        }
    }

    function add_barang(param = 'edit') {
        var nota_sistem = $("#nota_sistem").val();
        var list = cek_checkbox_checked('barang');

        $.ajax({
            url: "<?php echo base_url() . 'pembelian/add_list_barang' ?>",
            data: {nota_sistem_: nota_sistem, list_: list, param_: param},
            type: "POST",
            success: function (data) {
                if (data) {
                    get_tmp_detail();
                    $("#modal-container").modal('hide');
                }
            }
        });
    }

	$("#barcode").keyup(function (event) {
		var cat_cari = $("#cat_cari").val();
		var nota_sistem = $("#nota_sistem").val();
		var barcode = $("#barcode").val();
		var lokasi_beli = $("#lokasi_beli").val();
		var supplier = $("#supplier").val();
		hide_notif("alr_barang");

		if (event.keyCode == 13) {
			if (lokasi_beli == '') {
				$("#alr_lokasi_beli").text("Lokasi wajib dipilih!");
			}

			if (supplier == '') {
				$("#alr_supplier").text("Supplier wajib dipilih!");
			}

			if (lokasi_beli != '' && supplier != '' && barcode != '') {
				$.ajax({
					url: "<?php echo base_url() . 'pembelian/get_barang/' ?>" + btoa(nota_sistem) + "/" + btoa(barcode) + "/" + btoa(lokasi_beli) + "/" + btoa(supplier) + "/" + btoa(cat_cari) + "/" + btoa('edit'),
					type: "GET",
					dataType: "JSON",
					success: function (data) {
                        if (data.status == 1) {
						    if (data.barang == 'tersedia') {
                                $("#barcode").val("");
                                get_tmp_detail(2, data.col); //focus ke qty barang yg diinput
                                //get_tmp_detail(1); //focus ke barcode
                            } else { 
                                $("#barcode").val("");
                                //get_tmp_detail(1); //focus ke barcode
                                get_tmp_detail(); //focus ke qty barang yg diinput
                            }
						} else if (data.status == 2) {
							alert(data.notif);
							$("#barcode").val("");
							get_tmp_detail();
						} else {
							$("#barcode").val("").focus();
							$("#alr_barang").text(data.notif)
						}
					}
				});
			}
		}
	});

    function hitung_barang(column, id, value, length) {
        var d5 = 0; if (column != 'd5'){d5 = parseFloat(hapuskoma($("#d5"+id).val()));}else {d5 = parseFloat(hapuskoma(value));}
        var d6 = 0; if (column != 'd6'){d6 = parseFloat(hapuskoma($("#d6"+id).val()));}else {d6 = parseFloat(hapuskoma(value));}
        var d10 = 0; if (column != 'd10'){d10 = $("#d10"+id).val();}else {d10 = value;}
        var d11 = 0; if (column != 'd11'){d11 = $("#d11"+id).val();}else {d11 = value;}
        var d13 = 0; if (column != 'd13'){d13 = $("#d13"+id).val();}else {d13 = value;}
        var d14 = 0; if (column != 'd14'){d14 = $("#d14"+id).val();}else {d14 = value;}
        var d15 = 0; if (column != 'd15'){d15 = $("#d15"+id).val();}else {d15 = value;}
        var d16 = 0; if (column != 'd16'){d16 = $("#d16"+id).val();}else {d16 = value;}
        var diskon_harga = parseFloat(hapuskoma($("#discount_harga").val()));
        var pajak = $("#pajak").val();

        var jumlah_beli = d5 * d15;
        var d1 = d5*(1-(d10/100));
        var harga_beli = d1*(1-(d11/100));
        var diskon = double_diskon(jumlah_beli, [d10, d11]);
        var hitung_sub_total = hitung_ppn(diskon, 0, d14);
        var netto = harga_beli + (harga_beli * (d14 / 100));

        $("#sub_total"+id).val(to_rp(hitung_sub_total.toFixed(2)));

        if (d15 != '') {
            $("#netto" + id).val(to_rp(netto.toFixed(2)));
        } else {
            $("#netto" + id).val(0);
        }

        var sub_total = 0;
        for (var i=1; i<=length; i++) {
            sub_total = sub_total + parseFloat(hapuskoma(document.getElementById("sub_total"+i).value));
        }

        $("#sub_total").val(to_rp(sub_total.toFixed(2)));
        hitung_total();
        konversi_diskon('harga');
        hitung_margin(netto, d6, 'uang', id, d16);
        update_tmp_detail(d16,'d22',netto);
    }

    function hitung_margin(hrg_beli, field, tipe, id, d16) {
        hrg_beli = parseFloat(hapuskoma(hrg_beli));
        var hasil;
        if (tipe == 'persen') {
            if ($('#' + field).val() > 100) {
                $('#' + field).val(99)
            }
            if ($('#' + field).val() < 0) {
                $('#' + field).val(0)
            }

            hasil = (hrg_beli / (1 - (parseFloat(hapuskoma($('#' + field).val())) / 100))).toFixed(2);

            $("#d6"+id).val(to_rp(hasil));
            setTimeout(function () {
                update_tmp_detail(d16,'d6',hasil);
            }, 2000);
        } else {
            field = hapuskoma(field);
            if (field != 0) {
                hasil = ((1 - ((hrg_beli)/field))*100).toFixed(2);
                if (hasil < 0) {
                    hasil = 0;
                }
            } else {
                hasil = 0;
            }
            $("#d13"+id).val(hasil);
            setTimeout(function () {
                update_tmp_detail(d16,'d13',hasil);
            }, 2000);
        }
    }

    function konversi_diskon(jenis) {
        var sub_total = parseFloat(hapuskoma($("#sub_total").val()));
        var discount_persen = document.getElementById("discount_persen").value;
        var discount_harga = parseFloat(hapuskoma(document.getElementById("discount_harga").value));

        if (jenis == 'persen') {
            dsc_harga = diskon_harga(sub_total, discount_persen);
            $("#discount_harga").val(to_rp(dsc_harga.toFixed(2)));
        }else if (jenis == 'harga') {
            dsc_persen = diskon_persen(sub_total, discount_harga);
            $("#discount_persen").val(dsc_persen.toFixed(2));
        }
    }

    function hitung_total(param=0) {
        var sub_total = parseFloat(hapuskoma($("#sub_total").val()));
        var discount_persen = $("#discount_persen").val();
        var discount_harga = parseFloat(hapuskoma($("#discount_harga").val()));
        var total_return = $("#total_return").val();
        var pajak = $("#pajak").val();

        if (param==0){ var diskon = discount_harga}else {diskon = ((discount_persen/100)*sub_total)}

        total = (sub_total-diskon) + ((pajak/100) * (sub_total-diskon));
        $("#total").val(to_rp(total.toFixed(2)));
        $("#grand_total").val(to_rp((total - total_return).toFixed(2)));
    }

	$("#jenis_transaksi").change(function () {
		var jenis_transaksi = $(this).val();

		if (jenis_transaksi == "Kredit") {
			$("#container_tgl_jatuh_tempo").show();
		}else {
			$("#container_tgl_jatuh_tempo").hide();
		}
	});

	function trx_number() {
		var tgl_pembelian = $("#tgl_pembelian").val();
		var get_lokasi = $("#lokasi_beli").val();
		var lokasi = get_lokasi.split("|");

		if (tgl_pembelian != '' && get_lokasi != ''){
			$.ajax({
				url: "<?php echo base_url().'site/max_kode/' ?>" + btoa("BL") + "/" + btoa(tgl_pembelian) + "/" + btoa(lokasi[1]),
				type: "GET",
				success: function (data) {
					$("#nota_sistem").val(data);
				}
			});
		}else {
			$("#nota_sistem").val("");
		}
	}

	function batal_transaksi(param = 'edit') {
        $.ajax({
            url: "<?php echo base_url().'pembelian/delete_trans_pembelian/' ?>" + btoa(param),
            type: "GET",
            success: function (data) {
                if (data) {
                    window.location = "<?php echo base_url().'pembelian/pembelian_barang_report' ?>";
                }
            }
        });
    }

    function validate()
    {
        if( document.getElementById('nota_sistem').value == "" )
        {
            document.getElementById('nota_sistem').focus() ;
            $('html, body').animate({ scrollTop: 0 }, 'fast');
            return false;
        }

        if( document.getElementById('tgl_pembelian').value == "" )
        {
            document.getElementById('tgl_pembelian').focus() ;
            $('html, body').animate({ scrollTop: 0 }, 'fast');
            return false;
        }

        if( document.getElementById('lokasi_beli').value == "" )
        {
            document.getElementById('lokasi_beli').focus() ;
            $('html, body').animate({ scrollTop: 0 }, 'fast');
            return false;
        }

        if( document.getElementById('jenis_transaksi').value == "" )
        {
            document.getElementById('jenis_transaksi').focus() ;
            $('html, body').animate({ scrollTop: 0 }, 'fast');
            return false;
        }

        if( document.getElementById('supplier').value == "" )
        {
            document.getElementById('supplier').focus() ;
            $('html, body').animate({ scrollTop: 0 }, 'fast');
            return false;
        }

        if( document.getElementById('nota_supplier').value == "" )
        {
            document.getElementById('nota_supplier').focus() ;
            $('html, body').animate({ scrollTop: 0 }, 'fast');
            return false;
        }

        if( document.getElementById('col').value == 0 )
        {
            document.getElementById('barcode').focus() ;
            $('html, body').animate({ scrollTop: 0 }, 'fast');
            return false;
        }

        return true;
    }

	function simpan_transaksi() {
	    /*var nota_sistem = $("#nota_sistem").val();
	    var sub_total = $("#sub_total").val();
        var nota_supplier = $("#nota_supplier").val();

        if (sub_total == 0) {
            $("#alr_barang").text("Barang belum dimasukan!");
        }

        if (nota_supplier == '') {
            $("#alr_nota_supplier").text("Nota tidak boleh kosong!");
            $("#nota_supplier").focus();
        }*/

        if (validate()) {
            $.ajax({
                url: "<?php echo base_url().'pembelian/trans_pembelian_x' ?>",
                type: "POST",
				data: $(".form_head").serialize(),
                beforeSend: function () {
                    $('#loading').show();
                },
                complete: function () {
                    $('#loading').hide();
                },
                success: function (data) {
					if (data != '10') {
						if (confirm("Transaksi Berhasil! Akan Mencetak Nota?")) {
							window.open('<?=base_url().'cetak/nota_pembelian/'?>' + btoa(data.substring(1)));
						}
						window.location = "<?php echo base_url().'pembelian/pembelian_barang_report' ?>";
					} else {
						alert("Transaksi Gagal!");
						location.reload();
					}
                }
            });
        }
    }
</script>