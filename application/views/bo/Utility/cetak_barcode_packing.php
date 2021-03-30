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
                                <div class="col-sm-2 pull-right">
                                    <button type="button" class="btn btn-primary" id="packing" name="packing" onclick="modal_kode_packing();">Buat kode Packing</button>
                                </div>
                            </div>
                            <hr>
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="panel-heading">
                                        <div class="input-group">
                                            <select class="form-control" id="cat_cari" name="cat_cari">
                                                <option value="4">Kode Packing</option>
                                                <option value="1">Kode Barang</option>
                                                <option value="2">Barcode</option>
                                                <option value="3"><?=$menu_group['as_deskripsi']?></option>
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
                                                <th>Packing</th>
                                                <th>Kode Barang</th>
                                                <th>Barcode</th>
                                                <th>Nama Barang</th>
                                                <th>Kel. Barang</th>
                                                <th><?=$menu_group['as_deskripsi']?></th>
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

<div id="modal_content" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true" style="display: none;">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                <h4 class="modal-title" id="myLargeModalLabel">Form Barcode Packing</h4>
            </div>
            <div class="modal-body">
				<?= form_open(null, array('id'=>'form_barcode_packing')); ?>
                <div class="row">
                    <div class="col-sm-12">
                        <?php $field='kode_barang'; ?>
						<div class="input-group">
                            <label>Kode Barang</label>
                        </div>
						<div class="input-group">
							<input type="text" class="form-control" id="<?=$field?>" name="<?=$field?>" onkeyup="get_data_barang(event)" onchange="get_data_barang(13)" autofocus autocomplete="off" />
                        	<div class="input-group-btn">
								<button onclick="get_data_barang(13)" type="button" class="btn btn-primary"><i class="md md-search"></i></button>
							</div>
						</div>
                        <b class="error" id="alr_<?=$field?>"></b>
                    </div>
                </div>
                <div class="row">
                    <div class="col-sm-12">
                        <?php $field='kode_packing'; ?>
						<div class="input-group">
                            <label>Kode Packing</label>
                        </div>
						<div class="input-group">
							<input type="text" class="form-control" id="<?=$field?>" name="<?=$field?>" readonly autocomplete="off" />
							<div class="input-group-btn">
								<button onclick="generate_kode();" type="button" class="btn btn-primary" id="buat_kode">Buat Kode</button>
							</div>
						</div>
                        <b class="error" id="alr_<?=$field?>"></b>
                    </div>
                </div>
				<div class="row">
                    <div class="col-sm-12">
                        <?php $field='qty_packing'; ?>
						<div class="input-group">
                            <label>Qty Packing</label>
                        </div>
                        <input type="text" class="form-control" id="<?=$field?>" name="<?=$field?>" readonly autocomplete="off" />
                        <b class="error" id="alr_<?=$field?>"></b>
                    </div>
                </div>
				<div class="row">
                    <div class="col-sm-12">
                        <?php $field='barcode_barang'; ?>
						<div class="input-group">
                            <label>Barcode</label>
                        </div>
                        <input type="text" class="form-control" id="<?=$field?>" name="<?=$field?>" readonly autocomplete="off" />
                        <b class="error" id="alr_<?=$field?>"></b>
                    </div>
                </div>
				<div class="row">
                    <div class="col-sm-12">
                        <?php $field='nama_barang'; ?>
						<div class="input-group">
                            <label>Nama Barang</label>
                        </div>
                        <input type="text" class="form-control" id="<?=$field?>" name="<?=$field?>" readonly autocomplete="off" />
                        <b class="error" id="alr_<?=$field?>"></b>
                    </div>
                </div>
				<div class="row">
                    <div class="col-sm-12">
                        <?php $field='artikel'; ?>
						<div class="input-group">
                            <label>Artikel</label>
                        </div>
                        <input type="text" class="form-control" id="<?=$field?>" name="<?=$field?>" readonly autocomplete="off" />
                        <b class="error" id="alr_<?=$field?>"></b>
                    </div>
                </div>
				<div class="row">
                    <div class="col-sm-12">
                        <?php $field='harga_jual'; ?>
						<div class="input-group">
                            <label>Harga Jual</label>
                        </div>
                        <input type="text" class="form-control" id="<?=$field?>" name="<?=$field?>" readonly autocomplete="off" />
                        <b class="error" id="alr_<?=$field?>"></b>
                    </div>
                </div>
                <?= form_close(); ?>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary waves-effect pull-left" onclick="simpan_kode_packing()">Save</button>
                <button type="button" class="btn btn-default waves-effect" data-dismiss="modal">Close</button>
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div>

<script>
    $(document).ready(function () {
        /*get master*/
        get_tmp_master();

        /*get detail*/
        get_tmp_detail(1);
    }).on("keypress", ":input:not(textarea)", function(event) {
		return event.keyCode != 13;
	});
    
	function generate_kode() {
        var kode1 = ('<?=date('y')?>');
        var kode2 = '<?=date('md')?>';
        var random = Math.floor(100000 + Math.random() * 900000);

        var kode = '2'+kode1+kode2+random;
        document.getElementById("kode_packing").value = kode;
    }
	
    function modal_kode_packing(){
		$("#kode_packing").prop('readonly', true);
        $("#qty_packing").prop('readonly', true);
        $("#buat_kode").prop('disabled', true);
		
		$("#kode_barang").val("");
		$("#kode_packing").val("");
		$("#qty_packing").val("");
		$("#barcode_barang").val("");
		$("#nama_barang").val("");
		$("#artikel").val("");
		$("#harga_jual").val("");
        $('#modal_content').modal('show');
    }
    
	function simpan_kode_packing(){
		if($('#kode_barang').val()!='' && $('#kode_packing').val()!=''){
			$.ajax({
				url: "<?php echo base_url() . 'utility/simpan_kode_packing' ?>",
				type: "POST",
				data: $('#form_barcode_packing').serialize(),
				dataType: "JSON",
				success: function (data) {
					get_tmp_detail(1);
					alert('Kode Packing berhasil disimpan');
					$('#modal_content').modal('hide');
				}
			});
		}
	}
	
    function get_tmp_master() {
        $.ajax({
            url: "<?php echo base_url() . 'utility/get_tr_temp_m_packing' ?>",
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
            url: "<?php echo base_url() . 'utility/add_tr_temp_m_packing' ?>",
            data: {lokasi_:lokasi},
            type: "POST",
            dataType: "JSON"
        });
    }

    function update_tmp_master(column, data) {
        $.ajax({
            url: "<?php echo base_url() . 'utility/update_tr_temp_m_packing/' ?>" + btoa(column) + "/" + btoa(data),
            type: "GET"
        });
    }

    function get_tmp_detail(param = 0) {
        $.ajax({
            url: "<?php echo base_url() . 'utility/get_tr_temp_d_packing' ?>",
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
            url: "<?php echo base_url() . 'utility/update_tr_temp_d_packing/' ?>" + btoa(barcode) + "/" + btoa(column) + "/" + btoa(value),
            type: "GET"
        });
    }

    function hapus_barang(barcode) {
        $.ajax({
            url: "<?php echo base_url() . 'utility/delete_tr_temp_d_packing/' ?>" + btoa(barcode),
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
                    url: "<?php echo base_url() . 'utility/get_barang_packing/' ?>" + btoa(barcode) + "/" + btoa(cat_cari) + "/" + btoa(lokasi_barang),
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
    
    function get_data_barang(event) {
        //var cat_cari = $("#cat_cari").val();
        var kode_barang = $("#kode_barang").val();
        //var lokasi_barang = $("#lokasi_barang").val();
        $("#alr_kode_barang").text('');

        if (event.keyCode == 13 || event == 13) {
            $.ajax({
                url: "<?php echo base_url() . 'utility/get_data_barang/' ?>" + btoa(kode_barang),
                type: "GET",
                dataType: "JSON",
                success: function (data) {
                    if (data.status == 1) {
                        $("#kode_packing").prop('readonly', false);
                        $("#qty_packing").prop('readonly', false);
						$("#buat_kode").prop('disabled', false)
						
						$("#kode_barang").val(data.barang['kd_brg']);
                        $("#kode_packing").val(data.barang['kd_packing']);
                        $("#qty_packing").val(isNaN(parseInt(data.barang['qty_packing']))?0:parseInt(data.barang['qty_packing']));
                        $("#barcode_barang").val(data.barang['barcode']);
                        $("#nama_barang").val(data.barang['nm_brg']);
                        $("#artikel").val(data.barang['deskripsi']);
                        $("#harga_jual").val(to_rp(data.barang['hrg_jual_1']));
                    } else {
						$("#kode_packing").prop('readonly', true);
                        $("#qty_packing").prop('readonly', true);
						$("#buat_kode").prop('disabled', true)
						
                        $("#kode_barang").val("").focus();
                        $("#kode_packing").val("");
                        $("#qty_packing").val("");
                        $("#barcode_barang").val("");
                        $("#nama_barang").val("");
                        $("#artikel").val("");
                        $("#harga_jual").val("");
                        $("#alr_kode_barang").text('Barang tidak ditemukan');
                    }
                }
            });
        }
    };

    function batal_transaksi() {
        $.ajax({
            url: "<?php echo base_url().'utility/delete_trans_packing' ?>",
            type: "GET",
            success: function (data) {
                if (data) {
                    location.reload();
                }
            }
        });
    }

    function cetak_barcode() {
        window.open('<?=base_url().'cetak/cetak_barcode_packing'?>');
    }
</script>