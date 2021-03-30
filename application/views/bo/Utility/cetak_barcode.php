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
                            <div class="row" style="margin-bottom: 3px">
                                <label class="col-sm-1">Lokasi Barang</label>
                                <div class="col-sm-3">
                                    <select name="lokasi_barang" id="lokasi_barang" onchange="add_tmp_master(); hide_notif('alr_lokasi_barang')" class="select2">
                                        <option value="">Pilih</option>
                                        <?php
                                        foreach ($data_lokasi as $row) {
                                            echo "<option value=\"".$row['Kode']."|".$row['serial']."\">".$row['Nama']."</option>";
                                        }
                                        ?>
                                    </select>
                                    <b class="error" id="alr_lokasi_barang"></b>
                                </div>
                            </div>
                            <hr>
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="panel-heading">
                                        <div class="input-group">
                                            <select class="form-control" id="cat_cari" name="cat_cari">
                                                <option value="1">Kode Barang</option>
                                                <option value="2">Barcode</option>
                                                <option value="3"><?=$menu_group['as_deskripsi']?></option>
                                                <!--<option value="4">Nama Barang</option>-->
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
                                                <th style="width: 10px">No</th>
                                                <th>Aksi</th>
                                                <th>Kode Barang</th>
                                                <th>Barcode</th>
                                                <th>Nama Barang</th>
                                                <th><?=$menu_group['as_deskripsi']?></th>
                                                <th><?=$menu_group['as_group1']?></th>
                                                <th>Harga Jual</th>
                                                <th>Qty</th>
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
                                    <button class="btn btn-primary" onclick="if (confirm('Akan mencetak barcode?')){cetak_barcode()}" id="simpan" type="submit">Cetak Barcode</button>
                                    <button class="btn btn-primary" onclick="if (confirm('Akan membatalkan transaksi?')){batal_transaksi()}" id="batal" type="submit">Batal</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    $(document).ready(function () {
        /*get master*/
        get_tmp_master();

        /*get detail*/
        get_tmp_detail(1);
    });

    function get_tmp_master() {
        $.ajax({
            url: "<?php echo base_url() . 'utility/get_tr_temp_m' ?>",
            type: "GET",
            dataType: "JSON",
            success: function (data) {
                $("#lokasi_barang").select2('val', data['m2']);
            }
        });
    }

    function add_tmp_master() {
        var lokasi = $("#lokasi_barang").val();

        $.ajax({
            url: "<?php echo base_url() . 'utility/add_tr_temp_m' ?>",
            data: {lokasi_:lokasi},
            type: "POST",
            dataType: "JSON"
        });
    }

    function update_tmp_master(column, data) {
        $.ajax({
            url: "<?php echo base_url() . 'utility/update_tr_temp_m/' ?>" + btoa(column) + "/" + btoa(data),
            type: "GET"
        });
    }

    function get_tmp_detail(param = 0) {
        $.ajax({
            url: "<?php echo base_url() . 'utility/get_tr_temp_d' ?>",
            type: "GET",
            dataType: "JSON",
            success: function (data) {
                $("#list_barang").html(data.list_barang);
                if(param != 1) {
                    $("#d7" + $("#col").val()).focus().select();
                } else {
                    $("#barcode").focus();
                }
            }
        });
    }

    function to_barcode(event) {
        if (event.keyCode == 13) {
            $("#barcode").focus();
        }
    }

    function to_qty(event, id) {
        if (event.keyCode == 13) {
            $("#d7"+id).focus().select();
        }
    }

    function update_tmp_detail(barcode, column, value) {
        $.ajax({
            url: "<?php echo base_url() . 'utility/update_tr_temp_d/' ?>" + btoa(barcode) + "/" + btoa(column) + "/" + btoa(value),
            type: "GET"
        });
    }

    function hapus_barang(barcode) {
        $.ajax({
            url: "<?php echo base_url() . 'utility/delete_tr_temp_d/' ?>" + btoa(barcode),
            type: "GET",
            success: function (data) {
                if (data) {
                    get_tmp_detail();
                }
            }
        });
    }

    $("#barcode").keyup(function (event) {
        var cat_cari = $("#cat_cari").val();
        var barcode = $("#barcode").val();
        var lokasi_barang = $("#lokasi_barang").val();
        hide_notif("alr_barang");

        if (event.keyCode == 13) {
            if (lokasi_barang == '') {
                $("#alr_lokasi_barang").text("Lokasi wajib dipilih!");
            }

            if (lokasi_barang != '') {
                $.ajax({
                    url: "<?php echo base_url() . 'utility/get_barang/' ?>" + btoa(barcode) + "/" + btoa(cat_cari) + "/" + btoa(lokasi_barang),
                    type: "GET",
                    dataType: "JSON",
                    success: function (data) {
                        if (data.status == 1) {
                            $("#barcode").val("");
                            get_tmp_detail();
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

    function batal_transaksi() {
        $.ajax({
            url: "<?php echo base_url().'utility/delete_trans' ?>",
            type: "GET",
            success: function (data) {
                if (data) {
                    location.reload();
                }
            }
        });
    }

    function cetak_barcode() {
        window.open('<?=base_url().'cetak/cetak_barcode'?>');
    }
</script>