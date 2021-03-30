<style>
	th, td {
		font-size: 9pt;
	}

	.form-control {
		font-size: 9pt;
	}

    .width-uang2 {
        width: 85px !important;
        text-align: right;
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
										<form id="form_head">
                                            <div class="row">
                                                <input type="hidden" name="param" value="">
                                                <div class="col-sm-6">
                                                    <div class="row" style="margin-bottom: 3px">
                                                        <label class="col-sm-4">Tgl Transaksi</label>
                                                        <div class="col-sm-6">
                                                            <div class="input-group date">
                                                                <div class="input-group-addon">
                                                                    <i class="fa fa-calendar"></i>
                                                                </div>
                                                                <input class="form-control pull-right datepicker_date_from" readonly onchange="trx_number(); add_tmp_master()" name="tgl_jual" id="tgl_jual" type="text" value="<?=set_value('tgl_jual')?set_value('tgl_jual'):date("Y-m-d")?>">
                                                                <!--custom_front_date('pembelian', $(this).val())-->
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="row" style="margin-bottom: 3px">
                                                        <label class="col-sm-4">Lokasi</label>
                                                        <div class="col-sm-6">
                                                            <select name="lokasi" id="lokasi" onchange="trx_number(); hide_notif('alr_lokasi')" class="select2">
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
                                                <div class="col-sm-6">
                                                    <div class="row" style="margin-bottom: 3px">
                                                        <label class="col-sm-4">Customer</label>
                                                        <div class="col-sm-6">
                                                            <select type="text" name="customer" id="customer" onchange="update_tmp_master('m4', $(this).val()); hide_notif('alr_customer');" class="select2">
                                                                <option value="">Pilih</option>
                                                                <?php
                                                                foreach ($data_supplier as $row) {
                                                                    echo "<option value=\"".$row['kd_cust']."\">".$row['kd_cust']." | ".$row['Nama']."</option>";
                                                                }
                                                                ?>
                                                            </select>
                                                            <b class="error" id="alr_customer"></b>
                                                        </div>
                                                    </div>
                                                    <div class="row" style="margin-bottom: 3px">
                                                        <label class="col-sm-4">Keterangan</label>
                                                        <div class="col-sm-6">
                                                            <textarea onblur="update_tmp_master('m10', $(this).val())" class="form-control" id="ket" name="ket"></textarea>
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
														<input class="form-control" type="radio" onclick="update_tmp_master('m11', $(this).val())" id="<?=$field?>_barcode" name="<?=$field?>" value="1" checked required />
														<label for="<?=$field?>_barcode"> Barcode </label>
													</div>
													<div class="radio radio-primary">
														<input class="form-control" type="radio" onclick="update_tmp_master('m11', $(this).val())" id="<?=$field?>_qty" name="<?=$field?>" value="2" required />
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
													<!--<div class="input-group-btn">
														<button onclick="cari_barang()" type="button" id="cari_barcode" name="cari_barcode" class="btn btn-primary btn-sm"><i class="md md-search"></i></button>
													</div>-->
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
                                                <th><?=$menu_group['as_deskripsi']?></th>
                                                <th>Nama Barang</th>
                                                <th class="width-uang">Harga Jual</th>
                                                <th class="width-diskon">Diskon 1 %</th>
                                                <th class="width-diskon">Diskon 2 %</th>
                                                <th class="width-diskon">Qty Jual</th>
                                                <th class="width-uang">Nilai Jual</th>
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
									<button class="btn btn-primary" onclick="bayar()" id="simpan" type="submit">Bayar</button>
									<button class="btn btn-primary" onclick="if (confirm('Akan membatalkan transaksi?')){batal_transaksi()}" id="batal" type="submit">Batal</button>
									<button class="btn btn-primary" onclick="if (confirm('Akan menutup transaksi?')){tutup_transaksi()}" id="keluar" type="submit">Keluar</button>
									<!--<button class="btn btn-primary" id="tambah_return" type="submit">Tambah Return</button>
									<button class="btn btn-primary" id="rincian_return" type="submit">Rincian Return</button>-->
								</div>
                                <div class="col-md-5">
                                    <div class="pull-right">
                                        <div class="row" style="margin-bottom: 3px">
                                            <label class="col-sm-4">Sub Total</label>
                                            <div class="col-sm-8">
                                                <input type="text" id="sub_total" name="sub_total" class="form-control text-right" readonly>
                                                <b class="error" id="alr_subtotal"></b>
                                            </div>
                                        </div>
                                        <div class="row" style="margin-bottom: 3px">
                                            <label class="col-sm-4">Discount</label>
                                            <div class="col-sm-3">
                                                <input onblur="update_tmp_master('m8', $('#discount_harga').val())" onkeyup="hitung_total(1); konversi_diskon('persen')" onclick="this.select()" type="number" id="discount_persen" name="discount_persen" class="form-control" placeholder="%">
                                            </div>
                                            <div class="col-sm-5">
                                                <input onblur="update_tmp_master('m8', $(this).val())" onkeyup="hitung_total(); konversi_diskon('harga')" onclick="this.select()" type="text" id="discount_harga" name="discount_harga" class="form-control text-right" placeholder="Rp">
                                            </div>
                                        </div>
                                        <div class="row" style="margin-bottom: 3px; display: none;">
                                            <label class="col-sm-4">Pajak %</label>
                                            <div class="col-sm-3">
                                                <input onblur="update_tmp_master('m9', $(this).val())" onkeyup="hitung_total()" onclick="this.select()" type="number" id="pajak" name="pajak" value="0" class="form-control" placeholder="%">
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
<div id="modal_payment" class="modal fade" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-md">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                <h4 class="modal-title" id="modal-label">Pembayaran</h4>
            </div>
            <div class="row" style="margin-top: 5px">
                <h3>Total Penjualan</h3>
                <h2 class="pull-left" id="total_pembelian"></h2>
                <input onclick="simpan_transaksi()" type="button" class="btn btn-primary pull-right" value="Simpan">
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-12">
                        <div class="row" style="margin-bottom: 10px" id="container_pembayaran">
                            <select class="form-control" id="pembayaran" name="pembayaran" onchange="cek_pembayaran($(this).val())">
                                <option value="Tunai">Tunai</option>
                                <option value="Transfer">Transfer</option>
                                <option value="Cek">Cek</option>
                                <option value="Giro">Giro</option>
                                <option value="Kredit">Kredit</option>
                            </select>
                        </div>
                        <div class="row" style="margin-bottom: 10px" id="container_bayar">
                            <input type="text" class="form-control" id="jumlah_bayar" name="jumlah_bayar" onkeydown="isNumber(event)" onkeyup="isMoney('jumlah_bayar', '+'); cek_bayar('bayar', $(this).val()); return shortcut_simpan(event)" onclick="this.select()" placeholder="Jumlah Bayar">
                            <b class="error" id="alr_jumlah_bayar"></b>
                        </div>
                        <div class="row" style="margin-bottom: 10px" id="container_bank">
                            <select class="form-control" id="bank" name="bank" onchange="cek_bayar('bank', $(this).val())">
                                <?php
                                $data_bank = $this->m_crud->read_data("Bank", "Nama");
                                foreach ($data_bank as $row) {
                                    echo "<option value=\"".$row['Nama']."\">".$row['Nama']."</option>";
                                }
                                ?>
                            </select>
                            <b class="error" id="alr_bank"></b>
                        </div>
                        <div class="row" style="margin-bottom: 10px" id="container_tempo">
                            <input type="text" min="0" class="form-control datepicker_front" id="jatuh_tempo" onchange="cek_bayar('tempo', $(this).val())" name="jatuh_tempo" placeholder="Jatuh Tempo">
                            <b class="error" id="alr_jatuh_tempo"></b>
                        </div>
                        <div class="row" style="margin-bottom: 10px" id="container_dp">
                            <input type="text" class="form-control" id="dp" name="dp" onkeydown="isNumber(event)" onkeyup="isMoney('dp', '+'); cek_bayar('dp', $(this).val()); return shortcut_simpan(event)" onclick="this.select()" placeholder="DP">
                            <b class="error" id="alr_dp"></b>
                        </div>
                    </div>
                </div>
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->

<div id="modal_change" class="modal fade" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-md">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                <h4 class="modal-title" id="modal-label">Transaction Success</h4>
            </div>
            <div class="row" style="margin-top: 5px">
                <h3>Change</h3>
                <h2 class="pull-left" id="total_change"></h2>
                <input type="hidden" id="no_nota" name="no_nota">
                <input onclick="location.reload()" type="button" class="btn btn-primary pull-right" value="Finish">
                <input onclick="print_nota($('#no_nota').val())" type="button" class="btn btn-primary pull-right" value="Print" style="margin-right: 10px">
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
			url: "<?php echo base_url() . 'penjualan/get_tr_temp_m_jual' ?>",
			type: "GET",
			dataType: "JSON",
			success: function (data) {
				$('#tgl_jual').datepicker("setDate", data['m2']);
				$("#lokasi").val(data['m3']).change();
				$("#customer").val(data['m4']).change();
                $("#ket").val(data['m10']);
				if (data['m11'] == 1 || data['m11'] == '' || data['m11'] == null) {
					document.getElementById('set_focus_barcode').checked = true;
				} else if(data['m11'] == 2) {
					document.getElementById('set_focus_qty').checked = true;
				}
            }
		});
	}

    function add_tmp_master() {
        var data = new FormData($('#form_head')[0]);
        var nota_sistem = $("#nota_sistem").val();

        if (nota_sistem != '') {
            $.ajax({
                url: "<?php echo base_url() . 'penjualan/add_tr_temp_m_jual' ?>",
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

    function update_tmp_master(column, data) {
        $.ajax({
            url: "<?php echo base_url() . 'penjualan/update_tr_temp_m_jual/' ?>" + btoa(column) + "/" + btoa(data),
            type: "GET"
        });
    }

    function open_price(barcode, id) {
	    var check = document.getElementById('open_price'+id).checked;

	    if (check == true) {
	        $("#d5"+id).prop("readonly", false);
	        update_tmp_detail(barcode, 'd15', '1');
        } else {
            $("#d5"+id).prop("readonly", true);
            update_tmp_detail(barcode, 'd15', '0');
        }
    }

    function shortcut_simpan(event) {
        if (event.keyCode == 13) {
            simpan_transaksi();
        }
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
            $("#d8"+id).focus().select();
        }

        if (event.ctrlKey && event.keyCode == 13) {
            $("#barcode").focus();
            $('html, body').animate({ scrollTop: 0 }, 'fast');
        }
    }

	function get_tmp_detail(param = 0, col=0) {
		$.ajax({
			url: "<?php echo base_url() . 'penjualan/get_tr_temp_d_jual/' ?>" + btoa('add'),
			type: "GET",
			dataType: "JSON",
			success: function (data) {
                $("#list_barang").html(data.list_barang);
                $("#sub_total").val(to_rp(data.jumlah['sub_total'].toFixed(2)));
                $("#discount_persen").val(data.jumlah['discount_persen'].toFixed(2));
                $("#discount_harga").val(to_rp(data.jumlah['discount_harga'].toFixed(2)));
                $("#pajak").val(data.jumlah['pajak'].toFixed(2));
                $("#grand_total").val(to_rp(data.jumlah['total'].toFixed(2)));
                konversi_diskon('harga');
				if(document.getElementById("set_focus_barcode").checked == true){
					$("#barcode").focus();
				} else if(document.getElementById("set_focus_qty").checked == true){
					if(param != 1) {
						if (param == 2) {
							$("#d8" + col).focus().select();
						} else {
							$("#d8" + $("#col").val()).focus().select();
						}
					} else {
						$("#barcode").focus();
					}
				}
			},
            complete: function () {
                var col = $("#col").val();
                for (var i=1; i<=col; i++) {
                    if ($("#d15"+i).val() == '1') {
                        $("#open_price" + i).prop("checked", true);
                        open_price($("#d9"+i).val(), i);
                    }
                }
            }
		});
	}

    function update_tmp_detail(barcode, column, value) {
        $.ajax({
            url: "<?php echo base_url() . 'penjualan/update_tr_temp_d_jual/' ?>" + btoa(barcode) + "/" + btoa(column) + "/" + btoa(hapuskoma(value)),
            type: "GET"
        });
    }

	function hapus_barang(barcode) {
		$.ajax({
			url: "<?php echo base_url() . 'penjualan/delete_tr_temp_d_jual/' ?>" + btoa(barcode),
			type: "GET",
			success: function (data) {
				if (data) {
					get_tmp_detail();
					hitung_total();
				}
			}
		});
	}

	$("#barcode").keyup(function (event) {
		var cat_cari = $("#cat_cari").val();
		var nota_sistem = $("#nota_sistem").val();
		var barcode = $("#barcode").val();
		var lokasi = $("#lokasi").val();
		var customer = $("#customer").val();
		hide_notif("alr_barang");

		if (event.keyCode == 13) {
			if (lokasi == '') {
				$("#alr_lokasi").text("Lokasi wajib dipilih!");
			}

			if (customer == '') {
				$("#alr_customer").text("Customer wajib dipilih!");
			}

			if (lokasi != '' && customer != '' && barcode != '') {
				$.ajax({
					url: "<?php echo base_url() . 'penjualan/get_barang_jual/' ?>" + btoa(barcode) + "/" + btoa(lokasi) + "/" + btoa(customer) + "/" + btoa(cat_cari),
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
                            $("#alr_barang").text(data.notif);
							$("#barcode").val("");
						} else {
							$("#barcode").val("").focus();
							$("#alr_barang").text(data.notif);
						}
					}
				});
			}
		}
	});

	function hitung_barang(column, id, value, length) {
	    var d5 = 0; if (column != 'd5'){d5 = parseFloat(hapuskoma($("#d5"+id).val()));}else {d5 = parseFloat(hapuskoma(value));}
	    var d6 = 0; if (column != 'd6'){d6 = $("#d6"+id).val();}else {d6 = value;}
	    var d7 = 0; if (column != 'd7'){d7 = $("#d7"+id).val();}else {d7 = value;}
	    var d8 = 0; if (column != 'd8'){d8 = $("#d8"+id).val();}else {d8 = value;}
	    var d14 = 0; if (column != 'd14'){d14 = $("#d14"+id).val();}else {d14 = value;}

        var jumlah_jual = d5 * d8;
        var diskon = double_diskon(jumlah_jual, [d6, d7]);
        var hitung_sub_total = hitung_ppn(diskon, 0, 0);

        $("#nilai_jual"+id).val(to_rp(hitung_sub_total.toFixed(2)));

        var total_qty_jual = 0;
        var total_nilai_jual = 0;

        for (var i=1; i<=length; i++) {
            total_nilai_jual = total_nilai_jual + parseFloat(hapuskoma(document.getElementById("nilai_jual"+i).value));
            total_qty_jual = total_qty_jual + parseInt(document.getElementById("d8"+i).value);
        }

        $("#total_nilai_jual").text(to_rp(total_nilai_jual.toFixed(2)));
        $("#total_qty_jual").text(total_qty_jual);
        $("#sub_total").val(to_rp(total_nilai_jual.toFixed(2)));
        konversi_diskon('harga');
        hitung_total();
    }

    function konversi_diskon(jenis) {
        var sub_total = hapuskoma($("#sub_total").val());
        var discount_persen = document.getElementById("discount_persen").value;
        var discount_harga = hapuskoma(document.getElementById("discount_harga").value);

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
        var discount_harga = hapuskoma($("#discount_harga").val());
        var total_return = $("#total_return").val();
        var pajak = $("#pajak").val();

        if (param==0){ var diskon = discount_harga}else {diskon = ((discount_persen/100)*sub_total)}

        total = (sub_total-diskon) + ((pajak/100) * sub_total);
        $("#total").val(total.toFixed(2));
        $("#grand_total").val(to_rp((total - total_return).toFixed(2)));
        hide_notif("alr_subtotal");
    }

	function trx_number() {
        add_tmp_master();
        setTimeout(function () {
            var lokasi = $("#lokasi").val();
            update_tmp_master('m3', lokasi);
        }, 800);
	}

	function batal_transaksi() {
        $.ajax({
            url: "<?php echo base_url().'penjualan/delete_trans_jual' ?>",
            type: "GET",
            success: function (data) {
                location.reload();
            }
        });
    }

    function cek_pembayaran(data) {
        if (data == "Tunai") {
            document.getElementById("container_bayar").style.visibility = "visible";
            document.getElementById("container_bayar").style.display = "block";
            document.getElementById("container_bank").style.visibility = "hidden";
            document.getElementById("container_bank").style.display = "none";
            document.getElementById("container_tempo").style.visibility = "hidden";
            document.getElementById("container_tempo").style.display = "none";
            document.getElementById("container_dp").style.visibility = "hidden";
            document.getElementById("container_dp").style.display = "none";
        } else if (data == "Kredit") {
            document.getElementById("container_bayar").style.visibility = "hidden";
            document.getElementById("container_bayar").style.display = "none";
            document.getElementById("container_bank").style.visibility = "visible";
            document.getElementById("container_bank").style.display = "block";
            document.getElementById("container_tempo").style.visibility = "visible";
            document.getElementById("container_tempo").style.display = "block";
            document.getElementById("container_dp").style.visibility = "visible";
            document.getElementById("container_dp").style.display = "block";
        } else {
            document.getElementById("container_bayar").style.visibility = "hidden";
            document.getElementById("container_bayar").style.display = "none";
            document.getElementById("container_bank").style.visibility = "visible";
            document.getElementById("container_bank").style.display = "block";
            document.getElementById("container_tempo").style.visibility = "hidden";
            document.getElementById("container_tempo").style.display = "none";
            document.getElementById("container_dp").style.visibility = "hidden";
            document.getElementById("container_dp").style.display = "none";
        }
    }

    function bayar() {
	    var col = $("#col").val();
	    var sub_total = hapuskoma($("#sub_total").val());
	    var cust = $("#customer").val();

	    if (col <= 0) {
	        $("#alr_barang").text("Barang belum di input!");
        }

        if (sub_total < 0) {
	        $("#alr_subtotal").text("Sub total harus lebih dari 0!");
        }

        if (cust == '') {
            $("#alr_customer").text("Customer Wajib Dipilih!");
        }

	    if (col > 0 && sub_total >= 0 && cust != '') {
            document.getElementById("container_bank").style.visibility = "hidden";
            document.getElementById("container_tempo").style.visibility = "hidden";
            document.getElementById("container_dp").style.visibility = "hidden";
            $("#pembayaran").val("Tunai").change();
            $("#jumlah_bayar").val("");
            $("#jatuh_tempo").val("");
            $("#dp").val("");
            hide_notif("alr_jumlah_bayar");
            $("#total_pembelian").text("Rp " + to_rp(hapuskoma($("#grand_total").val())));
            $("#modal_payment").modal('show');
            setTimeout(function () {
                $("#jumlah_bayar").focus();
            }, 800);
        }
    }

    function cek_bayar(tipe, data) {
        if (tipe == 'bayar') {
            if (parseFloat(hapuskoma(data)) <= 0) {
                $("#alr_jumlah_bayar").text("Pembayaran harus lebih dari 0!");
            } else {
                $("#alr_jumlah_bayar").text("");
            }
        } else if (tipe == 'dp') {
            if (parseFloat(hapuskoma(data)) < 0) {
                $("#alr_dp").text("Pembayaran harus lebih dari 0!");
            } else {
                $("#alr_dp").text("");
            }
        } else {
            $("#alr_jatuh_tempo").text("");
            $("#alr_bank").text("");
        }
    }

    function print_nota(kd_trx) {
        window.open('<?=base_url().'cetak/nota_penjualan/'?>' + btoa(kd_trx));
        location.reload();
    }

	function simpan_transaksi() {
	    var jenis_trx = $("#pembayaran").val();
	    var bank = $("#bank").val();
	    var grand_total = parseFloat(hapuskoma($("#grand_total").val()));
	    var customer = $("#customer").val();
	    var ket = $("#ket").val();
	    var disc_hrg = parseFloat(hapuskoma($("#discount_harga").val()));
	    var status = false;
	    var data = {lokasi_:$("#lokasi").val(), tgl_jual_:$("#tgl_jual").val(), customer_:customer, ket_:ket, disc_hrg_:disc_hrg};

	    if (jenis_trx == "Tunai") {
            var jumlah_bayar = parseFloat(hapuskoma($("#jumlah_bayar").val()));
            if (jumlah_bayar < grand_total) {
                $("#alr_jumlah_bayar").text("Pembayaran kurang!");
                status = false;
            } else {
                data['jumlah_bayar_'] = jumlah_bayar;
                status = true;
            }
        } else if (jenis_trx == "Kredit") {
	        var jatuh_tempo = $("#jatuh_tempo").val();
	        var dp = parseFloat(hapuskoma($("#dp").val()));

	        if (jatuh_tempo == "") {
	            $("#alr_jatuh_tempo").text("Tgl harus diisi!");
	            status = false;
            } else {
                $("#alr_jatuh_tempo").text("");
            }

            if (dp < 0) {
	            $("#alr_dp").text("Dp miminal 0!");
	            status = false;
            } else {
                $("#alr_dp").text("");
            }

            if (jatuh_tempo != "" && dp >= 0) {
                data['jatuh_tempo_'] = jatuh_tempo;
                data['bank_'] = bank;
                data['dp_'] = dp;
                status = true;
            }
        } else {
	        status = true;
            data['bank_'] = bank;
        }

        if (status == true) {
	        if (confirm("Akan menyimpan transaksi?")) {
                $.ajax({
                    url: "<?php echo base_url() . 'penjualan/trans_jual' ?>",
                    type: "POST",
                    data: {jenis_trx_: jenis_trx, param_: 'add', data_: data},
                    dataType: "JSON",
                    beforeSend: function () {
                        $('#loading').show();
                        $("#modal_payment").modal('hide');
                    },
                    complete: function () {
                        $("#loading").hide();
                    },
                    success: function (data) {
                        if (data.status == 'success') {
                            if (data.transaksi == 'Tunai') {
                                if (data.change >= 0) {
                                    $("#no_nota").val(data.kd_trx);
                                    $("#modal_payment").modal('hide');
                                    $("#modal_change").modal('show');
                                    $("#total_change").text("Rp " + to_rp(data.change));
                                }
                            } else {
                                if (confirm("Print Nota Penjualan?")) {
                                    print_nota(data.kd_trx);
                                } else {
                                    location.reload();
                                }
                            }
                        } else {
                            alert("Transaksi Gagal!");
                        }
                    }
                });
            }
        }
    }
</script>