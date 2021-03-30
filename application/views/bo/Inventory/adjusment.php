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
                                                        <label class="col-sm-4">Kode Adjusment</label>
                                                        <div class="col-sm-6">
                                                            <input type="text" id="kode_adjusment" name="kode_adjusment" class="form-control" readonly>
                                                        </div>
                                                    </div>
                                                    <div class="row" style="margin-bottom: 3px">
                                                        <label class="col-sm-4">Tgl Adjusment</label>
                                                        <div class="col-sm-6">
                                                            <div class="input-group date">
                                                                <div class="input-group-addon">
                                                                    <i class="fa fa-calendar"></i>
                                                                </div>
                                                                <input class="form-control pull-right datepicker_date_from" readonly onchange="trx_number()" name="tgl_adjusment" id="tgl_adjusment" type="text" value="<?=set_value('tgl_mutasi')?set_value('tgl_mutasi'):date("Y-m-d")?>">
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
                                                        <label class="col-sm-4">Keterangan</label>
                                                        <div class="col-sm-6">
                                                            <textarea class="form-control" id="keterangan" onblur="update_tmp_master('m4', $(this).val())"  name="keterangan"></textarea>
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
                                                <th width="60px">Aksi</th>
                                                <th>Kode Barang</th>
                                                <th>Barcode</th>
                                                <th><?=$menu_group['as_deskripsi']?></th>
                                                <th>Nama Barang</th>
                                                <th>Satuan</th>
                                                <th>Harga Beli</th>
                                                <th>Stock Sistem</th>
                                                <th>Jenis</th>
                                                <th>Stock Adjusment</th>
                                                <th>Saldo Stock</th>
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
        /*get master*/
        get_tmp_master();

        /*get detail*/
        get_tmp_detail(1);
    });

    function get_tmp_master() {
        $.ajax({
            url: "<?php echo base_url() . 'inventory/get_tr_temp_m_adj' ?>",
            type: "GET",
            dataType: "JSON",
            success: function (data) {
                if (data.status == 1) {
                    $("#kode_adjusment").val(data.temp['m1']);
                    $('#tgl_adjusment').datepicker("setDate", data.temp['m2']);
                    $("#lokasi").val(data.temp['m3']).change();
                    $("#keterangan").val(data.temp['m4']);
                }

            }
        });
    }

    function add_tmp_master() {
        var data = new FormData($('#form_head')[0]);
        var nota_sistem = $("#kode_adjusment").val();

        if (nota_sistem != '') {
            $.ajax({
                url: "<?php echo base_url() . 'inventory/add_tr_temp_m_adj' ?>",
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
            url: "<?php echo base_url() . 'inventory/get_tr_temp_d_adj' ?>",
            type: "GET",
            dataType: "JSON",
            success: function (data) {
                $("#list_barang").html(data.list_barang);
                if(param != 1) {
                    $("#d10" + $("#col").val()).focus().select();
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

    function update_tmp_master(column, value, trx='') {
        if (trx == '') {
            trx = $("#kode_adjusment").val();
        }

        if (trx != '') {
            $.ajax({
                url: "<?php echo base_url() . 'inventory/update_tr_temp_m_adj/' ?>" + btoa(trx) + "/" + btoa(column) + "/" + btoa(value),
                type: "GET"
            });
        }
    }

    function update_tmp_detail(barcode, column, value) {
        $.ajax({
            url: "<?php echo base_url() . 'inventory/update_tr_temp_d_adj/' ?>" + btoa(barcode) + "/" + btoa(column) + "/" + btoa(value),
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
                    url: "<?php echo base_url() . 'inventory/get_adjusment_barang/' ?>" + btoa(kode_adjusment) + "/" + btoa(barcode) + "/" + btoa(lokasi) + "/" + btoa(cat_cari),
                    type: "GET",
                    dataType: "JSON",
                    success: function (data) {
                        if (data.status == 1) {
                            $("#barcode").val('');
                            get_tmp_detail();
                        } else {
                            $("#alr_barang").text(data.notif);
                            $("#barcode").val("").focus();
                        }
                    }
                });
            }
        }
    });

    function hitung(id) {
        var stock_sistem = $("#d13"+id).val();
        var jenis = $("#d6"+id).val();
        var stock_adjusment = $("#d10"+id).val();

        if (parseInt(stock_adjusment) < 0 || isNaN(parseInt(stock_adjusment))) {
            $("#alr_stock_adjusment"+id).text("Stock adjusment harus lebih dari 0!");
            $("#simpan").prop("disabled", true);
        } else {
            $("#alr_stock_adjusment"+id).text("");
            $("#simpan").prop("disabled", false);
        }

        if (jenis == 'Tambah') {
            saldo_stock = parseInt(stock_sistem) + parseInt(stock_adjusment);
        } else {
            saldo_stock = parseInt(stock_sistem) - parseInt(stock_adjusment);
        }

        $("#saldo_stock"+id).val(saldo_stock);
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
                    add_tmp_master();
                    setTimeout(function () {
                        update_tmp_master('m3', get_lokasi, data);
                    }, 800);
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

    function hapus_barang(barcode) {
        $.ajax({
            url: "<?php echo base_url() . 'inventory/delete_tr_temp_d_adj/' ?>" + btoa(barcode),
            type: "GET",
            success: function (data) {
                if (data) {
                    get_tmp_detail();
                }
            }
        });
    }

    function batal_transaksi() {
        $.ajax({
            url: "<?php echo base_url().'inventory/delete_trans_adjusment' ?>",
            type: "GET",
            success: function (data) {
                location.reload();
            }
        });
    }

    function validate()
    {
        if( document.getElementById('kode_adjusment').value == "" )
        {
            document.getElementById('kode_adjusment').focus() ;
            $('html, body').animate({ scrollTop: 0 }, 'fast');
            return false;
        }

        if( document.getElementById('tgl_adjusment').value == "" )
        {
            document.getElementById('tgl_adjusment').focus() ;
            $('html, body').animate({ scrollTop: 0 }, 'fast');
            return false;
        }

        if( document.getElementById('lokasi').value == "" )
        {
            document.getElementById('lokasi').focus() ;
            $('html, body').animate({ scrollTop: 0 }, 'fast');
            return false;
        }

        if( document.getElementById('keterangan').value == "" )
        {
            document.getElementById('keterangan').focus() ;
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
        /*var kd_adjusment = $("#kode_adjusment").val();
        var lokasi = $("#lokasi").val();

        if (lokasi == '') {
            $("#alr_lokasi").text("Lokasi belum dipilih!");
        }*/

        if (validate()) {
            $.ajax({
                url: "<?php echo base_url().'inventory/trans_adjusment_x' ?>",
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
                        alert("Transaksi Berhasil!");
                        cetak_transaksi('adjusment_report', 'adjustment', data.kode);
                    } else {
                        alert("Transaksi Gagal!");
                    }
                    //location.reload();
                }
            });
        }
    }
</script>