<style>
	th, td {
		font-size: 9pt;
	}

	.form-control {
		font-size: 9pt;
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
										<?=form_open(null, array('id'=>'form_head'))?>
										<div class="row">
											<div class="col-sm-6">
												<div class="row" style="margin-bottom: 3px">
													<label class="col-sm-4">Nota Retur</label>
													<div class="col-sm-6">
														<input type="text" id="nota_retur" name="nota_retur" class="form-control" readonly value="">
													</div>
												</div>
												<div class="row" style="margin-bottom: 3px">
													<label class="col-sm-4">Tgl Retur</label>
													<div class="col-sm-6">
														<div class="input-group date">
															<div class="input-group-addon">
																<i class="fa fa-calendar"></i>
															</div>
															<input class="form-control pull-right datepicker_date_from" readonly onchange="trx_number(); add_tmp_master()" name="tgl_retur" id="tgl_retur" type="text" value="<?=set_value('tgl_retur')?set_value('tgl_retur'):date("Y-m-d")?>">
															<!--custom_front_date('pembelian', $(this).val())-->
														</div>
													</div>
												</div>
												<div class="row" style="margin-bottom: 3px">
													<label class="col-sm-4">Lokasi Cabang</label>
													<div class="col-sm-6">
														<select name="lokasi" id="lokasi" onchange="trx_number(); update_stock(); hide_notif('alr_lokasi')" class="select2">
															<option value="">Pilih</option>
															<?php
															foreach ($data_lokasi as $row) {
																echo "<option value=\"".$row['Kode']."|".$row['serial']."\">".$row['Nama']."</option>";
															}
															?>
														</select>
														<b class="error" id="alr_lokasi"></b>
													</div>
												</div>
											</div>
										</div>
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
														<input class="form-control" type="radio" onclick="add_tmp_master()" id="<?=$field?>_barcode" name="<?=$field?>" value="1" checked required />
														<label for="<?=$field?>_barcode"> Barcode </label>
													</div>
													<div class="radio radio-primary">
														<input class="form-control" type="radio" onclick="add_tmp_master()" id="<?=$field?>_qty" name="<?=$field?>" value="2" required />
														<label for="<?=$field?>_qty"> Qty </label>
													</div>
													<?=form_error($field, '<div class="error" style="color:red;">', '</div>')?>
												</div>
											</div>
										</div>
										<?=form_close()?>
										<div class="row">
											<div class="col-md-2">
												<select class="form-control" id="cat_cari" name="cat_cari">
													<option value="1">Kode Barang</option>
													<option value="2">Barcode</option>
													<option value="3"><?=$menu_group['as_deskripsi']?></option>
													<option value="4">Kode Packing</option>
												</select>
											</div>
											<div class="col-md-3">
												<div class="input-group">
													<input type="text" class="form-control" id="barcode" name="barcode">
													<div class="input-group-btn">
														<button onclick="cari_barang()" type="button" class="btn btn-primary"><i class="md md-search"></i></button>
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
												<th>Aksi</th>
												<th>Kode Barang</th>
												<th>Barcode</th>
												<th>Nama Barang</th>
												<th><?=$menu_group['as_deskripsi']?></th>
												<th>Harga Beli</th>
												<th>Harga Jual 1</th>
												<th>Stock</th>
												<th>Qty</th>
												<th>Sub Total</th>
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
									<!--<button class="btn btn-primary" id="cek_min_stock" type="submit">Cek Min Stock</button>-->
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
	});

	function get_tmp_master() {
		$.ajax({
			url: "<?php echo base_url() . 'retur_cabang/get_tr_temp_m_retur' ?>",
			type: "GET",
			dataType: "JSON",
			success: function (data) {
				$("#nota_retur").val(data['m1']);
				$('#tgl_retur').val(data['m2']);
				$("#lokasi").select2("val", data['m3']);
				if (data['m5'] == 1 || data['m5'] == '' || data['m5'] == null) {
					document.getElementById('set_focus_barcode').checked = true;
				} else if(data['m5'] == 2) {
					document.getElementById('set_focus_qty').checked = true;
				}
			}
		});
	}

    function add_tmp_master() {
        var data = new FormData($('#form_head')[0]);
        var nota_po = $("#nota_po").val();

        if (nota_po!= '') {
            $.ajax({
                url: "<?php echo base_url() . 'retur_cabang/add_tr_temp_m_retur' ?>",
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

    function update_stock() {
        var lokasi = $("#lokasi").val();
        $.ajax({
            url: "<?=base_url()?>retur_cabang/update_stock_retur",
            type: "POST",
            data: {lokasi_:lokasi},
            success: function (res) {
                get_tmp_detail();
            }
        });
    }

    function update_tmp_master(column, data) {
        $.ajax({
            url: "<?php echo base_url() . 'retur_cabang/update_tr_temp_m_retur/' ?>" + btoa(column) + "/" + btoa(data),
            type: "GET"
        });
    }

	function get_tmp_detail(param = 0, col=0) {
		$.ajax({
			url: "<?php echo base_url() . 'retur_cabang/get_tr_temp_d_retur' ?>",
			type: "GET",
			dataType: "JSON",
			success: function (data) {
                $("#list_barang").html(data.list_barang);
                $("#sub_total").val(data.sub_total);
				if(document.getElementById("set_focus_barcode").checked == true){
					$("#barcode").focus();
				} else if(document.getElementById("set_focus_qty").checked == true){
					if(param != 1) {
						if (param == 2) {
							$("#d9" + col).focus().select();
						} else {
							$("#d9" + $("#col").val()).focus().select();
						}
					} else {
						$("#barcode").focus();
					}
				}
			}
		});
	}

	function to_barcode(event) {
		if (event.keyCode == 13) {
			$("#barcode").focus();
            $('html, body').animate({ scrollTop: 0 }, 'fast');
		}
	}

	function to_qty(event, id) {
		if (event.keyCode == 13) {
			$("#d9"+id).focus().select();
		}
	}

    function update_tmp_detail(barcode, column, value) {
        $.ajax({
            url: "<?php echo base_url() . 'retur_cabang/update_tr_temp_d_retur/' ?>" + btoa(barcode) + "/" + btoa(column) + "/" + btoa(value),
            type: "GET"
        });
    }

	function hapus_barang(barcode) {
		$.ajax({
			url: "<?php echo base_url() . 'retur_cabang/delete_tr_temp_d_retur/' ?>" + btoa(barcode),
			type: "GET",
			success: function (data) {
				if (data) {
					get_tmp_detail();
				}
			}
		});
	}

	function cari_barang() {
        var lokasi = $("#lokasi").val();
        var supplier = $("#supplier").val();
        hide_notif("alr_barang");

        if (lokasi == '') {
            $("#alr_lokasi").text("Lokasi wajib dipilih!");
        }

        if (supplier == '') {
            $("#alr_supplier").text("Supplier wajib dipilih!");
        }

        if (lokasi != '' && supplier != '') {
            $.ajax({
                url: "<?php echo base_url() . 'retur_cabang/get_list_barang' ?>",
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

    function add_barang() {
        var nota_po = $("#nota_po").val();
        var list = cek_checkbox_checked('barang');

        $.ajax({
            url: "<?php echo base_url() . 'retur_cabang/add_list_barang_retur' ?>",
            data: {nota_po_: nota_po, list_: list},
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
		var nota_retur = $("#nota_retur").val();
		var barcode = $("#barcode").val();
		var lokasi = $("#lokasi").val();
		hide_notif("alr_barang");

		if (event.keyCode == 13) {
			if (lokasi == '') {
				$("#alr_lokasi").text("Lokasi wajib dipilih!");
			}

			/*if (supplier == '') {
				$("#alr_supplier").text("Supplier wajib dipilih!");
			}*/

			if (lokasi != '') {
				$.ajax({
					url: "<?php echo base_url() . 'retur_cabang/get_barang_retur/' ?>" + btoa(nota_retur) + "/" + btoa(barcode) + "/" + btoa(lokasi) + "/" + btoa(cat_cari),
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
							$("#barcode").val("").focus();
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
	    var d5 = 0; if (column != 'd5'){d5 = $("#d5"+id).val();}else {d5 = value;}
	    var d9 = 0; if (column != 'd9'){d9 = $("#d9"+id).val();}else {d9 = value;}

	    var sub_total = d5 * d9;

        $("#sub_total"+id).val(sub_total.toFixed(2));

        var t_sub_total = 0;
        for (var i=1; i<=length; i++) {
            t_sub_total = t_sub_total + parseFloat(document.getElementById("sub_total"+i).value);
        }

        $("#sub_total").val(sub_total.toFixed(2));
	}

	function trx_number() {
		var tgl_retur = $("#tgl_retur").val();
		var get_lokasi = $("#lokasi").val();
		var lokasi = get_lokasi.split("|");

		if (tgl_retur != '' && get_lokasi != ''){
			$.ajax({
				url: "<?php echo base_url().'site/max_kode/' ?>" + btoa("RR") + "/" + btoa(tgl_retur) + "/" + btoa(lokasi[1]),
				type: "GET",
				success: function (data) {
					$("#nota_retur").val(data);
                    add_tmp_master();
                }
			});
		}else {
			$("#nota_retur").val("");
		}
	}

	function batal_transaksi() {
        $.ajax({
            url: "<?php echo base_url().'retur_cabang/delete_trans_retur' ?>",
            type: "GET",
            success: function () {
                location.reload();
            }
        });
    }

    function validate()
    {
        if( document.getElementById('nota_retur').value == "" )
        {
            document.getElementById('nota_retur').focus() ;
            $('html, body').animate({ scrollTop: 0 }, 'fast');
            return false;
        }

        if( document.getElementById('tgl_retur').value == "" )
        {
            document.getElementById('tgl_retur').focus() ;
            $('html, body').animate({ scrollTop: 0 }, 'fast');
            return false;
        }

        if( document.getElementById('lokasi').value == "" )
        {
            document.getElementById('lokasi').focus() ;
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
	    /*var nota_retur = $("#nota_retur").val();
	    var col = $("#col").val();
		var supplier = $("#supplier").val();

        if (col == 0) {
            $("#alr_barang").text("Barang belum dimasukan!");
			$("#barcode").focus();
        }

		if (supplier == '') {
			$("#alr_supplier").text("Supplier wajib dipilih!");
			$("#supplier").focus();
		}*/

        if (validate()) {
            $.ajax({
                url: "<?php echo base_url().'retur_cabang/trans_retur_x' ?>",
                type: "POST",
                data: $("#form_head").serialize(),
                dataType: "JSON",
                beforeSend: function () {
                    $('#loading').show();
                },
                complete: function () {
                    $("#loading").hide();
                },
                success: function (data) {
                    if (data.status) {
                        //alert("Transaksi Berhasil!");
                        if (confirm('Transaksi Berhasil! Akan mencetak transaksi?')){
                            cetak_transaksi('retur_cabang_report', 'none', data.kode);
                        }
                    } else {
                        alert('Transaksi gagal');
                        location.reload();
                    }
                }
            });
        }
    }
</script>