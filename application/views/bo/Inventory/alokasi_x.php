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
                                                                <input class="form-control pull-right datepicker_date_from" readonly onchange="trx_number();add_tmp_master()" name="tgl_mutasi" id="tgl_mutasi" type="text" value="<?=set_value('tgl_mutasi')?set_value('tgl_mutasi'):date("Y-m-d")?>">
                                                                <!--custom_front_date('pembelian', $(this).val())-->
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="row" style="margin-bottom: 3px">
                                                        <label class="col-sm-4">Lokasi Asal</label>
                                                        <div class="col-sm-6">
                                                            <select name="lokasi_asal" id="lokasi_asal" onchange="trx_number(); hide_notif('alr_lokasi_asal'); get_data_pembelian(); check_lokasi()" class="select2">
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
                                                            <select name="lokasi_tujuan" id="lokasi_tujuan" onchange="add_tmp_master(); hide_notif('alr_lokasi_tujuan'); hide_notif('alr_lokasi_asal'); check_lokasi()" class="select2">
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
                                                        <label class="col-sm-4">Kode Pembelian</label>
                                                        <div class="col-sm-7">
                                                            <div class="input-group">
                                                                <select id="kode_pembelian" name="kode_pembelian" class="select2" style="width: 100%">
                                                                    <option></option>
                                                                    <option value="-">Pilih</option>
                                                                    <?php
                                                                    foreach ($data_pembelian as $row) {
                                                                        echo "<option value=\"".$row['no_faktur_beli']."\">".$row['no_faktur_beli']."</option>";
                                                                    }
                                                                    ?>
                                                                </select>
                                                                <div class="input-group-btn">
                                                                    <button onclick="add_data_pembelian()" type="button" id="cari_kode_pembelian" name="cari_kode_pembelian" class="btn btn-primary"><i class="md md-search"></i></button>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="row" style="margin-bottom: 3px">
                                                        <label class="col-sm-4">Catatan</label>
                                                        <div class="col-sm-6">
                                                            <textarea onblur="add_tmp_master()" class="form-control" id="catatan" name="catatan"></textarea>
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
                                <div class="col-md-4">
                                    <div class="panel-heading">
                                        <div class="input-group">
                                            <select class="form-control" id="cat_cari" name="cat_cari">
                                                <option value="1">Kode Barang</option>
                                                <option value="2">Barcode</option>
                                                <option value="3"><?=$menu_group['as_deskripsi']?></option>
                                                <option value="4">Kode Packing</option>
                                            </select>
                                        </div>
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
                                                <th>Stock</th>
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
                                    <button class="btn btn-primary" onclick="if (confirm('Akan menyimpan transaksi?')){simpan_transaksi()}" id="simpan" type="submit">Simpan</button>
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
        get_tmp_detail();
    });

    function add_data_pembelian() {
        var no_mutasi = $("#no_mutasi").val();
        var kode_pembelian = $("#kode_pembelian").val();
        var split_lokasi_asal = $("#lokasi_asal").val().split("|");
        var lokasi_asal = split_lokasi_asal[0];

        if (kode_pembelian != '') {
            $.ajax({
                url: "<?php echo base_url() . 'inventory/add_data_pembelian/' ?>" + btoa(no_mutasi) + '/' + btoa(kode_pembelian) + '/' + btoa(lokasi_asal),
                type: "GET",
                dataType: "JSON",
                success: function (data) {
                    get_data_pembelian();
                    $("#cari_kode_pembelian").prop("disabled", true);
                    location.reload();
                }
            });
        }
    }

    $("#kode_pembelian").click(function () {
        $("#cari_kode_pembelian").prop("disabled", false);
    });

    function get_data_pembelian() {
        var split_lokasi_asal = $("#lokasi_asal").val().split("|");
        var lokasi_asal = split_lokasi_asal[0];

        if (lokasi_asal != '') {
            $.ajax({
                url: "<?php echo base_url() . 'inventory/get_data_pembelian/' ?>" + btoa(lokasi_asal),
                type: "GET",
                success: function (data) {
                    $("#kode_pembelian").html(data).val('-').change().prop("disabled", false);
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
            url: "<?php echo base_url() . 'inventory/get_tr_temp_m' ?>",
            type: "GET",
            dataType: "JSON",
            success: function (data) {
                if (data.status == 1) {
                    $("#no_mutasi").val(data.temp['m1']);
                    $('#tgl_mutasi').datepicker("setDate", data.temp['m2']);
                    $("#lokasi_asal").val(data.temp['m3']).change();
                    $("#lokasi_tujuan").val(data.temp['m4']).change();
                    $("#catatan").val(data.temp['m5']);
                    if (data.temp['m3'] != '') {
                        $("#kode_pembelian").prop("disabled", true);
                    } else {
                        $("#kode_pembelian").prop("disabled", false);
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

    function get_tmp_detail() {
        $.ajax({
            url: "<?php echo base_url() . 'inventory/get_tr_temp_d' ?>",
            type: "GET",
            dataType: "JSON",
            success: function (data) {
                $("#list_barang").html(data.list_barang);
                $("#sub_total").val(data.sub_total);
                hitung_barang('d10', data.id, data.value, data.length);
                /*cek_simpan(data.qty);*/
                if (data.kode_pembelian != '-') {
                    if (data.kode_pembelian != '') {
                        $("#kode_pembelian").select2({placeholder: data.kode_pembelian});
                        $("#cari_kode_pembelian").prop("disabled", true);
                    } else {
                        $("#kode_pembelian").val("-").change().prop('disabled', true);
                        $("#cari_kode_pembelian").prop("disabled", false);
                    }
                }
                $("#d10" + $("#col").val()).focus().select();
            }
        });
    }

    function to_barcode(event) {
        if (event.keyCode == 13) {
            $("#barcode").focus();
        }
    }

    function update_tmp_detail(barcode, column, value) {
        $.ajax({
            url: "<?php echo base_url() . 'inventory/update_tr_temp_d/' ?>" + btoa(barcode) + "/" + btoa(column) + "/" + btoa(value),
            type: "GET"
        });
    }

    function hapus_barang(barcode) {
        $.ajax({
            url: "<?php echo base_url() . 'inventory/delete_tr_temp_d/' ?>" + btoa(barcode),
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
                data: {lokasi_asal_: lokasi_asal},
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
            data: {no_mutasi_: no_mutasi, list_: list},
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
        hide_notif("alr_barang");

        if (event.keyCode == 13) {
            if (lokasi_asal == '') {
                $("#alr_lokasi_asal").text("Lokasi wajib dipilih!");
            }

            if (lokasi_asal != '') {
                $.ajax({
                    url: "<?php echo base_url() . 'inventory/get_barang/' ?>" + btoa(no_mutasi) + "/" + btoa(barcode) + "/" + btoa(lokasi_asal) + "/" + btoa(cat_cari),
                    type: "GET",
                    dataType: "JSON",
                    success: function (data) {
                        if (data.status == 1) {
                            $("#barcode").val("").focus();
                            get_tmp_detail();
                        } else {
                            alert(data.notif);
                            $("#barcode").val("").focus();
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

        if (parseInt(d10) <= 0 || isNaN(parseInt(d10))) {
            if (value != null && id != null) {
                for (x; x <= length; x++) {
                    if (x != id) {
                        $("#d10" + x).prop("disabled", true);
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
        var tgl_mutasi = $("#tgl_mutasi").val();
        var get_lokasi = $("#lokasi_asal").val();
        var lokasi = get_lokasi.split("|");

        if (tgl_mutasi != '' && get_lokasi != ''){
            $.ajax({
                url: "<?php echo base_url().'site/max_kode/' ?>" + btoa("MU") + "/" + btoa(tgl_mutasi) + "/" + btoa(lokasi[1]),
                type: "GET",
                success: function (data) {
                    $("#no_mutasi").val(data);
                    add_tmp_master();
                }
            });
        }else {
            $("#no_mutasi").val("");
        }
    }

    function batal_transaksi() {
        $.ajax({
            url: "<?php echo base_url().'inventory/delete_trans_mutasi' ?>",
            type: "GET",
            success: function (data) {
                location.reload();
            }
        });
    }

    function simpan_transaksi() {
        var no_mutasi = $("#no_mutasi").val();
        var sub_total = $("#sub_total").val();
        var lokasi_tujuan = $("#lokasi_tujuan").val();

        if (lokasi_tujuan == '') {
            $("#alr_lokasi_tujuan").text("Lokasi tujuan belum dipilih!");
        }

        /*if (sub_total == 0) {
            $("#alr_barang").text("Barang belum dimasukan!");
        }*/

        if (/*sub_total != 0 && */lokasi_tujuan != '') {
            $.ajax({
                url: "<?php echo base_url().'inventory/trans_mutasi/' ?>" + btoa(no_mutasi),
                type: "GET",
                success: function (data) {
                    if (data) {
                        alert("Transaksi Berhasil!");
                    } else {
                        alert("Transaksi Gagal!");
                    }
                    location.reload();
                }
            });
        }
    }
</script>