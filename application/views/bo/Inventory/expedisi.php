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
                        <?php isset($_GET['trx'])?$update='?trx='.$_GET['trx']:$update=null; ?>
                        <?=form_open_multipart($this->control.'/'.$page.$update, array('class'=>"cmxform form-horizontal tasi-form", 'id'=>'form_transaksi'))?>
                        <?=isset($_GET['trx'])?'<input type="hidden" name="update" value="1" />':''; ?>
                        <div class="panel-body">
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="panel-body">
                                        <div class="row">
                                            <div class="col-sm-6">
                                                <div class="row" style="margin-bottom: 3px">
                                                    <label class="col-sm-4">Tgl Expedisi</label>
                                                    <div class="col-sm-6">
                                                        <div class="input-group date">
                                                            <div class="input-group-addon">
                                                                <i class="fa fa-calendar"></i>
                                                            </div>
                                                            <input class="form-control pull-right datepicker_date_from" readonly name="tgl_expedisi" id="tgl_expedisi" type="text" onchange="update_tmp_master('m3', $(this).val())" value="<?=set_value('tgl_expedisi')?set_value('tgl_expedisi'):date("Y-m-d")?>">
                                                            <!--custom_front_date('pembelian', $(this).val())-->
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="row" style="margin-bottom: 3px">
                                                    <label class="col-sm-4">Lokasi Asal</label>
                                                    <div class="col-sm-6">
                                                        <?php $field = 'lokasi_asal'; ?>
                                                        <?php $option = null; $option[''] = 'Pilih Lokasi';
                                                        foreach($data_lokasi as $row){ $option[$row['Kode']] = $row['Nama']; }
                                                        echo form_dropdown($field, $option, set_value($field), array('class' => 'select2', 'id'=>$field, 'onclick'=>'update_lokasi(\'m4\', \''.$field.'\')', 'required' => 'true')); ?>
                                                        <b class="error" id="alr_<?=$field?>"></b>
                                                    </div>
                                                </div>
                                                <div class="row" style="margin-bottom: 3px">
                                                    <label class="col-sm-4">Lokasi Tujuan</label>
                                                    <div class="col-sm-6">
                                                        <?php $field = 'lokasi_tujuan'; ?>
                                                        <?php $option = null; $option[''] = 'Pilih Lokasi';
                                                        foreach($data_lokasi as $row){ $option[$row['Kode']] = $row['Nama']; }
                                                        echo form_dropdown($field, $option, set_value($field), array('class' => 'select2', 'id'=>$field, 'onclick'=>'update_lokasi(\'m5\', \''.$field.'\')', 'required' => 'true')); ?>
                                                        <b class="error" id="alr_<?=$field?>"></b>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-sm-6">
                                                <div class="row" style="margin-bottom: 3px">
                                                    <label class="col-sm-4">Pengirim</label>
                                                    <div class="col-sm-6">
                                                        <input type="text" onblur="update_tmp_master('m7', $(this).val())" class="form-control" id="pengirim" name="pengirim" required>
                                                        <b class="error" id="alr_pengirim"></b>
                                                    </div>
                                                </div>
                                                <div class="row" style="margin-bottom: 3px">
                                                    <label class="col-sm-4">Catatan</label>
                                                    <div class="col-sm-6">
                                                        <textarea onblur="update_tmp_master('m6', $(this).val())" class="form-control" id="catatan" name="catatan" required></textarea>
                                                        <b class="error" id="alr_catatan"></b>
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
                                            <input type="text" class="form-control" id="barcode" name="barcode" placeholder="Kode Packing">
                                            <div class="input-group-btn">
                                                <?php $field = 'packing' ?>
                                                <button onclick="cari_permohonan()" type="button" id="cari_<?=$field?>" name="cari_<?=$field?>" class="btn btn-primary btn-sm"><i class="fa fa-search"></i></button>
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
                                                <th>Kode Packing</th>
                                                <th>Kode Mutasi</th>
                                                <th>Nama Supp/Jns Brg</th>
                                                <th>Jumlah Koli</th>
                                            </tr>
                                            </thead>
                                            <tbody id="listPacking">
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                            <input type="hidden" name="save">
                            <?=form_close()?>
                            <div class="row">
                                <div class="col-md-7">
                                    <button class="btn btn-primary" onclick="simpan_transaksi()" name="save" id="simpan" type="button">Simpan</button>
                                    <button class="btn btn-primary" onclick="if (confirm('Akan membatalkan transaksi?')){batal_transaksi()}" id="batal" type="button">Batal</button>
                                    <button class="btn btn-primary" onclick="if (confirm('Akan menutup transaksi?')){tutup_transaksi()}" id="keluar" type="button">Keluar</button>
                                    <!--<button class="btn btn-primary" id="arsip_pembelian" type="submit">Arsip Pembelian</button>-->
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade modal-3d-sign" id="modalPermohonan" data-backdrop="static" data-keyboard="false" aria-hidden="true" aria-labelledby="exampleModalTitle" role="dialog" tabindex="-1" style="display: none;">
    <div class="modal-dialog modal-md">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">×</span>
                </button>
                <h4 class="modal-title">Daftar Packing</h4>
            </div>
            <div class="modal-footer">
                <input onclick="tambah_expedisi()" type="button" class="btn btn-primary pull-right" value="Pilih">
            </div>
            <div class="modal-body">
                <table class="table table-bordered">
                    <thead>
                    <tr>
                        <th style="width: 1%">
                            <?php $field = 'semua_permohonan'; ?>
                            <div class="checkbox checkbox-primary">
                                <input class="form-control" type="checkbox" onclick="check_permohonan()" id="<?=$field?>" name="<?=$field?>" value="<?=set_value($field)?set_value($field):null?>">
                                <label for="<?=$field?>"></label>
                            </div>
                        </th>
                        <th>Kode Packing</th>
                        <th>Kode Mutasi</th>
                    </tr>
                    </thead>
                    <tbody id="listPermohonan"></tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<script>
    var trx_edit = ''; if('<?=$this->uri->segment(3)?>'=='edit'){ trx_edit='<?=$_GET['trx']?>'; }

    function check_lokasi() {
        var lokasi_asal = $("#lokasi_asal").val();
        var lokasi_tujuan = $("#lokasi_tujuan").val();

        if (lokasi_asal != '' && lokasi_tujuan != '') {
            if (lokasi_asal == lokasi_tujuan) {
                $("#alr_lokasi_asal").text("Lokasi tidak boleh sama!");
                $("#barcode").prop("disabled", true);
                $("#cari").prop("disabled", true);
                $("#simpan").prop("disabled", true);
                $("#cari_packing").prop("disabled", true);
            } else {
                $("#alr_lokasi_asal").text("");
                $("#barcode").prop("disabled", false);
                $("#cari").prop("disabled", false);
                $("#simpan").prop("disabled", false);
                $("#cari_packing").prop("disabled", false);
            }
        }
    }

    function simpan_transaksi() {
        var max_data = $("#max_expedisi").val();
        if (max_data > 0) {
            if (confirm('Akan menyimpan transaksi?')) {
                $.ajax({
                    url: "<?php echo base_url() . 'inventory/expedisi' ?>",
                    type: "POST",
                    data: $("#form_transaksi").serialize(),
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
                            if (data.trx == 'add') {
                                cetak_transaksi('expedisi_report', 'expedisi', data.kode);
                            } else {
                                window.location = "<?=base_url() . 'inventory/expedisi_report'?>";
                            }
                        } else {
                            alert("Transaksi Gagal!");
                            //location.reload();
                        }
                    }
                });
            }
        } else {
            $("#alr_barang").text("Kode packing masih kosong");
        }
    }

    $("#barcode").keyup(function (event) {
        var lokasi = $("#lokasi_asal").val();
        var lokasi2 = $("#lokasi_tujuan").val();
        hide_notif("alr_barang");

        if (event.keyCode == 13) {
            if (lokasi == '') {
                $("#alr_lokasi_asal").text("Lokasi wajib dipilih!");
            }

            if (lokasi2 == '') {
                $("#alr_lokasi_tujuan").text("Lokasi wajib dipilih!");
            }

            if (lokasi != '' && lokasi2 != '') {
                $.ajax({
                    url: "<?php echo base_url() . $this->control . '/get_kode_packing/' ?>" + btoa($(this).val()) + "/" + btoa(lokasi) + "/" + btoa(lokasi2) + "/" + trx_edit,
                    type: "GET",
                    dataType: "JSON",
                    success: function (data) {
                        if (data.status == 1) {
                            get_tmp_detail();
                        } else {
                            $("#alr_barang").text(data.notif);
                        }
                        $("#barcode").val("").focus();
                    }
                });
            }
        }
    });

    function cari_permohonan() {
        var lokasi = $("#lokasi_asal").val();
        var lokasi2 = $("#lokasi_tujuan").val();

        if (lokasi == '') {
            $("#alr_lokasi_asal").text("Lokasi wajib dipilih!");
        }

        if (lokasi2 == '') {
            $("#alr_lokasi_tujuan").text("Lokasi wajib dipilih!");
        }

        if (lokasi != '' && lokasi2 != '') {
            $("#semua_permohonan").prop('checked', false);
            $.ajax({
                url: "<?php echo base_url() . $this->control . '/cari_permohonan/' ?>" + btoa(lokasi) + "/" + btoa(lokasi2) + "/" + trx_edit,
                type: "GET",
                dataType: "JSON",
                success: function (res) {
                    $("#listPermohonan").html(res.list);
                    $("#modalPermohonan").modal('show');
                }
            });
        }
    }

    function check_permohonan() {
        if ($("#semua_permohonan").is(":checked")) {
            $(".permohonan").prop('checked', true);
        } else {
            $(".permohonan").prop('checked', false);
        }
    }

    function tambah_expedisi() {
        var list = cek_checkbox_checked('permohonan');

        if (list != '') {
            $.ajax({
                url: "<?= base_url() . $this->control . '/tambah_expedisi' ?>",
                type: "POST",
                data: {list_: list, trx_edit_:trx_edit},
                success: function (res) {
                    $("#modalPermohonan").modal('hide');
                    get_tmp_detail();
                }
            });
        } else {
            alert("Data belum dipilih!")
        }
    }

    $(document).ready(function () {
        get_tmp_master();
        get_tmp_detail();
    }).on("keypress", ":input:not(textarea)", function(event) {
        return event.keyCode != 13;
    });

    function get_tmp_master() {
        $.ajax({
            url: "<?= base_url() . $this->control . '/get_tr_temp_m_expedisi/' ?>" + trx_edit,
            type: "GET",
            dataType: "JSON",
            success: function (data) {
                if (data.status == 1) {
                    $('#tgl_expedisi').datepicker("setDate", data.temp['m3']);
                    $('#lokasi_asal').val(data.temp['m4']).trigger('change.select2');
                    $('#lokasi_tujuan').val(data.temp['m5']).trigger('change.select2');
                    $('#catatan').val(data.temp['m6']);
                    $('#pengirim').val(data.temp['m7']);
                }
            },
            error: function (jqXHR, textStatus, errorThrown){ /*location.reload();*/ }
        });
    }

    function update_lokasi(col, id) {
        update_tmp_master(col, $("#"+id).val());
        hide_notif('alr_'+id);
        //delete_tmp_detail('all');
        check_lokasi();
    }

    function update_tmp_master(column, value) {
        $.ajax({
            url: "<?= base_url() . $this->control . '/update_tr_temp_m_expedisi/' ?>" + btoa(column) + "/" + btoa(value) + "/" + trx_edit,
            type: "GET"
        });
    }

    function get_tmp_detail() {
        $.ajax({
            url: "<?= base_url() . $this->control . '/get_tr_temp_d_expedisi/' ?>" + trx_edit,
            type: "GET",
            dataType: "JSON",
            success: function (data) {
                if (data.status == 1) {
                    $("#save").prop('disabled', false);
                } else {
                    $("#save").prop('disabled', true);
                }
                $("#listPacking").html(data.list);
            },
            error: function (jqXHR, textStatus, errorThrown){ /*location.reload();*/ }
        });
    }

    function update_tmp_detail(column, value, id) {
        $.ajax({
            url: "<?= base_url() . $this->control . '/update_tr_temp_d_expedisi/' ?>" + btoa(column) + "/" + btoa(value) + "/" + btoa(id) + "/" + trx_edit,
            type: "GET"
        });
    }

    function delete_tmp_detail(kode) {
        $.ajax({
            url: "<?= base_url() . $this->control . '/delete_tr_temp_d_expedisi/' ?>" + btoa(kode) + "/" + trx_edit,
            type: "GET",
            success: function(){
                get_tmp_detail();
            }
        });
    }

    function batal_transaksi() {
        $.ajax({
            url: "<?= base_url() . $this->control . '/delete_tr_temp_expedisi/' ?>" + trx_edit,
            type: "GET",
            success: function(){
                if (trx_edit != '') {
                    window.location="<?=base_url()?>Report/expedisi_paspor"
                } else {
                    location.reload();
                }
            }
        });
    }
</script>