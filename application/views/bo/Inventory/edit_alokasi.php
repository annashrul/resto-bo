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
                                            <input type="hidden" id="param" name="param" value="edit">
                                            <div class="row">
                                                <div class="col-sm-6">
                                                    <div class="row" style="margin-bottom: 3px">
                                                        <label class="col-sm-4">Kode Mutasi</label>
                                                        <div class="col-sm-6">
                                                            <input type="text" id="no_mutasi" name="no_mutasi" class="form-control" readonly value="">
                                                        </div>
                                                    </div>
                                                    <div class="row" style="margin-bottom: 3px">
                                                        <label class="col-sm-4">Tgl Mutasi</label>
                                                        <div class="col-sm-6">
                                                            <div class="input-group date">
                                                                <div class="input-group-addon">
                                                                    <i class="fa fa-calendar"></i>
                                                                </div>
                                                                <input class="form-control pull-right datepicker_date_from" readonly onchange="trx_number()" name="tgl_mutasi" id="tgl_mutasi" type="text" value="<?=set_value('tgl_mutasi')?set_value('tgl_mutasi'):date("Y-m-d")?>">
                                                                <!--custom_front_date('pembelian', $(this).val())-->
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="row" style="margin-bottom: 3px">
                                                        <label class="col-sm-4">Lokasi Asal</label>
                                                        <div class="col-sm-6">
                                                            <select name="lokasi_asal" id="lokasi_asal" onchange="get_data_pembelian(); trx_number(); hide_notif('alr_lokasi_asal'); check_lokasi()" class="select2">
                                                                <option value="">Pilih</option>
                                                                <?php
                                                                foreach ($data_lokasi as $row) {
                                                                    echo "<option value=\"".$row['Kode']."|".$row['serial']."\">".$row['Nama']."</option>";
                                                                }
                                                                ?>
                                                            </select>
                                                            <b class="error" id="alr_lokasi_asal"></b>
                                                        </div>
                                                    </div>
                                                    <div class="row" style="margin-bottom: 3px">
                                                        <label class="col-sm-4">Lokasi Tujuan</label>
                                                        <div class="col-sm-6">
                                                            <select name="lokasi_tujuan" id="lokasi_tujuan" onchange="get_data_pembelian(); update_tmp_master('m4', $(this).val()); hide_notif('alr_lokasi_tujuan'); hide_notif('alr_lokasi_asal'); check_lokasi()" class="select2">
                                                                <option value="">Pilih</option>
                                                                <?php
                                                                foreach ($data_lokasi as $row) {
                                                                    echo "<option value=\"".$row['Kode']."|".$row['serial']."\">".$row['Nama']."</option>";
                                                                }
                                                                ?>
                                                            </select>
                                                            <b class="error" id="alr_lokasi_tujuan"></b>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-sm-6">
                                                    <div class="row" style="margin-bottom: 3px">
                                                        <label class="col-sm-4">Jenis Transaksi</label>
                                                        <div class="col-sm-7">
                                                            <div class="form-inline">
                                                                <?php $field = 'jns_trx'; ?>
                                                                <div class="radio radio-primary">
                                                                    <input class="form-control" type="radio" onclick="trx_number()" id="alokasi" name="<?=$field?>" value="alokasi" required />
                                                                    <label for="alokasi"> Alokasi </label>
                                                                </div>
                                                                <div class="radio radio-primary">
                                                                    <input class="form-control" type="radio" onclick="trx_number()" id="branch" name="<?=$field?>" value="branch" required />
                                                                    <label for="branch"> Branch </label>
                                                                </div>
                                                                <?=form_error($field, '<div class="error" style="color:red;">', '</div>')?>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="row" style="margin-bottom: 3px">
                                                        <label class="col-sm-4">Kode Pembelian</label>
                                                        <div class="col-sm-7">
                                                            <div class="input-group">
                                                                <select id="kode_pembelian" name="kode_pembelian" onchange="update_tmp_master('m8', $(this).val()); clear_list(1)" class="select2" style="width: 100%">
                                                                    <option value="-">Pilih</option>
                                                                    <?php
                                                                    foreach ($data_pembelian as $row) {
                                                                        echo "<option value=\"".$row['no_faktur_beli']."\">".$row['no_faktur_beli']."</option>";
                                                                    }
                                                                    ?>
                                                                </select>
                                                                <div class="input-group-btn">
                                                                    <button onclick="add_data_pembelian(); return false;" type="button" id="cari_kode_pembelian" name="cari_kode_pembelian" class="btn btn-primary"><i class="md md-search"></i></button>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="row" style="margin-bottom: 3px">
                                                        <label class="col-sm-4">Catatan</label>
                                                        <div class="col-sm-6">
                                                            <textarea onblur="update_tmp_master('m5', $(this).val())" class="form-control" id="catatan" name="catatan"></textarea>
                                                            <b class="error" id="alr_catatan"></b>
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
														<button onclick="cari_barang()" id="cari" name="cari" type="button" class="btn btn-primary"><i class="md md-search"></i></button>
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
                                                <th>Satuan</th>
                                                <th>Harga Beli</th>
                                                <th>Harga Jual 1</th>
                                                <!--<th>Harga Jual 2</th>
                                                <th>Harga Jual 3</th>
                                                <th>Harga Jual 4</th>-->
                                                <th>Qty DN</th>
                                                <th>Jumlah</th>
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
                                    <button class="btn btn-primary" onclick="simpan_transaksi()" id="simpan" type="submit">Simpan</button>
                                    <button class="btn btn-primary" onclick="if (confirm('Akan membatalkan transaksi?')){batal_transaksi()}" id="batal" type="submit">Batal</button>
                                    <button class="btn btn-primary" onclick="if (confirm('Akan menutup transaksi?')){tutup_transaksi()}" id="keluar" type="submit">Keluar</button>
                                    <!--<button class="btn btn-primary" id="arsip_pembelian" type="submit">Arsip Pembelian</button>-->
                                </div>
                                <div class="col-md-5">
                                    <div class="row" style="margin-bottom: 3px">
                                        <label class="col-sm-4">Sub Total</label>
                                        <div class="col-sm-8">
                                            <input type="text" id="sub_total" name="sub_total" class="form-control text-right" readonly>
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
                        <table class="table table-bordered table_check">
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
        /*get master*/
        get_tmp_master();

        /*get detail*/
        get_tmp_detail(1);
    });

    function add_data_pembelian() {
        var kode_pembelian = $("#kode_pembelian").val();

        if (kode_pembelian != '') {
            $.ajax({
                url: "<?php echo base_url() . 'inventory/add_data_pembelian/' ?>" + '/' + btoa('edit'),
                type: "GET",
                dataType: "JSON",
                success: function (data) {
                    get_tmp_detail();
                }
            });
        }
    }

    function get_data_pembelian() {
        /*var split_lokasi_asal = $("#lokasi_asal").val().split("|");
        var lokasi_asal = split_lokasi_asal[0];*/
        var lokasi_asal = $("#lokasi_asal").val();
		var lokasi_tujuan = $("#lokasi_tujuan").val();

        if (lokasi_asal!='' && lokasi_tujuan!='') {
            $.ajax({
                url: "<?php echo base_url() . 'inventory/get_data_pembelian/' ?>" + btoa(lokasi_asal) + "/" + btoa('edit'),
                type: "POST",
				data: {lokasi_tujuan:btoa(lokasi_tujuan)},
                dataType: "JSON",
                success: function (data) {
                    if (data.status == '1') {
                        $("#kode_pembelian").html(data.list).select2("val", data.kode).prop("disabled", false);
                    } else {
                        $("#kode_pembelian").html(data.list).select2("val", '-').change().prop("disabled", false);
                        clear_list(1);
                    }
                }
            });
        }
    }

    function clear_list(param=0) {
        if (param==1) {
            $.ajax({
                url: "<?php echo base_url() . 'inventory/delete_tmp_d/' ?>" + btoa('edit'),
                type: "GET",
                success: function (res) {
                    get_tmp_detail();
                }
            });
        }
    }

    function check_lokasi() {
        var lokasi_asal = $("#lokasi_asal").val();
        var lokasi_tujuan = $("#lokasi_tujuan").val();

        if (lokasi_asal ==  lokasi_tujuan) {
            $("#alr_lokasi_asal").text("Lokasi tidak boleh sama!");
            $("#barcode").prop("disabled", true);
            $("#cari").prop("disabled", true);
            $("#simpan").prop("disabled", true);
        } else {
            $("#barcode").prop("disabled", false);
            $("#cari").prop("disabled", false);
            $("#simpan").prop("disabled", false);
        }
    }

    function get_tmp_master() {
        $.ajax({
            url: "<?php echo base_url() . 'inventory/get_tr_temp_m/' ?>" + btoa('edit'),
            type: "GET",
            dataType: "JSON",
            success: function (data) {
                if (data.status == 1) {
                    $("#no_mutasi").val(data.temp['m1']);
                    $('#tgl_mutasi').datepicker("setDate", data.temp['m2']);
                    $("#lokasi_asal").val(data.temp['m3']).change();
                    $("#lokasi_tujuan").val(data.temp['m4']).change();
                    $("#catatan").val(data.temp['m5']);
                    document.getElementById(data.temp['m7']).checked = true;
                    if (data.temp['m3'] != '') {
                        $("#kode_pembelian").prop("disabled", true);
                    } else {
                        $("#kode_pembelian").prop("disabled", false);
                    }
					if (data.temp['m11'] == 1 || data.temp['m11'] == '' || data.temp['m11'] == null) {
                        document.getElementById('set_focus_barcode').checked = true;
                    } else if(data.temp['m11'] == 2) {
                        document.getElementById('set_focus_qty').checked = true;
                    }
                }

            }
        });
    }

    function add_tmp_master() {
        var data = new FormData($('#form_head')[0]);
        var nota_sistem = $("#no_mutasi").val();

        if (nota_sistem != '') {
            $.ajax({
                url: "<?php echo base_url() . 'inventory/add_tr_temp_m' ?>",
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

    function get_tmp_detail(param = 0, col = 0) {
        $.ajax({
            url: "<?php echo base_url() . 'inventory/get_tr_temp_d/' ?>" + btoa('edit'),
            type: "GET",
            dataType: "JSON",
            success: function (data) {
                $("#list_barang").html(data.list_barang);
                $("#sub_total").val(data.sub_total);
                hitung_barang('d10', data.id, data.value, data.length);
                /*cek_simpan(data.qty);*/
                if (data.kode_pembelian != '-') {
                    if (data.kode_pembelian != '') {
                        $("#kode_pembelian").select2('val', data.kode_pembelian);
                    } else {
                        $("#kode_pembelian").val("-").change().prop('disabled', true);
                    }
                }

                if(document.getElementById("set_focus_barcode").checked == true){
					$("#barcode").focus();
				} else if(document.getElementById("set_focus_qty").checked == true){
					if(param != 1) {
						if (param == 2) {
							$("#d10" + col).focus().select();
						} else {
							$("#d10" + $("#col").val()).focus().select();
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
        }
    }

    function update_tmp_master(column, value, trx='') {
        if (trx == '') {
            trx = $("#no_mutasi").val();
        }

        if (trx != '') {
            $.ajax({
                url: "<?php echo base_url() . 'inventory/update_tr_temp_m/' ?>" + btoa(trx) + "/" + btoa(column) + "/" + btoa(value) + "/" + btoa('edit'),
                type: "GET"
            });
        }
    }

    function update_tmp_detail(barcode, column, value) {
        $.ajax({
            url: "<?php echo base_url() . 'inventory/update_tr_temp_d/' ?>" + btoa(barcode) + "/" + btoa(column) + "/" + btoa(value) + "/" + btoa('edit'),
            type: "GET"
        });
    }

    function hapus_barang(barcode) {
        $.ajax({
            url: "<?php echo base_url() . 'inventory/delete_tr_temp_d/' ?>" + btoa(barcode) + "/" + btoa('edit'),
            type: "GET",
            success: function (data) {
                if (data) {
                    get_tmp_detail();
                }
            }
        });
    }

    function cek_simpan(qty) {
        if (qty <= 0) {
            $("#simpan").prop("disabled", true);
        } else {
            $("#simpan").prop("disabled", false);
        }
    }

    function cari_barang() {
        var split_lokasi_asal = $("#lokasi_asal").val().split("|");
        var lokasi_asal = split_lokasi_asal[0];

        hide_notif("alr_barang");

        if (lokasi_asal == '') {
            $("#alr_lokasi_asal").text("Lokasi wajib dipilih!");
        }

        if (lokasi_asal != '') {
            $.ajax({
                url: "<?php echo base_url() . 'inventory/get_list_barang' ?>",
                data: {lokasi_asal_: lokasi_asal, param:'edit'},
                type: "POST",
                dataType: "JSON",
                success: function (data) {
                    $("#modal-container").modal('show');
                    $("#modal-label").text('Daftar Barang dari Lokasi ' + data.lokasi);
                    $("#list_barang_modal").html(data.list_barang);
                }
            });
        }
    }

    function add_barang() {
        var no_mutasi = $("#no_mutasi").val();
        var list = cek_checkbox_checked('barang');

        $.ajax({
            url: "<?php echo base_url() . 'inventory/add_list_barang' ?>",
            data: {no_mutasi_: no_mutasi, list_: list, param:'edit'},
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
        var no_mutasi = $("#no_mutasi").val();
        var barcode = $("#barcode").val();
        var lokasi_asal = $("#lokasi_asal").val();
        var kode_pembelian = $("#kode_pembelian").val();
        hide_notif("alr_barang");

        if (event.keyCode == 13) {
            if (lokasi_asal == '') {
                $("#alr_lokasi_asal").text("Lokasi wajib dipilih!");
            }

            if (kode_pembelian != '-') {
                $("#alr_barang").text("Alokasi By Pembelian!");
            }

            if (lokasi_asal != '' && kode_pembelian == '-') {
                $.ajax({
                    url: "<?php echo base_url() . 'inventory/get_barang/' ?>" + btoa(no_mutasi) + "/" + btoa(barcode) + "/" + btoa(lokasi_asal) + "/" + btoa(cat_cari) + "/" + btoa('edit'),
                    type: "GET",
                    dataType: "JSON",
                    success: function (data) {
                        /*if (data.status == 1) {
                            $("#barcode").val("");
                            get_tmp_detail();
                        } else {
                            $("#alr_barang").text(data.notif);
                            $("#barcode").val("").focus();
                        }*/
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
        var d5 = 0; if (column != 'd5'){d5 = $("#d5"+id).val();}else {d5 = value;}
        var d10 = 0; if (column != 'd10'){d10 = $("#d10"+id).val();}else {d10 = value;}
        var d13 = 0; if (column != 'd13'){d13 = $("#d13"+id).val();}else {d13 = value;}
        var x=1;
		
		if(parseInt(d10)<parseInt(d13) && parseInt(d10)!=0){
			document.getElementById("d10"+id).style.background = "#ff4d4d";
		} else if(parseInt(d10)>parseInt(d13)){
			document.getElementById("d10"+id).style.background = "#ffff00";
		} else {
			document.getElementById("d10"+id).style.background = "";
		}
		
        if (parseInt(d10) <= 0 || isNaN(parseInt(d10))) {
            if (value != null && id != null) {
                for (x; x <= length; x++) {
                    if (x != id) {
                        //$("#d10" + x).prop("disabled", true);
                    }
                }
            }
            $("#alr_jumlah_"+id).text("Jumlah harus lebih dari 0!");
            //$("#simpan").prop("disabled", true);
        } /*else if (parseInt(d10) > parseInt(d13)) {
            if (value != null && id != null) {
                for (x; x <= length; x++) {
                    if (x != id) {
                        $("#d10" + x).prop("disabled", true);
                    }
                }
            }
            $("#alr_jumlah_"+id).text("Jumlah tidak boleh melebihi stock!");
            $("#simpan").prop("disabled", true);
        }*/ else {
            if (value != null && id != null) {
                for (x; x <= length; x++) {
                    if (x != id) {
                        $("#d10" + x).prop("disabled", false);
                    }
                }
            }
            $("#alr_jumlah_"+id).text("");
            $("#simpan").prop("disabled", false);
        }

        if (parseInt(d10) > 0/* && parseInt(d10) <= parseInt(d13)*/) {
            var hitung_sub_total = d5 * d10;

            $("#sub_total" + id).val(hitung_sub_total.toFixed(2));

            var sub_total = 0;
            var qty = 0;
            for (var i = 1; i <= length; i++) {
                sub_total = sub_total + parseFloat(document.getElementById("sub_total" + i).value);
                qty = qty + parseInt(document.getElementById("d10"+i).value);
            }

            $("#sub_total").val(sub_total.toFixed(2));
            $("#total_qty").text(qty);
        }
    }

    function trx_number() {
        var jns_trx = $('input[name="jns_trx"]:checked').val();

        var tgl_mutasi = $("#tgl_mutasi").val();
        var get_lokasi = $("#lokasi_asal").val();
        var get_lokasi2 = $("#lokasi_tujuan").val();
        var lokasi = get_lokasi.split("|");

        if (jns_trx != null) {
            if (jns_trx == 'alokasi') {
                if (tgl_mutasi != '' && get_lokasi != '') {
                    $.ajax({
                        url: "<?php echo base_url() . 'site/max_kode/' ?>" + btoa("MU") + "/" + btoa(tgl_mutasi) + "/" + btoa(lokasi[1]),
                        type: "GET",
                        success: function (data) {
                            $("#no_mutasi").val(data);
                            add_tmp_master();
                            setTimeout(function () {
                                update_tmp_master('m3', get_lokasi, data);
                                update_tmp_master('m4', get_lokasi2, data);
                                update_tmp_master('m7', jns_trx, data);
                            }, 500);
                        }
                    });
                } else {
                    $("#no_mutasi").val("");
                }
            } else {
                if (tgl_mutasi != '' && get_lokasi != '') {
                    $.ajax({
                        url: "<?php echo base_url() . 'site/max_kode/' ?>" + btoa("MC") + "/" + btoa(tgl_mutasi) + "/" + btoa(lokasi[1]),
                        type: "GET",
                        success: function (data) {
                            $("#no_mutasi").val(data);
                            add_tmp_master();
                            setTimeout(function () {
                                update_tmp_master('m3', get_lokasi, data);
                                update_tmp_master('m4', get_lokasi2, data);
                                update_tmp_master('m7', jns_trx, data);
                            }, 500);
                        }
                    });
                } else {
                    $("#no_mutasi").val("");
                }
            }
        }
    }

    function batal_transaksi() {
        $.ajax({
            url: "<?php echo base_url().'inventory/delete_trans_mutasi/' ?>" + btoa('edit'),
            type: "GET",
            success: function (data) {
                window.location = "<?php echo base_url().'inventory/alokasi_report' ?>";
            }
        });
    }

    function validate()
    {
        if( document.getElementById('no_mutasi').value == "" )
        {
            document.getElementById('no_mutasi').focus() ;
            $('html, body').animate({ scrollTop: 0 }, 'fast');
            return false;
        }

        if( document.getElementById('tgl_mutasi').value == "" )
        {
            document.getElementById('tgl_mutasi').focus() ;
            $('html, body').animate({ scrollTop: 0 }, 'fast');
            return false;
        }

        if( document.getElementById('lokasi_asal').value == "" )
        {
            document.getElementById('lokasi_asal').focus() ;
            $('html, body').animate({ scrollTop: 0 }, 'fast');
            return false;
        }

        if( document.getElementById('lokasi_tujuan').value == "" )
        {
            document.getElementById('lokasi_tujuan').focus() ;
            $('html, body').animate({ scrollTop: 0 }, 'fast');
            return false;
        }

        if( document.getElementById('alokasi').value == "" )
        {
            document.getElementsByName('jns_trx').focus() ;
            $('html, body').animate({ scrollTop: 0 }, 'fast');
            return false;
        }

        if( document.getElementById('branch').value == "" )
        {
            document.getElementsByName('jns_trx').focus() ;
            $('html, body').animate({ scrollTop: 0 }, 'fast');
            return false;
        }

        if( document.getElementById('catatan').value == "" )
        {
            document.getElementById('catatan').focus() ;
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
        /*var no_mutasi = $("#no_mutasi").val();
        var sub_total = $("#sub_total").val();
        var lokasi_tujuan = $("#lokasi_tujuan").val();*/

        /*if (lokasi_tujuan == '') {
            $("#alr_lokasi_tujuan").text("Lokasi tujuan belum dipilih!");
        }
        if (sub_total == 0) {
            $("#alr_barang").text("Barang belum dimasukan!");
        }*/
		
		var selisih = '';
			
		for(var i=1; i<=$("#col").val(); i++){
			 var d10 = $("#d10"+i).val();
			 var d13 = $("#d13"+i).val();

			if(parseInt(d10)<parseInt(d13)){
				selisih = 'Qty Alokasi dan DN ada yang selisih. ';
			} else if(parseInt(d10)>parseInt(d13)){
				selisih = 'Qty Alokasi dan DN ada yang selisih. ';
			}
		}
		
        if (confirm(selisih+'Akan menyimpan transaksi?')) {
			if (validate()) {
				$.ajax({
					url: "<?php echo base_url() . 'inventory/trans_mutasi_x' ?>",
					type: "POST",
					data: $("#form_head").serialize(),
					beforeSend: function () {
						$('#loading').show();
					},
					complete: function () {
						$("#loading").hide();
					},
					success: function (data) {
						if (data) {
							alert("Transaksi Berhasil!");
						} else {
							alert("Transaksi Gagal!");
						}
						window.location = "<?php echo base_url().'inventory/alokasi_report' ?>";
					}
				});
			}
		}
    }
</script>