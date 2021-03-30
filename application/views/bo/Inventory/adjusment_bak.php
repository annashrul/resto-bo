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
                                        <div class="row">
                                            <div class="col-sm-6">
                                                <div class="row" style="margin-bottom: 3px">
                                                    <label class="col-sm-4">Kode Adjusment</label>
                                                    <div class="col-sm-6">
                                                        <input type="text" id="kode_adjusment" name="kode_adjusment" class="form-control" readonly value="">
                                                    </div>
                                                </div>
                                                <div class="row" style="margin-bottom: 3px">
                                                    <label class="col-sm-4">Tgl Adjusment</label>
                                                    <div class="col-sm-6">
                                                        <div class="input-group date">
                                                            <div class="input-group-addon">
                                                                <i class="fa fa-calendar"></i>
                                                            </div>
                                                            <input class="form-control pull-right datepicker_date_from" readonly onchange="trx_number()" name="tgl_adjusment" id="tgl_adjusment" type="text" value="<?=set_value('tgl_adjusment')?set_value('tgl_adjusment'):date("Y-m-d")?>">
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
                                                                if ($_SESSION['lokasi_adjusment']==$row['Kode']."|".$row['serial']) {
                                                                    $selected = 'selected';
                                                                } else {
                                                                    $selected = '';
                                                                }
                                                                echo "<option ".$selected." value=\"".$row['Kode']."|".$row['serial']."\">".$row['Nama']."</option>";
                                                            }
                                                            ?>
                                                        </select>
                                                        <b class="error" id="alr_lokasi"></b>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-sm-6">
                                                <div class="row" style="margin-bottom: 3px">
                                                    <label class="col-sm-4">Keterangan</label>
                                                    <div class="col-sm-6">
                                                        <textarea class="form-control" id="keterangan" name="keterangan"></textarea>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
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
                                                <option value="4">Nama Barang</option>
                                            </select>
                                        </div>
                                        <input type="text" class="form-control" id="barcode" name="barcode">
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
                                                <th>Kode Barang</th>
                                                <th>Deskripsi Barang</th>
                                                <th>Satuan</th>
                                                <th>Harga Beli</th>
                                                <th>Stock Sistem</th>
                                                <th>Jenis</th>
                                                <th>Stock Adjusment</th>
                                                <th>Saldo Stock</th>
                                            </tr>
                                            </thead>
                                            <tbody id="list_adjusment">
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
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- end row -->

<script>
    $(document).ready(function () {
        trx_number();
        $("#barcode").focus();
    });

    $("#barcode").keyup(function (event) {
        var cat_cari = $("#cat_cari").val();
        var kode_adjusment = $("#kode_adjusment").val();
        var barcode = $("#barcode").val();
        var lokasi = $("#lokasi").val();
        hide_notif("alr_barang");

        if (event.keyCode == 13) {
            if (lokasi == '') {
                $("#alr_lokasi").text("Lokasi wajib dipilih!");
            }

            if (lokasi != '') {
                $.ajax({
                    url: "<?php echo base_url() . 'inventory/get_adjusment_barang/' ?>" + btoa(barcode) + "/" + btoa(lokasi) + "/" + btoa(cat_cari),
                    type: "GET",
                    dataType: "JSON",
                    success: function (data) {
                        if (data.status == 1) {
                            $("#barcode").val('');
                            $("#list_adjusment").html(data.list_adjusment);
                        } else {
                            $("#alr_barang").text(data.notif);
                        }
                    }
                });
            }
        }
    });

    function hitung() {
        var stock_sistem = $("#stock_sistem").val();
        var jenis = $("#jenis").val();
        var stock_adjusment = $("#stock_adjusment").val();

        if (parseInt(stock_adjusment) < 0 || isNaN(parseInt(stock_adjusment))) {
            $("#alr_stock_adjusment").text("Stock adjusment harus lebih dari 0!");
            $("#simpan").prop("disabled", true);
        } else {
            $("#alr_stock_adjusment").text("");
            $("#simpan").prop("disabled", false);
        }

        if (jenis == 'Tambah') {
            saldo_stock = parseInt(stock_sistem) + parseInt(stock_adjusment);
        } else {
            saldo_stock = parseInt(stock_sistem) - parseInt(stock_adjusment);
        }

        $("#saldo_stock").val(saldo_stock);
    }

    function trx_number() {
        var tgl_adjusment = $("#tgl_adjusment").val();
        var get_lokasi = $("#lokasi").val();
        var lokasi = get_lokasi.split("|");

        if (tgl_adjusment != '' && get_lokasi != ''){
            $.ajax({
                url: "<?php echo base_url().'site/max_kode/' ?>" + btoa("AA") + "/" + btoa(tgl_adjusment) + "/" + btoa(lokasi[1]),
                type: "GET",
                success: function (data) {
                    $("#kode_adjusment").val(data);
                    session_lokasi(get_lokasi);
                }
            });
        }else {
            $("#kode_adjusment").val("");
        }
    }

    function session_lokasi(lokasi) {
        $.ajax({
            url: "<?php echo base_url().'site/set_session/' ?>" + btoa('lokasi_adjusment') + "/" + btoa(lokasi),
            type: "GET"
        });
    }

    function batal_transaksi() {
        location.reload();
    }

    function simpan_transaksi() {
        var saldo_stock = $("#saldo_stock").val();

        if (saldo_stock == '') {
            $("#alr_barang").text("Barang belum dimasukan!");
        }

        if (saldo_stock != '') {
            $.ajax({
                url: "<?php echo base_url().'inventory/trans_adjusment' ?>",
                type: "POST",
                data: {kode_adjusment_: $("#kode_adjusment").val(), tanggal_: $("#tgl_adjusment").val(), lokasi_: $("#lokasi").val(), kode_barang_: $("#kode_barang").val(), stock_sistem_: $("#stock_sistem").val(), stock_adjusment_: $("#stock_adjusment").val(), saldo_stock_: $("#saldo_stock").val(), jenis_: $("#jenis").val(), harga_beli_: $("#harga_beli").val()},
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